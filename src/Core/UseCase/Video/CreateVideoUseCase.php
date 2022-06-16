<?php

namespace Core\UseCase\Video;

use Core\Domain\Entity\Video as VideoEntity;
use Core\Domain\Enum\MediaStatus;
use Core\Domain\Enum\Rating;
use Core\Domain\Event\VideoCreatedEvent;
use Core\Domain\Exception\NotFoundException;
use Core\Domain\Repository\CastMemberRepositoryInterface;
use Core\Domain\Repository\CategoryRepositoryInterface;
use Core\Domain\Repository\GenreRepositoryInterface;
use Core\Domain\Repository\VideoRepositoryInterface;
use Core\Domain\ValueObject\Image;
use Core\Domain\ValueObject\Media;
use Core\UseCase\DTO\Video\Create\CreateVideoInputDTO;
use Core\UseCase\DTO\Video\Create\CreateVideoOutputDTO;
use Core\UseCase\Interface\{EventDispatcherInterface, FileStorageInterface, TransactionInterface};
use Exception;

class CreateVideoUseCase
{
    private readonly VideoEntity $videoEntity;

    public function __construct(
        protected readonly CastMemberRepositoryInterface $castMemberRepository,
        protected readonly CategoryRepositoryInterface $categoryRepository,
        protected readonly GenreRepositoryInterface $genreRepository,
        protected readonly VideoRepositoryInterface $videoRepository,
        protected readonly TransactionInterface $transaction,
        protected readonly FileStorageInterface $fileStorage,
        protected readonly EventDispatcherInterface $eventDispatcher,
    ) {}

    /**
     * @throws Exception
     */
    public function execute(CreateVideoInputDTO $input): CreateVideoOutputDTO
    {
        try {
            $this->createVideoEntity($input);

            $this->videoRepository->insert($this->videoEntity);

            $this->storageFiles($input);

            $this->videoRepository->updateMedia($this->videoEntity);

            $this->transaction->commit();

            return $this->output();
        } catch (Exception $exception) {
            $this->transaction->rollback();

            if (isset($mediaPath)) $this->fileStorage->delete($mediaPath);

            throw $exception;
        }
    }

    private function createVideoEntity(CreateVideoInputDTO $input): void
    {
        $this->videoEntity = new VideoEntity(
            title: $input->title,
            description: $input->description,
            yearLaunched: $input->yearLaunched,
            duration: $input->duration,
            opened: $input->opened,
            rating: Rating::from($input->rating),
        );

        $this->validateCastMembersId($input->castMembersId);
        foreach ($input->castMembersId as $castMemberId) {
            $this->videoEntity->addCastMember($castMemberId);
        }

        $this->validateCategoriesId($input->categoriesId);
        foreach ($input->categoriesId as $categoryId) {
            $this->videoEntity->addCategory($categoryId);
        }

        $this->validateGenresId($input->genresId);
        foreach ($input->genresId as $genreId) {
            $this->videoEntity->addGenre($genreId);
        }
    }

    private function validate(array $id = []): void
    {

    }

    /**
     * @throws NotFoundException
     */
    private function validateCastMembersId(array $castMembersId = [])
    {
        $castMembers = $this->castMemberRepository->getIdsByListId($castMembersId);

        $arrayDifference = array_diff($castMembersId, $castMembers);
        if ($arrayDifference) {
            $message = sprintf(
                '%s with id: %s, not found in database',
                count($arrayDifference) > 1 ? 'Cast members' : 'Cast member',
                implode(', ', $arrayDifference)
            );

            throw new NotFoundException($message);
        }
    }

    /**
     * @throws NotFoundException
     */
    private function validateCategoriesId(array $categoriesId = [])
    {
        $categories = $this->categoryRepository->getIdsByListId($categoriesId);

        $arrayDifference = array_diff($categoriesId, $categories);
        if ($arrayDifference) {
            $message = sprintf(
                '%s with id: %s, not found in database',
                count($arrayDifference) > 1 ? 'Categories' : 'Category',
                implode(', ', $arrayDifference)
            );

            throw new NotFoundException($message);
        }
    }

