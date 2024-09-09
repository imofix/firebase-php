<?php

declare(strict_types=1);

namespace Kreait\Firebase\Firestore;

use Beste\Json;
use GuzzleHttp\ClientInterface;
use Kreait\Firebase\Exception\FirebaseException;
use Kreait\Firebase\Exception\FirestoreApiExceptionConverter;
use Kreait\Firebase\Exception\FirestoreException;
use Throwable;

/**
 * @internal
 */
class ApiClient
{
    private readonly FirestoreApiExceptionConverter $errorHandler;

    /**
     * @internal
     */
    public function __construct(private readonly ClientInterface $client)
    {
        $this->errorHandler = new FirestoreApiExceptionConverter();
    }

    /**
     * @param array<string, mixed> $options
     *
     * @throws FirebaseException
     * @throws FirestoreException
     */
    public function get(string $path, array $options = []): mixed
    {
        return $this->requestApi('GET', $path, $options);
    }

    /**
     * @param array<string, mixed> $data
     * @param array<string, mixed> $options
     *
     * @throws FirebaseException
     * @throws FirestoreException
     */
    public function patch(string $path, array $data, array $options = []): mixed
    {
        $options['json'] = $data;

        return $this->requestApi('PATCH', $path, $options);
    }

    /**
     * @param array<string, mixed> $options
     *
     * @throws FirestoreException
     * @throws FirebaseException
     */
    private function requestApi(string $method, string $uri, array $options = []): mixed
    {
        try {
            $response = $this->client->request($method, $uri, $options);

            return Json::decode((string) $response->getBody());
        } catch (Throwable $e) {
            throw $this->errorHandler->convertException($e);
        }
    }
}
