<?php

namespace Core\UseCase\Genre;

use App\Models\Category as CategoryModel;
use App\Models\Genre as GenreModel;
use App\Repositories\Eloquent\CategoryEloquentRepository;
use App\Repositories\Eloquent\GenreEloquentRepository;
use App\Repositories\Transaction\DatabaseTransaction;
use Core\Domain\Exception\NotFoundException;
use Core\Domain\Repository\CategoryRepositoryInterface;
use Core\Domain\Repository\GenreRepositoryInterface;
use Core\UseCase\DTO\Genre\Create\CreateGenreInputDTO;
use Core\UseCase\Genre\CreateGenreUseCase;
use Core\UseCase\Interface\TransactionInterface;
use Ramsey\Uuid\Uuid as RamseyUuid;
use Tests\TestCase;

class CreateGenreUseCaseTest extends TestCase
{
    protected CategoryRepositoryInterface $categoryRepository;
    protected GenreRepositoryInterface $genreRepository;
    protected TransactionInterface $databaseTransaction;
    protected CreateGenreUseCase $createGenreUseCase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->categoryRepository = new CategoryEloquentRepository(new CategoryModel());
        $this->genreRepository = new GenreEloquentRepository(new GenreModel());
        $this->databaseTransaction = new DatabaseTransaction();
        $this->createGenreUseCase = new CreateGenreUseCase(
            $this->categoryRepository,
            $this->genreRepository,
            $this->databaseTransaction
        );
    }

    public function testShouldBeAbleToCreateANewGenre()
    {
        $createGenreInputDTO = new CreateGenreInputDTO(name: 'Genre name');

        $this->createGenreUseCase->execute($createGenreInputDTO);

        $this->assertDatabaseHas('genres', ['name' => 'Genre name']);
    }

    public function testShouldBeAbleToCreateANewGenreWithCategories()
    {
        $categoriesId = CategoryModel::factory(10)->create()->pluck('id')->toArray();
        $createGenreInputDTO =
            new CreateGenreInputDTO(name: 'Genre name', categoriesId: $categoriesId);

        $this->createGenreUseCase->execute($createGenreInputDTO);

        $this->assertDatabaseCount('category_genre', 10);
    }

    public function testShouldBeThrowAnErrorWithInvalidCategoryIdIsReceivedInCreateGenreUseCase()
    {
        $this->expectException(NotFoundException::class);

        $categoriesId = CategoryModel::factory(10)->create()->pluck('id')->toArray();
        $categoriesId[] = RamseyUuid::uuid4()->toString();
        $createGenreInputDTO =
            new CreateGenreInputDTO(name: 'Genre name', categoriesId: $categoriesId);

        $this->createGenreUseCase->execute($createGenreInputDTO);
    }

    public function testShouldExecuteARollbackOnErrorOccurence()
    {
        try {
            $categoriesId = CategoryModel::factory(10)->create()->pluck('id')->toArray();
            $categoriesId[] = RamseyUuid::uuid4()->toString();
            $createGenreInputDTO =
                new CreateGenreInputDTO(name: 'Genre name', categoriesId: $categoriesId);

            $this->createGenreUseCase->execute($createGenreInputDTO);

            $this->assertTrue(false);
        } catch (\Throwable $throwable) {
            $this->assertDatabaseCount('genres', 0);
            $this->assertDatabaseCount('category_genre', 0);
        }
    }
}
