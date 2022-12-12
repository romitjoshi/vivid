<?php

namespace App\Models\admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comics_episode_page_mapping extends Model
{
    use HasFactory;
    protected $table = 'comics_episode_page_mapping';

    protected $fillable = [
        'id',
        'comics_series_id',
        'episode_id'
    ];
}
