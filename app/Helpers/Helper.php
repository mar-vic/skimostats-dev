<?php

namespace App\Helpers;

class Helper
{
    public static function rawTimeToMillis(string $time)
    {
        $exp = explode(":", str_replace(",", ".", $time));
        $milis = 0;
        $x = 0;
        for ($i = count($exp) - 1; $i > -1; $i--) {
            $part = (float)$exp[$i];
            $milis += $part * 1000 * pow(60, $x);
            $x++;
        }
        return $milis;
    }

    public static function millisToTime($millis = 0, $showHours = true)
    {
        $fullSeconds = (int)$millis / 1000;
        $hours = floor($fullSeconds / 3600);
        $minutes = $showHours ? floor(($fullSeconds / 60) % 60) : floor($fullSeconds / 60);
        $seconds = ((int)$millis % 60000) / 1000;

        if ($showHours) {
            return sprintf("%02d:%02d:%04.1f", $hours, $minutes, $seconds);
        }

        return sprintf("%02d:%04.1f", $minutes, $seconds);
    }
    public static function addOrdinalNumberSuffix($num) {
        if (!in_array(($num % 100),array(11,12,13))){
            switch ($num % 10) {
                // Handle 1st, 2nd, 3rd
                case 1:  return $num.'st';
                case 2:  return $num.'nd';
                case 3:  return $num.'rd';
            }
        }
        return $num.'th';
    }
}
