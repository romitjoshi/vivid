<?php

namespace App\Models\admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
class Plan extends Model
{
    use HasFactory;
    use HasApiTokens, HasFactory, Notifiable;
    protected $table = 'plans';

   
    
}
