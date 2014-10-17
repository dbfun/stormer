<?
require_once("lib/EavCore.php");
require_once("lib/SearchCond.php");
require_once("../../../lib/interlabscms/mysql.php");





class eav {
  public function __construct() {
    $dbConfig = new stdClass();
    $dbConfig->dbhost = 'mysql.interlabs.lan';
    $dbConfig->dbuser = 'devel';
    $dbConfig->dbpass = 'Nerryad3';
    $dbConfig->dbtable = 'dev_test';
    $this->db = DataBaseMysql::getDBO($dbConfig);
    
    $this->eavParams = new stdClass();
    $this->eavParams->itemsSelectQuery = "SELECT `id` FROM `entity`";
    $this->eavParams->setId = 1;
    
    $_REQUEST['price_min'] = 0;
    $_REQUEST['price_max'] = '100000';
    $_REQUEST['catid'] = 1;
    $_REQUEST['fabid'] = 1;
    $_REQUEST['text'] = '';
    $_REQUEST['is_whole'] = false;
    $_REQUEST['filters'] = array(12 => array(48));
    $_REQUEST['order']['field'] = 'price';
    $_REQUEST['order']['direction'] = 'desc';
    $_REQUEST['count'] = 20;
    $_REQUEST['page'] = 1;
    
    
    }
    
  public function filters() {
    
    $this->eavCore = new EavCore($this->eavParams->itemsSelectQuery);
    $this->eavCore->
      initMinMaxPrice()->
      initPossibleValues($this->eavParams->setId)->
      fillPossibleValues();
    
    die(var_dump($this->eavCore->eavFields));
    }
    
  private function is_closure($t) {
    return is_object($t) && ($t instanceof Closure);
    }
    
  public function search() {
    $searchCond = new SearchCond();
      
    $plugins = array('price.php', 'catalog.php', 'text.php');
    if (count($plugins) > 0) {
      foreach ($plugins as $plug) {
        require_once("lib/plugins/$plug");
        if (self::is_closure($filter)) $searchCond->addCommonFilter($filter);
        }
      }
      
    require_once("lib/plugins/eav.php"); // $eavFilter
    $searchCond->addEavFilter($eavFilter);
    
    require_once("lib/plugins/limit.php"); // $limit
    $searchCond->setLimit($limit);
    
    $searchCond->setOrderBy(array('price', 'id'), $_REQUEST['order']['field'], $_REQUEST['order']['direction']); 
    
    $searchCond->search();
    
    var_dump($searchCond->entityIds);
    var_dump($searchCond->countEntity);
    var_dump($searchCond->eavCore->fieldCounters);
    // die();

    }
  
  
  }
  
$eav = new eav();
// $eav->filters();
$eav->search();



/*
    private function getCustomValue($name) {
    return isset($_SESSION['eav'.$name]) ? $_SESSION['eav'.$name] : $this->getDefaultValue($name);
    }
    
  private function setCustomValue($name, $value) {
    $_SESSION['eav'.$name] = $value;
    }
    */