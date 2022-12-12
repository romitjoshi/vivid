<?php

namespace App\Models\admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\{User};
class Comics_series extends Model
{
    use HasFactory;
    protected $table = 'comics_series';

    public function getRating()
    {
        return $this->hasOne(Comic_ratings::class, 'comic_id', 'id');
    }

    public function getCreateBy()
    {
        return $this->hasOne(User::class, 'id', 'created_by');
    }

    public function getCategory()
    {
        return $this->hasMany(Comics_categories_mapping::class, 'comics_id', 'id');
    }
}
