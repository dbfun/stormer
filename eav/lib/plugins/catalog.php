<?php

$filter = function($obj) {
  $obj->catId = isset($_REQUEST['catid']) ? (int)$_REQUEST['catid'] : null;
  $obj->isWholeCatalog = isset($_REQUEST['is_whole']) ? (bool)$_REQUEST['is_whole'] : null;
  if (!$obj->isWholeCatalog && isset($obj->catId)) {
    // $branchIds = CatalogStructure::getInstance()->getBranchIds($obj->catId);
    // $whereConditions[] = 'catalog_id IN ('.implode(',', $branchIds).')';
    return "catalog_id = {$obj->catId}";
    }
  return null;
  };