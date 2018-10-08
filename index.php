<?php
require_once('Teacher.php');
require_once('Limit.php');
require_once('Groups.php');
require_once('ScheduleEntry.php');


const PRIORITY_G = 0.25; //приоритет группы
const PRIORITY_D = 0.25; //дисциплині
const PRIORITY_L = 0.5; //преподы
const CLS_GROUPS = "group";
const CLS_DISCIPLINE = "discipline";
const CLS_TIME = "time";
const CLS_TEACHERS = "teacher";

const INTERSECT_CLS_GROUP_TIME = "INTERSECT_CLS_GROUP_TIME";
const INTERSECT_CLS_GROUP_DISCIPLINE = "INTERSECT_CLS_GROUP_DISCIPLINE";
const INTERSECT_CLS_GROUP_LECTURER = "INTERSECT_CLS_GROUP_LECTURER";
const INTERSECT_CLS_DISCIPLINE_TIME = "INTERSECT_CLS_DISCIPLINE_TIME";
const INTERSECT_CLS_DISCIPLINE_LECTURER = "INTERSECT_CLS_DISCIPLINE_LECTURER";
const INTERSECT_CLS_LECTURER_TIME = "INTERSECT_CLS_LECTURER_TIME";

$restricts[] = array(
    INTERSECT_CLS_GROUP_TIME => array(), //екземпляры обьектов правил
    INTERSECT_CLS_GROUP_DISCIPLINE => array(),
    INTERSECT_CLS_GROUP_LECTURER => array(),
    INTERSECT_CLS_DISCIPLINE_TIME => array(),
    INTERSECT_CLS_DISCIPLINE_LECTURER => array(),
    INTERSECT_CLS_LECTURER_TIME => array(),
);


const NUM_OF_EVENTS_IN_PERIOD = 20; //пар в неделю - посчитать
const NUM_OF_EVENTS_PER_DAY = 4; //пар в день

//ruleinst - екземпляр абстрактн класса rule
//
function getClassRuleValue($ruleInstance, $entry)
{
    //вызо метода обьекта унаследованного от абс клас
    $method = array($ruleInstance, 'calculate');
    $arguments = array($entry);
    $value = call_user_func_array($method, $arguments);
    return $value;
}

//rules - части restrict
function getClassRuleValues($rules, $entry)
{
    $values = array();
    foreach ($rules as $rule) {
        $value = getClassRuleValue($rule, $entry);

        if ($value === 0) {
            return 0;
        }

        $values[] = $value;
    }

    return array_sum($values) / count($values);
}

//вызов правил
function calculateLocalClassValue($entry, $weight, $classes)
{
    $values = array();
    foreach ($classes as $cls) {
        $value = getClassRuleValues($cls, $entry); //вызываем правила на обьекты класса

        if ($value === 0) {
            return 0;
        }

        $values[] = $value; // в массив все получнный значения
    }

    $kf = array_sum($values) / count($values); //среднее передали

    return calculateLocalWight($kf, $weight);//вес это приоритет
}

//TODO: разставить класи в порядке следоватльности от первого наиболее вероятного 0.
//высчитать коефициент групп
function calculateLocalGroupKf($restricts, $entry)
{
    $weight = PRIORITY_G;
    $classes = array(
        $restricts(INTERSECT_CLS_GROUP_TIME),
        $restricts(INTERSECT_CLS_GROUP_DISCIPLINE),
        $restricts(INTERSECT_CLS_GROUP_LECTURER),
    );

    return calculateLocalClassValue($entry, $weight, $classes);
}

//коефдисц
function calculateLocalDisciplineKf($restricts, $entry)
{
    $weight = PRIORITY_D;
    $classes = array(
        $restricts(INTERSECT_CLS_GROUP_DISCIPLINE),
        $restricts(INTERSECT_CLS_DISCIPLINE_TIME),
        $restricts(INTERSECT_CLS_DISCIPLINE_LECTURER),
    );

    return calculateLocalClassValue($entry, $weight, $classes);
}

