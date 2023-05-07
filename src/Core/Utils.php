<?php

namespace MyBlog\Core;


use DateTime;

class Utils
{
    public static function sanitize_html(array $values): array
    {
        $resultArray = [];
        foreach ($values as $key => $value) 
        {
            if(is_array($value)) {
                $resultArray[$key] = self::sanitize_html($value);
            }
            else {
                $resultArray[$key] = htmlentities($value, ENT_QUOTES);
            }
        }
        return $resultArray;
    }

    public static function formatDatetime(string $datetime, string $format = 'd M Y H:m'): string
    {
        try {
            $datetimeObj = new DateTime($datetime);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
        return $datetimeObj->format($format);
    }


    public static function toJson(mixed $value)
    {
        return json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    }
}