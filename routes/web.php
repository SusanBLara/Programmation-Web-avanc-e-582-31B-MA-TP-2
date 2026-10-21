<?php

use App\Routes\Route;

// Accueil
Route::get('/', 'HomeController@index');
Route::get('/home', 'HomeController@index');

// Livres
Route::get('/books', 'BookController@index');
Route::get('/book/show', 'BookController@show');
Route::get('/book/create', 'BookController@create');
Route::post('/book/store', 'BookController@store');
Route::get('/book/edit', 'BookController@edit');
Route::post('/book/update', 'BookController@update');
Route::post('/book/delete', 'BookController@delete');

// Auteurs
Route::get('/authors', 'AuthorController@index');
Route::get('/author/create', 'AuthorController@create');
Route::post('/author/create', 'AuthorController@create');
Route::post('/author/store', 'AuthorController@store');

// Catégories
Route::get('/categories', 'CategoryController@index');

Route::dispatch();
