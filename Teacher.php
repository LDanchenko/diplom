<?php
/**
 * Created by PhpStorm.
 * User: lubasha
 * Date: 06.10.2018
 * Time: 13:46
 */


class Teacher
{
public $id;
public $name;
public $limit = array();
public $coeficient;
public $discipline;
public $time;

    /**
     * Teacher constructor.
     * @param $id
     * @param $name
     * @param array $limit
     * @param $coeficient
     * @param $discipline
     * @param $time
     */
    public function __construct($id, $name, array $limit, $coeficient, $discipline, $time)
    {
        $this->id = $id;
        $this->name = $name;
        $this->limit = new Limit();
        $this->coeficient = $coeficient;
        $this->discipline = $discipline;
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
    public function setDiscipline($discipline)
    {
        $this->discipline = $discipline;
    }

    /**
     * Teacher constructor.
     * @param $id
     * @param $name
     * @param array $limit
     * @param $coeficient
     */


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
     * @return array
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * @param array $limit
     */
    public function setLimit($limit)
    {
        $this->limit = $limit;
    }

    /**
     * @return mixed
     */
    public function getCoeficient()
    {
        return $this->coeficient;
    }

    /**
     * @param mixed $coeficient
     */
    public function setCoeficient($coeficient)
    {
        $this->coeficient = $coeficient;
    }
    /**
     * Teacher constructor.
     * @param $id
     * @param $name
     * @param $limit
     */
    }