<?php
class EavEntity {
  private $db;
  public function __construct() {
    $this->db = DataBaseMysql::getDBO();
    $this->reload();
    }
    
  public function attach($entityId, $av) {
    
    $query = "INSERT IGNORE INTO `eav_field_val` (`entity_id`, `field_id`, `value_id`) VALUES ";
    }
    
  public function getAvailable($_setId) {
    $setId = (int)$_setId;
    $query = "SELECT FROM 
      `eav_set_fields` JOIN `eav_fields` 
      ON eav_set_fields.eav_fields_id = eav_fields.id
      WHERE eav_set_fields.eav_set_id = $setId";
    $this->db->SelectSet($query);
    }  
  


}