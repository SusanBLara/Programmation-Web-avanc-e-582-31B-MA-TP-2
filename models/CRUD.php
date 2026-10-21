<?php

namespace App\Models;
abstract class CRUD extends \PDO {

    final public function __construct(){
        parent::__construct(
            'mysql:host=localhost; dbname=librairie; port=3306; charset=utf8mb4', 'root', '');

    }
    // Récupère toutes les lignes d'une table, triées selon le champ choisi.
    final public function select($field = null, $order = "ASC"): array {
        if ($field == null) {
            $field = $this->primaryKey;
        }

        $sql = "SELECT * FROM $this->table ORDER BY $field $order";
        $stmt = $this->query($sql);

        return $stmt->fetchAll();
    }

    // Cherche une ligne selon sa clé primaire.
    final public function selectId(int|string $value): bool|array {
        $sql = "SELECT * FROM $this->table WHERE $this->primaryKey = :$this->primaryKey";

        $stmt = $this->prepare($sql);
        $stmt->bindValue(":$this->primaryKey", $value);
        $stmt->execute();

        $result = $stmt->fetch();

        return $result ?: false;
    }

    // Ajoute les données autorisées dans la table.
    final public function insert(array $data) {
        $data_keys = array_fill_keys($this->fillable, '');
        $data = array_intersect_key($data, $data_keys);

        $fieldName = implode(', ', array_keys($data));
        $fieldBindValue = ":" . implode(', :', array_keys($data));

        $sql = "INSERT INTO $this->table ($fieldName) VALUES ($fieldBindValue);";

        $stmt = $this->prepare($sql);

        foreach ($data as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }

        if ($stmt->execute()) {
            return $this->lastInsertId();
        } else {
            return false;
        }
    }

    // Modifie les données autorisées dans la table selon l'id donné.
    final public function update(array $data, $id) {
        $data_keys = array_fill_keys($this->fillable, '');
        $data = array_intersect_key($data, $data_keys);

        $fieldName = null;

        foreach ($data as $key => $value) {
            $fieldName .= "$key = :$key, ";
        }

        $fieldName = rtrim($fieldName, ', ');

        $sql = "UPDATE $this->table SET $fieldName WHERE $this->primaryKey = :$this->primaryKey;";

        $data[$this->primaryKey] = $id;

        $stmt = $this->prepare($sql);

        foreach ($data as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }

        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }

    // Supprime une ligne selon sa clé primaire.
    final public function delete(int|string $value): bool {
        $sql = "DELETE FROM $this->table WHERE $this->primaryKey = :$this->primaryKey";

        $stmt = $this->prepare($sql);
        $stmt->bindValue(":$this->primaryKey", $value);

        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }
}