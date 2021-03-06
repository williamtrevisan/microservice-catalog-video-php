<?php

namespace App\Repositories\Eloquent;

use App\Models\Genre as GenreModel;
use App\Repositories\Presenters\PaginationPresenter;
use Core\Domain\Entity\{BaseEntity, Genre as GenreEntity};
use Core\Domain\Exception\NotFoundException;
use Core\Domain\Repository\{GenreRepositoryInterface, PaginationInterface};
use Core\Domain\ValueObject\Uuid;
use DateTime;

class GenreEloquentRepository implements GenreRepositoryInterface
{
    public function __construct(protected readonly GenreModel $genreModel) {}

    public function insert(BaseEntity $genreEntity): BaseEntity
    {
        $genre = $this->genreModel->create([
            'id' => $genreEntity->id(),
            'name' => $genreEntity->name,
            'is_active' => $genreEntity->isActive,
            'created_at' => $genreEntity->createdAt(),
        ]);

        if ($genreEntity->categoriesId) {
            $genre->categories()->sync($genreEntity->categoriesId);
        }

        return $this->toGenre($genre);
    }

    /**
     * @throws NotFoundException
     */
    public function findById(string $id): BaseEntity
    {
        $genre = $this->genreModel->find($id);
        if (! $genre) throw new NotFoundException("Genre with id: $id not found");

        return $this->toGenre($genre);
    }

    public function getIdsByListId(array $genresId = []): array
    {
        return $this->genreModel
            ->whereIn('id', $genresId)
            ->pluck('id')
            ->toArray();
    }

    public function findAll(string $filter = '', string $order = 'DESC'): array
    {
        return $this->genreModel
            ->where(function($query) use ($filter) {
                if ($filter) $query->where('name', 'LIKE', "%$filter%");
            })
            ->orderBy('name', $order)
            ->get()
            ->toArray();
    }

    public function paginate(
        string $filter = '',
        string $order = 'DESC',
        int $page = 1,
        int $totalPage = 15
    ): PaginationInterface {
        $genre = $this->genreModel
            ->where(function($query) use ($filter) {
                if ($filter) $query->where('name', 'LIKE', "%$filter%");
            })
            ->orderBy('name', $order)
            ->paginate($totalPage);

        return new PaginationPresenter($genre);
    }

    public function update(BaseEntity $genreEntity): BaseEntity
    {
        $genre = $this->genreModel->find($genreEntity->id());

        $genre->update(['name' => $genreEntity->name]);
        if ($genreEntity->categoriesId) {
            $genre->categories()->sync($genreEntity->categoriesId);
        }
        $genre->refresh();

        return $this->toGenre($genre);
    }

    /**
     * @throws NotFoundException
     */
    public function delete(string $id): bool
    {
        $genre = $this->genreModel->find($id);
        if (! $genre) throw new NotFoundException("Genre with id: $id not found");

        return $genre->delete();
    }

    private function toGenre(GenreModel $genreModel): BaseEntity
    {
        $genreEntity = new GenreEntity(
            name: $genreModel->name,
            id: new Uuid($genreModel->id),
            createdAt: new DateTime($genreModel->created_at),
        );

        $genreModel->is_active ? $genreEntity->activate() : $genreEntity->deactivate();

        return $genreEntity;
    }
}
