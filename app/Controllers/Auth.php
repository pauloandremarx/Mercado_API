<?php

namespace App\Controllers;

use App\Database\models\Base;
use App\Database\models\Users as ModelsUser;
use App\Class\Validate;
use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use JetBrains\PhpStorm\NoReturn;

class Auth extends Base
{
    private ModelsUser $user;
    private Validate $validate;

    public function __construct()
    {
        parent::__construct();
        $this->validate = new Validate();
        $this->user = new ModelsUser();
    }


    #[NoReturn] public function login_store(): string
    {
        $jwtSecret = '123456789';
        $jwtAlgorithm = 'HS256';

        $nome = filter_input(INPUT_POST, "nome", FILTER_SANITIZE_SPECIAL_CHARS);
        $password = filter_input(INPUT_POST, "password", FILTER_VALIDATE_INT);


        $this->validate
            ->required(["nome", "password"])
            ->login($this->user, "nome", $nome, "password", $password);

        $errors = $this->validate->getErrors();

        if ($errors) {
            http_response_code(400);
            exit(  json_encode($errors) );
        }

        $payload = [
            'nome' => $nome,
            'password' => $password,
            'exp' => time() + 3600,
            'kid' => $nome
        ];


        $token = JWT::encode($payload, $jwtSecret, $jwtAlgorithm);

        // Define o valor do $nome no cookie esse identificador serve para dar match com o token
        setcookie('nome', $nome, time() + 3600, '/');

        $response = [
            'success' => true,
            'message' => 'Login bem-sucedido',
            'token' => $token
        ];

        exit( json_encode($response));
    }

     public function login_show(): string
    {
        $jwtSecret = '123456789';

        $KeyId = $_COOKIE['nome'] ?? '';
        if (empty($KeyId)) {
            $response = [
                'logged_in' => false,
                'error' => 'Nenhum token encontrado no cookie'
            ];
            exit( json_encode($response) );

        }


        $token = $_SERVER['HTTP_AUTHORIZATION'] ?? '';

        if ($token) {
            try {
                $decodedToken = JWT::decode($token, new Key($jwtSecret, 'HS256'));
                $kid = $decodedToken->kid ?? '';

                if (empty($kid)) {
                    $response = [
                        'logged_in' => false,
                        'error' => 'Nenhum identificador de chave encontrado no token'
                    ];

                    exit( json_encode($response) );

                }

                // Verificar se o identificador da chave corresponde ao valor esperado
                if ($kid !== $_COOKIE['nome']) {
                    $response = [
                        'logged_in' => false,
                        'error' => 'Identificador de chave inválido'
                    ];
                     json_encode($response);

                }

                $response = [
                    'logged_in' => true,
                    'user' => $decodedToken->nome
                ];
                exit( json_encode($response) );
            } catch (Exception $e) {
                $error = ['logged_in' => false, 'error' => $e->getMessage(), 'token' => $token];
                exit( json_encode($error) );

            }
        } else {
            $response = [
                'logged_in' => false,
                'error' => 'Nenhum token encontrado no parâmetro de consulta'
            ];
            exit( json_encode($response) );

        }

    }

    #[NoReturn] public function logout(): string
    {
        setcookie('nome', '', time() - 3600, '/');
        $response = [
            'success' => true,
            'message' => 'Logout bem-sucedido'
        ];
        exit( json_encode($response) );
    }


}





