<?php

$filter = function($obj) {
  $obj->text = isset($_REQUEST['text']) ? $_REQUEST['text'] : null;
  if(!$obj->text) $obj->text = null;
  if(isset($obj->text)) {
    return 'CONCAT_WS(" ", name) LIKE "%'.addslashes($obj->text).'%"';
    }
  return null;
  };