<?php

namespace Tests\Feature\Api;

use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Tests\TestCase;

class CategoryApiTest extends TestCase
{
    protected $endpoint = '/api/categories';

    public function testListEmptyCategories()
    {
        $response = $this->getJson($this->endpoint);

        $response->assertStatus(Response::HTTP_OK);
    }

    public function testListAllCategories()
    {
        Category::factory()->count(30)->create();

        $response = $this->getJson($this->endpoint);

        $response->assertStatus(Response::HTTP_OK);
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

    public function testListPaginateCategories()
    {
        Category::factory()->count(30)->create();

        $response = $this->getJson("$this->endpoint?page=2");

        $response->assertStatus(Response::HTTP_OK);
        $this->assertEquals(2, $response['meta']['current_page']);
        $this->assertEquals(30, $response['meta']['total']);
    }

    public function testFindCategoryNotFound()
    {
        $response = $this->getJson("$this->endpoint/categoryId");

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function testFindCategory()
    {
        $category = Category::factory()->create();

        $response = $this->getJson("$this->endpoint/$category->id");

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'description',
                'is_active',
                'created_at',
            ],
        ]);
        $this->assertEquals($category->id, $response['data']['id']);
    }

    public function testStoreValidations()
    {
        $payload = [];

        $response = $this->postJson($this->endpoint, $payload);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonStructure([
            'message',
            'errors' => ['name']
        ]);
    }

    public function testStore()
    {
        $payload = ['name' => 'Category name'];

        $response = $this->postJson($this->endpoint, $payload);

        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'description',
                'is_active',
                'created_at'
            ],
        ]);
        $this->assertEquals($payload['name'], $response['data']['name']);
        $this->assertTrue($response['data']['is_active']);
    }

    public function testUpdateNotFound()
    {
        $payload = ['name' => 'Category name updated'];

        $response = $this->putJson("$this->endpoint/categoryId", $payload);

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function testUpdateValidations()
    {
        $response = $this->putJson("$this->endpoint/categoryId", []);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonStructure([
            'message',
            'errors' => ['name']
        ]);
    }

    public function testUpdate()
    {
        $category = Category::factory()->create();
        $payload = ['name' => 'Category name updated'];

        $response = $this->putJson("$this->endpoint/$category->id", $payload);

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'description',
                'is_active',
                'created_at'
            ],
        ]);
        $this->assertEquals($payload['name'], $response['data']['name']);
        $this->assertTrue($response['data']['is_active']);
        $this->assertDatabaseHas('categories', ['name' => 'Category name updated']);
    }

    public function testDeleteNotFound()
    {
        $response = $this->deleteJson("$this->endpoint/categoryId");

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function testDelete()
    {
        $category = Category::factory()->create();

        $response = $this->deleteJson("$this->endpoint/$category->id");

        $response->assertStatus(Response::HTTP_NO_CONTENT);
        $this->assertSoftDeleted('categories', ['id' => $category->id]);
    }
}
