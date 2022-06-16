<?php

namespace Core\UseCase\CastMember;

use Core\Domain\Entity\CastMember;
use Core\Domain\Enum\CastMemberType;
use Core\Domain\Repository\CastMemberRepositoryInterface;
use Core\UseCase\DTO\CastMember\Create\CreateCastMemberInputDTO;
use Core\UseCase\DTO\CastMember\Create\CreateCastMemberOutputDTO;

class CreateCastMemberUseCase
{
    public function __construct(
        protected readonly CastMemberRepositoryInterface $castMemberRepository
    ) {}

    public function execute(
        CreateCastMemberInputDTO $input
    ): CreateCastMemberOutputDTO {
        $castMemberEntity = new CastMember(
            name: $input->name,
            type: CastMemberType::from($input->type)
        );

        $castMemberDatabase = $this->castMemberRepository->insert($castMemberEntity);

        return new CreateCastMemberOutputDTO(
            id: $castMemberDatabase->id(),
            name: $castMemberDatabase->name,
            type: $castMemberDatabase->type->value,
            created_at: $castMemberDatabase->createdAt()
        );
    }
}
