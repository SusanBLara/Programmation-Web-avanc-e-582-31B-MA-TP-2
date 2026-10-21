<?php

namespace App\Providers;

class Validator {

    private $errors = [];
    private string $key;
    private string $value;
    private string $name;

    public function field(string $key, string $value, $name = null) {
        $this->key = $key;
        $this->value = $value;
        $this->name = $name ?? $key;
        return $this;
    }

    public function required() {
        if (trim($this->value) === '') {
            $this->errors[$this->key] = "$this->name est obligatoire";
        }
        return $this;
    }

    public function min(int $length) {
        if (mb_strlen($this->value) < $length) {
            $this->errors[$this->key] = "$this->name doit contenir au moins $length caractères";
        }
        return $this;
    }

    public function max(int $length) {
        if (mb_strlen($this->value) > $length) {
            $this->errors[$this->key] = "$this->name doit contenir au maximum $length caractères";
        }
        return $this;
    }

    public function int() {
        if (filter_var($this->value, FILTER_VALIDATE_INT) === false) {
            $this->errors[$this->key] = "$this->name doit être un nombre entier";
        }
    }

    public function isSuccess() {
        return empty($this->errors);
    }

    public function getErrors() {
        if (!$this->isSuccess()) {
            return $this->errors;
        }
    }
}
