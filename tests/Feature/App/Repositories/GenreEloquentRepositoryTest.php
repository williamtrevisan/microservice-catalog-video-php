<?php

namespace App\Repositories;

use App\Models\Category as CategoryModel;
use App\Models\Genre as GenreModel;
use App\Repositories\Eloquent\GenreEloquentRepository;
use Core\Domain\Entity\Genre as GenreEntity;
use Core\Domain\Exception\NotFoundException;
use Core\Domain\Repository\GenreRepositoryInterface;
use Core\Domain\ValueObject\Uuid;
use DateTime;
use Tests\TestCase;

class GenreEloquentRepositoryTest extends TestCase
{
    protected GenreRepositoryInterface $genreEloquentRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->genreEloquentRepository = new GenreEloquentRepository(new GenreModel());
    }

    public function testImplementsInterface()
    {
        $this->assertInstanceOf(
            GenreRepositoryInterface::class,
            $this->genreEloquentRepository
        );
    }

    public function testInsert()
    {
        $genreEntity = new GenreEntity(name: 'Genre name');

        $response = $this->genreEloquentRepository->insert($genreEntity);

        $this->assertNotEmpty($response->id());
        $this->assertEquals($genreEntity->name, $response->name);
        $this->assertTrue($response->isActive);
        $this->assertNotEmpty($response->createdAt());
        $this->assertDatabaseHas('genres', ['id' => $genreEntity->id()]);
    }

    public function testInsertDeactivate()
    {
        $genreEntity = new GenreEntity(name: 'Genre name', isActive: false);

        $response = $this->genreEloquentRepository->insert($genreEntity);

        $this->assertNotEmpty($response->id());
        $this->assertEquals($genreEntity->name, $response->name);
        $this->assertFalse($response->isActive);
        $this->assertNotEmpty($response->createdAt());
        $this->assertDatabaseHas('genres', ['id' => $genreEntity->id()]);
    }

    public function testInsertWithRelationships()
    {
        $categoriesId = CategoryModel::factory()
            ->count(4)
            ->create()
            ->pluck('id')
            ->toArray();
        $genreEntity = new GenreEntity(name: 'Genre name', categoriesId: $categoriesId);

        $response = $this->genreEloquentRepository->insert($genreEntity);

        $this->assertDatabaseHas('genres', ['id' => $response->id()]);
        $this->assertDatabaseCount('category_genre', 4);
    }

    public function testFindByIdNotFound()
    {
        $this->expectException(NotFoundException::class);

        $this->genreEloquentRepository->findById('categoryId');
    }

    public function testFindById()
    {
        $genre = GenreModel::factory()->create();

        $response = $this->genreEloquentRepository->findById($genre->id);

        $this->assertEquals($genre->id, $response->id());
        $this->assertEquals($genre->name, $response->name);
    }

    public function testFindAll()
    {
        GenreModel::factory()->count(2)->create([
            'name' => 'Genre name',
        ]);
        GenreModel::factory()->count(13)->create();

        $response = $this->genreEloquentRepository->findAll();

        $this->assertCount(15, $response);
    }

    public function testFindAllWithFilter()
    {
        GenreModel::factory()->count(2)->create([
            'name' => 'Genre name',
        ]);
        GenreModel::factory()->count(13)->create();

        $response = $this->genreEloquentRepository->findAll(filter: 'Genre name');

        $this->assertCount(2, $response);
    }

    public function testFindAllEmpty()
    {
        $response = $this->genreEloquentRepository->findAll();

        $this->assertCount(0, $response);
    }

    public function testPagination()
    {
        GenreModel::factory()->count(132)->create();

        $response = $this->genreEloquentRepository->paginate();

        $this->assertCount(15, $response->items());
        $this->assertEquals(132, $response->total());
    }

    public function testPaginationEmpty()
    {
        $response = $this->genreEloquentRepository->paginate();

        $this->assertCount(0, $response->items());
        $this->assertEquals(0, $response->total());
    }

    public function testUpdate()
    {
        $genre = GenreModel::factory()->create();
        $genreEntity = new GenreEntity(
            name: $genre->name,
            id: new Uuid($genre->id),
            isActive: (bool) $genre->is_active,
            createdAt: new DateTime($genre->created_at)
        );
        $genreEntity->update(name: 'Genre name updated');

        $response = $this->genreEloquentRepository->update($genreEntity);

        $this->assertEquals('Genre name updated', $response->name);
        $this->assertDatabaseHas('genres', ['name' => 'Genre name updated']);
    }

    public function testDeleteIdNotFound()
    {
        $this->expectException(NotFoundException::class);

        $this->genreEloquentRepository->delete("genreId");
    }

    public function testDelete()
    {
        $genre = GenreModel::factory()->create();

        $response = $this->genreEloquentRepository->delete($genre->id);

        $this->assertTrue($response);
        $this->assertSoftDeleted('genres', ['id' => $genre->id]);
    }
}
