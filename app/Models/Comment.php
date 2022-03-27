<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    //set the table name
    protected $table = 'comments';
    protected $fillable = ['user_name', 'body', 'book_id', 'client_ip'];
}
