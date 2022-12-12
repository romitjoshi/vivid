<?php

namespace App\Models\admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
class Orders extends Model
{
    use HasFactory;
    protected $table = 'orders'; 

    public function getOrderProductDetails()
    {
        return $this->hasMany(Order_product_details::class,'order_id','id'); 
    }
    public function getUserDetails()
    {
        return $this->hasOne(User::class,'id','user_id'); 
    }   
}
 