//по преподам
function calculateLocalLecturerKf($restricts, $entry)
{
    $weight = PRIORITY_L;
    $classes = array(
        $restricts(INTERSECT_CLS_GROUP_LECTURER),
        $restricts(INTERSECT_CLS_LECTURER_TIME),
        $restricts(INTERSECT_CLS_DISCIPLINE_LECTURER),
    );

    return calculateLocalClassValue($entry, $weight, $classes);
}

function calculateClassValue($localValues)
{
    return array_sum($localValues) / count($localValues);
}

//??? нормалищация - не используется - всунуть
function normalize($value, $minLimitValue, $maxLimitValue)
{
    $maxLimitValue = $maxLimitValue - $minLimitValue;

    if ($maxLimitValue === 0) {
        $maxLimitValue = 1;
    }

    return ($value - $minLimitValue) - $maxLimitValue;
}

function calculateLocalWight($localKf, $weight)
{
    return $localKf ^ (1 / $weight); //приоритет - weight
}

//определяем колличество значений для итерации
function calculateBatchSize($numOfEvents, $minNumOfEvents)
{
    return max(array(
        sqrt($numOfEvents),
        ceil($minNumOfEvents), //округление к большенй
    ));
}

function createFlatArray($array)
{
    // TODO: 1 массив из массивов
    return $array;
}

function clsHasIntersect($collection, $value)
{
    //TODO:
    //$collection -

    return false;
}

$disciplines = array();
$groups = array();
$lecturers = array();

function calculateKt($restricts, $time, $groups, $disciplines, $lecturers)
{
    $values = array();

    foreach ($groups as $group) {
        foreach ($disciplines as $discipline) {
            foreach ($lecturers as $lecturer) {
                $entry = new ScheduleEntry($time, $discipline, $lecturer, $group);
                $localG = calculateLocalGroupKf($restricts, $entry);
                if ($localG === 0) {
                    continue;
                }

                $localD = calculateLocalDisciplineKf($restricts, $entry);
                if ($localD === 0) {
                    continue;
                }

                $localL = calculateLocalLecturerKf($restricts, $entry);
                if ($localL === 0) {
                    continue;
                }

                $key = $entry->hash();
                $dsClasses = [$localG, $localD, $localL];
                $kf = array_sum($dsClasses) / count($dsClasses);
                $values[$key] = $kf;
            }
        }
    }

    return $values;
}

// TODO: сгенерировать пример eventsTimes
// TODO: Исключть из наборов данные которы уже распределены;
function distributeEvents($eventsTimes, $restricts, $groups, $disciplines, $lecturers)
{
    $distributedSchedule = array();
    $conflicts = array();
    $batchSize = calculateBatchSize(
        NUM_OF_EVENTS_IN_PERIOD,
        NUM_OF_EVENTS_PER_DAY
    );

    // TODO: Макс количество прходов.whilt остались липарі - еслиза 5 проходов не прошло то стоп
    // TODO: Остались ли нераспределение пари...
    while (true) {
        $availableToDistribute = count($eventsTimes) - count($distributedSchedule);
        $batchToDistribute = array();

        foreach ($eventsTimes as $time) {
            // TODO: if $time not alredy distributed...
            if (false) {
                continue;
            }
            $values = calculateKt($restricts, $time, $groups, $disciplines, $lecturers);
            sort($values);
            $values = array_slice($values, 0, $batchSize);
            $batchToDistribute[] = $values;
        }

        for ($i = 0; $i < $availableToDistribute; $i++) {
            $batchToDistribute = createFlatArray($batchToDistribute);
            sort($batchToDistribute);

            foreach ($batchToDistribute as $value) {
                if (clsHasIntersect($distributedSchedule, $value) || clsHasIntersect($conflicts, $value)) {
                    $conflicts[] = $value;
                    continue;
                }

                $distributedSchedule[] = $value;
            }
        }

        $conflicts = array();
    }

    return $distributedSchedule;
}

// TODO: Заполнить необходимими парами...
$eventsTimes = array();


$schedule = distributeEvents($eventsTimes, $restricts, $groups, $disciplines, $lecturers);
var_dump($schedule);
