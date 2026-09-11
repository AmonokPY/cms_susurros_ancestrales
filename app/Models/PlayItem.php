<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlayItem extends Model
{
    protected $fillable = [
        'title',
        'description',
        'video_url',
        'image_path',
        'sort_order',
    ];
}
