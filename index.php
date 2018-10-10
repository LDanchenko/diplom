<?php

require_once('Utils.php');
require_once('KeyValueMapStorage.php');
require_once('Rules.php');
require_once('Time.php');
require_once('Lecturer.php');
require_once('Group.php');
require_once('Discipline.php');
require_once('Limits.php');
require_once('ScheduleEntry.php');


const PRIORITY_GROUP_WEIGHT = 0.25; //приоритет группы
const PRIORITY_DISCIPLINE_WEIGHT = 0.25; //дисциплині
const PRIORITY_LECTURER_WEIGHT = 0.5; //преподы


const INTERSECT_CLS_GROUP_TIME = "INTERSECT_CLS_GROUP_TIME"; //???
const INTERSECT_CLS_GROUP_DISCIPLINE = "INTERSECT_CLS_GROUP_DISCIPLINE"; //done
const INTERSECT_CLS_GROUP_LECTURER = "INTERSECT_CLS_GROUP_LECTURER";//done
const INTERSECT_CLS_DISCIPLINE_TIME = "INTERSECT_CLS_DISCIPLINE_TIME"; //???
const INTERSECT_CLS_DISCIPLINE_LECTURER = "INTERSECT_CLS_DISCIPLINE_LECTURER"; //done
const INTERSECT_CLS_LECTURER_TIME = "INTERSECT_CLS_LECTURER_TIME"; //делаю

const ADDITIONAL_RETRY_TIMES_COUNT = 3; //попыток?

/**
 * Дни недели в которые учаться студенты, для которых необходимо составить расписания.
 * 0=Sunday, 1=Monday, etc.
 * INFO: Разные групи могут учиться на разных сменах в разные дни, соответственно они попадают в одно расписания,
 *       им необходимо прописать соответствующее правило(Rule). Данный же параметр является глобальным для всех груп
 *       одновременно.
 */
const EDUCATION_WEEKDAYS = array(false, true, false, false, false, false, false); //понедельник - 1 TODO: в какие дни группа учится из БД

const NUM_OF_EVENTS_IN_PERIOD = 20; //сколько за указанній период пар - указать - посчитать
const NUM_OF_EVENTS_PER_DAY = 4; //пар в день
const SCHEDULE_PERIOD_EDUCATION_DAYS_LENGTH = NUM_OF_EVENTS_IN_PERIOD / NUM_OF_EVENTS_PER_DAY; //к-во учебных дней TODO: задавать конец периода и считать данные параметры


//$ruleInstance - екземпляр абстрактн класса rule
//вызываем правила из RULE для разных классов рахные и считаем коефициенты
function callRuleCalculations(Rule $ruleInstance, $entry) {
    $value = $ruleInstance->calculate($entry); // calculate вызываем из класса
    $priority = $ruleInstance->getPriority(); //получаем приоритет - setPriority  RULEі к классам дописать контрукторы
    return Utils::applyWeightImpact($value, $priority); //приоритет + вес
}

//rules - части restrict
//вернули средний коефициент для оного обьекта entry по разным правилам классов - вычитали среднеее
function getClassRuleValues($rules, $entry) {
    $rulesCount = count($rules);
    if ($rulesCount === 0) {
        return 1;
    }

    $values = array();
    foreach ($rules as $rule) {
        //вызываем функцию для разных обьектов правил чтобы получить коефициенты
        $value = callRuleCalculations($rule, $entry);

        if ($value === 0) {
            return 0;
        }

        $values[] = $value; //собравли все коеф. в один массив
    }

    return array_sum($values) / $rulesCount; //среднее - все коеф. на кол-во правил/классов
}

// getClassRuleValues- кол правил и классов!!!!!!
function calculateLocalClassCoefficient($entry, $weight, $classes) {
    $values = array();
    foreach ($classes as $cls) {
        //вызываем правила на обьекты класса
        $value = getClassRuleValues($cls, $entry);

        if ($value === 0) {
            return 0;
        }

        // в массив все получнный значения
        $values[] = $value;
    }

    $valuesCount = count($values);
    if ($valuesCount === 0) {
        return 0;
    }

    //среднее передали
    $localClassCoefficient = array_sum($values) / $valuesCount;

    //вес это приоритет
    return Utils::applyWeightImpact($localClassCoefficient, $weight);
}

