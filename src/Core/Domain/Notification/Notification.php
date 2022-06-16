<?php

namespace Core\Domain\Notification;

class Notification
{
    private array $errors = [];

    public function addError(array $error): void
    {
        $this->errors[] = $error;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function messages(?string $context = null): string
    {
        $messages = [];

        foreach ($this->errors as $error) {
            if ($context && $error['context'] !== $context) continue;

            $messages[] = "{$error['context']}: {$error['message']}";
        }

        return implode(', ', $messages);
    }

    public function hasErrors(): bool
    {
        return count($this->errors) > 0;
    }
}
