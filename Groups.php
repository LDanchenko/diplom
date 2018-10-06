<?php
/**
 * Created by PhpStorm.
 * User: lubasha
 * Date: 06.10.2018
 * Time: 13:46
 */

class Groups
{
private $id;
private $name;
private $discipline;
private $time;

    /**
     * Groups constructor.
     * @param $id
     * @param $name
     * @param $discipline
     * @param $time
     */
    public function __construct($id, $name, $discipline, $time)
    {
        $this->id = $id;
        $this->name = $name;
        $this->discipline = $discipline;
        $this->time = $time;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
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
    public function setDiscipline($discipline)
    {
        $this->discipline = $discipline;
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
    public function setTime($time)
    {
        $this->time = $time;
    }

}