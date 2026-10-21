-- Librairie pour enfants - Base de donnees du TP2
CREATE DATABASE librairie;
USE librairie;

CREATE TABLE category (
    id INT NOT NULL AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    PRIMARY KEY (id)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO category (id, name, description) VALUES
(1, 'Aventure', 'Histoires pleines d’aventures et de découvertes.'),
(2, 'Fantaisie', 'Histoires avec des mondes imaginaires et fantastiques.'),
(3, 'Éducatif', 'Livres pour apprendre tout en s’amusant.'),
(4, 'Animaux', 'Histoires sur les animaux et la nature.');

CREATE TABLE author (
    id INT NOT NULL AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    country VARCHAR(100) DEFAULT NULL,
    birth_year INT DEFAULT NULL,
    biography TEXT,
    PRIMARY KEY (id)
) ENGINE=InnoDB AUTO_INCREMENT=39 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO author (id, name, country, birth_year, biography) VALUES
(1, 'Mark Twain', 'États-Unis', 1835, 'Écrivain américain connu pour ses romans d’aventure.'),
(2, 'Michael Ende', 'Allemagne', 1929, 'Écrivain allemand connu pour ses œuvres de littérature jeunesse.'),
(3, 'Antoine de Saint-Exupéry', 'France', 1900, 'Écrivain et aviateur français, auteur du Petit Prince.'),
(4, 'Gilbert Delahaye', 'Belgique', 1923, 'Écrivain belge connu pour les histoires de Martine.'),
(5, 'Charles Dudley Warner', 'États-Unis', 1829, 'Écrivain et essayiste américain.'),
(6, 'Charles Perrault', 'France', 1628, 'Charles Perrault est un écrivain français, connu pour ses contes pour enfants comme Le Petit Chaperon rouge, Cendrillon et La Belle au bois dormant.');

CREATE TABLE book (
    id INT NOT NULL AUTO_INCREMENT,
    title VARCHAR(150) NOT NULL,
    description TEXT,
    page_count INT DEFAULT NULL,
    price DECIMAL(10,2) NOT NULL,
    edition VARCHAR(100) DEFAULT NULL,
    age_group VARCHAR(50) DEFAULT NULL,
    category_id INT NOT NULL,
    PRIMARY KEY (id),
    FOREIGN KEY (category_id) REFERENCES category(id)
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO book (id, title, description, page_count, price, edition, age_group, category_id) VALUES
(1, 'Les Aventures de Tom Sawyer', 'Les aventures d’un jeune garçon curieux et courageux.', 288, 12.99, 'Gallimard Jeunesse', '9-12 ans', 1),
(2, 'L\'Histoire sans fin', 'Un jeune garçon découvre un livre mystérieux qui le transporte dans un monde fantastique.', 528, 16.99, 'Le Livre de Poche', '10-14 ans', 2),
(3, 'Le Petit Prince', 'Un jeune prince voyage de planète en planète et découvre le monde et l’amitié.', 96, 10.99, 'Gallimard', '8-12 ans', 1),
(4, 'Martine à la ferme', 'Martine découvre la vie à la ferme et les animaux qui y vivent.', 24, 7.99, 'Casterman', '5-8 ans', 1),
(9, 'Le Petit Chaperon rouge', 'Une petite fille traverse la forêt pour voir sa grand-mère.', 32, 10.00, 'Gallimard Jeunesse', '4-7 ans', 1);

CREATE TABLE book_author (
    book_id INT NOT NULL,
    author_id INT NOT NULL,
    PRIMARY KEY (book_id, author_id),
    FOREIGN KEY (book_id) REFERENCES book(id),
    FOREIGN KEY (author_id) REFERENCES author(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO book_author (book_id, author_id) VALUES
(1, 1),
(2, 2),
(3, 3),
(4, 4),
(9, 6);