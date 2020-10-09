<?php

namespace app\components;

class MyArrayHelper
{
    /**
     * @param array $arr
     * @return int|string|null
     */
    public static function arrayKeyFirst(array $array)
    {
        foreach ($array as $key => $unused) {
            return $key;
        }
        return null;
    }

}