<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Generation extends Model
{
    protected $fillable = [
        'user_id','type','prompt','result','image_path','model','status','cost'
    ];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}
