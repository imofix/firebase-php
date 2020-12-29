<?php

declare(strict_types=1);

namespace Kreait\Firebase\Tests\Integration\Auth;

use Kreait\Firebase\Auth;
use Kreait\Firebase\Auth\ImportUserRecord;
use Kreait\Firebase\Request\CreateUser;
use Kreait\Firebase\Tests\IntegrationTestCase;

/**
 * @internal
 */
class ImportUsersTest extends IntegrationTestCase
{
    /** @var Auth */
    private $auth;

    protected function setUp(): void
    {
        $this->auth = self::$factory->createAuth();
    }

    public function testImportUser(): void
    {
        $this->auth->importUsers(
            [
                ImportUserRecord::new()
                    ->withUid($uid = \bin2hex(\random_bytes(5)))
                    ->withDisplayName($displayName = 'Some display name')
                    ->withPhotoUrl($photoUrl = 'https://example.org/photo.jpg')
                    ->withPhoneNumber($phoneNumber = '+1234567'.\random_int(1000, 9999))
                    ->withVerifiedEmail($email = $uid.'@example.org')
                    ->withCustomClaims(['admin' => true]),
            ]
        );

        $user = $this->auth->getUser($uid);

        $this->assertSame($uid, $user->uid);
        $this->assertSame($displayName, $user->displayName);
        $this->assertSame($photoUrl, $user->photoUrl); // Firebase stores the photo url in the email provider info
        $this->assertSame($phoneNumber, $user->phoneNumber);
        $this->assertSame($email, $user->email);
        $this->assertTrue($user->emailVerified);
        $this->assertEquals(['admin' => true], $user->customClaims);
        $this->assertFalse($user->disabled);

        $this->auth->deleteUser($user->uid);
    }

    public function testImportUserReplacesExistingUser(): void
    {
        $this->auth->createUser(
            CreateUser::new()
                ->withUid($uid = \bin2hex(\random_bytes(5)))
                ->withDisplayName($displayName = 'Old display name')
                ->withPhotoUrl($photoUrl = 'https://example.org/old-photo.jpg')
                ->withPhoneNumber($phoneNumber = '+12345674')
                ->withVerifiedEmail($email = $uid.'@example.org')
                ->markAsDisabled()
        );

        $user = $this->auth->getUser($uid);

        $this->assertSame($uid, $user->uid);
        $this->assertSame($displayName, $user->displayName);
        $this->assertSame($photoUrl, $user->photoUrl);
        $this->assertSame($phoneNumber, $user->phoneNumber);
        $this->assertSame($email, $user->email);
        $this->assertTrue($user->emailVerified);
        $this->assertFalse($user->disabled);

        $this->auth->importUsers(
            [
                ImportUserRecord::new()
                    ->withUid($uid)
                    ->withDisplayName($displayName = 'Some display name')
                    ->withPhotoUrl($photoUrl = 'https://example.org/photo.jpg')
                    ->withPhoneNumber($phoneNumber = '+1234567'.\random_int(1000, 9999))
                    ->withVerifiedEmail($email = $uid.'@example.org')
                    ->markAsEnabled(),
            ]
        );

        $user = $this->auth->getUser($uid);

        $this->assertSame($uid, $user->uid);
        $this->assertSame($displayName, $user->displayName);
        $this->assertSame($photoUrl, $user->photoUrl);
        $this->assertSame($phoneNumber, $user->phoneNumber);
        $this->assertSame($email, $user->email);
        $this->assertTrue($user->emailVerified);
        $this->assertFalse($user->disabled);

        $this->auth->deleteUser($user->uid);
    }
}
