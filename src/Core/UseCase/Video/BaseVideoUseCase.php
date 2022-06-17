<?php

namespace Core\UseCase\Video;

use Core\Domain\Builder\Video\CreateVideoBuilder;
use Core\Domain\Enum\MediaStatus;
use Core\Domain\Event\VideoCreatedEvent;
use Core\Domain\Exception\NotFoundException;
use Core\Domain\Repository\CastMemberRepositoryInterface;
use Core\Domain\Repository\CategoryRepositoryInterface;
use Core\Domain\Repository\GenreRepositoryInterface;
use Core\Domain\Repository\RepositoryInterface;
use Core\Domain\Repository\VideoRepositoryInterface;
use Core\UseCase\Interface\{EventDispatcherInterface, FileStorageInterface, TransactionInterface};

abstract class BaseVideoUseCase
{
    protected readonly CreateVideoBuilder $videoBuilder;

    public function __construct(
        protected readonly CastMemberRepositoryInterface $castMemberRepository,
        protected readonly CategoryRepositoryInterface $categoryRepository,
        protected readonly GenreRepositoryInterface $genreRepository,
        protected readonly VideoRepositoryInterface $videoRepository,
        protected readonly TransactionInterface $transaction,
        protected readonly FileStorageInterface $fileStorage,
        protected readonly EventDispatcherInterface $eventDispatcher,
    ) {
        $this->videoBuilder = new CreateVideoBuilder;
    }

    /**
     * @throws NotFoundException
     */
    protected function validateAllEntitiesId(object $input): void
    {
        $this->validateEntitiesId(
            listEntitiesId: $input->castMembersId,
            repository: $this->castMemberRepository,
            singularEntityName: 'Cast member'
        );

        $this->validateEntitiesId(
            listEntitiesId: $input->categoriesId,
            repository: $this->categoryRepository,
            singularEntityName: 'Category',
            pluralEntityName: 'Categories'
        );

        $this->validateEntitiesId(
            listEntitiesId: $input->genresId,
            repository: $this->genreRepository,
            singularEntityName: 'Genre'
        );
    }

    /**
     * @throws NotFoundException
     */
    private function validateEntitiesId(
        array $listEntitiesId,
        RepositoryInterface $repository,
        string $singularEntityName,
        string $pluralEntityName = ''
    ): void {
        if (! $listEntitiesId) return;

        $entitiesId = $repository->getIdsByListId($listEntitiesId);
        $pluralEntityName = $pluralEntityName ?: $singularEntityName . 's';

        $arrayDifference = array_diff($listEntitiesId, $entitiesId);
        if ($arrayDifference) {
            $message = sprintf(
                '%s with id: %s, not found in database',
                count($arrayDifference) > 1 ? $pluralEntityName : $singularEntityName,
                implode(', ', $arrayDifference)
            );

            throw new NotFoundException($message);
        }
    }

    protected function storageFiles(object $input): void
    {
        $this->storageThumbFile($input);
        $this->storageThumbHalfFile($input);
        $this->storageBannerFile($input);
        $this->storageTrailerFile($input);
        $this->storageVideoFile($input);
    }

    private function storageThumbFile(object $input): void
    {
        $filePath = $this->storageFile(
            filePath: $this->videoBuilder->getEntity()->id(),
            media: $input->thumbFile
        );
        if (! $filePath) return;

        $this->videoBuilder->addThumbFile(filePath: $filePath);
    }

    private function storageThumbHalfFile(object $input): void
    {
        $filePath = $this->storageFile(
            filePath: $this->videoBuilder->getEntity()->id(),
            media: $input->thumbHalfFile
        );
        if (! $filePath) return;

        $this->videoBuilder->addThumbHalfFile(filePath: $filePath);
    }

    private function storageBannerFile(object $input): void
    {
        $filePath = $this->storageFile(
            filePath: $this->videoBuilder->getEntity()->id(),
            media: $input->bannerFile
        );
        if (! $filePath) return;

        $this->videoBuilder->addBannerFile(filePath: $filePath);
    }

    private function storageTrailerFile(object $input): void
    {
        $filePath = $this->storageFile(
            filePath: $this->videoBuilder->getEntity()->id(),
            media: $input->trailerFile
        );
        if (! $filePath) return;

        $this->videoBuilder->addTrailerFile(filePath: $filePath);
    }

    private function storageVideoFile(object $input): void
    {
        $filePath = $this->storageFile(
            filePath: $this->videoBuilder->getEntity()->id(),
            media: $input->videoFile
        );
        if (! $filePath) return;

        $this->videoBuilder->addVideoFile(
            filePath: $filePath,
            status: MediaStatus::Processing
        );

        $videoCreatedEvent = new VideoCreatedEvent($this->videoBuilder->getEntity());
        $this->eventDispatcher->dispatch(event: $videoCreatedEvent);
    }

    private function storageFile(string $filePath, array $media = []): ?string
    {
        if (! $media) return null;

        return $this->fileStorage->store(filePath: $filePath, file: $media);
    }
}
