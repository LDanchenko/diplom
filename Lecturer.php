<?php


class Lecturer {
    private $id;
    private $name;
    private $disciplines;
    private $limits;
    /**
     * Lecturer constructor.
     * @param $id
     * @param $name
     * @param $disciplines
     */
    //limits по сути тоже что и time убарть? запутаюсь
    public function __construct($id, $name, $disciplines,$limits) {
        $this->id = $id;
        $this->name = $name;
        $this->disciplines = $disciplines;
        $this->limits = $limits;
    }

    /**
     * @return mixed
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id): void {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name): void {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getDisciplines() {
        return $this->disciplines;
    }

    /**
     * @return mixed
     */
    public function getLimits()
    {
        return $this->limits;
    }

    /**
     * @param mixed $limits
     */
    public function setLimits($limits): void
    {
        $this->limits = $limits;
    }

    /**
     * @param mixed $disciplines
     */
    public function setDisciplines($disciplines): void {
        $this->disciplines = $disciplines;
    }

    public function hasDiscipline(Discipline $discipline) {
        return in_array($discipline, $this->disciplines);
    }

}
