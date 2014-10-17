<?php

class EavSet {
  private $db;
  public function __construct() {
    $this->db = DataBaseMysql::getDBO();
    $this->reload();
    }
  
  private $sets = array();  
  public function reload() {
    $query = "SELECT * FROM `eav_set`";
    $this->sets = $this->db->SelectSet($query, 'id');
    return $this;
    }
  
  public function getByName($name) {
    $ret = array();
    foreach($this->sets as $set) {
      if ($set['name'] == $name) $ret[] = $set['id'];
      }
    return $ret;
    }
    
  public function add($name, $_ord = 0) {
    $ord = (int)$_ord;
    $query = "INSERT INTO `eav_set` (`name`, `ord`) VALUES ('".addslashes($name)."', $ord)";
    $this->db->Query($query);
    $id = $this->db->SelectLastInsertId();
    $this->reload();
    return $id;
    }
    
  public function remove() {}
  
  public function attach($_setId, $_fieldId) {
    $setId = (int)$_setId;
    $fieldId = (int)$_fieldId;
    if ($setId == 0 || $fieldId == 0) return false;
    $query = "INSERT INTO `eav_set_fields` (`eav_set_id`, `eav_fields_id`) VALUES ($setId, $fieldId)";
    $this->db->Query($query);
    $id = $this->db->SelectLastInsertId();
    return $id > 0;
    }
  
  public function __get($name) {
    switch($name) {
      case 'sets':
        return $this->sets;
      default:
        return null;
      }
    }
  
  }