<?php

namespace zephy\daily\utils;

final class TimeUtils
{
    public static function stringRoundTime(int $time): string
    {
        $s = $time % 60;
        $m = floor(($time % 3600) / 60);
        $h = floor(($time % 86400) / 3600);
        $d = floor(($time % 2592000) / 86400);
        return "$d : $h : $m : $s";
    }
}
