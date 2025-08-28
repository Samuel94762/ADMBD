<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CodigoOtp extends Model
{
    protected $table = 'codigos_otp'; // 👈 fuerza el nombre de la tabla

    protected $fillable = ['user_id', 'codigo', 'expira_en', 'utilizado'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
