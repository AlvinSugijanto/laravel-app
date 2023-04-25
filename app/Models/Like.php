<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Like extends Model
{
    protected $table = 'likes';

    protected $primaryKey = 'like_id';

    public $timestamps = true;

    protected $fillable = [
        'post_id',
        'user_id',
    ];
}
