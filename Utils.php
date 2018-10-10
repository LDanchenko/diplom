<?php

abstract class Utils {
    public static function normalize($value, $minLimitValue, $maxLimitValue) {
        $maxLimitValue = $maxLimitValue - $minLimitValue;

        if ($maxLimitValue == 0.0) {
            $maxLimitValue = 1.0;
        }

        return ($value - $minLimitValue) / $maxLimitValue;
    }

    public static function normalizeCollection($values, $minLimitValue, $maxLimitValue) {
        $result = array();
        foreach ($values as $value) {
            $result[] = self::normalize($value, $minLimitValue, $maxLimitValue);
        }

        return $result;
    }
//делить полученный коефициент на определенный нами приоритет
    public static function applyWeightImpact($value, $weight) {
        return $value ** (1 / $weight); //по формуле?
    }

    public static function removeFromArrayByValue(&$array, $value) {
        if (($key = array_search($value, $array)) !== false) {
            unset($array[$key]);
        }
    }

}
