<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Device_info extends Model
{
    use HasFactory;
    protected $table = 'device_info';
    protected $primaryKey = 'id';
}
