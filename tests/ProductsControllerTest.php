<?php

use Bramus\Router\Router;
use GuzzleHttp\Exception\GuzzleException;
use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;

class ProductsControllerTest extends TestCase
{
    protected $router;
    protected $client;

    protected function setUp(): void
    {
        $this->router = new Router();
        $this->router->setNamespace('\App\Http\Controllers');

        // Define Route
        $this->router->post('/products', 'ProductsController@store');
        $this->router->put('/products/{id}', 'ProductsController@update');
        $this->router->delete('/products/{id}', 'ProductsController@destroy');
        $this->router->get('/products/{id}', 'ProductsController@show');


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

    public function testGetProductsRoutes()
    {
        // GET /products
        $response = $this->client->get('/products');
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertJson($response->getBody());
    }

    /**
     * @throws GuzzleException
     * @throws Exception
     */
    public function testPostProductsRoutes()
    {
        //  POST /products
        $formData = [
            'nome' => $this->generateRandomName(),
            'valor' => 10.99,
            'category_id' => '1',
            'token' => 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJub21lIjoiUGVkcm8iLCJwYXNzd29yZCI6MTIzLCJleHAiOjE2ODYxMDgxODUsImtpZCI6IlBlZHJvIn0.vOi6qKbls4SnX-8k263t3O8S7cILJcgB2kmtF2Q1W5M',
        ];

        $response = $this->client->post('/products', [
            RequestOptions::FORM_PARAMS => $formData,
        ]);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertJson($response->getBody());
    }


    /**
     * @throws GuzzleException
     * @throws Exception
     */
    public function testPutProductsRoutes()
    {
        //  PUT /products
        $formData = [
            'nome' => $this->generateRandomName(),
            'valor' => '10.99',
            'category_id' => '1',
            'token' => 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJub21lIjoiUGVkcm8iLCJwYXNzd29yZCI6MTIzLCJleHAiOjE2ODYxMDgxODUsImtpZCI6IlBlZHJvIn0.vOi6qKbls4SnX-8k263t3O8S7cILJcgB2kmtF2Q1W5M',
        ];

        $queryString = http_build_query($formData);

        $request = new \GuzzleHttp\Psr7\Request('PUT', '/products/44', [
            'Content-Type' => 'application/x-www-form-urlencoded',
        ], $queryString);

        $response = $this->client->send($request);

        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * @throws GuzzleException
     */
    public function testDeleteProductsRoutes()
    {
      // DELETE /products/
      $response = $this->client->delete('/products/43');
      $this->assertEquals(200, $response->getStatusCode());
      $this->assertJson($response->getBody());
    }
}
