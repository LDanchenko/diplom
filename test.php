<?php
require_once ('Discipline.php');
require_once ('Limits.php');
require_once ('Lecturer.php');
require_once ('Time.php');
$disciplines = array(
    new Discipline(0, 'Європейські інформаційно-аналітичні сисмети'),
    new Discipline(1, 'Сучасні технології в зовнішній торгівлі'),
    new Discipline(2, 'Міжнародні та регіональні фінансові структури'),
    new Discipline(3, 'Креативно-інноваційний менеджмент'),
);
$lectureTime = new DateTime(
    gmdate('d.m.Y H:i', strtotime('2018-10-08')),
    new DateTimeZone('GMT'));
$lectureTime->setTime(14,03);
$timeLec = new Time($lectureTime, 80); //время лекции


$limit1Start = new DateTime(
    gmdate('d.m.Y H:i', strtotime('2018-10-08')),
    new DateTimeZone('GMT'));
$limit1Start->setTime(14,55);

$limit1End = clone ($limit1Start);
$limit1End->setTime(16,40);

$limit = array (new Limits($limit1Start, $limit1End),
    new Limits($limit1Start, $limit1End) );

$lecturers = array(
    new Lecturer(0, 'Мельничук Д.П.', array(
        $disciplines[2],
        $disciplines[3],
    ), $limit),
    new Lecturer(1, 'Чумаченко О.Г.', array(
        $disciplines[1],
        $disciplines[2],
    ), $limit),
    new Lecturer(2, 'Сова О.Ю.',   $disciplines[0],$limit),
);
//лимиты - нужно вывести дату начала и продолжительность
$lim_lect = $lecturers[0]->getLimits();
//$lim_lect_time = (string)($lecturers[0]->getLimits()->getDurationInMinutes());
//var_dump($lim_lect);
foreach ($lim_lect as $limit){
    $startLecture = $limit->getStartDate();
    $endLecture = $limit->getEndDate();

    $timePara = $timeLec->getStartDate();

    $hoursStart = $startLecture->format('H');
    $minutesStart = $startLecture->format('i');

    $hoursEnd = $endLecture->format('H');
    $minutesEnd = $endLecture->format('i');
  //  $resut
    $day = $timePara->format('d.m.Y');
 //   echo 'day ' . $day;
    $dayLectureStart = $startLecture->format('d.m.Y');
    $dayLectureEnd = $endLecture->format('d.m.Y');

    //  echo 'day 2' . $day2;
    if (($day == $dayLectureEnd) && ($day == $dayLectureStart)){ // пример одного дня- переделать для многих дней

        $minutesParaStart = ($timePara->format('H'))*60 + ((int)($timePara->format('i')));
        $minutesParaEnd = $minutesParaStart + $timeLec->getDurationInMinutes();
     //   echo 'старт пары ' . $minutesParaStart . ' ';
     //   echo 'конец пары ' . $minutesParaEnd . ' ';
        //$hoursLectureSt

        $minutesLectureStart = $hoursStart*60 + $minutesStart;
     //   echo 'лектор старт' . $minutesLectureStart . ' ';

        $minutesLectureEnd = $hoursEnd*60 + $minutesEnd;
       // echo 'лекто конец ' . $minutesLectureEnd . ' ';
            if ($minutesLectureStart <= $minutesParaStart && $minutesParaEnd <=$minutesLectureEnd )
            {
                echo 1;
            }
            else {
                echo 0;
            }
    }
    else {
        echo  0;
    }

}



//echo 'Лимит препода ден с ' . $lim_lect . ' время ' . $lim_lect_time . 'минут';
//сделать чтобы тоже выводило date time - dte time start end!!!!
//echo "\n";
//$lim_pair = (string)($Limit2->getStartDate()->format('Y-m-d H:i:s'));
//echo 'Пара ' . $lim_pair ;
//echo "\n";
//определяем дату пар из time - с duration по одной в time
//в цикле проверка
