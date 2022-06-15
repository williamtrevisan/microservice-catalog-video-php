<?php

namespace Tests\Feature\Core\UseCase\Genre;

use App\Models\Category as CategoryModel;
use App\Models\Genre as GenreModel;
use App\Repositories\Eloquent\CategoryEloquentRepository;
use App\Repositories\Eloquent\GenreEloquentRepository;
use App\Repositories\Transaction\DatabaseTransaction;
use Core\Domain\Entity\Genre as GenreEntity;
use Core\Domain\Exception\NotFoundException;
use Core\Domain\Repository\CategoryRepositoryInterface;
use Core\Domain\Repository\GenreRepositoryInterface;
use Core\Domain\ValueObject\Uuid;
use Core\UseCase\DTO\Genre\Update\UpdateGenreInputDTO;
use Core\UseCase\Genre\FindGenreUseCase;
use Core\UseCase\Genre\UpdateGenreUseCase;
use Core\UseCase\Interface\TransactionInterface;
use Ramsey\Uuid\Uuid as RamseyUuid;
use Tests\TestCase;

class UpdateGenreUseCaseTest extends TestCase
{
    protected CategoryRepositoryInterface $categoryRepository;
    protected GenreRepositoryInterface $genreRepository;
    protected TransactionInterface $databaseTransaction;
    protected FindGenreUseCase $findGenreUseCase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->categoryRepository = new CategoryEloquentRepository(new CategoryModel());
        $this->genreRepository = new GenreEloquentRepository(new GenreModel());
        $this->databaseTransaction = new DatabaseTransaction();
        $this->updateGenreUseCase = new UpdateGenreUseCase(
            categoryRepository: $this->categoryRepository,
            genreRepository: $this->genreRepository,
            transaction: $this->databaseTransaction
        );
    }

    public function testShouldBeAbleToUpdateAGenre()
    {
        $categoryId = CategoryModel::factory()->create()->pluck('id')->toArray();
        $genre = GenreModel::factory()->create();
        $genreEntity = new GenreEntity(
            name: $genre->name,
            id: new Uuid($genre->id),
            createdAt: new \DateTime($genre->created_at)
        );
        $genre->is_active ? $genreEntity->activate() : $genreEntity->deactivate();
        $updateGenreInputDTO = new UpdateGenreInputDTO(
            id: $genre->id,
            name: 'Genre name updated',
            categoriesId: $categoryId
        );

        $response = $this->updateGenreUseCase->execute(input: $updateGenreInputDTO);

        $this->assertEquals($genre->id, $response->id);
        $this->assertDatabaseHas('genres', [
            'id' => $genre->id,
            'name' => 'Genre name updated'
        ]);
        $this->assertDatabaseCount('category_genre', 1);
    }

    public function testShouldBeThrowAnErrorWithInvalidCategoryIdIsReceivedInUpdateGenreUseCase()
    {
        $categoryId = RamseyUuid::uuid4()->toString();

        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage("Category with id: $categoryId, not found in database");

        $genre = GenreModel::factory()->create();
        $genreEntity = new GenreEntity(
            name: $genre->name,
            id: new Uuid($genre->id),
            createdAt: new \DateTime($genre->created_at)
        );
        $genre->is_active ? $genreEntity->activate() : $genreEntity->deactivate();
        $updateGenreInputDTO = new UpdateGenreInputDTO(
            id: $genre->id,
            name: 'Genre name updated',
            categoriesId: [$categoryId]
        );

        $this->updateGenreUseCase->execute(input: $updateGenreInputDTO);
    }

    public function testShouldExecuteARollbackOnErrorOccurence()
    {
        try {
            $categoriesId = CategoryModel::factory(2)->create()->pluck('id')->toArray();
            $categoryId = RamseyUuid::uuid4()->toString();
            $categoriesId[] = $categoryId;
            $genre = GenreModel::factory()->create();
            $genreEntity = new GenreEntity(
                name: $genre->name,
                id: new Uuid($genre->id),
                createdAt: new \DateTime($genre->created_at)
            );
            $genre->is_active ? $genreEntity->activate() : $genreEntity->deactivate();
            $updateGenreInputDTO = new UpdateGenreInputDTO(
                id: $genre->id,
                name: 'Genre name updated',
                categoriesId: $categoriesId
            );

            $this->updateGenreUseCase->execute($updateGenreInputDTO);

            $this->assertTrue(false);
        } catch (\Throwable $throwable) {
            $this->assertInstanceOf(
                NotFoundException::class,
                $throwable,
                "Category with id: $categoryId, not found in database"
            );
            $this->assertDatabaseCount('genres', 0);
            $this->assertDatabaseCount('category_genre', 0);
        }
    }
}
