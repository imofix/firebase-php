<?php

declare(strict_types=1);

namespace Kreait\Firebase\Auth;

use Beste\Json;
use DateTimeImmutable;
use DateTimeInterface;
use GuzzleHttp\Psr7\Uri;
use JsonSerializable;
use Kreait\Firebase\Exception\InvalidArgumentException;
use Kreait\Firebase\Value\Email;
use Kreait\Firebase\Value\Uid;
use Kreait\Firebase\Value\Url;

use function array_filter;
use function count;

class ImportUserRecord implements JsonSerializable
{
    private ?Uid $uid = null;
    private ?Email $email = null;
    private ?bool $emailVerified = null;
    private ?string $displayName = null;
    private ?Url $photoUrl = null;
    private ?string $phoneNumber = null;

    /** @var array<string, mixed> */
    private array $customClaims = [];

    private ?DateTimeImmutable $tokensValidAfterTime = null;

    private ?bool $markAsEnabled = null;
    private ?bool $markAsDisabled = null;

    /** @var list<UserInfo> */
    private array $providers = [];

    private function __construct()
    {
    }

    public static function new(): self
    {
        return new self();
    }

    public function withUid(string $uid): self
    {
        $request = clone $this;
        $request->uid = Uid::fromString($uid);

        return $request;
    }

    /**
     * @return static
     */
    public function withEmail(string $email): self
    {
        $request = clone $this;
        $request->email = Email::fromString($email);

        return $request;
    }

    public function withVerifiedEmail(string $email): self
    {
        $request = clone $this;
        $request->email = Email::fromString($email);
        $request->emailVerified = true;

        return $request;
    }

    public function withUnverifiedEmail(string $email): self
    {
        $request = clone $this;
        $request->email = Email::fromString($email);
        $request->emailVerified = false;

        return $request;
    }

    public function withDisplayName(string $displayName): self
    {
        $request = clone $this;
        $request->displayName = $displayName;

        return $request;
    }

    public function withPhotoUrl(string $url): self
    {
        $request = clone $this;
        $request->photoUrl = Url::fromString(new Uri($url));

        return $request;
    }

    public function withPhoneNumber(string $phoneNumber): self
    {
        $request = clone $this;
        $request->phoneNumber = $phoneNumber;

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
     * @param list<UserInfo> $providers
     *
     * @return $this
     */
    public function withProviders(array $providers): self
    {
        $request = clone $this;
        $request->providers = $providers;

        return $request;
    }

    /**
     * @return array<string, mixed>
     */
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

        $customClaims = count($this->customClaims) > 0 ? JSON::encode($this->customClaims) : null;
        $tokensValidAfterTime = $this->tokensValidAfterTime?->format(DateTimeInterface::ATOM);

        $record = [
            'localId' => $this->uid->value,
            'email' => $this->email?->value,
            'emailVerified' => $this->emailVerified,
            'displayName' => $this->displayName,
            'disabled' => $disableUser,
            'phoneNumber' => $this->phoneNumber,
            'photoUrl' => $this->photoUrl?->value,
            'customAttributes' => $customClaims,
            'validSince' => $tokensValidAfterTime,
        ];

        foreach ($this->providers as $providerData) {
            $record['providerUserInfo'][] = [
                'providerId' => $providerData->providerId,
                'displayName' => $providerData->displayName,
                'photoUrl' => $providerData->photoUrl,
                'email' => $providerData->email,
                'rawId' => $providerData->uid,
                'phoneNumber' => $providerData->phoneNumber,
            ];
        }

        return array_filter(
            $record,
            static fn($value): bool => $value !== null,
        );
    }
}
