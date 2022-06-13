<?php

namespace Core\UseCase\Interface;

interface TransactionInterface
{
    public function commit(): void;
    public function rollback(): void;
}
