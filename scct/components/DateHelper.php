<?php

namespace app\components;

use function date;
use function print_r;
use function strtotime;

/**
 * Class DateHelper
 * @package app\components
 */
class DateHelper
{
    /**
     * Checks if datetime is between another datetime
     * @param $startDate
     * @param $endDate
     * @param $existingStartDate
     * @param $existingEndDate
     * @return bool
     */
    public static function checkDateBetween($startDate, $endDate, $existingStartDate, $existingEndDate)
    {
        //
        $startDate = strtotime($startDate);
        $endDate = strtotime($endDate);
        $existingStartDate = strtotime($existingStartDate);
        $existingEndDate = strtotime($existingEndDate);


        $startDateCheck = self::_isBetweenDate($startDate, $existingStartDate, $existingEndDate);
        $endDateCheck = self::_isBetweenDate($endDate, $existingStartDate, $existingEndDate);

        if ($startDateCheck) {
//            echo "Start Date Check \n";
//            echo "New Start Date: " . date("H:i:s", $startDate) . "\n";
//            echo "New End Date: " . date("H:i:s", $endDate) . "\n";
//            echo "Existing Start Date: " . date("H:i:s", $existingStartDate) . "\n";
//            echo "Existing End Date: " . date("H:i:s", $existingEndDate) . "\n\n\n";

            return true;
        }

        if ($endDateCheck) {
//            echo "End Date Check \n";
//            echo "New Start Date: " . date("H:i:s", $startDate) . "\n";
//            echo "New End Date: " . date("H:i:s", $endDate) . "\n";
//            echo "Existing Start Date: " . date("H:i:s", $existingStartDate) . "\n";
//            echo "Existing End Date: " . date("H:i:s", $existingEndDate) . "\n\n\n";

            return true;
        }
//        $startDateDT = Carbon::createFromFormat('m/d/Y H:i:00', $startDate);
//
//        $endDateDT = Carbon::createFromFormat('m/d/Y H:i:00', $endDate);
//
//
//        $check = Carbon::createFromFormat('m/d/Y H:i:00', $existingStartDate)->between($startDateDT, $endDateDT);
//        $check2 = Carbon::createFromFormat('m/d/Y H:i:00', $existingEndDate)->between($startDateDT, $endDateDT);
//
//
//        if ($check && $check2) {
//
//            echo "Existing Start Date: " . $existingStartDate . "\n";
//            echo "Existing End Date: " . $existingEndDate . "\n";
//            return true;
//        }
    }

    /**
     * @param $existingStartEndDate
     * @param $startTime
     * @param $endTime
     * @return bool
     */
    private static function _isBetweenDate($newStartEndDate, $existingStartTime, $existingEndTime)
    {
        if ($newStartEndDate > $existingStartTime && $newStartEndDate < $existingEndTime) {
            // echo "true\n";
            return true;
        } else {
            // echo "false\n";
            return false;
        }
    }
}