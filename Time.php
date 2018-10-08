<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 08.10.18
 * Time: 10:41
 */
//время - дата пары начало конец
//нужен ли день недели?*
//T
class Time
{

    public $date;
    public $time_start;
    public $time_end;

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
    public function getTimeStart()
    {
        return $this->time_start;
    }

    /**
     * @param mixed $ime_start
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
     * Time constructor.
     * @param $date
     * @param $ime_start
     * @param $time_end
     */
    public function __construct($date, $ime_start, $time_end)
    {
        $this->date = $date;
        $this->ime_start = $ime_start;
        $this->time_end = $time_end;
    }

}