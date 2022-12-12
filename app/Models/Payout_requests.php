<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payout_requests extends Model
{
    use HasFactory;
    protected $table = "payout_requests";


    public function getUser()
    {
        return $this->hasOne(User::class,'id','user_id');
    }
}
