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

    protected $fillable = ['propiedad_id', 'cliente_id', 'tipo_transaccion', 'fecha_transaccion', 'monto_transaccion'];

    public function propiedad()
    {
        return $this->belongsTo(Propiedad::class);
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

}
