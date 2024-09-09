<?php

declare(strict_types=1);

namespace Kreait\Firebase\Auth;

class ImportUsersResult
{
    /**
     * @param array<ImportUserError> $errors
     */
    public function __construct(
        public readonly int $users,
        public readonly array $errors = [],
    ) {
    }
}
