<?php

namespace App\Controllers;

use App\Database\models\Base;
use App\Database\models\Tax as ModelsTax;
use App\Class\Validate;
use Exception;
use JetBrains\PhpStorm\NoReturn;

class TaxController extends Base
{
    private ModelsTax $tax;
    private Validate $validate;

    public function __construct()
    {
        parent::__construct(); // Chama o construtor da classe pai
        $this->validate = new Validate();
        $this->tax = new ModelsTax();
    }

    /**
     * @throws Exception
     */
    #[NoReturn] public function c_show(): string
    {
        exit( json_encode($this->tax->find()) );
    }

    #[NoReturn] public function c_find($id): string
    {
        $id = filter_var($id, FILTER_SANITIZE_NUMBER_INT);
        $tax = $this->tax->findBy("id", $id);

        if (!$tax) {
            $response = [
                "status" => "Imposto não encontrada!",
                "message" => "alert",
            ];
            http_response_code(400);
            exit(  json_encode($response) );
        }
        exit(  json_encode($tax) );
    }

    #[NoReturn] public function c_store(): string
    {
        $id_category = filter_input(INPUT_POST, "id_category", FILTER_SANITIZE_SPECIAL_CHARS);
        $valor = filter_input(INPUT_POST, "valor", FILTER_VALIDATE_FLOAT);

        $this->validate
            ->required(["id_category", "valor"])
            ->exist($this->tax, "id_category", $id_category);

        $errors = $this->validate->getErrors();

        if ($errors) {
            http_response_code(400);
            exit(  json_encode($errors) );
        }

        $created = $this->tax->create(["id_category" => $id_category, "valor" => $valor]);
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

        $id_category = isset($putData['id_category']) ? filter_var($putData['id_category'], FILTER_SANITIZE_SPECIAL_CHARS) : null;
        $valor = isset($putData['valor']) ? filter_var($putData['valor'], FILTER_VALIDATE_FLOAT) : null;

        $_POST['id_category'] = $id_category;
        $_POST['valor'] = $valor;

        $this->validate
            ->required(["id_category", "valor"])
            ->exist_id($this->tax, "id_category", $id_category, $id);

        $errors = $this->validate->getErrors();

        if ($errors) {
            http_response_code(400);
            exit(  json_encode($errors) );
        }

        $updated = $this->tax->update([
            "fields" => [
                "id_category" => $id_category,
                "valor" => $valor,
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
        return json_encode($response);

    }

    #[NoReturn] public function c_destroy($id) : string
    {
        $id = filter_var($id, FILTER_SANITIZE_NUMBER_INT);
        $tax = $this->tax->findBy("id", $id);

        if (!$tax) {
            $response = [
                "status" => "Imposto não encontrado!",
                "message" => "alert",
            ];
            http_response_code(400);
            exit(  json_encode($response) );
        }

        $deleted = $this->tax->delete("id", $id);

        if ($deleted) {
            $response = [
                "status" => "Imposto deletado!",
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
