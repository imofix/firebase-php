<?php

declare(strict_types=1);

namespace Kreait\Firebase;

use Kreait\Firebase\Firestore\ApiClient;

final class Firestore
{
    /** @var ApiClient */
    private $client;

    private function __construct()
    {
    }

    public static function withApiClient(ApiClient $apiClient): self
    {
        $firestore = new self();
        $firestore->client = $apiClient;

        return $firestore;
    }

    public function database(): ApiClient
    {
        return $this->client;
    }
}
