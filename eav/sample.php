<?php

require_once("../../../lib/interlabscms/mysql.php");

class sample {
  private $db;
  public function __construct() {
    $dbConfig = new stdClass();
    $dbConfig->dbhost = 'mysql.interlabs.lan';
    $dbConfig->dbuser = 'devel';
    $dbConfig->dbpass = 'Nerryad3';
    $dbConfig->dbtable = 'dev_test';
    $this->db = DataBaseMysql::getDBO($dbConfig);
    
    $this->eavValues = $this->db->SelectSet("SELECT * FROM `entity_eav` WHERE `eav_set_id` = 6");
    $this->eavCount = count($this->eavValues);
    
    
    }
    
  public function add() {
    // die(var_dump($this->eavValues));
    
    
    $numEav = rand(1, 10);
    $props = array();
    for($i = 1; $i <= $numEav; $i++) {
      $propId = rand(0, $this->eavCount - 1);
      $prop = $this->eavValues[$propId];
      $props[$prop['field_id']] = $prop['id'];
      }
    $price = rand(100, 10000);
    $name = 'Фарфор '.$prop['value'];
    $query = "INSERT INTO `entity` (`name`, `price`, `catalog_id`) VALUES ('".addslashes($name)."', $price, 1)";
    
    $this->db->Query($query);
    $entityId = $this->db->SelectLastInsertId();
    
    foreach($props as $fieldId => $valueId) {
      $query = "INSERT INTO `eav_field_val` VALUES (DEFAULT, $entityId, $fieldId, $valueId)";
      $this->db->Query($query);
      }
      
      
    }
  }
  
$sample = new sample();
for($c = 1; $c <= 1000; $c++) {
  $sample->add();
  }