<?php

declare(strict_types=1);

namespace Kreait\Firebase\Request;

use Kreait\Firebase\Auth\UserRecord;
use Kreait\Firebase\Request;

class ImportUsers implements Request
{
    /** @var array<UserRecord> */
    private $users;

    private function __construct()
    {
    }

    public static function new(): self
    {
        return new self();
    }

    /**
     * @param array<UserRecord> $users
     * @return $this
     */
    public function withUserRecords(array $users): self
    {
        $request = clone $this;
        $request->users = $users;

        return $request;
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return [
            'users' => $this->users
        ];
    }
}
