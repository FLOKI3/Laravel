<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    protected $fillable = ['name', 'club_id'];

    public function club()
    {
        return $this->belongsTo(Club::class, 'club_id');
    }
}