//TODO: разставить класи в порядке следоватльности от первого наиболее вероятного 0.
//высчитать коефициент групп
function calculateLocalGroupCoefficient($restricts, $entry) {
    $weight = PRIORITY_GROUP_WEIGHT;
    $classes = array(
        $restricts[INTERSECT_CLS_GROUP_DISCIPLINE],
        $restricts[INTERSECT_CLS_GROUP_LECTURER],
        $restricts[INTERSECT_CLS_GROUP_TIME],
    );

    return calculateLocalClassCoefficient($entry, $weight, $classes);
}

//коефдисц
function calculateLocalDisciplineCoefficient($restricts, $entry) {
    $weight = PRIORITY_DISCIPLINE_WEIGHT;
    $classes = array(
        $restricts[INTERSECT_CLS_GROUP_DISCIPLINE],
        $restricts[INTERSECT_CLS_DISCIPLINE_TIME],
        $restricts[INTERSECT_CLS_DISCIPLINE_LECTURER],
    );

    return calculateLocalClassCoefficient($entry, $weight, $classes);
}

//по преподам
function calculateLocalLecturerCoefficient($restricts, $entry) {
    $weight = PRIORITY_LECTURER_WEIGHT;
    $classes = array(
        $restricts[INTERSECT_CLS_GROUP_LECTURER],
        $restricts[INTERSECT_CLS_LECTURER_TIME],
        $restricts[INTERSECT_CLS_DISCIPLINE_LECTURER],
    );

    return calculateLocalClassCoefficient($entry, $weight, $classes);
}

//определяем колличество значений для итерации
//передаем пар в неделю пар в день ??
function calculateBatchSize(...$values) {
    return ceil(max($values));
}

//пересечение по времени - конфликты?
function clsHasTimeIntersect($collection, ScheduleEntry $value) {
    $time = $value->getTime(); //время взяли из сборного обьекта
    $lecturer = $value->getLecturer(); //препод
    $group = $value->getGroup(); //группа

    foreach ($collection as $entry) { // collection - уже созданное расписание
        if ($collection instanceof KeyValueMapStorage) { //логика не понятна?
            $entry = $entry[0];
        }

        if (($entry->getTime() === $time && $entry->getLecturer() === $lecturer) || //препод в определенное время уже занят
            ($entry->getTime() === $time && $entry->getGroup() == $group)) { // группа в опр. время занята?
            return true;
        }
    }

    return false;
}


function calculateCoefficients($restricts, $time, $groups, $disciplines, $lecturers) {
    $coefficients = new KeyValueMapStorage();

    foreach ($groups as $group) {
        foreach ($disciplines as $discipline) {
            foreach ($lecturers as $lecturer) {
                $entry = new ScheduleEntry($time, $discipline, $lecturer, $group);

                $localGroupCoefficient = calculateLocalGroupCoefficient($restricts, $entry);
                if ($localGroupCoefficient === 0) {
                    continue;
                }

                $localDisciplineCoefficient = calculateLocalDisciplineCoefficient($restricts, $entry);
                if ($localDisciplineCoefficient === 0) {
                    continue;
                }

                $localLecturerCoefficient = calculateLocalLecturerCoefficient($restricts, $entry);
                if ($localLecturerCoefficient === 0) {
                    continue;
                }
                //все коефициенты полученный от всех правил
                $classesCoefficients = [$localGroupCoefficient, $localDisciplineCoefficient, $localLecturerCoefficient];
                // In fact extra. When data from Rule.calculate corrected don`t needed.
                // TODO: убрать?
                $classesCoefficients = Utils::normalizeCollection($classesCoefficients, 0, 1);
                //усреднить все коеф.
                $entryCoefficient = array_sum($classesCoefficients) / count($classesCoefficients);
                $coefficients->append($entry, $entryCoefficient); //добавили в массив - для всех обьектов будут массивы коеф.
            }
        }
    }

    return $coefficients;
}

