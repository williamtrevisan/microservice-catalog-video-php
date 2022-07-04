<?php

namespace App\Repositories\Eloquent;

use App\Models\Video as VideoModel;
use Core\Domain\Entity\BaseEntity;
use Core\Domain\Entity\Video as VideoEntity;
use Core\Domain\Enum\Rating;
use Core\Domain\Repository\PaginationInterface;
use Core\Domain\Repository\VideoRepositoryInterface;
use Core\Domain\ValueObject\Uuid;

class VideoEloquentRepository implements VideoRepositoryInterface
{
    public function __construct(protected readonly VideoModel $videoModel)
    {
    }

    public function insert(BaseEntity $baseEntity): BaseEntity
    {
        $video = $this->videoModel->create([
            'id' => $baseEntity->id(),
            'title' => $baseEntity->title,
            'description' => $baseEntity->description,
            'year_launched' => $baseEntity->yearLaunched,
            'duration' => $baseEntity->duration,
            'opened' => $baseEntity->opened,
            'rating' => $baseEntity->rating->value,
        ]);

        return $this->toVideo($video);
    }

    public function findById(string $id): BaseEntity
    {
        // TODO: Implement findById() method.
    }

    public function findAll(string $filter = '', string $order = 'DESC'): array
    {
        // TODO: Implement findAll() method.
    }

    public function paginate(string $filter = '', string $order = 'DESC', int $page = 1, int $totalPage = 15): PaginationInterface
    {
        // TODO: Implement paginate() method.
    }

    public function update(BaseEntity $baseEntity): BaseEntity
    {
        // TODO: Implement update() method.
    }

    public function delete(string $id): bool
    {
        // TODO: Implement delete() method.
    }

    public function updateMedia(BaseEntity $baseEntity): BaseEntity
    {
        // TODO: Implement updateMedia() method.
    }

    private function toVideo(VideoModel $videoModel): BaseEntity
    {
        $videoEntity = new VideoEntity(
            title: $videoModel->title,
            description: $videoModel->description,
            yearLaunched: (int) $videoModel->year_launched,
            duration: $videoModel->duration,
            opened: (bool) $videoModel->opened,
            rating: Rating::from($videoModel->rating),
            id: new Uuid($videoModel->id),
        );

        return $videoEntity;
    }
}
