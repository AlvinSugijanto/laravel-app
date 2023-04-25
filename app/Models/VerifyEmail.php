<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VerifyEmail extends Model
{
    protected $table = 'verify_email';

    
    public $timestamps = false;

    protected $fillable = [
        'token',
        'user_id',
    ];
    public function user(){
        return $this->belongsTo('App\Models\User', 'user_id','user_id');
    }
}
