<?php

namespace App\Controllers;
use App\Database\models\Users as ModelsUser;
use App\Database\models\Base;
use App\Class\Validate;
use JetBrains\PhpStorm\NoReturn;

class UsersController extends Base
{

    private ModelsUser $user;
    private Validate $validate;

    public function __construct()
    {
        parent::__construct(); // Chama o construtor da classe pai
        $this->validate = new Validate();
        $this->user = new ModelsUser;
    }


    #[NoReturn] public function c_show() : string
    {
        exit( json_encode($this->user->find()) );
    }

    #[NoReturn] public function c_find($id): string
    {
        $id = filter_var($id, FILTER_SANITIZE_NUMBER_INT);
        $user = $this->user->findBy("id", $id);

        if (!$user) {
            $response = [
                "status" => "Imposto não encontrada!",
                "message" => "alert",
            ];
            http_response_code(400);
            exit(  json_encode($response) );
        }
        exit(  json_encode($user) );
    }

    #[NoReturn] public function c_store(): string
    {
        $nome = filter_input(INPUT_POST, "nome", FILTER_SANITIZE_SPECIAL_CHARS);
        $password = filter_input(INPUT_POST, "password", FILTER_SANITIZE_SPECIAL_CHARS);
        $email = filter_input(INPUT_POST, "email", FILTER_VALIDATE_EMAIL);
        $admin = filter_input(INPUT_POST, "admin", FILTER_VALIDATE_BOOLEAN);

        $this->validate
            ->required(["nome", "password", "email"])
            ->exist($this->user, "email", $email);

        $errors = $this->validate->getErrors();

        if ($errors) {
            http_response_code(400);
            exit(  json_encode($errors) );
        }

        $admin = $admin ? 1 : 0;

        $created = $this->user->create(["nome" => $nome, "password" => $password , "email" => $email, "admin" => 0]);

        if ($created) {
            $response = [
                "status" => "Cadastrado com sucesso",
                "message" => "success",
            ];
            exit( json_encode($response) );
        }

        $response = [
            "status" => "Erro ao gravar no banco de dados!" . $admin,
            "message" => "danger",
        ];
        http_response_code(400);
        exit( json_encode($response) );

    }

    #[NoReturn] public function c_update( $id) : string
    {

        $inputData = file_get_contents('php://input');
        $putData = json_decode($inputData, true);

        $nome = isset($putData['nome']) ? filter_var($putData['nome'], FILTER_SANITIZE_SPECIAL_CHARS) : null;
        $password = isset($putData['password']) ? filter_var($putData['password'], FILTER_SANITIZE_SPECIAL_CHARS) : null;
        $email = isset($putData['email']) ? filter_var($putData['email'], FILTER_VALIDATE_EMAIL) : null;
        $admin = isset($putData['admin']) ? filter_var($putData['admin'], FILTER_VALIDATE_BOOLEAN) : null;

        $_POST['nome'] = $nome;
        $_POST['password'] = $password;
        $_POST['email'] = $email;
        $_POST['admin'] = $admin;

        $this->validate
            ->required(["nome", "password", "email", "admin"])
            ->exist_id($this->user, "email", $email, $id);

        $errors = $this->validate->getErrors();

        $admin = $admin ? 1 : 0;

        if ($errors) {
            http_response_code(400);
            exit(  json_encode($errors) );
        }

        $updated = $this->user->update([
            "fields" => [
                "nome" => $nome,
                "password" => $password,
                "email" => $email,
                "admin" => $admin,
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
        $product = $this->user->findBy("id", $id);

        if (!$product) {
            $response = [
                "status" => "Usuário não encontrado!",
                "message" => "alert",
            ];
            http_response_code(400);
            exit(  json_encode($response) );
        }

        $deleted = $this->user->delete("id", $id);

        if ($deleted) {
            $response = [
                "status" => "Usuário deletado!",
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
