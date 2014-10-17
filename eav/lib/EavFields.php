<?php

class EavFields {
  private $db;
  public function __construct() {
    $this->db = DataBaseMysql::getDBO();
    $this->reload();
    }
    
  private $fields = array();  
  public function reload() {
    $query = "SELECT * FROM `eav_fields`";
    $this->fields = $this->db->SelectSet($query, 'id');
    return $this;
    }
    
  public function getByName($name) {
    $ret = array();
    foreach($this->fields as $field) {
      if ($field['name'] == $name) $ret[] = $field['id'];
      }
    return $ret;
    }
    
  public function add($name, $alias = '', $_ord = 0) {
    $ord = (int)$_ord;
    $query = "INSERT INTO `eav_fields` (`name`, `alias`, `ord`) VALUES ('".addslashes($name)."', '".addslashes($alias)."', $ord)";
    $this->db->Query($query);
    $id = $this->db->SelectLastInsertId();
    $this->reload();
    return $id;
    }
    
  public function remove() {}
  
  public function addValues($_fieldId, $_values) {
    $values = (array)$_values;
    $fieldId = (int)$_fieldId;
    $ord = 0;
    $insert = array();
    if (count($values) == 0 || $fieldId == 0) return false;
    foreach($values as $val) {
      $insert[] = "($fieldId, '".addslashes($val)."', $ord)";
      $ord++;
      }
    $query = "INSERT IGNORE INTO `eav_available_values` (`eav_fields_id`, `value`, `ord`) VALUES ".implode(', ', $insert);
    $this->db->Query($query);
    return $this->db->getNumAffectedRows() > 0;
    }
    
  public function __get($name) {
    switch($name) {
      case 'fields':
        return $this->fields;
      default:
        return null;
      }
    }

}