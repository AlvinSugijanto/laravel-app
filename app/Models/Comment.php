<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $table = 'comments';

    protected $primaryKey = 'comment_id';
    
    public $timestamps = true;

    protected $fillable = [
        'content',
        'post_id',
        'user_id'
    ];

}
