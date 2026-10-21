<?php

namespace App\Controllers;

use App\Models\Author;
use App\Providers\View;
use App\Providers\Validator;

class AuthorController {

    public function index() {
        $author = new Author;
        $authors = $author->select('name');

        return View::render('author/index', [
            'authors' => $authors
        ]);
    }

    public function create($data = [], $params = []) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $fromBook = ($_SERVER['REQUEST_METHOD'] === 'GET')
            ? (($data['from'] ?? '') === 'book')
            : (($params['from'] ?? '') === 'book');

        // Conserver les informations du livre avant d'ajouter un auteur.
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $fromBook) {
            $draft = [];

            foreach ([
                'title', 'description', 'page_count',
                'price', 'edition', 'age_group', 'category_id'
            ] as $field) {
                $draft[$field] = $data[$field] ?? '';
            }

            $draft['authors'] = $data['authors'] ?? [];
            $draft['new_authors'] = $_SESSION['book_draft']['new_authors'] ?? [];

            $_SESSION['book_draft'] = $draft;

            return View::redirect('author/create?from=book');
        }

        return View::render('author/create', [
            'fromBook' => $fromBook
        ]);
    }

    public function store($data, $params = []) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $fromBook = ($params['from'] ?? '') === 'book';

        $val = new Validator;

        $val->field('name', $data['name'] ?? '', 'Nom')
            ->required()
            ->max(100);

        $val->field('country', $data['country'] ?? '', 'Pays')
            ->max(100);

        $val->field('biography', $data['biography'] ?? '', 'Biographie')
            ->max(16000);

        $birthYear = trim((string) ($data['birth_year'] ?? ''));

        $year = null;

        $errors = $val->getErrors() ?? [];

        // Valider uniquement si une année est saisie
        if ($birthYear !== '') {
            $year = filter_var(
                $birthYear,
                FILTER_VALIDATE_INT,
                [
                    'options' => [
                        'min_range' => 1,
                        'max_range' => (int) date('Y')
                    ]
                ]
            );

            if ($year === false) {
                $errors['birth_year'] = 'Année de naissance invalide';
            }
        }

        // Vérifier si l'auteur existe déjà
        if (!isset($errors['name'])) {
            $author = new Author;
            $authors = $author->select('name');

            $name = trim($data['name']);

            foreach ($authors as $existingAuthor) {
                if (strcasecmp(trim($existingAuthor['name']), $name) === 0) {
                    $errors['name'] = 'Cet auteur existe déjà.';
                    break;
                }
            }
        }

        if ($fromBook && !isset($errors['name'])) {
            foreach ($_SESSION['book_draft']['new_authors'] ?? [] as $pendingAuthor) {
                if (strcasecmp($pendingAuthor['name'], trim($data['name'])) === 0) {
                    $errors['name'] = 'Cet auteur existe déjà dans le brouillon.';
                    break;
                }
            }
        }

        if (empty($errors)) {
            $authorData = [
                'name' => trim($data['name']),
                'country' => trim($data['country'] ?? '') ?: null,
                'birth_year' => $year,
                'biography' => trim($data['biography'] ?? '') ?: null
            ];

            if ($fromBook) {
                if (!isset($_SESSION['book_draft'])) {
                    $_SESSION['book_draft'] = ['authors' => []];
                }

                // Garder l'auteur en session jusqu'à l'enregistrement du livre.
                $_SESSION['book_draft']['new_authors'][] = $authorData;
                $key = 'new_' . (count($_SESSION['book_draft']['new_authors']) - 1);
                $_SESSION['book_draft']['authors'][] = $key;

                return View::redirect('book/create');
            }

            $author = new Author;
            $author->insert($authorData);

            return View::redirect('authors');
        }

        return View::render('author/create', [
            'fromBook' => $fromBook,
            'data' => $data,
            'errors' => $errors
        ]);
    }

}
