<?php

namespace Core\Domain\Enum;

enum MediaStatus: int
{
    case Processing = 0;
    case Complete = 1;
    case Pending = 2;
}
