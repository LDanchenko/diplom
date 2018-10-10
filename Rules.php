<?php


abstract class Rule {
    const DEFAULT_PRIORITY = 5;
    private $priority;
//чего не передаем в функцию?
    /**
     * Rule constructor.
     */
    public function __construct() {
        $this->priority = Rule::DEFAULT_PRIORITY;
    }

    /**
     * @return mixed
     */
    public function getPriority() {
        return $this->priority;
    }

    /**
     * @param mixed $priority
     */
    public function setPriority($priority): void {
        $this->priority = $priority;
    }

    public abstract function calculate(ScheduleEntry $entry);
}


class GroupDisciplineAvailable extends Rule {
    public function calculate(ScheduleEntry $entry) {
        $group = $entry->getGroup();
        $discipline = $entry->getDiscipline();

        return (float)$group->hasDiscipline($discipline);
    }
}


class DisciplineLectureAvailableToEducationDiscipline extends Rule
{

    public function calculate(ScheduleEntry $entry)
    {
        $discipline = $entry->getDiscipline();
        $lecturer = $entry->getLecturer();

        return (float)$lecturer->hasDiscipline($discipline);
    }

}
//???
    class GroupTimeAvailable extends Rule {

    public function calculate(ScheduleEntry $entry)
    {
        $time = $entry -> getTime();
        //  группа свободна ли для этой пары?
        //проверить рассписание что заполнено на это время:?


        return null;
    }

}


//ведет ли у группы проепод
    class GroupLecture extends Rule{

    public function calculate(ScheduleEntry $entry)
    {
        $discipline = $entry->getDiscipline();
        $lecturer = $entry->getLecturer();
        $group = $entry->getGroup();
        //если препод ведет дисциплину и у группы читается эта дисциплина
        if (((float)$lecturer->hasDiscipline($discipline) && (float)$group->hasDiscipline($discipline)) == true){
            return true;
        }
        else return false;

    }

}

///???
class DisciplineTimeAvailable extends Rule {

    public function calculate(ScheduleEntry $entry)
    {
       //тут непонятно?
//time - то что определили в  buildEventsTimes

        return null;
    }

}


class TimeLecture extends Rule{

    public function calculate(ScheduleEntry $entry)
    {


    }

}

    //TODO:нагрузка
//Это одно из правил. Но здесь нужно бить внимательным ведь у нас за раз распределяться несколько пар,
//и более чем вероятна ситуация когда распределяться больше пар чем того допускает нагрузка.
//Хранить в БД нужно не вычитанные две недели а конкретно вычитанные пари,
//таким образом можно будет создавать расписание заново с любой даты и в любой момент времени.
//Возможно если все будет хорошо стоит сделать чтобы в батче можно было одновременно распределять только одно значения,
//вроде все достаточно бистро, поэтому можно будет значительно повесить эффективность распределения и упростить себе задачу.


