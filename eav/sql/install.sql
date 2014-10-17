-- ������������ �������� �������, ����� �� ��� (������) �� ������ EAV

-- ������� �������� ����������� � �����-���� �������� (����� ������� ���� ������� �������� ��������)
-- ������ ������������ ����� ���������� ����� ������� (����)

-- ������ �������� ���������� ��������� �������
-- ��������, ������ �������� "�������" (�������� ��������� � ������� �������� "�������")
-- � ��� ������ ������ "�������� �������", "�������� �����" - �� ���� ���������� �������
-- �������� ������� - ��������� ��������: ��������� �����, ������ �����, ���������

-- ������ ������
-- ������ �������� "����������"
-- ������� ���� ������ - ��� ���������, �����, ������, ������
-- ��������� �������� ���� ��������� - ����������� ��������, ����������������
-- ����������� ������ �� ����� � ������ ��������� �� �������������, ��������� ���������
-- ������ �������� ��������� ��� �������� 0-100k ��, 100k-200k ��, ��������, �������� ��������� �� �������������

-- � ��������, �������� ������������������ �� �������� ������, �������� ����������� �� ���� ��������������
-- InnoDB ������������ ��� �������� �������� �����, � ��� �������� ������ ��������� ����� 
-- ������� ���������� (���� �������� ��������� �������� �������� �����)
-- ������ ������ ������ �����: http://imperator-art.ru/catalog_7.htm

-- filters_set > eav_set
-- items_fields > eav_fields
-- filters_fields > eav_set_fields
-- items_available_values > eav_available_values
-- items_attr_val > eav_field_val
-- attr_id > field_id
-- item_id > entity_id
-- items > entity
-- filter_id > field_id
-- filter > field

-- ����� ��������
CREATE TABLE `eav_set` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '',
  `ord` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name_idx` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ������� - ��������, ����� (����� �� ����������)
CREATE TABLE `eav_fields` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '',
  `alias` varchar(255) NOT NULL DEFAULT '',
  `type` int(11) unsigned NOT NULL DEFAULT '0',
  `ord` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- �������-������ �������� � ������ ��������
CREATE TABLE `eav_set_fields` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `eav_set_id` int(11) unsigned NOT NULL,
  `eav_fields_id` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_idx` (`eav_set_id`,`eav_fields_id`),
  KEY `fk_eav_fields_id` (`eav_fields_id`),
  CONSTRAINT `eav_set_fields_ibfk_1` FOREIGN KEY (`eav_set_id`) REFERENCES `eav_set` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_eav_fields_id` FOREIGN KEY (`eav_fields_id`) REFERENCES `eav_fields` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ��������� �������� �������� (����)
CREATE TABLE `eav_available_values` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `eav_fields_id` int(11) unsigned NOT NULL DEFAULT '0',
  `value` char(255) NOT NULL DEFAULT '',
  `ord` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_idx` (`eav_fields_id`,`value`),
  KEY `eav_fields_idx` (`eav_fields_id`),
  CONSTRAINT `eav_available_values_ibfk_1` FOREIGN KEY (`eav_fields_id`) REFERENCES `eav_fields` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



-- �������-������ �������, �������� � ��������
CREATE TABLE `eav_field_val` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `entity_id` int(11) unsigned NOT NULL DEFAULT '0',
  `field_id` int(11) unsigned NOT NULL DEFAULT '0',
  `value_id` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_attr_value` (`entity_id`,`field_id`),
  KEY `search_idx` (`entity_id`, `field_id`, `value_id`),
  CONSTRAINT `eav_field_val_ibfk_1` FOREIGN KEY (`entity_id`) REFERENCES `entity` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `eav_field_val_ibfk_2` FOREIGN KEY (`field_id`) REFERENCES `eav_fields` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `eav_field_val_ibfk_3` FOREIGN KEY (`value_id`) REFERENCES `eav_available_values` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ������������� ��� ����������� � ������
CREATE VIEW `entity_eav` AS 
SELECT `eav_set_fields`.`eav_set_id` AS `eav_set_id`, `eav_set_fields`.`eav_fields_id` AS `eav_fields_id`, `eav_available_values`.`id` AS `id`,  `eav_available_values`.`value` AS `value`,  `eav_fields`.`id` AS `field_id`,  `eav_fields`.`name` AS `attr_name`,  `eav_fields`.`type` AS `field_type` 
FROM ((`eav_set_fields` JOIN `eav_fields` ON ((`eav_fields`.`id` = `eav_set_fields`.`eav_fields_id`))) 
JOIN `eav_available_values` ON ((`eav_available_values`.`eav_fields_id` = `eav_fields`.`id`)));

CREATE VIEW `entity_field_values` AS 
SELECT `eav_field_val`.`entity_id` AS `entity_id`,  `eav_field_val`.`value_id` AS `value_id`,  `eav_fields`.`name` AS `field_name`,  `eav_fields`.`id` AS `field_id`,  `eav_fields`.`type` AS `field_type`,  `eav_available_values`.`value` AS `value`,  `eav_fields`.`ord` AS `eav_fields_ord`,  `eav_available_values`.`ord` AS `eav_available_values_ord` 
FROM ((`eav_field_val` JOIN `eav_fields` ON ((`eav_fields`.`id` = `eav_field_val`.`field_id`))) 
JOIN `eav_available_values` ON ((`eav_available_values`.`id` = `eav_field_val`.`value_id`)));

CREATE VIEW `entity_search_values` AS 
SELECT `eav_field_val`.`entity_id` AS `entity_id`,  `eav_field_val`.`value_id` AS `value_id`,  `eav_fields`.`name` AS `field_name`,  `eav_fields`.`id` AS `field_id`,  `eav_fields`.`type` AS `field_type`,  `eav_available_values`.`value` AS `value`
FROM ((`eav_field_val`  join `eav_fields` ON ((`eav_fields`.`id` = `eav_field_val`.`field_id`)))  
JOIN `eav_available_values` ON ((`eav_available_values`.`id` = `eav_field_val`.`value_id`)));

-- ��������
ALTER TABLE `eav_set` ADD `catalog_id` INT(11) UNSIGNED NOT NULL DEFAULT 0;

