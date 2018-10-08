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
    public $discipline_id;
    public $teacher_id;
    public $group_id;

    /**
     * ScheduleEntry constructor.
     * @param $time
     * @param $discipline_id
     * @param $teacher_id
     * @param $group_id
     */
    public function __construct($time, $discipline_id, $teacher_id, $group_id)
    {
        $this->time = $time;
        $this->discipline_id = $discipline_id;
        $this->teacher_id = $teacher_id;
        $this->group_id = $group_id;
    }

    public function hash() {

        return (string)$this->time . ':' . $this -> discipline_id . ':' . $this -> $teacher_id . ':' . $this->group_id;

    }

    public static function createHash($time, $discipline_id, $teacher_id, $group_id){
        $schedule = new ScheduleEntry($time, $discipline_id, $teacher_id, $group_id);
        return $schedule->hash();
    }

}