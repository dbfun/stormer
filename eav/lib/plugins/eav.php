<?php

$eavFilter = function($obj) {
  $obj->eavFilters = isset($_REQUEST['filters']) && is_array($_REQUEST['filters']) ? $_REQUEST['filters'] : null;
  if (isset($obj->eavFilters)) {
    /* 
     * есть фильтры по параметрам
     * выбираем с использование подзапросов id
    */
    foreach($obj->eavFilters as $filterId => $valueId) {
      $obj->commonSelectQueryDirection = "SELECT `entity_id` AS `id` FROM `entity_search_values` WHERE "
        ." `field_id` = ".(int)$filterId
        ." AND `value_id` IN(".addslashes(implode(',', (array)$valueId)).")"
        ." AND `entity_id` IN($obj->commonSelectQueryDirection)";
      
      }
  } else {
    /*
     * поиск исключительно по свойствам товара
    */
    }
  };