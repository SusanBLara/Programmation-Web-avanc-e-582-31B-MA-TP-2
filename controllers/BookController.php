<?php

namespace App\Controllers;

use App\Models\Book;
use App\Models\Category;
use App\Providers\View;
use App\Models\Author;
use App\Models\BookAuthor;
use App\Providers\Validator;

class BookController {

    public function index() {
        $book = new Book;
        $books = $book->select('title');

        $category = new Category;
        $categories = $category->select('name');

        $categoryNames = [];

        foreach ($categories as $category) {
            $categoryNames[$category['id']] = $category['name'];
        }

        // Récupérer les auteurs
        $bookAuthor = new BookAuthor;
        $authors = $bookAuthor->getAllAuthorsByBook();

        $authorsByBook = [];

        foreach ($authors as $author) {
            $authorsByBook[$author['book_id']][] = $author['name'];
        }

        foreach ($books as &$item) {
            $item['category_name'] = $categoryNames[$item['category_id']] ?? 'Non renseignée';
            $item['author_names'] = implode(', ', $authorsByBook[$item['id']] ?? []) ?: 'Non renseigné';
        }
        unset($item);

        return View::render('book/index', [
            'books' => $books
        ]);
    }

    public function show($params = []) {
        $id = $params['id'] ?? null;

        if (!$id) {
            return View::render('error', ['msg' => 'Livre introuvable']);
        }

        $bookModel = new Book;
        $book = $bookModel->selectId($id);

        if (!$book) {
            return View::render('error', ['msg' => 'Livre introuvable']);
        }

        // Récupérer le nom de la catégorie
        $categoryModel = new Category;
        $category = $categoryModel->selectId($book['category_id']);
        $categoryName = $category['name'] ?? 'Non renseignée';

        // Récupérer les auteurs du livre
        $bookAuthorModel = new BookAuthor;
        $allAuthors = $bookAuthorModel->getAllAuthorsByBook();

        $authors = [];

        foreach ($allAuthors as $author) {
            if ($author['book_id'] == $id) {
                $authors[] = $author;
            }

        }

        return View::render('book/show', [
            'book' => $book,
            'categoryName' => $categoryName,
            'authors' => $authors
        ]);
    }

    public function create() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $category = new Category;
        $categories = $category->select('name');

        $author = new Author;
        $authors = $author->select('name');

        // Récupérer les informations du livre sauvegardées
        $draft = $_SESSION['book_draft'] ?? [];

