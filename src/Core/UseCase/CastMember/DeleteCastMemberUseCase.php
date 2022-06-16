<?php

namespace Core\UseCase\CastMember;

use Core\Domain\Repository\CastMemberRepositoryInterface;
use Core\UseCase\DTO\CastMember\CastMemberInputDTO;
use Core\UseCase\DTO\CastMember\Delete\DeleteCastMemberOutputDTO;

class DeleteCastMemberUseCase
{
    public function __construct(
        protected readonly CastMemberRepositoryInterface $categoryRepository
    ) {}

    public function execute(CastMemberInputDTO $input): DeleteCastMemberOutputDTO
    {
        $hasBeenDeleted = $this->categoryRepository->delete($input->id);

        return new DeleteCastMemberOutputDTO(
            success: $hasBeenDeleted
        );
    }
}
