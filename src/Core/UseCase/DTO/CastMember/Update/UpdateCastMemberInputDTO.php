<?php

namespace Core\UseCase\DTO\CastMember\Update;

class UpdateCastMemberInputDTO
{
    public function __construct(public string $id, public string $name) {}
}
