<?php
namespace LoopAnime\AppBundle\Utils;


class DateUtil 
{

    public static function getReadableDateFormat(\DateTime $date)
    {
        $second = 1;
        $minute = 60 * $second;
        $hour = 60 * $minute;
        $day = 24 * $hour;
        $month = 30 * $day;
        $diff = time() - $date->getTimestamp();

        if ($diff < $minute) {
            return ($diff <5) ? "just now" : $diff . "s ago";
        } elseif ($diff < 60 * $minute) {
            return floor($diff / 60) . "m ago";
        } elseif ($diff < 24 * $hour) {
            return floor($diff / 60 / 60). "h ago";
        } elseif ($diff < 31 * $day) {
            return "1d ago";
        } elseif ($diff < 12 * $month) {
            $months = floor($diff / 60 / 60 / 24 / 30);
            return $months <= 1 ? "1mo ago" : $months . "mo ago";
        } else {
            $years = floor  ($diff / 60 / 60 / 24 / 30 / 12);
            return $years <= 1 ? "1y ago" : $years."y ago";
        }

    }

}
