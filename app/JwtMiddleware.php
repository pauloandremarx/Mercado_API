<?php

namespace App;

use App\Database\models\Base;

use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JwtMiddleware extends Base
{
    public function handle()
    {
        $jwtSecret = '123456789';


        $token = $_SERVER['HTTP_AUTHORIZATION'] ?? '';

        if ($token) {
            try {
                $decodedToken = JWT::decode($token, new Key($jwtSecret, 'HS256'));
                $kid = $decodedToken->kid ;

                if (empty($kid)) {
                    $response = [
                        'logged_in' => false,
                        'error' => 'Nenhum identificador de chave encontrado no token'
                    ];

                    http_response_code(400);
                    echo json_encode($response);
                    exit();
                }



                $response = [
                    'logged_in' => true,
                    'user' => $decodedToken->nome
                ];
                return json_encode($response);
            } catch (Exception $e) {
                $error = ['logged_in' => false, 'error' => $e->getMessage(), 'token' => $_SERVER['HTTP_AUTHORIZATION'] ];
                http_response_code(400);
                echo json_encode($error);
                exit();
            }
        } else {
            $response = [
                'logged_in' => false,
                'error' => 'Nenhum token encontrado no parâmetro de consulta'
            ];
            http_response_code(400);
            echo json_encode($response);
            exit();
        }

    }

}