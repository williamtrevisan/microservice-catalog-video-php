<?php

namespace Core\UseCase\CastMember;

use Core\Domain\Repository\CastMemberRepositoryInterface;
use Core\UseCase\DTO\CastMember\CastMemberInputDTO;
use Core\UseCase\DTO\CastMember\Find\FindCastMemberOutputDTO;

class FindCastMemberUseCase
{
    public function __construct(
        protected readonly CastMemberRepositoryInterface $castMemberRepository
    ) {}

    public function execute(CastMemberInputDTO $input): FindCastMemberOutputDTO
    {
        $castMember = $this->castMemberRepository->findById(id: $input->id);

        return new FindCastMemberOutputDTO(
            id: $castMember->id(),
            name: $castMember->name,
            type: $castMember->type->value,
            created_at: $castMember->createdAt(),
        );
    }
}
