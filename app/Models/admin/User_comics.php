<?php

namespace App\Models\admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class User_comics extends Model
{
    use HasFactory;
    protected $table = 'user_comics';

    protected $fillable = [
        'user_id',
        'comic_id',
        'comic_episode_id',
        'page_number',
    ];

    public function getEpisodePages()
    {
        return $this->hasMany(Comics_episode_page_mapping::class, 'id', 'page_number');
    }
    public function getComicName()
    {
        return $this->hasMany(Comics_series::class, 'id', 'comic_id');
    }
    public function getEpisodeName()
    {
        return $this->hasMany(Comics_episodes::class, 'id', 'comic_episode_id');
    }
}
