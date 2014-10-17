<?php


require_once("lib/EavApi.php");
require_once("../../../lib/interlabscms/mysql.php");


$dbConfig = new stdClass();
$dbConfig->dbhost = 'mysql.interlabs.lan';
$dbConfig->dbuser = 'devel';
$dbConfig->dbpass = 'Nerryad3';
$dbConfig->dbtable = 'dev_test';
$db = DataBaseMysql::getDBO($dbConfig);

$set = new EavSet();
$fields = new EavFields();



// echo var_dump($set->sets);
// echo var_dump($set->getByName('Фарфор'));


// echo var_dump($fields->getByName('Жанр живописи'));
// echo var_dump($fields->fields);

// $setId = $set->add('Пушки');
// $fieldId = $fields->add('Калибр');

$setId = 11;
$fieldId = 24;

// echo var_dump($set->attach($setId, $fieldId));

// echo var_dump($fields->addValues($fieldId, array('122мм', '152мм', '203мм', '270мм')));


