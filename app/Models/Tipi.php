<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tipi extends Model
{
    use HasFactory;
    protected $table = 'tipi';

    protected $fillable = [
        'nome', 'descrizione', 'has_partita_iva', 'required_partita_iva',
        'has_codice_fiscale', 'required_codice_fiscale'
    ];

    public function anagrafiches()
    {
        return $this->hasMany(Anagrafiche::class);
    }
}
