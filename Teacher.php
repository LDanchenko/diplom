<?php
/**
 * Created by PhpStorm.
 * User: lubasha
 * Date: 06.10.2018
 * Time: 13:46
 */

//L
class Teacher
{
public $id;
public $name;
public $limit;
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
    public function __construct($id, $name, Limit $limit, $coeficient, Discipline $discipline, $time)
    {
        $this->id = $id;
        $this->name = $name;
        $this->limit = $limit;
        $this->coeficient = $coeficient;
        $this->discipline = $discipline;
        $this->time = $time;
    }

    /**
     * @return mixed

    /**
     * @return array
     */


    /**
     * @return mixed

     * @param $limit
     */
    }