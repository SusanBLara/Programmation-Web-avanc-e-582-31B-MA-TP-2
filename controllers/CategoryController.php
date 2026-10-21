<?php

namespace App\Controllers;

use App\Models\Category;
use App\Providers\View;

class CategoryController {

    public function index() {

        $category = new Category;
        $categories = $category->select();

        return View::render('category/index', [
            'categories' => $categories
        ]);
    }
}
