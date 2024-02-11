<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class PartitaIvaRule implements Rule
{
    public function passes($attribute, $value)
    {
        $value = str_replace([' ', "\t", "\n", "\r"], '', $value);
        if(strlen($value) != 11) return false;

        $s = 0;
        for($i = 0; $i < 11; $i++){
            $n = ord($value[$i]) - ord('0');
            if(($i & 1) == 1){
                $n *= 2;
                if($n > 9) $n -= 9;
            }
            $s += $n;
        }
        
        return $s % 10 == 0;
    }

    public function message()
    {
        return 'La partita IVA non è valida.';
    }
}

