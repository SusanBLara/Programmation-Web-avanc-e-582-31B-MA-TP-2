<?php

namespace App\Models;
use App\Models\CRUD;

class Book extends CRUD {

    protected string $table = 'book';
    protected string $primaryKey = 'id';
    protected $fillable = [
        'title',
        'description',
        'page_count',
        'price',
        'edition',
        'age_group',
        'category_id'
    ];
}