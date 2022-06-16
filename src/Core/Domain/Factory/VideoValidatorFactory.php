<?php

namespace Core\Domain\Factory;

use Core\Domain\Notification\ValidatorInterface;
use Core\Domain\Notification\VideoLaravelValidator;
use Core\Domain\Notification\VideoRakitValidator;

class VideoValidatorFactory
{
    public static function create(): ValidatorInterface
    {
//        return new VideoLaravelValidator();
        return new VideoRakitValidator();
    }
}
