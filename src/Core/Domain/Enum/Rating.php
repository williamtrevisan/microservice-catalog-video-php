<?php

namespace Core\Domain\Enum;

enum Rating: string
{
    case Er = 'ER';
    case L = 'L';
    case Rate10 = '10';
    case Rate12 = '12';
    case Rate14 = '14';
    case Rate16 = '16';
    case Rate18 = '18';
}
