<?php

namespace App\Controllers;

use App\Database\models\Base;
use App\Database\models\Category as ModelsCategory;
use App\Class\Validate;
use JetBrains\PhpStorm\NoReturn;

class CategoryController extends Base
{
    private ModelsCategory $category;
    private Validate $validate;

    public function __construct()
    {
        parent::__construct(); // Chama o construtor da classe pai
        $this->validate = new Validate();
        $this->category = new ModelsCategory();
    }

    #[NoReturn] public function c_show(): string
    {
        exit( json_encode($this->category->find()) );
    }

    #[NoReturn] public function c_find($id): string
    {
        $id = filter_var($id, FILTER_SANITIZE_NUMBER_INT);
        $category = $this->category->findBy("id", $id);

        if (!$category) {
            $response = [
                "status" => "Categoria não encontrada!",
                "message" => "alert",
            ];
            http_response_code(400);
            exit(  json_encode($response) );
        }
        exit(  json_encode($category) );
    }

    #[NoReturn] public function c_store(): string
    {
        $nome = filter_input(INPUT_POST, "nome", FILTER_SANITIZE_SPECIAL_CHARS);


        $this->validate
            ->required(["nome"])
            ->exist($this->category, "nome", $nome);

        $errors = $this->validate->getErrors();

        if ($errors) {
            http_response_code(400);
            exit(  json_encode($errors) );
        }

        $created = $this->category->create(["nome" => $nome]);
        if ($created) {
            $response = [
                "status" => "Cadastrado com sucesso",
                "message" => "success",
            ];
            exit( json_encode($response) );
        }

        $response = [
            "status" => "Erro ao gravar no banco de dados!" ,
            "message" => "danger",
        ];
        http_response_code(400);
        exit( json_encode($response) );
    }

    #[NoReturn] public function c_update($id): string
    {

        $inputData = file_get_contents('php://input');
        $putData = json_decode($inputData, true);

        $nome = isset($putData['nome']) ? filter_var($putData['nome'], FILTER_SANITIZE_SPECIAL_CHARS) : null;


        $_POST['nome'] = $nome;


        $this->validate
            ->required(["nome"])
            ->exist_id($this->category, "nome", $nome, $id);

        $errors = $this->validate->getErrors();

        if ($errors) {
            http_response_code(400);
            exit(  json_encode($errors) );
        }

        $updated = $this->category->update([
            "fields" => [
                "nome" => $nome,

            ],
            "where" => ["id" => $id],
        ]);

        if ($updated) {
            $response = [
                "status" => "Atualizado com sucesso!",
                "message" => "success",
            ];
            exit( json_encode($response) );
        }

        $response = [
            "status" => "Erro ao atualizar no banco de dados!",
            "message" => "danger",
        ];
        http_response_code(400);
        exit( json_encode($response) );

    }

    #[NoReturn] public function c_destroy($id) : string
    {
        $id = filter_var($id, FILTER_SANITIZE_NUMBER_INT);
        $category = $this->category->findBy("id", $id);

        if (!$category) {
            $response = [
                "status" => "Categoria não encontrada!",
                "message" => "alert",
            ];
            http_response_code(400);
            exit(  json_encode($response) );
        }

        $deleted = $this->category->delete("id", $id);

        if ($deleted) {
            $response = [
                "status" => "Categoria deletada!",
                "message" => "success",
            ];
            exit(  json_encode($response) );
        }

        $response = [
            "status" => "Ocorreu um erro ao deletar",
            "message" => "danger",
        ];

        http_response_code(400);
        exit(  json_encode($response) );
    }
}
