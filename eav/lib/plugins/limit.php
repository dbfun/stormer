<?php

$limit = function($obj) {
  $obj->page = isset($_REQUEST['page']) ? (int)$_REQUEST['page'] : 1;
  $obj->page = $obj->page > 0 ? $obj->page : 1;
  
  $obj->itemsCount = isset($_REQUEST['count']) ? (int)$_REQUEST['count'] : 20;
  $obj->itemsCount = $obj->itemsCount > 0 ? $obj->itemsCount : 20;
  
  $obj->offset = ($obj->page - 1) * $obj->itemsCount;
  };