function distributeEvents($eventsTimes, $restricts, $groups, $disciplines, $lecturers) {
    $distributedSchedule = new KeyValueMapStorage();//сделали обьект ключ значение
    $scheduleMaxSize = count($eventsTimes); //кол-во обьектов time в массиве
    $additionalRetryTimesCounter = ADDITIONAL_RETRY_TIMES_COUNT; //попытки?

    while (count($eventsTimes) > 0 && $additionalRetryTimesCounter > 0) { //пока в массиве времени и попытки есть
        $availableToDistribute = count($eventsTimes); //считаем свободное время
        $entriesToDistribute = new KeyValueMapStorage(); //новый обьект ключ значение куда будем писать коеф
        $batchSize = calculateBatchSize( //тут не понятно как считаем? срез?
            NUM_OF_EVENTS_IN_PERIOD,
            NUM_OF_EVENTS_PER_DAY,
            $availableToDistribute
        );

        foreach ($eventsTimes as $time) { //перебираем обьекты Time
            //тут передалть для GroupTimeAvailable уже готовое рассписание?
            $coefficients = calculateCoefficients($restricts, $time, $groups, $disciplines, $lecturers); //считаем коефициенты для заданых данных
            $coefficients = $coefficients->topByValue($batchSize);
            $entriesToDistribute->extend($coefficients);
        }

        $batchToDistributeCount = count($entriesToDistribute);
        $entriesToDistribute = $entriesToDistribute->topByValue($batchToDistributeCount, true); //???
//???
        foreach ($entriesToDistribute as $item) {
            if (count($distributedSchedule) >= $scheduleMaxSize) {
                break;
            }

            list($entry, $coefficient) = $item;

            if (!clsHasTimeIntersect($distributedSchedule, $entry)) {
                Utils::removeFromArrayByValue($eventsTimes, $entry->getTime());
                $distributedSchedule[$entry] = $coefficient;

                break;
            }
        }

        $currentDistributedCount = count($eventsTimes);
        if ($availableToDistribute <= $currentDistributedCount) {
            $additionalRetryTimesCounter--;
        }

    }

    return $distributedSchedule;
}


// TODO: Fixed time from DB with dynamic duration & break between them.
//TODO: для каждой группы нужно проверять смену, задавать перемены иправильное время пар
function buildEventsTimes(DateTime $startDate, $length, $weekDaysFilter, $numOfEventsPerDay) {
    $eventsTimes = array();
    $numOfEventsPerDay = ceil($numOfEventsPerDay); //округлили в большую сторону полученное кол пар в день
    $nextDay = clone $startDate; //склонили дату начала составления расписания

    while ($length > 0) { //к-во дней учебных
        $currentDay = clone $nextDay;
        $nextDay = (clone $currentDay)->modify('+1 day'); //следующий денm
        $currentWeekDay = $currentDay->format('w'); //день недели

        if ($weekDaysFilter[$currentWeekDay] !== true) { //день недели - по номеру из масива  - true или false true -делаем распис false выходной
            continue;
        }

        for ($i = 0; $i < $numOfEventsPerDay; $i++) { //цикл - кол пар в день
            $eventTime = clone $currentDay->setTime(14 + (2 * $i), 0); //TODO: почему 14? пары по два часа - поправить?
            //записали в массив расписания пар - обьект типа time - начало пары и продолжительность
            $eventsTimes[] = new Time($eventTime, 80);
        }

        $length--; //одну пару сделали, уменьшили кол
    }

    return $eventsTimes; //вернули массив обьектов типа Time
}

//екземпляры обьектов правил
$restricts = array(
    INTERSECT_CLS_GROUP_TIME => array(),
    INTERSECT_CLS_GROUP_DISCIPLINE => array(new GroupDisciplineAvailable()),
    INTERSECT_CLS_GROUP_LECTURER => array(),
    INTERSECT_CLS_DISCIPLINE_TIME => array(),
    INTERSECT_CLS_DISCIPLINE_LECTURER => array(new DisciplineLectureAvailableToEducationDiscipline()),
    INTERSECT_CLS_LECTURER_TIME => array(),
);

$schedulePeriodStartDate = new DateTime(
    gmdate('d.m.Y H:i', strtotime('2018-10-08')),
    new DateTimeZone('GMT')
);

