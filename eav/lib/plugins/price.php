<?php

$filter = function($obj) {
  $obj->priceMin = isset($_REQUEST['price_min']) ? (float)$_REQUEST['price_min'] : null;
  $obj->priceMax = isset($_REQUEST['price_max']) ? (float)$_REQUEST['price_max'] : null;
  if ($obj->priceMin == 0) $obj->priceMin = null;
  switch (true) {
    case isset($obj->priceMin) && isset($obj->priceMax):
      return "price BETWEEN {$obj->priceMin} AND {$obj->priceMax}";
    case isset($obj->priceMin):
      return "price >= {$obj->priceMin}";
    case isset($obj->priceMax):
      return "price <= {$obj->priceMax}";
    default:
      return null;
    }
  };