<?php

declare(strict_types=1);

namespace Kreait\Firebase\Auth;

final class ImportUserError
{
    private function __construct(public readonly int $index, public readonly string $message)
    {
    }

    /**
     * @param array<string, mixed> $error
     */
    public static function fromResponseData(array $error): self
    {
        return new self($error['index'], $error['message']);
    }

    public function getIndex(): int
    {
        return $this->index;
    }

    public function getMessage(): string
    {
        return $this->message;
    }
}
