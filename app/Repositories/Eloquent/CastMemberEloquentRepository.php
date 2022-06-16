<?php

namespace App\Repositories\Eloquent;

use App\Models\CastMember as CastMemberModel;
use App\Repositories\Presenters\PaginationPresenter;
use Core\Domain\Entity\CastMember as CastMemberEntity;
use Core\Domain\Enum\CastMemberType;
use Core\Domain\Exception\NotFoundException;
use Core\Domain\Repository\CastMemberRepositoryInterface;
use Core\Domain\Repository\PaginationInterface;
use Core\Domain\ValueObject\Uuid;
use DateTime;

class CastMemberEloquentRepository implements CastMemberRepositoryInterface
{
    public function __construct(protected readonly CastMemberModel $castMemberModel) {}

    public function insert(CastMemberEntity $castMemberEntity): CastMemberEntity
    {
        $castMember = $this->castMemberModel->create([
            'id' => $castMemberEntity->id(),
            'name' => $castMemberEntity->name,
            'type' => $castMemberEntity->type->value,
            'created_at' => $castMemberEntity->createdAt(),
        ]);

        return $this->toCastMember($castMember);
    }

    /**
     * @throws NotFoundException
     */
    public function findById(string $id): CastMemberEntity
    {
        $castMember = $this->castMemberModel->find($id);
        if (! $castMember) throw new NotFoundException("CastMember with id: $id not found");

        return $this->toCastMember($castMember);
    }

    public function findAll(string $filter = '', string $order = 'DESC'): array
    {
        return $this->castMemberModel
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
        $castMember = $this->castMemberModel
            ->where(function($query) use ($filter) {
                if ($filter) $query->where('name', 'LIKE', "%$filter%");
            })
            ->orderBy('name', $order)
            ->paginate($totalPage);

        $this->castMemberModel->paginate();

        return new PaginationPresenter($castMember);
    }

    public function update(CastMemberEntity $castMemberEntity): CastMemberEntity
    {
        $castMember = $this->castMemberModel->find($castMemberEntity->id());

        $castMember->update(['name' => $castMemberEntity->name]);
        $castMember->refresh();

        return $this->toCastMember($castMember);
    }

    /**
     * @throws NotFoundException
     */
    public function delete(string $id): bool
    {
        $castMember = $this->castMemberModel->find($id);
        if (! $castMember) throw new NotFoundException("CastMember with id: $id not found");

        return $castMember->delete();
    }

    private function toCastMember(CastMemberModel $castMemberModel): CastMemberEntity
    {
        $castMemberEntity = new CastMemberEntity(
            name: $castMemberModel->name,
            type: CastMemberType::from($castMemberModel->type),
            id: new Uuid($castMemberModel->id),
            createdAt: new DateTime($castMemberModel->created_at),
        );

        return $castMemberEntity;
    }
}
