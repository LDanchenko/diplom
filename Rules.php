<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 08.10.18
 * Time: 10:59
 */

//нагрузка на препода группу  + приоритет

abstract class Rule
{
    public abstract function calculate(ScheduleEntry $entry);
}

class GroupDisciplineAvailable extends Rule
{
    public function calculate(ScheduleEntry $entry)
    {
        $group = $entry->getGroup();
        $discipline = $entry->getDiscipline();


        return $group->hasDiscipline($discipline);//if 1
    }
}


abstract class Rules
{
    public function GT(Groups $groups, Time $time)
    {
        return 1; //???
        //    может в группе еще где то определять время пары?
    }

    //читается ли у группыы дисциполина
    public function GD($group_disp, Discipline $discipline)
    {

        if ($group_disp == $discipline->name) {
            return 1;//какие цифры - важность?
        } else return 0;

    }

//тут просто читаем ли препод дисциплину оч важно
    public function GL($group_disp, $teacher_disp)
    {


        if ($group_disp == $teacher_disp) {
            return 1;
        } else return 0;

    }


    public function DT(Discipline $discipline, Time $time)
    {

        return 1; //вообще никак не влияет - значит 1
    }

    //читает и учитель дисциплину оч важно
    public function DL(Discipline $discipline, $teacher_disc)
    {
        if ($teacher_disc == $discipline->name) {
            return 1;
        } else return 0;
        //список дисциплин в групе или отдельно?
    }

//тут у учителя берутся ограничения! - когда может читать!
//НУЖНО ГДЕ ТО ПРАВИЛО В ПРИОРИТЕТОМ!!!!!
//тоже непонятно - берем расписание на две недели делаем - тогда ограничения норм - пару дат, для семестра считать что все остальные дни может?
//как то учесть что он не занят на других парах???
    public function LT(Time $time, Teacher $teacher)
    {
//teacher time
        $limit = new Limit();
        $limit = $teacher->limit;
        $date = $limit->date;
        $time_start = strtotime($limit->time_start); //время когда препод может - начало
        $time_end = strtotime($limit->time_end); // время когда может - конец
        if (($time->date == $date) && ($time_start <= $time->time_start) && ($time_end >= $time->time_end)) {
            return 1;
        } else return 0;


    }
    //все ли правила?
}