<?php

declare(strict_types=1);

namespace Kreait\Firebase\Tests\Unit\Auth;

use Beste\Json;
use DateTimeImmutable;
use Generator;
use Kreait\Firebase\Auth\ImportUserRecord;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

use function bin2hex;
use function random_bytes;

/**
 * @internal
 */
class UserImportRecordTest extends TestCase
{
    #[DataProvider('getExpectations')]
    public function testJsonSerialization(ImportUserRecord $record, string $expectedFormat): void
    {
        $this->assertSame(JSON::encode($record), $expectedFormat);
    }

    public static function getExpectations(): Generator
    {
        yield [
            ImportUserRecord::new()
                ->withUid($uid = bin2hex(random_bytes(5)))
                ->withDisplayName($displayName = 'Some display name')
                ->withPhotoUrl($photoUrl = 'https://example.org/photo.jpg')
                ->withPhoneNumber($phoneNumber = '+1234567'.\random_int(1000, 9999))
                ->withVerifiedEmail($email = $uid.'@example.org')
                ->markTokensValidAfter($validSince = new DateTimeImmutable())
                ->markAsEnabled()
                ->withCustomClaims($claims = ['admin' => true]),
            JSON::encode([
                'localId' => $uid,
                'email' => $email,
                'emailVerified' => true,
                'displayName' => $displayName,
                'disabled' => false,
                'phoneNumber' => $phoneNumber,
                'photoUrl' => $photoUrl,
                'customAttributes' => json_encode($claims),
                'validSince' => $validSince->format(DATE_ATOM),
            ]),
        ];
    }
}
