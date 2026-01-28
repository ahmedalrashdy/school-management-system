<?php

namespace App\Support;

class GuestWriteBlocker
{
    private bool $blocked = false;
    private string $message = '';

    public function block(string $message): void
    {
        $this->blocked = true;
        $this->message = $message;
    }

    public function isBlocked(): bool
    {
        return $this->blocked;
    }

    public function message(): string
    {
        return $this->message;
    }
}
