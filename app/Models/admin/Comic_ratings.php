<?php

namespace App\Models\admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comic_ratings extends Model
{
    use HasFactory;
    protected $table = 'comic_ratings';
    protected $primaryKey = 'id';
    protected $fillable = ['user_id','comic_id', 'ratings'];

    public function comicRating()
    { 
        return $this->belongsTo(Comics_series::class, 'comic_id', 'id');
    }
}
 