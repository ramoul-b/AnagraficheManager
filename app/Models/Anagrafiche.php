<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Anagrafiche extends Model
{
    use HasFactory;
    protected $table = 'anagrafiche';
    protected $fillable = ['partita_iva', 'codice_fiscale', 'unique_code', 'tipo_id'];

    public function tipi()
    {
        return $this->belongsTo(Tipi::class, 'tipo_id');
    }

    public function values()
    {
        return $this->hasMany(AnagraficheValue::class, 'anagrafica_id');
    }
}
