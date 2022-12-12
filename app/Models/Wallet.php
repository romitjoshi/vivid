<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    use HasFactory;
    protected $table = 'wallet';

    public function getUser()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function getUserDetails()
    {
        return $this->hasOne(User_details::class, 'user_id', 'user_id');
    }

}
