<?php

declare(strict_types=1);

namespace Kreait\Firebase\Auth;

use function count;

class ImportUsersResult
{
    /**
     * @param array<ImportUserError> $errors
     */
    public function __construct(public readonly int $users, public readonly array $errors = [])
    {
    }

    public function getSuccessCount(): int
    {
        return $this->users - count($this->errors);
    }

    public function getFailureCount(): int
    {
        return count($this->errors);
    }
}
