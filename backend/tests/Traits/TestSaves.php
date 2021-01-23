<?php

namespace Tests\Traits;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\TestResponse;
use PHPUnit\Framework\TestResult;
use Tests\TestCase;

trait TestSaves {

    protected function assertStore(array $sendData, array $testDatabase, array $testJsonData = null): TestResponse
    {
        $response = $this->json('POST', $this->routeStore(),$sendData);
        if($response->status() !== 201){
            throw new \Exception("Response status must be 201, given {$response->status()}:\n{$response->content()}");
        }

        $this->assertInDatabase($response, $testDatabase);

        $this->assertJsonResponseContent($response, $testDatabase, $testJsonData);

        return $response;
    }

    protected function assertUpdate(array $sendData, array $testDatabase, array $testJsonData = null): TestResponse
    {
        $response = $this->json('PUT', $this->routeUpdate(),$sendData);
        if($response->status() !== 200){
            throw new \Exception("Response status must be 200, given {$response->status()}:\n{$response->content()}");
        }

        $this->assertInDatabase($response, $testDatabase);

        $this->assertJsonResponseContent($response, $testDatabase, $testJsonData);

        return $response;
    }

    private function assertInDatabase($response, $testDatabase){
        $model = $this->model();
        $tabela = (new $model)->getTable();
        $this->assertDatabaseHas($tabela, $testDatabase + [ 'id' => $this->getIdFromResponse($response)]);
    }

    private function assertJsonResponseContent($response, $testDatabase, $testJsonData){
        $testResponse = $testJsonData ?? $testDatabase;
        $response->assertJsonFragment($testResponse + [ 'id' => $this->getIdFromResponse($response)]);
        return $response;
    }

    private function getIdFromResponse(TestResponse $response) {
        return $response->json('id') ?? $response->json('data.id');
    }

}
