<?php

namespace App\Models\admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Comics_episodes extends Model
{
    use HasFactory;
    protected $table = 'comics_episodes';

    public function episode_page_mapping()
    {
        return $this->hasMany(Comics_episode_page_mapping::class, 'episode_id', 'id');
    }
}
