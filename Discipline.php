<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 08.10.18
 * Time: 10:44
 */
//D
class discipline
{
public $id;
public $name;
public $time;

    /**
     * discipline constructor.
     * @param $id
     * @param $name
     * @param $time
     */
    public function __construct($id, $name, $time)
    {
        $this->id = $id;
        $this->name = $name;
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

    public static function searchDisciplineName($id, $array){
        $name = "";
        foreach ($array as $item){
            if ($item->id == $id){
                $name =  $item->name;
            }
        }
        return $name;
    }

}