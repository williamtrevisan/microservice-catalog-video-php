<?php

namespace Core\UseCase\CastMember;

use Core\Domain\Repository\CastMemberRepositoryInterface;
use Core\UseCase\DTO\CastMember\Update\UpdateCastMemberInputDTO;
use Core\UseCase\DTO\CastMember\Update\UpdateCastMemberOutputDTO;

class UpdateCastMemberUseCase
{
    public function __construct(
        protected readonly CastMemberRepositoryInterface $castMemberRepository
    ) {}

    public function execute(
        UpdateCastMemberInputDTO $input
    ): UpdateCastMemberOutputDTO {
        $castMember = $this->castMemberRepository->findById($input->id);

        $castMember->update(name: $input->name);

        $castMember = $this->castMemberRepository->update($castMember);

        return new UpdateCastMemberOutputDTO(
            id: $castMember->id(),
            name: $castMember->name,
            type: $castMember->type->value,
            created_at: $castMember->createdAt(),
        );
    }
}