    /**
     * @throws NotFoundException
     */
    private function validateGenresId(array $genresId = [])
    {
        $genres = $this->genreRepository->getIdsByListId($genresId);

        $arrayDifference = array_diff($genresId, $genres);
        if ($arrayDifference) {
            $message = sprintf(
                '%s with id: %s, not found in database',
                count($arrayDifference) > 1 ? 'Genres' : 'Genre',
                implode(', ', $arrayDifference)
            );

            throw new NotFoundException($message);
        }
    }

    private function storageFiles(object $input): void
    {
        $this->storageThumbFile($input);
        $this->storageThumbHalfFile($input);
        $this->storageBannerFile($input);
        $this->storageTrailerFile($input);
        $this->storageVideoFile($input);
    }

    private function storageThumbFile(object $input): void
    {
        $mediaPath = $this->storageFile($this->videoEntity->id(), $input->thumbFile);
        if (! $mediaPath) return;

        $this->videoEntity->changeThumbFile(new Image(filePath: $mediaPath));
    }

    private function storageThumbHalfFile(object $input): void
    {
        $mediaPath = $this->storageFile($this->videoEntity->id(), $input->thumbHalfFile);
        if (! $mediaPath) return;

        $this->videoEntity->changeThumbHalfFile(new Image(filePath: $mediaPath));
    }

    private function storageBannerFile(object $input): void
    {
        $mediaPath = $this->storageFile($this->videoEntity->id(), $input->bannerFile);
        if (! $mediaPath) return;

        $this->videoEntity->changeBannerFile(new Image(filePath: $mediaPath));
    }

    private function storageTrailerFile(object $input): void
    {
        $mediaPath = $this->storageFile($this->videoEntity->id(), $input->trailerFile);
        if (! $mediaPath) return;

        $trailerFile = new Media(
            filePath: $mediaPath,
            status: MediaStatus::Processing,
        );

        $this->videoEntity->changeTrailerFile($trailerFile);
    }

    private function storageVideoFile(object $input): void
    {
        $mediaPath = $this->storageFile($this->videoEntity->id(), $input->videoFile);
        if (! $mediaPath) return;

        $videoFile = new Media(
            filePath: $mediaPath,
            status: MediaStatus::Processing,
        );

        $this->videoEntity->changeVideoFile($videoFile);

        $this->eventDispatcher->dispatch(new VideoCreatedEvent($this->videoEntity));
    }

    private function storageFile(string $filePath, array $media = []): ?string
    {
        if (! $media) return null;

        return $this->fileStorage->store(filePath: $filePath, file: $media);
    }

    private function output(): CreateVideoOutputDTO
    {
        return new CreateVideoOutputDTO(
            id: $this->videoEntity->id(),
            title: $this->videoEntity->title,
            description: $this->videoEntity->description,
            year_launched: $this->videoEntity->yearLaunched,
            duration: $this->videoEntity->duration,
            opened: $this->videoEntity->opened,
            rating: $this->videoEntity->rating->value,
            published: $this->videoEntity->published,
            created_at: $this->videoEntity->createdAt(),
            castMembersId: $this->videoEntity->castMembersId,
            categoriesId: $this->videoEntity->categoriesId,
            genresId: $this->videoEntity->genresId,
            thumbFile: $this->videoEntity->thumbFile()?->filePath(),
            thumbHalfFile: $this->videoEntity->thumbHalfFile()?->filePath(),
            bannerFile: $this->videoEntity->bannerFile()?->filePath(),
            trailerFile: $this->videoEntity->trailerFile()?->filePath,
            videoFile: $this->videoEntity->videoFile()?->filePath,
        );
    }
}

