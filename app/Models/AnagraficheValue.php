<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnagraficheValue extends Model
{
    use HasFactory;

    protected $fillable = ['anagrafica_id', 'attributes_id', 'value'];

    public function anagrafiche()
    {
        return $this->belongsTo(Anagrafiche::class, 'anagrafica_id');
    }

    public function attribute()
    {
        return $this->belongsTo(Attribute::class, 'attributes_id');
    }
}

