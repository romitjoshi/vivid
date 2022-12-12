<?php

namespace App\Models\admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comics_categories_mapping extends Model
{

    use HasFactory;
    protected $table = 'comics_categories_mapping';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'comics_id',
        'category_id',
    ];

    public function comic()
    {
        return $this->hasMany(Comics_series::class,'comics_id','id');
    }
}
