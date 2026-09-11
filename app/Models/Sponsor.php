<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sponsor extends Model
{
    protected $fillable = [
        'name',
        'description',
        'image_path',
        'website_url',
        'phone',
        'email',
        'sort_order',
    ];
}
