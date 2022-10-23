<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dashboard extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'url',
        'description',
        'image',
        'permission',
    ];
    protected $casts = [
        'permission' => 'boolean'
    ];
}
