<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class User_subscription_details extends Model
{
    use HasFactory;
    public function getUserDetails()
    {
        return $this->hasOne(User::class,'id','user_id');
    }

}
