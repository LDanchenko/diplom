<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 08.10.18
 * Time: 10:59
 */

abstract class Rules
{
    public function GT(Groups $groups, Time $time) {
      return 1; //???
    //    может в группе еще где то определять время пары?
    }

    public function GD ( $group_disp, Discipline $discipline)
    {

            if ($group_disp == $discipline->name) {
                return 1;//какие цифры - важность?
            } else return 0;

    }

    public function GL($group_disp, $teacher_disp){


            if ($group_disp == $teacher_disp){
                return 1;
            }
        else return 0;

    }


    public function DT(Discipline $discipline, Time $time){

            return 1; //вообще никак не влияет - значит 1
    }

    public function DL(Discipline $discipline, Teacher $teacher){


       // if ($discipline>)
        return 1; //вообще никак не влияет - значит 1 - список дисциплин нужен для подставноки в групу ? можно учителю определить нагрузку?:
    //список дисциплин в групе или отдельно?
    }


    public function LT(Discipline $discipline, Teacher $teacher){
//teacher time

    }
    //все ли правила?
}