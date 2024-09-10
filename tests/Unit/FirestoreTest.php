<?php

declare(strict_types=1);

namespace Kreait\Firebase\Tests\Unit;

use Kreait\Firebase\Firestore;
use Kreait\Firebase\Firestore\ApiClient;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
final class FirestoreTest extends TestCase
{
    #[Test]
    public function itReturnsTheSameClientItWasGiven(): void
    {
        $client = $this->createMock(ApiClient::class);
        $firestore = Firestore::withApiClient($client);

        $this->assertSame($client, $firestore->database());
    }
}
