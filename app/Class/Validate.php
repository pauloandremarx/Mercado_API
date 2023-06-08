<?php

namespace App\Class;

class Validate
{
    private array $errors = [];

    public function required(array $fields): static
    {
        foreach ($fields as $field) {
            if (empty($_POST[$field])) {
                $this->errors[$field] = 'O campo é obrigatório';
            }
        }

        return $this;
    }

    public function exist($model, $field, $value ): static
    {
        $data = $model->findBy($field, $value);

        if ($data) {
            $this->errors[$field] = 'Esse nome já está cadastrado no banco de dados';
        }

        return $this;
    }

    public function login($model, $field_u, $value_u, $field_p, $value_p ): static
    {
        $data = $model->findBy($field_u, $value_u);

        if (!$data || $data->$field_p != $value_p) {
            $this->errors[$field_u] = 'Usuário inválido verifique os dados e tente novamente' ;
        }

        return $this;
    }

    public function exist_id($model, $field, $value, $id): static
    {
        $data = $model->findBy('id', $id);
        $data_nome = $model->findBy($field, $value);

        if ($data_nome && $data_nome->id != $id || !$data ) {
            $this->errors[$field] = 'Esse nome já está cadastrado no banco de dados';
        }

        return $this;
    }

    public function email($email): void
    {
        $validated = filter_var($email, FILTER_VALIDATE_EMAIL);
        if (!$validated) {
            $this->errors['email'] = 'Email inválido';
        }
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
