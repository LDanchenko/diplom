<?php
/**
 * Created by PhpStorm.
 * User: lubasha
 * Date: 06.10.2018
 * Time: 13:46
 */

class ScheduleEntry
{

    public $time;
    public $discipline;
    public $teacher;
    public $group;

    /**
     * ScheduleEntry constructor.
     * @param $time
     * @param $discipline
     * @param $teacher
     * @param $group
     */
    public function __construct($time, $discipline, $teacher, $group)
    {
        $this->time = $time;
        $this->discipline = $discipline;
        $this->teacher = $teacher;
        $this->group = $group;
    }

    public function hash()
    {
        return (string)$this->time . ':' . $this->discipline->getId() . ':' .
            $this->teacher->getId() . ':' . $this->group->getId();
    }

    public static function createHash($time, $discipline_id, $teacher_id, $group_id)
    {
        $schedule = new ScheduleEntry($time, $discipline_id, $teacher_id, $group_id);
        return $schedule->hash();
    }

    /**
     * @return mixed
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * @param mixed $time
     */
    public function setTime($time): void
    {
        $this->time = $time;
    }

    /**
     * @return mixed
     */
    public function getDiscipline()
    {
        return $this->discipline;
    }

    /**
     * @param mixed $discipline
     */
    public function setDiscipline($discipline): void
    {
        $this->discipline = $discipline;
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
    public function setTeacher($teacher): void
    {
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
    public function setGroup($group): void
    {
        $this->group = $group;
    }


}