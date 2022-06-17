<?php

namespace Core\UseCase\Video;

use Core\UseCase\DTO\Video\Create\{CreateVideoInputDTO, CreateVideoOutputDTO};
use Exception;

class CreateVideoUseCase extends BaseVideoUseCase
{
    /**
     * @throws Exception
     */
    public function execute(CreateVideoInputDTO $input): CreateVideoOutputDTO
    {
        $this->validateAllEntitiesId($input);
        $this->videoBuilder->createEntity($input);

        try {
            $this->videoRepository->insert($this->videoBuilder->getEntity());
            $this->storageFiles($input);
            $this->videoRepository->updateMedia($this->videoBuilder->getEntity());

            $this->transaction->commit();

            return $this->output();
        } catch (Exception $exception) {
            $this->transaction->rollback();

            throw $exception;
        }
    }

    private function output(): CreateVideoOutputDTO
    {
        $videoEntity = $this->videoBuilder->getEntity();

        return new CreateVideoOutputDTO(
            id: $videoEntity->id(),
            title: $videoEntity->title,
            description: $videoEntity->description,
            year_launched: $videoEntity->yearLaunched,
            duration: $videoEntity->duration,
            opened: $videoEntity->opened,
            rating: $videoEntity->rating->value,
            published: $videoEntity->published,
            created_at: $videoEntity->createdAt(),
            castMembersId: $videoEntity->castMembersId,
            categoriesId: $videoEntity->categoriesId,
            genresId: $videoEntity->genresId,
            thumbFile: $videoEntity->thumbFile()?->filePath(),
            thumbHalfFile: $videoEntity->thumbHalfFile()?->filePath(),
            bannerFile: $videoEntity->bannerFile()?->filePath(),
            trailerFile: $videoEntity->trailerFile()?->filePath,
            videoFile: $videoEntity->videoFile()?->filePath,
        );
    }
}

