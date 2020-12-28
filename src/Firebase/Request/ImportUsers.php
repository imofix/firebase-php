<?php

declare(strict_types=1);

namespace Kreait\Firebase\Request;

use Kreait\Firebase\Auth\ImportUserRecord;
use Kreait\Firebase\Auth\UserRecord;
use Kreait\Firebase\Exception\InvalidArgumentException;
use Kreait\Firebase\Request;

class ImportUsers implements Request
{
    public const MAX_IMPORT_USERS = 1000;

    /** @var array<ImportUserRecord> */
    private $usersToImport;

    /**
     * @param array<UserRecord> $users
     */
    public function __construct(array $usersToImport)
    {
        if (count($usersToImport) === 0) {
            throw new InvalidArgumentException('Users must not be empty.');
        }

        if (count($usersToImport) > 1000) {
            throw new InvalidArgumentException(
                sprintf('Users list must not contain more than %s records', self::MAX_IMPORT_USERS)
            );
        }

        $this->usersToImport = $usersToImport;
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return [
            'users' => $this->usersToImport
        ];
    }
}
