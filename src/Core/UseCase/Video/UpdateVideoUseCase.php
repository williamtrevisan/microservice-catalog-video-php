<?php

namespace Core\UseCase\Video;

use Core\Domain\Builder\Video\UpdateVideoBuilder;
use Core\Domain\Builder\Video\VideoBuilderInterface;
use Core\UseCase\DTO\Video\Update\UpdateVideoInputDTO;
use Core\UseCase\DTO\Video\Update\UpdateVideoOutputDTO;
use Exception;

class UpdateVideoUseCase extends BaseVideoUseCase
{
    protected function getBuilder(): VideoBuilderInterface
    {
        return new UpdateVideoBuilder;
    }

    /**
     * @throws Exception
     */
    public function execute(UpdateVideoInputDTO $input): UpdateVideoOutputDTO
    {
        $video = $this->videoRepository->findById($input->id);
        $video->update(title: $input->title, description: $input->title);
        $this->videoBuilder->setEntity($video);

        $this->validateAllEntitiesId($input);

        try {
            $this->videoRepository->update($this->videoBuilder->getEntity());
            $this->storageFiles($input);
            $this->videoRepository->updateMedia($this->videoBuilder->getEntity());

            $this->transaction->commit();

            return $this->output();
        } catch (Exception $exception) {
            $this->transaction->rollback();

            throw $exception;
        }
    }

    private function output(): UpdateVideoOutputDTO
    {
        $videoEntity = $this->videoBuilder->getEntity();

        return new UpdateVideoOutputDTO(
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
