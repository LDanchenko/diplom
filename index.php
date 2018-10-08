<?php
require_once('Teacher.php');
require_once('Limit.php');
require_once('Groups.php');
require_once('Schedule.php');

//
//$limit = new Limit();
//$limit->setDay("19.09.2018");
//$limit->setTimeStart("09:00");
//$limit->setTimeEnd("12.00");
//$limit2 = new Limit();
//$limit2->setDay("20.09.2018");
//$limit2->setTimeStart("09:00");
//$limit2->setTimeEnd("12.00");
//
//$array[] = [$limit, $limit2];
//$array2[] = [$limit];
//
//$teacher = new Teacher(1, "Данченко", $array, 5, "math", 20);
//$teacher2 = new Teacher(2, "ДАДАД", $array2, 5, "math", 20);
//
//$group = new Groups(1, "KN17", "math", 20);
//
////print_r($group);
//
//$mas[] = [$teacher, $teacher2];
//
////foreach($mas as $value){
//// $array3[]= $value->limit;
//// var_dump($value);
//// foreach ($array3 as $value2) {
////    var_dump($value2);
//// echo "<br/>";
////echo "<br/>";

//}
const NUM_OF_CLS = 3; // оКоличество китериальних класов заисимих от вибраного критнрия.
const PRIORITY_G = 0.25;
const PRIORITY_D = 0.25;
const PRIORITY_L = 0.5;
const CLS_GROUPS = "group";
const CLS_DISCIPLINE = "discipline";
const CLS_TIME = "time";
const CLS_TEACHERS = "teacher";
// Вибраний крит. время.
//$time -время дата пары

function getEnumHash($a, $b)
{
    return $a . ', ' . $b;
}

$restricts[] = array(
    getEnumHash(CLS_GROUPS, CLS_TIME) => array(), //ограничения
    getEnumHash(CLS_GROUPS, CLS_DISCIPLINE) => array(),
    getEnumHash(CLS_GROUPS, CLS_TEACHERS) => array(),
    getEnumHash(CLS_DISCIPLINE, CLS_TIME) => array(),
    getEnumHash(CLS_DISCIPLINE, CLS_TEACHERS) => array(),
    getEnumHash(CLS_TEACHERS, CLS_TIME) => array(),
);
function KT($time): float
{
  //  $result =
    //return $result
}

//function findClassUnions($classA, $classB, $restricts): array
//{
//    $result = array();
//    foreach ($restricts as $key => $value) {
//        list($restrictA, $restrictB) = explode(", ", $key);
//        if ($restrictA === $classA || $restrictA === $classB ||
//            $restrictB === $classA || $restrictB === $classB) {
//            $result[] = $key;
//        }
//    }
//
//    return $result;
//}


function Local_K($priority, $restricts, $localclas): float
{
    $values[] = array();
    $masiv[] = array();
    foreach ($localclas as $value){
       // list($restrictA, $restrictB) = explode(", ", $value);
        //array_push($masiv, findClassUnions($restrictA, $restrictB, $restricts));
      //  $value
    }
    $masiv [] = array_unique($masiv);

    weight_fm(max($masiv), $priority);
    //&????

}

function weight_fm($priority, $val): float
{
    $result = normalizate_value($val) * $priority;
    return $result;

}

//нормализация значений
function normalizate_value(... $values): float
{
    $result = 0;
    foreach ($values as $value) {
        if ($value == 0) {
            $value = 0.0000001;
        }
        $result += floatval($value);
    }
    return $result / count($values);


}