        return View::render('book/create', [
            'categories' => $categories,
            'authors' => $authors,
            'draft' => $draft
        ]);
    }

    public function store($data) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Valider les données avant leur enregistrement.
        $newAuthors = $_SESSION['book_draft']['new_authors'] ?? [];
        $errors = [];
        foreach (['title', 'description', 'page_count', 'price', 'edition', 'age_group', 'category_id'] as $field) {
            $value = $data[$field] ?? '';
            if (!is_scalar($value)) {
                $errors[$field] = 'Valeur invalide : ' . $field;
                $value = '';
            }
            $data[$field] = trim((string) $value);
        }

        $val = new Validator;
        $val->field('title', $data['title'], 'Titre')->required()->max(150);
        if ($data['description'] !== '') {
            $val->field('description', $data['description'], 'Description')->min(10)->max(60);
        }
        $val->field('edition', $data['edition'], 'Édition')->max(100);
        $val->field('age_group', $data['age_group'], 'Âge recommandé')->max(50);
        $val->field('category_id', $data['category_id'], 'Catégorie')->required()->int();

        $errors = array_merge($errors, $val->getErrors() ?? []);

        // Vérifier si une catégorie a été sélectionnée.
        if ($data['category_id'] === '') {
            $errors['category_id'] = 'Veuillez sélectionner une catégorie.';
        }

        if (strlen($data['description']) > 65535) {
            $errors['description'] = 'La description est trop longue';
        }
        if (!preg_match('/^\d{1,8}(?:\.\d{1,2})?$/D', $data['price'])) {
            $errors['price'] = 'Prix invalide : de 0 à 99999999.99, avec au maximum deux décimales';
        }
        if ($data['page_count'] !== '' && filter_var($data['page_count'], FILTER_VALIDATE_INT,
            ['options' => ['min_range' => 1, 'max_range' => 2147483647]]) === false) {
            $errors['page_count'] = 'Le nombre de pages doit être un entier entre 1 et 2147483647';
        }

        $category = new Category;

        if (!isset($errors['category_id']) &&
            ((int) $data['category_id'] < 1 || !$category->selectId($data['category_id']))) {
            $errors['category_id'] = 'La catégorie sélectionnée est invalide';
        }

        $authors = $data['authors'] ?? [];
        $data['authors'] = [];

        if (!is_array($authors) || empty($authors)) {
            $errors['authors'] = 'Choisissez au moins un auteur';
        } else {
            $author = new Author;

            foreach ($authors as $authorId) {
                // Accepter les auteurs du brouillon avant de vérifier les identifiants en base.
                if (is_string($authorId)) {
                    foreach ($newAuthors as $key => $newAuthor) {
                        if ($authorId === 'new_' . $key) {
                            $data['authors'][] = $authorId;
                            continue 2;
                        }
                    }
                }
                if (!is_scalar($authorId) || filter_var($authorId, FILTER_VALIDATE_INT,
                    ['options' => ['min_range' => 1]]) === false || !$author->selectId($authorId)) {
                    $errors['authors'] = 'Un auteur sélectionné est invalide';
                } else {
                    $data['authors'][] = (int) $authorId;
                }
            }
            $data['authors'] = array_values(array_unique($data['authors']));
        }
        if (!empty($errors)) {
            $data['new_authors'] = $newAuthors;
            $_SESSION['book_draft'] = $data;

            $category = new Category;
            $author = new Author;

            return View::render('book/create', [
                'draft' => $data,
                'categories' => $category->select('name'),
                'authors' => $author->select('name'),
                'errors' => $errors
            ]);
        }

        // Récupérer les informations du formulaire
        $title = $data['title'] ?? '';
        $description = $data['description'] ?? '';
        $pageCount = $data['page_count'] ?? null;
        $price = $data['price'] ?? '';
        $edition = $data['edition'] ?? '';
        $ageGroup = $data['age_group'] ?? '';
        $categoryId = $data['category_id'] ?? null;
        $authors = $data['authors'] ?? [];

        // Enregistrer le livre
        $book = new Book;

        $bookId = $book->insert([
            'title' => $title,
            'description' => $description,
            'page_count' => $pageCount === '' ? null : $pageCount,
            'price' => $price,
            'edition' => $edition,
            'age_group' => $ageGroup,
            'category_id' => $categoryId
        ]);

        if (!$bookId) {
            $data['new_authors'] = $newAuthors;
            $_SESSION['book_draft'] = $data;

            return View::redirect('book/create');
        }

        // Associer les auteurs au livre
        $bookAuthor = new BookAuthor;

        foreach ($authors as $authorId) {
            // Enregistrer les nouveaux auteurs sélectionnés après l'ajout du livre.
            foreach ($newAuthors as $key => $newAuthor) {
                if ($authorId === 'new_' . $key) {
                    $author = new Author;
                    $authorId = $author->insert($newAuthor);
                    if (!$authorId) {
                        return View::render('error', ['msg' => "Impossible d'enregistrer l'auteur du livre."]);
                    }
                    break;
                }
            }
            $bookAuthor->insert([
                'book_id' => $bookId,
                'author_id' => $authorId
            ]);
        }

        // Supprimer le brouillon après l'enregistrement
        unset($_SESSION['book_draft']);

        return View::redirect('books');
    }

    public function edit($params = []) {
        $id = $params['id'] ?? null;

        if (!$id) {
            return View::redirect('books');
        }

        $bookModel = new Book;
        $book = $bookModel->selectId($id);

        if (!$book) {
            return View::redirect('books');
        }

        $categoryModel = new Category;
        $categories = $categoryModel->select('name');

        $authorModel = new Author;
        $authors = $authorModel->select('name');

        $bookAuthorModel = new BookAuthor;
        $bookAuthors = $bookAuthorModel->getAllAuthorsByBook();

        $selectedAuthors = [];

        foreach ($bookAuthors as $bookAuthor) {
            if ($bookAuthor['book_id'] == $id) {
                $selectedAuthors[] = $bookAuthor['author_id'];
            }
        }

        return View::render('book/edit', [
            'book' => $book,
            'categories' => $categories,
            'authors' => $authors,
            'selectedAuthors' => $selectedAuthors
        ]);
    }

    public function update($data, $params = []) {

        // Récupérer l'identifiant du livre
        $id = $params['id'] ?? null;

        if (!$id) {
            return View::redirect('books');
        }

        // Vérifier que le livre existe
        $book = new Book;

        if (!$book->selectId($id)) {
            return View::redirect('books');
        }

        // Valider les données avant leur enregistrement.
        $errors = [];
        foreach (['title', 'description', 'page_count', 'price', 'edition', 'age_group', 'category_id'] as $field) {
            $value = $data[$field] ?? '';
            if (!is_scalar($value)) {
                $errors[$field] = 'Valeur invalide : ' . $field;
                $value = '';
            }
            $data[$field] = trim((string) $value);
        }

        $val = new Validator;
        $val->field('title', $data['title'], 'Titre')->required()->max(150);
        if ($data['description'] !== '') {
            $val->field('description', $data['description'], 'Description')->min(10)->max(60);
        }
        $val->field('edition', $data['edition'], 'Édition')->max(100);
        $val->field('age_group', $data['age_group'], 'Âge recommandé')->max(50);
        $val->field('category_id', $data['category_id'], 'Catégorie')->required()->int();
        $errors = array_merge($errors, $val->getErrors() ?? []);

        if (strlen($data['description']) > 65535) {
            $errors['description'] = 'La description est trop longue';
        }
        if (!preg_match('/^\d{1,8}(?:\.\d{1,2})?$/D', $data['price'])) {
            $errors['price'] = 'Prix invalide : de 0 à 99999999.99, avec au maximum deux décimales';
        }
        if ($data['page_count'] !== '' && filter_var($data['page_count'], FILTER_VALIDATE_INT,
            ['options' => ['min_range' => 1, 'max_range' => 2147483647]]) === false) {
            $errors['page_count'] = 'Le nombre de pages doit être un entier entre 1 et 2147483647';
        }

        $category = new Category;
        if (!isset($errors['category_id']) &&
            ((int) $data['category_id'] < 1 || !$category->selectId($data['category_id']))) {
            $errors['category_id'] = 'La catégorie sélectionnée est invalide';
        }

        $authors = $data['authors'] ?? [];
        $data['authors'] = [];
        if (!is_array($authors) || empty($authors)) {
            $errors['authors'] = 'Choisissez au moins un auteur';
        } else {
            $author = new Author;
            foreach ($authors as $authorId) {
                if (!is_scalar($authorId) || filter_var($authorId, FILTER_VALIDATE_INT,
                    ['options' => ['min_range' => 1]]) === false || !$author->selectId($authorId)) {
                    $errors['authors'] = 'Un auteur sélectionné est invalide';
                } else {
                    $data['authors'][] = (int) $authorId;
                }
            }
            $data['authors'] = array_values(array_unique($data['authors']));
        }
        if (!empty($errors)) {
            $category = new Category;
            $author = new Author;
            return View::render('book/edit', [
                'book' => array_merge($data, ['id' => $id]),
                'categories' => $category->select('name'),
                'authors' => $author->select('name'),
                'selectedAuthors' => $data['authors'],
                'errors' => $errors
            ]);
        }

        // Récupérer les informations du formulaire
        $title = $data['title'] ?? '';
        $description = $data['description'] ?? '';
        $pageCount = $data['page_count'] ?? null;
        $price = $data['price'] ?? '';
        $edition = $data['edition'] ?? '';
        $ageGroup = $data['age_group'] ?? '';
        $categoryId = $data['category_id'] ?? null;
        $authors = $data['authors'] ?? [];

        // Modifier les informations du livre
        $update = $book->update([
            'title' => $title,
            'description' => $description,
            'page_count' => $pageCount === '' ? null : $pageCount,
            'price' => $price,
            'edition' => $edition,
            'age_group' => $ageGroup,
            'category_id' => $categoryId
        ], $id);

        if (!$update) {
            return View::render('error', [
                'msg' => 'Impossible de modifier le livre.'
            ]);
        }

        // Récupérer les auteurs déjà associés
        $bookAuthor = new BookAuthor;
        $currentAuthors = $bookAuthor->getAllAuthorsByBook();

        // Supprimer uniquement les auteurs décochés
        foreach ($currentAuthors as $author) {
            if ($author['book_id'] == $id &&
                !in_array($author['author_id'], $authors)) {

                $bookAuthor->deleteBookAuthor($id, $author['author_id']);
            }
        }

        // Ajouter uniquement les nouveaux auteurs
        foreach ($authors as $authorId) {
            $alreadyExists = false;

            foreach ($currentAuthors as $author) {
                if ($author['book_id'] == $id &&
                    $author['author_id'] == $authorId) {

                    $alreadyExists = true;
                    break;
                }
            }

            if (!$alreadyExists) {
                $bookAuthor->insert([
                    'book_id' => $id,
                    'author_id' => $authorId
                ]);
            }
        }
        return View::redirect('book/show?id=' . $id);

    }

    

    public function delete($data) {
        $id = $data['id'] ?? null;

        if (!$id) {
            return View::redirect('books');
        }

        $book = new Book;
        $bookAuthor = new BookAuthor;
        $authorModel = new Author;

        // Récupérer les auteurs associés au livre
        $currentAuthors = $bookAuthor->getAllAuthorsByBook();
        $authorsToCheck = [];

        foreach ($currentAuthors as $author) {
            if ($author['book_id'] == $id) {
                $authorsToCheck[] = $author['author_id'];

                // Supprimer l'association entre le livre et l'auteur
                $bookAuthor->deleteBookAuthor($id, $author['author_id']);
            }
        }

        // Supprimer le livre
        $delete = $book->delete($id);

        if ($delete) {
            // Supprimer les auteurs qui n'ont plus de livres
            foreach (array_unique($authorsToCheck) as $authorId) {
                if (!$bookAuthor->hasBooks($authorId)) {
                    $authorModel->delete($authorId);
                }
            }

            return View::redirect('books');
        } else {
            echo "error";
        }
    }

}
