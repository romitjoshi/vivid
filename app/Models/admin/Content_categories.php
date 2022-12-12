<?php

namespace App\Models\admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Content_categories extends Model
{
    use HasFactory;
    protected $table = 'content_categories';

    public function getComicsCategoriesMapping()
    {
        return $this->hasMany(Comics_categories_mapping::class,'category_id','id');
    }
}
