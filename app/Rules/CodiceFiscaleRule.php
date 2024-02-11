<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class CodiceFiscaleRule implements Rule
{
    public function passes($attribute, $value)
    {
        $cf = strtoupper($value);
        $cf = str_replace([' ', "\t", "\n", "\r"], '', $cf);
        
        if(strlen($cf) == 16){
            // Validation pour un CF régulier
            if(preg_match("/^[0-9A-Z]{16}$/", $cf) !== 1) return false;
            $s = 0;
            $even_map = "BAFHJNPRTVCESULDGIMOQKWZYX";
            for($i = 0; $i < 15; $i++){
                $c = $cf[$i];
                $n = ctype_digit($c) ? ord($c) - ord('0') : ord($c) - ord('A');
                if(($i & 1) == 0) $n = ord($even_map[$n]) - ord('A');
                $s += $n;
            }
            return ($s % 26 + ord('A')) == ord($cf[15]);
        } elseif(strlen($cf) == 11) {
            // Validation pour un CF temporaire
            if(preg_match("/^[0-9]{11}$/", $cf) !== 1) return false;
            $s = 0;
            for($i = 0; $i < 11; $i++){
                $n = ord($cf[$i]) - ord('0');
                if(($i & 1) == 1){
                    $n *= 2;
                    if($n > 9) $n -= 9;
                }
                $s += $n;
            }
            return $s % 10 == 0;
        } else {
            return false; // Longueur invalide
        }
    }

    public function message()
    {
        return 'Il codice fiscale non è valido.';
    }
}

