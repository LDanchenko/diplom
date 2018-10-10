<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 10.10.18
 * Time: 11:10
 */

class Limits
{
    private $dateStart; //когда может дата время с пол - буду вырывать из базы и записывать дата время старт
    private $dateEnd; //та же дата время сто

    public function __construct(DateTime $dateStart, DateTime $dateEnd ){
        $this->dateStart = $dateStart;
        $this->dateEnd = $dateEnd;
    }

   // public function __toString() {
   //     return $this->dateStart->getTimestamp() . '->' . $this->durationInMinutes;
  //  }

    /**
     * @return DateTime
     */
    public function getStartDate(): DateTime {
        return $this->dateStart;
    }

    public function setStartDate(DateTime $date): void {
        $this->dateStart = $date;
    }

    /**
     * @param DateTime $startDate
     */
    public function setEndDate(DateTime $dateEnd): void {
        $this->dateEnd = $dateEnd;
    }
    public function getEndDate(): DateTime {
        return $this->dateEnd;
    }

    /**
     * @param DateTime $startDate
     */


}