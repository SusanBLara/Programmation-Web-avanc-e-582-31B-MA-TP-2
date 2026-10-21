<?php

namespace App\Models;
use App\Models\CRUD;

class BookAuthor extends CRUD {
    protected $table = 'book_author';
    protected $fillable = [
        'book_id',
        'author_id'
    ];

    // Vérifie si un auteur est associé à un livre.
    public function hasBooks($authorId): bool {
        $sql = "SELECT COUNT(*) FROM book_author
                WHERE author_id = :author_id";

        $stmt = $this->prepare($sql);
        $stmt->bindValue(':author_id', $authorId);
        $stmt->execute();

        return $stmt->fetchColumn() > 0;
    }

    // Récupère les noms des auteurs associés aux livres.
    public function getAllAuthorsByBook() {
        $sql = "SELECT book_author.book_id, book_author.author_id, author.name
                FROM book_author INNER JOIN author ON book_author.author_id = author.id
                ORDER BY author.name";

        $stmt = $this->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    // Supprime l'association entre un livre et un auteur.
    public function deleteBookAuthor($bookId, $authorId): bool {
        $sql = "DELETE FROM book_author
                WHERE book_id = :book_id
                AND author_id = :author_id";

        $stmt = $this->prepare($sql);
        $stmt->bindValue(':book_id', $bookId);
        $stmt->bindValue(':author_id', $authorId);

        return $stmt->execute();
    }
}

