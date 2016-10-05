<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    $fillable = ['content', 'user_id'];
}
