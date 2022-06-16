<?php

namespace Core\UseCase\CastMember;

use Core\Domain\Repository\CastMemberRepositoryInterface;
use Core\UseCase\DTO\CastMember\List\ListCastMembersInputDTO;
use Core\UseCase\DTO\CastMember\List\ListCastMembersOutputDTO;

class ListCastMembersUseCase
{
    public function __construct(
        protected readonly CastMemberRepositoryInterface $castMemberRepository
    ) {}

    public function execute(ListCastMembersInputDTO $input): ListCastMembersOutputDTO
    {
        $castMembers = $this->castMemberRepository->paginate(
            filter: $input->filter,
            order: $input->order,
            page: $input->page,
            totalPage: $input->totalPage,
        );

        return new ListCastMembersOutputDTO(
            items: $castMembers->items(),
            total: $castMembers->total(),
            current_page: $castMembers->currentPage(),
            first_page: $castMembers->firstPage(),
            last_page: $castMembers->lastPage(),
            per_page: $castMembers->perPage(),
            to: $castMembers->to(),
            from: $castMembers->from(),
        );
    }
}
