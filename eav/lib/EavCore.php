<?php

/*
  Таблицы
    `eav_set` - наборы полей (фильтров)
    `eav_set_fields` - поля
    `eav_set_fields` - связка наборы-поля
    `eav_available_values` - допустимые значения полей

*/

class EavCore {
  private $itemsSelectQuery,
    $eavFields = array(),
    $minPrice, $maxPrice, $priceSignMagnitude = 100, $db;
  public $fieldCounters = array();
  
  public function __construct($itemsSelectQuery) {
    $this->db = DataBaseMysql::getDBO();
    $this->itemsSelectQuery = $itemsSelectQuery;
    }
    
  public function initMinMaxPrice() {
    list($this->minPrice, $this->maxPrice) = self::getMinMaxPrice($this->itemsSelectQuery);
    return $this;
    }
    
  private function getMinMaxPrice($itemsSelectQuery) {
    if (empty($itemsSelectQuery)) return array(0, 0);
    $query = "SELECT MIN(price) AS `min_price`, MAX(price) AS `max_price`
              FROM `entity` WHERE `id` IN ($itemsSelectQuery)";
    $results = $this->db->SelectSet($query);
    $results = current($results);
    return array($results['min_price'], $results['max_price']);
    }
    
  
  public function initPossibleValues($_setId) {
    $setId = (int)$_setId;
    $query = "SELECT * FROM `eav_set_fields` AS `sf` 
              JOIN `eav_fields` AS `f` ON (f.id = sf.eav_fields_id) 
              WHERE sf.eav_set_id = $setId
              ORDER BY f.ord";
    $this->eavFields = $this->db->SelectSet($query, 'id');
    return $this;
    }
  
  // Заполнение полей возможными значениями (страница каталога)
  public function fillPossibleValues() {
    if (count($this->eavFields) == 0) return $this;
    foreach ($this->eavFields as &$fieldRow) {
      $possibleValues = $this->getPossibleValues($fieldRow['id'], $this->itemsSelectQuery);
      if (is_array($possibleValues) && count($possibleValues) > 0) {
        $fieldRow['possibleValues'] = $possibleValues;
        $_firstItem = current($fieldRow['possibleValues']);
        $fieldRow['field_type'] = $_firstItem['field_type'];
        $fieldRow['field_id'] = $_firstItem['field_id'];
        } else { $fieldRow['field_type'] = -1; }
      }
    return $this;
    }
    
  function getPossibleValues($_fieldId, $itemsSelectQuery) {
    if (empty($itemsSelectQuery)) return null;
    $fieldId = (int)$_fieldId;
    $query = "SELECT COUNT(*) AS `entity_count`, entity_field_values.* FROM `entity_field_values` WHERE `field_id` = $fieldId
      AND `entity_id` IN ($itemsSelectQuery) 
      GROUP BY field_id, value_id 
      ORDER BY eav_fields_ord, eav_available_values_ord";
    $fieldValues = $this->db->SelectSet($query);
    return $fieldValues;
    }
    
  public function fillFiltersCounters($eavFilters) {
    foreach($eavFilters as $_filterId => $_valueId) {
      $eavFiltersExcluded = $eavFilters;
      unset($eavFiltersExcluded[$_filterId]);
      $this->eavFields[$_filterId]['filtered'] = $eavFiltersExcluded;
      $this->eavFields[$_filterId]['is_plus'] = true;
      }
    foreach($this->eavFields as $_filterId => &$_filterField) {
      if(!isset($_filterField['filtered'])) {
        $_filterField['filtered'] = $eavFilters;
        $_filterField['is_plus'] = false;
        }
      }
    unset($_filterField, $_valueId, $eavFiltersExcluded);
    
    
    foreach($this->eavFields as $_filterId => $_filterField) {
      
      $_itemsSelectQuery = $this->itemsSelectQuery;
      foreach($_filterField['filtered'] as $filterId => $valueId) {
          $_itemsSelectQuery = "SELECT `entity_id` AS `id` FROM `entity_search_values` WHERE "
            ." `field_id` = ".(int)$filterId
            ." AND `value_id` IN(".addslashes(implode(',', $valueId)).")"
            ." AND `entity_id` IN(".$_itemsSelectQuery.")";
          }
        $filterValues = $this->getFilterValues($_filterId, $_itemsSelectQuery);
        if(is_array($filterValues) && count($filterValues) > 0) foreach($filterValues as $fValue) {
          $isCurrentFilter = isset($eavFilters[$_filterField['id']]) && in_array($fValue['value_id'], $eavFilters[$_filterField['id']]);
          $sign = !$isCurrentFilter && $_filterField['is_plus'] ? '+' : '';
          $this->fieldCounters[$_filterId][$fValue['value_id']] = $sign.$fValue['items_count'];
          }
      }
    return $this;
    }
    
  private function getFilterValues($fieldId, $itemsSelectQuery) {
    if (empty($itemsSelectQuery)) return null;
    $query = "SELECT COUNT(*) AS `items_count`, `value_id`, `field_id` FROM `entity_search_values` WHERE "
            ." `field_id` = ".(int)$fieldId
            ." AND `entity_id` IN(".$itemsSelectQuery.") "
            ." GROUP BY `value_id`";
    $filterValues = $this->db->SelectSet($query);
    return $filterValues;
    }
    
  public function __get($name) {
    switch ($name) {
      case 'minPrice':
        return (int)floor($this->minPrice / $this->priceSignMagnitude) * $this->priceSignMagnitude;
        break;
      case 'maxPrice':
        return (int)ceil($this->maxPrice / $this->priceSignMagnitude) * $this->priceSignMagnitude;
        break;
      case 'eavFields':
        return $this->eavFields;
      default:
        return null;
      }
    }
    
  public function __isset($name) { return isset($this->$name); }
  }
