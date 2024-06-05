<?php

namespace App\Helpers;

class GenerateAlias
{
    public static function generate($text)
    {
        $words = explode(' ', $text);

        // Ambil huruf pertama dari setiap kata
        $firstLetters = '';
        foreach ($words as $word) {
            $firstLetters .= substr($word, 0, 1);
        }

        return $firstLetters;
    }
}
