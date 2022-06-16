<?php

namespace Core\UseCase\DTO\CastMember\Create;

class CreateCastMemberInputDTO
{
    public function __construct(public string $name, public int $type) {}
}
