<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use LaravelKorea\Carbon;

class Post extends Model
{
    public function User()
    {
        return $this->belongsTo('App\User');
    }

    public function getAuthIdentifier()
    {
        return $this->getKey();
    }

    public function createdAt()
    {
        return Carbon::createFromFormat('Y-m-d H:i:s', $this->created_at)->diffForHumans();
    }
}