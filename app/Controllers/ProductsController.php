<?php

namespace App\Controllers;

use App\Database\models\Base;
use App\Database\models\Products as ModelsProducts;
use App\Class\Validate;
use Exception;
use JetBrains\PhpStorm\NoReturn;

class ProductsController extends Base
{
    private ModelsProducts $product;
    private Validate $validate;

    public function __construct()
    {
        parent::__construct(); // Chama o construtor da classe pai
        $this->validate = new Validate();
        $this->product = new ModelsProducts();
    }

    /**
     * @throws Exception
     */
    #[NoReturn] public function c_show(): string
    {
        exit( json_encode($this->product->find()) );
    }

    #[NoReturn] public function c_find($id): string
    {
        $id = filter_var($id, FILTER_SANITIZE_NUMBER_INT);
        $product = $this->product->findBy("id", $id);

        if (!$product) {
            $response = [
                "status" => "Produto não encontrada!",
                "message" => "alert",
            ];
            http_response_code(400);
            exit(  json_encode($response) );
        }
        exit(  json_encode($product) );
    }


    #[NoReturn] public function c_store(): string
    {
        $nome = filter_input(INPUT_POST, "nome", FILTER_SANITIZE_SPECIAL_CHARS);
        $valor = filter_input(INPUT_POST, "valor", FILTER_VALIDATE_FLOAT);
        $category_id = filter_input(INPUT_POST, "category_id", FILTER_DEFAULT);

        $this->validate->required(["nome", "valor", "category_id"])->exist($this->product, "nome", $nome);
        $errors = $this->validate->getErrors();

        if ($errors) {
            http_response_code(400);
            exit (json_encode($errors));
        }else{
            $created = $this->product->create(["nome" => $nome, "valor" => $valor, "category_id" => $category_id]);
            if ($created) {
                $response = [
                    "status" => "Cadastrado com sucesso",
                    "message" => "success",
                ];
                exit(json_encode($response));
        }}

        $response = [
            "status" => "Erro ao gravar no banco de dados!" ,
            "message" => "danger",
        ];
        http_response_code(400);
        exit(json_encode($response));
    }

    #[NoReturn] public function c_update($id): string
    {

        $inputData = file_get_contents('php://input');
        $putData = json_decode($inputData, true);

        $nome = isset($putData['nome']) ? filter_var($putData['nome'], FILTER_SANITIZE_SPECIAL_CHARS) : null;
        $valor = isset($putData['valor']) ? filter_var($putData['valor'], FILTER_VALIDATE_FLOAT) : null;
        $category_id = isset($putData['category_id']) ? filter_var($putData['category_id'], FILTER_VALIDATE_FLOAT) : null;


        $_POST['nome'] = $nome;
        $_POST['valor'] = $valor;
        $_POST['category_id'] = $category_id;

        $this->validate
            ->required(["nome", "valor", "category_id"])
            ->exist_id($this->product, "nome", $nome, $id);

        $errors = $this->validate->getErrors();

        if ($errors) {
            http_response_code(400);
            exit(  json_encode($errors) );
        }

        $updated = $this->product->update([
            "fields" => [
                "nome" => $nome,
                "valor" => $valor,
                "category_id" => $category_id,
            ],
            "where" => ["id" => $id],
        ]);

        if ($updated) {
            $response = [
                "status" => "Atualizado com sucesso!",
                "message" => "success",
                "id" => $id,
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
        $product = $this->product->findBy("id", $id);

        if (!$product) {
            $response = [
                "status" => "Produto não encontrado!",
                "message" => "alert",
            ];
            http_response_code(400);
            exit(  json_encode($response) );
        }

        $deleted = $this->product->delete("id", $id);

        if ($deleted) {
            $response = [
                "status" => "Produto deletado!",
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
