<?php

namespace App\Repositories\Eloquent;

use App\Models\Video as VideoModel;
use App\Repositories\Presenters\PaginationPresenter;
use Core\Domain\Entity\BaseEntity;
use Core\Domain\Entity\Video as VideoEntity;
use Core\Domain\Enum\Rating;
use Core\Domain\Exception\NotFoundException;
use Core\Domain\Repository\PaginationInterface;
use Core\Domain\Repository\VideoRepositoryInterface;
use Core\Domain\ValueObject\Uuid;
use Illuminate\Database\Eloquent\Model;

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

        $this->syncRelationships($video, $baseEntity);

        return $this->toVideo($video);
    }

    public function findById(string $id): BaseEntity
    {
        $video = $this->videoModel->find($id);
        if (! $video) throw new NotFoundException("Video with id: $id not found");

        return $this->toVideo($video);
    }

    public function findAll(string $filter = '', string $order = 'DESC'): array
    {
        return $this->videoModel
            ->where(function($query) use ($filter) {
                if ($filter) $query->where('title', 'LIKE', "%$filter%");
            })
            ->orderBy('title', $order)
            ->get()
            ->toArray();
    }

    public function paginate(
        string $filter = '',
        string $order = 'DESC',
        int $page = 1,
        int $totalPage = 15
    ): PaginationInterface {
        $video = $this->videoModel
            ->where(function($query) use ($filter) {
                if ($filter) $query->where('title', 'LIKE', "%$filter%");
            })
            ->orderBy('title', $order)
            ->paginate($totalPage, ['*'], 'page', $page);

        return new PaginationPresenter($video);
    }

    public function update(BaseEntity $baseEntity): BaseEntity
    {
        $video = $this->videoModel->find($baseEntity->id());

        $video->update([
            'title' => $baseEntity->title,
            'description' => $baseEntity->description,
            'year_launched' => $baseEntity->yearLaunched,
            'rating' => $baseEntity->rating->value,
            'duration' => $baseEntity->duration,
            'opened' => $baseEntity->opened,
        ]);
        $video->refresh();
        $this->syncRelationships($video, $baseEntity);

        return $this->toVideo($video);
    }

    public function delete(string $id): bool
    {
        // TODO: Implement delete() method.
    }

    public function updateMedia(BaseEntity $baseEntity): BaseEntity
    {
        // TODO: Implement updateMedia() method.
    }

    protected function syncRelationships(Model $videoModel, BaseEntity $baseEntity) {
        $videoModel->castMembers()->sync($baseEntity->castMembersId);
        $videoModel->categories()->sync($baseEntity->categoriesId);
        $videoModel->genres()->sync($baseEntity->genresId);
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

        $videoModel->castMembers
            ->map(fn($castMember) => $videoEntity->addCastMember($castMember->id));
        $videoModel->categories
            ->map(fn($category) => $videoEntity->addCategory($category->id));
        $videoModel->genres
            ->map(fn($genre) => $videoEntity->addGenre($genre->id));

        return $videoEntity;
    }
}
