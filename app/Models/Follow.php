<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Follow extends Model
{
    protected $table = 'follows';

    protected $primaryKey = 'follow_id';

    public $timestamps = true;

    protected $fillable = [
        'follower_id',
        'following_id',
    ];
}
