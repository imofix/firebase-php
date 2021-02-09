<?php

declare(strict_types=1);

namespace Kreait\Firebase\Auth;

use DateTimeImmutable;
use Kreait\Firebase\Exception\InvalidArgumentException;
use Kreait\Firebase\Util\JSON;
use Kreait\Firebase\Value\Email;
use Kreait\Firebase\Value\PhoneNumber;
use Kreait\Firebase\Value\Uid;
use Kreait\Firebase\Value\Url;

class ImportUserRecord implements \JsonSerializable
{
    /** @var Uid|null */
    private $uid;

    /** @var Email|null */
    private $email;

    /** @var bool|null */
    private $emailVerified;

    /** @var string|null */
    private $displayName;

    /** @var Url|null */
    private $photoUrl;

    /** @var PhoneNumber|null */
    private $phoneNumber;

    /** @var array<string, mixed> */
    private $customClaims = [];

    /** @var DateTimeImmutable|null */
    private $tokensValidAfterTime;

    /** @var bool|null */
    private $markAsEnabled;

    /** @var bool|null */
    private $markAsDisabled;

    /** @var UserInfo[] */
    private $providers = [];

    private function __construct()
    {
    }

    public static function new(): self
    {
        return new self();
    }

    /**
     * @return static
     */
    public function withUid(Uid $uid): self
    {
        $request = clone $this;
        $request->uid = $uid;

        return $request;
    }

    /**
     * @return static
     */
    public function withEmail(Email $email): self
    {
        $request = clone $this;
        $request->email = $email;

        return $request;
    }

    public function withVerifiedEmail(Email $email): self
    {
        $request = clone $this;
        $request->email = $email->__toString();
        $request->emailVerified = true;

        return $request;
    }

    public function withUnverifiedEmail(Email $email): self
    {
        $request = clone $this;
        $request->email = $email->__toString();
        $request->emailVerified = false;

        return $request;
    }

    public function withDisplayName(string $displayName): self
    {
        $request = clone $this;
        $request->displayName = $displayName;

        return $request;
    }

    public function withPhotoUrl(Url $url): self
    {
        $request = clone $this;
        $request->photoUrl = $url->__toString();

        return $request;
    }

    public function withPhoneNumber(PhoneNumber $phoneNumber): self
    {
        $request = clone $this;
        $request->phoneNumber = $phoneNumber->__toString();

        return $request;
    }

    /**
     * @param array<string, mixed> $claims
     */
    public function withCustomClaims(array $claims): self
    {
        $request = clone $this;
        $request->customClaims = $claims;

        return $request;
    }

    public function markTokensValidAfter(DateTimeImmutable $after): self
    {
        $request = clone $this;
        $request->tokensValidAfterTime = $after;

        return $request;
    }

    /**
     * @return static
     */
    public function markAsDisabled(): self
    {
        $request = clone $this;
        $request->markAsEnabled = null;
        $request->markAsDisabled = true;

        return $request;
    }

    /**
     * @return static
     */
    public function markAsEnabled(): self
    {
        $request = clone $this;
        $request->markAsDisabled = null;
        $request->markAsEnabled = true;

        return $request;
    }

    /**
     * @param array<UserInfo> $providers
     *
     * @return $this
     */
    public function withProviders(array $providers): self
    {
        $request = clone $this;
        $request->providers = $providers;

        return $request;
    }

    public function jsonSerialize(): array
    {
        if ($this->uid === null) {
            throw new InvalidArgumentException('A uid is required to import user.');
        }

        $disableUser = null;

        if ($this->markAsDisabled) {
            $disableUser = true;
        } elseif ($this->markAsEnabled) {
            $disableUser = false;
        }

        $customClaims = \count($this->customClaims) > 0 ? JSON::encode($this->customClaims) : null;
        $tokensValidAfterTime = $this->tokensValidAfterTime !== null
            ? $this->tokensValidAfterTime->format(\DATE_ATOM)
            : null;

        $record = [
            'localId' => $this->uid,
            'email' => $this->email,
            'emailVerified' => $this->emailVerified,
            'displayName' => $this->displayName,
            'disableUser' => $disableUser,
            'phoneNumber' => $this->phoneNumber,
            'photoUrl' => $this->photoUrl,
            'customClaims' => $customClaims,
            'validSince' => $tokensValidAfterTime,
        ];

        if (\count($this->providers) > 0) {
            foreach ($this->providers as $providerData) {
                $record['providerUserInfo'][] = $providerData->jsonSerialize();
            }
        }

        return \array_filter(
            $record,
            static function ($value) {
                return $value !== null;
            }
        );
    }
}