$eventsTimes = buildEventsTimes(
    $schedulePeriodStartDate,
    SCHEDULE_PERIOD_EDUCATION_DAYS_LENGTH, //к-во учебных дней
    EDUCATION_WEEKDAYS,
    NUM_OF_EVENTS_PER_DAY
);


// Sample data;
$disciplines = array(
    new Discipline(0, 'Європейські інформаційно-аналітичні сисмети'),
    new Discipline(1, 'Сучасні технології в зовнішній торгівлі'),
    new Discipline(2, 'Міжнародні та регіональні фінансові структури'),
    new Discipline(3, 'Креативно-інноваційний менеджмент'),
    new Discipline(4, 'Управління фінансовими ризиками'),
    new Discipline(5, 'Управління конкурентоспроможністю підприємства'),
    new Discipline(6, 'Дослідницькі семінари та підготовка дипломної роботи'),
    new Discipline(7, 'Іноземна мова мова спеціальності'),
    new Discipline(8, 'Податкові системи ЄС'),
    new Discipline(9, 'Пропагандистські технології у міжнародних відносинах'),
    new Discipline(10, 'Проблеми адаптації укр. компаній до Європейського бізнес-середовища'),
    new Discipline(11, 'Економіка нематеріальних активів'),
    new Discipline(12, 'Етика і культура маркетингової діяльності'),
    new Discipline(13, 'Інформаційні війни'),
    new Discipline(14, 'Моделювання в міжнародному менеджменті'),
);

$groups = array(
    new Group(0, 'МЕВ/ЄС-17м', array(
        $disciplines[2],
        $disciplines[3],
        $disciplines[6],
        $disciplines[7],
        $disciplines[8],
        $disciplines[12],
        $disciplines[13],
        $disciplines[14],
    )),
    new Group(1, 'Е/Креатив-17м', array(
        $disciplines[1],
        $disciplines[4],
        $disciplines[5],
        $disciplines[6],
        $disciplines[10],
        $disciplines[11],
    )),
    new Group(2, 'МЕВ/ЄС-18м', array(
        $disciplines[0],
        $disciplines[8],
        $disciplines[9],
        $disciplines[10],
        $disciplines[11],
        $disciplines[12],
        $disciplines[13],
        $disciplines[14],
    )),
);

$limit1Start = new DateTime(
    gmdate('d.m.Y H:i', strtotime('2018-10-08')),
    new DateTimeZone('GMT'));
$limit1Start->setTime(14,55);

$limit1End = clone ($limit1Start)->setTime(18,00);


$limit = array (new Limits($limit1Start, $limit1End),
    new Limits($limit1Start, $limit1End) );

$lecturers = array(
    new Lecturer(0, 'Мельничук Д.П.', array(
        $disciplines[2],
        $disciplines[3],
        $disciplines[6],
        $disciplines[7],
        $disciplines[8],
        $disciplines[12],
        $disciplines[13],
        $disciplines[14],
    ), array(  $limit[0],
    $limit[1])),
    new Lecturer(1, 'Чумаченко О.Г.', array(
        $disciplines[1],
        $disciplines[4],
        $disciplines[5],
        $disciplines[6],
        $disciplines[10],
        $disciplines[11],
    ), array(  $limit[0],
        $limit[1])),
    new Lecturer(2, 'Сова О.Ю.', array(
        $disciplines[0],
        $disciplines[8],
        $disciplines[9],
        $disciplines[10],
        $disciplines[11],
        $disciplines[12],
        $disciplines[13],
        $disciplines[14],
    ),array(  $limit[0],
        $limit[1])),
);

$schedule = distributeEvents($eventsTimes, $restricts, $groups, $disciplines, $lecturers);
prettySchedulePrint($schedule);

function prettySchedulePrint($schedule) {
    $index = 1;
    foreach ($schedule as $item) {
        list($entry, $kf) = $item;
        echo '#' . $index . ' [' . $kf . '][' .
            $entry->getTime()->getStartDate()->format('Y-m-d H:i:s') . '] ' .
            $entry->getGroup()->getName() . ' - ' .
            $entry->getDiscipline()->getName() . ' - ' .
            $entry->getLecturer()->getName() .
            PHP_EOL;

        $index++;
    }
}
