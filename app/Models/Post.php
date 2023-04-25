<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Post extends Model
{
    protected $table = 'posts';

    protected $primaryKey = 'post_id';
    
    public $timestamps = true;

    protected $fillable = [
        'content',
        'user_id',
    ];

    public function user()
    {
        $this->belongsTo(User::class,'user_id','user_id');
    }
}
