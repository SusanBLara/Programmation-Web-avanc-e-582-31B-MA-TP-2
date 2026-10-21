<?php
namespace App\Models;
use App\Models\CRUD;

class Category extends CRUD {
    protected $table = 'category';
    protected $primaryKey = 'id';
    protected $fillable = [
        'name',
        'description'
    ];
}