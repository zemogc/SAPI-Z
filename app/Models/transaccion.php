<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class transaccion extends Model
{
    use HasFactory;

    protected $table = 'transacciones'; 
    protected $primaryKey = 'id';
    public $timestamps = false;

    //como tuve un problema encontre la configuracion de columnas en masa porque no me detecctaba la direccion pero espero esto funcione

    protected $fillable = ['propiedad_id', 'cliente_id', 'tipo_transaccion', 'fecha_transaccion', 'monto_transaccion'];





}
