<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class propiedad extends Model
{
    use HasFactory;
    protected $table = 'propiedades';
    protected $primaryKey = 'id';
    public $timestamps = false;
}
