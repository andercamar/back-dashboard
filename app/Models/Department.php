<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;

    protected $fillable =[
        'name'
    ];
    public function users(){
        return $this->belongsToMany(Users::class, 'department_user');
    }
    public function dashboards(){
        return $this->belongsToMany(Users::class, 'department_dashboard');
    }
}
