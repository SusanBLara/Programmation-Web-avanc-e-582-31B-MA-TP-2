<?php

namespace App\Models;
use App\Models\CRUD;

class Author extends CRUD {
    protected $table = 'author';
    protected $primaryKey = 'id';
    protected $fillable = [
        'name',
        'country',
        'birth_year',
        'biography'
    ];
}