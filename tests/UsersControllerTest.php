<?php

use Bramus\Router\Router;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Request;
use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;


class TaxControllerTest extends TestCase
{
    protected $router;
    protected $client;

    protected function setUp(): void
    {
        $this->router = new Router();
        $this->router->setNamespace('\App\Http\Controllers');

        // Define Route
        $this->router->get('/tax', 'TaxController@show');
        $this->router->post('/tax', 'TaxController@store');
        $this->router->put('/tax/{id}', 'TaxController@update');
        $this->router->delete('/tax/{id}', 'TaxController@destroy');

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
        // GET /tax
        $response = $this->client->get('/tax');
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertJson($response->getBody());
    }

    /**
     * @throws GuzzleException
     * @throws Exception
     */
    public function testPostCategoryRoutes()
    {
        //  POST /tax
        $formData = [
            'nome' => $this->generateRandomName(),
            'token' => 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJub21lIjoiUGVkcm8iLCJwYXNzd29yZCI6MTIzLCJleHAiOjE2ODYxMDgxODUsImtpZCI6IlBlZHJvIn0.vOi6qKbls4SnX-8k263t3O8S7cILJcgB2kmtF2Q1W5M',
        ];

        $response = $this->client->post('/tax', [
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
        //  PUT /tax
        $formData = [
            'nome' => $this->generateRandomName(),
            'token' => 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJub21lIjoiUGVkcm8iLCJwYXNzd29yZCI6MTIzLCJleHAiOjE2ODYxMDgxODUsImtpZCI6IlBlZHJvIn0.vOi6qKbls4SnX-8k263t3O8S7cILJcgB2kmtF2Q1W5M',
        ];

        $queryString = http_build_query($formData);

        $request = new Request('PUT', '/tax/3', [
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
      // DELETE /tax
      $response = $this->client->delete('/tax/5');
      $this->assertEquals(200, $response->getStatusCode());
      $this->assertJson($response->getBody());
    }
}
