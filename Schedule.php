<?php
/**
 * Created by PhpStorm.
 * User: lubasha
 * Date: 06.10.2018
 * Time: 13:46
 */

class Schedule
{
private $group;
private $date;
private $day;
private $time_start;
private $time_end;
private $teacher;

    /**
     * Schedule constructor.
     * @param $group
     * @param $date
     * @param $day
     * @param $time_start
     * @param $time_end
     * @param $teacher
     */
    public function __construct($group, $date, $day, $time_start, $time_end, $teacher)
    {
        $this->group = $group;
        $this->date = $date;
        $this->day = $day;
        $this->time_start = $time_start;
        $this->time_end = $time_end;
        $this->teacher = $teacher;
    }

    /**
     * @return mixed
     */
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * @param mixed $group
     */
    public function setGroup($group)
    {
        $this->group = $group;
    }

    /**
     * @return mixed
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param mixed $date
     */
    public function setDate($date)
    {
        $this->date = $date;
    }

    /**
     * @return mixed
     */
    public function getDay()
    {
        return $this->day;
    }

    /**
     * @param mixed $day
     */
    public function setDay($day)
    {
        $this->day = $day;
    }

    /**
     * @return mixed
     */
    public function getTimeStart()
    {
        return $this->time_start;
    }

    /**
     * @param mixed $time_start
     */
    public function setTimeStart($time_start)
    {
        $this->time_start = $time_start;
    }

    /**
     * @return mixed
     */
    public function getTimeEnd()
    {
        return $this->time_end;
    }

    /**
     * @param mixed $time_end
     */
    public function setTimeEnd($time_end)
    {
        $this->time_end = $time_end;
    }

    /**
     * @return mixed
     */
    public function getTeacher()
    {
        return $this->teacher;
    }

    /**
     * @param mixed $teacher
     */
    public function setTeacher($teacher)
    {
        $this->teacher = $teacher;
    }

}