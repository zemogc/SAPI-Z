<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Propiedad extends Model
{
    use HasFactory;

    protected $table = 'propiedades'; 
    protected $primaryKey = 'id';
    public $timestamps = false;

    //como tuve un problema encontre la configuracion de columnas en masa porque no me detecctaba la direccion pero espero esto funcione

    protected $fillable = ['direccion', 'tipo', 'tamano', 'numero_habitaciones', 'precio', 'estado'];
}
    





