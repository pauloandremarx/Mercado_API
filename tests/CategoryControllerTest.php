<?php

use Bramus\Router\Router;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Request;
use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;


class CategoryControllerTest extends TestCase
{
    protected $router;
    protected $client;

    protected function setUp(): void
    {
        $this->router = new Router();
        $this->router->setNamespace('\App\Http\Controllers');

        // Define Route
        $this->router->get('/category', 'CategoryController@show');
        $this->router->post('/category', 'CategoryController@store');
        $this->router->put('/category/{id}', 'CategoryController@update');
        $this->router->delete('/category/{id}', 'CategoryController@destroy');

       //Execute
        $this->router->run();

        // Create Guzzle Client
        $this->client = new Client([
            'base_uri' => 'http://localhost:8080',
        ]);
    }

    /**
     * @throws Exception
     */
    function generateRandomName($length = 10): string
    {
        $bytes = random_bytes($length);
        return bin2hex($bytes);
    }

    /**
     * @throws GuzzleException
     */

    public function testGetCategoryRoutes()
    {
        // GET /category
        $response = $this->client->get('/category');
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertJson($response->getBody());
    }

    /**
     * @throws GuzzleException
     * @throws Exception
     */
    public function testPostCategoryRoutes()
    {
        //  POST /category
        $formData = [
            'nome' => $this->generateRandomName(),
            'token' => 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJub21lIjoiUGVkcm8iLCJwYXNzd29yZCI6MTIzLCJleHAiOjE2ODYxMDgxODUsImtpZCI6IlBlZHJvIn0.vOi6qKbls4SnX-8k263t3O8S7cILJcgB2kmtF2Q1W5M',
        ];

        $response = $this->client->post('/category', [
            RequestOptions::FORM_PARAMS => $formData,
        ]);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertJson($response->getBody());
    }


    /**
     * @throws GuzzleException
     * @throws Exception
     */
    public function testPutCategoryRoutes()
    {
        //  PUT /category
        $formData = [
            'nome' => $this->generateRandomName(),
            'token' => 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJub21lIjoiUGVkcm8iLCJwYXNzd29yZCI6MTIzLCJleHAiOjE2ODYxMDgxODUsImtpZCI6IlBlZHJvIn0.vOi6qKbls4SnX-8k263t3O8S7cILJcgB2kmtF2Q1W5M',
        ];

        $queryString = http_build_query($formData);

        $request = new Request('PUT', '/category/3', [
            'Content-Type' => 'application/x-www-form-urlencoded',
        ], $queryString);

        $response = $this->client->send($request);

        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * @throws GuzzleException
     */
    public function testDeleteCategoryRoutes()
    {
      // DELETE /category
      $response = $this->client->delete('/category/5');
      $this->assertEquals(200, $response->getStatusCode());
      $this->assertJson($response->getBody());
    }
}
