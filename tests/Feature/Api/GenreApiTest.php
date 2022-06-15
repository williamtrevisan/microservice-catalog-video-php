<?php

namespace Tests\Feature\Api;

use App\Models\Category as CategoryModel;
use App\Models\Genre as GenreModel;
use Illuminate\Http\Response;
use Tests\TestCase;

class GenreApiTest extends TestCase
{
    protected string $endpoint = '/api/genres';

    public function testShouldBeAbleToListWithoutGenresCreated()
    {
        $response = $this->getJson($this->endpoint);

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonCount(0, 'data');
    }

    public function testShouldBeAbleToListAllGenres()
    {
        GenreModel::factory(30)->create();

        $response = $this->getJson($this->endpoint);

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonCount(15, 'data');
        $response->assertJsonStructure([
            'meta' => [
                'total',
                'current_page',
                'first_page',
                'last_page',
                'per_page',
                'to',
                'from',
            ]
        ]);
    }

    public function testShouldBeReturnAnErrorIfCategoriesIdIsEmpty()
    {
        $payload = ['name' => 'Genre name'];

        $response = $this->postJson($this->endpoint, $payload);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonStructure([
            'message',
            'errors' => ['categories_id']
        ]);
    }

    public function testShouldBeReturnAnErrorIfNameIsEmpty()
    {
        $categories = CategoryModel::factory(3)->create();
        $payload = ['categories_id' => $categories->pluck('id')->toArray()];

        $response = $this->postJson($this->endpoint, $payload);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonStructure([
            'message',
            'errors' => ['name']
        ]);
    }

    public function testShouldBeReturnAnErrorIfNameAndCategoriesIdAreEmpty()
    {
        $payload = [];

        $response = $this->postJson($this->endpoint, $payload);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonStructure([
            'message',
            'errors' => ['name', 'categories_id']
        ]);
    }

    public function testShouldBeAbleToCreateANewGenre()
    {
        $categories = CategoryModel::factory(3)->create();
        $payload = [
            'name' => 'Genre name',
            'categories_id' => $categories->pluck('id')->toArray(),
        ];

        $response = $this->postJson($this->endpoint, $payload);

        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'is_active',
                'created_at'
            ],
        ]);
        $this->assertEquals($payload['name'], $response['data']['name']);
        $this->assertTrue($response['data']['is_active']);
    }

    public function testShouldBeReturnNotFoundIfReceivedAInvalidIdOnShowRoute()
    {
        $response = $this->getJson("$this->endpoint/genreId");

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function testShouldBeAbleToFindAGenreById()
    {
        $genre = GenreModel::factory()->create();

        $response = $this->getJson("$this->endpoint/$genre->id");

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'is_active',
                'created_at',
            ],
        ]);
        $this->assertEquals($genre->id, $response['data']['id']);
    }

    public function testShouldBeReturnAnErrorIfCategoriesIdIsEmptyOnUpdateRoute()
    {
        $genre = GenreModel::factory()->create();
        $payload = ['name' => 'Genre name'];

        $response = $this->putJson("$this->endpoint/$genre->id", $payload);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonStructure([
            'message',
            'errors' => ['categories_id']
        ]);
    }

    public function testShouldBeReturnAnErrorIfNameIsEmptyOnUpdateRoute()
    {
        $genre = GenreModel::factory()->create();
        $categories = CategoryModel::factory(3)->create();
        $payload = ['categories_id' => $categories->pluck('id')->toArray()];

        $response = $this->putJson("$this->endpoint/$genre->id", $payload);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonStructure([
            'message',
            'errors' => ['name']
        ]);
    }

    public function testShouldBeReturnAnErrorIfNameAndCategoriesIdAreEmptyOnUpdateRoute()
    {
        $genre = GenreModel::factory()->create();
        $payload = [];

        $response = $this->putJson("$this->endpoint/$genre->id", $payload);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonStructure([
            'message',
            'errors' => ['name', 'categories_id']
        ]);
    }

    public function testShouldBeReturnNotFoundIfReceivedAInvalidIdOnUpdateRoute()
    {
        $categories = CategoryModel::factory(3)->create();
        $payload = [
            'name' => 'Genre name',
            'categories_id' => $categories->pluck('id')->toArray(),
        ];

        $response = $this->putJson("$this->endpoint/genreId", $payload);

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function testShouldBeAbleToUpdateAGenre()
    {
        $categories = CategoryModel::factory(3)->create();
        $genre = GenreModel::factory()->create();
        $payload = [
            'name' => 'Genre name updated',
            'categories_id' => $categories->pluck('id')->toArray(),
        ];

        $response = $this->putJson("$this->endpoint/$genre->id", $payload);

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'is_active',
                'created_at',
            ],
        ]);
        $this->assertEquals($genre->id, $response['data']['id']);
    }

    public function testShouldBeReturnNotFoundIfReceivedAInvalidIdOnDeleteRoute()
    {
        $response = $this->deleteJson("$this->endpoint/genreId");

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function testShouldBeAbleToDeleteAGenre()
    {
        $genre = GenreModel::factory()->create();

        $response = $this->deleteJson("$this->endpoint/$genre->id");

        $response->assertStatus(Response::HTTP_NO_CONTENT);
    }
}
