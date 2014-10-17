<?php
class SearchCond {
  private $db;
  public function __construct() { $this->db = DataBaseMysql::getDBO(); }
    
  private $commonFilters = array();
  public function addCommonFilter($filter) { $this->commonFilters[] = $filter; return $this; }
  
  private $eavFilter;
  public function addEavFilter($filter) { $this->eavFilter = $filter; return $this; }
  
  private $acceptOrder = array(), $acceptOrderDirection = array('ASC', 'DESC'),
          $orderField, $orderDirection;
  public function setOrderBy($acceptOrder, $orderField, $_orderDirection) {
    $this->acceptOrder = (array)$acceptOrder;
    $orderDirection = mb_strtoupper($_orderDirection);

    $_defVal = reset($this->acceptOrder);
    $this->orderField = in_array($orderField, $this->acceptOrder) ? $orderField : $_defVal;
    
    $_defVal = reset($this->acceptOrderDirection);
    $this->orderDirection = in_array($orderDirection, $this->acceptOrderDirection) ? $orderDirection : $_defVal;
    }
  
  private $limitPlugin;
  public function setLimit($limitPlugin) {
    $this->limitPlugin = $limitPlugin;
    }
  
  private $eavCore, $commonSelectQuery, $commonSelectQueryDirection, $entityIds, $countEntity;
  public function search() {
    // Limit
    if(isset($this->limitPlugin)) call_user_func($this->limitPlugin, $this);
  
    // Common filters
    $whereConditions = array();
    if (count($this->commonFilters) > 0) foreach($this->commonFilters as $filter) {
      $condition = $filter($this);
      if($condition) $whereConditions[] = $condition;
      }
      
    $whereConditions = count($whereConditions) > 0 ? 'WHERE '.implode(' AND ', $whereConditions) : null;
    
    $this->commonSelectQuery = "SELECT `id` FROM `entity` $whereConditions ";
    $this->commonSelectQueryDirection = "SELECT `id` FROM `entity` $whereConditions "
      .(isset($this->orderField, $this->orderDirection) ? "ORDER BY {$this->orderField} {$this->orderDirection}" : null);
      
    if(isset($this->eavFilter)) call_user_func($this->eavFilter, $this);
    
    // die($this->commonSelectQueryDirection);
    
    $this->entityIds = $this->db->SelectSet($this->commonSelectQueryDirection." LIMIT {$this->offset}, {$this->itemsCount}");
    $this->countEntity = $this->db->SelectValue("SELECT COUNT(*) FROM (".$this->commonSelectQueryDirection.") AS `any_alias`");
    
    

    $this->eavCore = new EavCore($this->commonSelectQuery); 
    $this->eavCore->
      initPossibleValues(3)->
      fillFiltersCounters($this->eavFilters);
    }
    
  public function __set($name, $value) { $this->$name = is_array($value) || is_object($value) ? $value : trim($value); }
  
  public function __get($name) { return isset($this->$name) ? $this->$name : null; }
    
  public function __isset($name) { return isset($this->$name); }
  }
  
?>