<?php


use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Message\ResponseInterface;
use http\Client\Request;
use Mollie\Api\Exceptions\ApiException;
use Mollie\Api\HttpAdapter\MollieHttpAdapterInterface;

class Guzzle5MollieHttpAdapter implements MollieHttpAdapterInterface
{
    /**
     * Default response timeout (in seconds).
     */
    const DEFAULT_TIMEOUT = 10;

    /**
     * Default connect timeout (in seconds).
     */
    const DEFAULT_CONNECT_TIMEOUT = 2;

    /**
     * HTTP status code for an empty ok response.
     */
    const HTTP_NO_CONTENT = 204;

    /**
     * @var \GuzzleHttp\ClientInterface
     */
    protected $httpClient;

    public function send($httpMethod, $url, $headers, $httpBody)
    {
        $request = new Request($httpMethod, $url, $headers, $httpBody);

        try {
            $response = $this->httpClient->send($request);
        } catch (RequestException $e) {

            // Not all Guzzle Exceptions implement hasResponse() / getResponse()
            if (method_exists($e, 'hasResponse') && method_exists($e, 'getResponse')) {
                if ($e->hasResponse()) {
                    throw ApiException::createFromResponse($e->getResponse(), $request);
                }
            }

            throw new ApiException($e->getMessage(), $e->getCode(), null, $request, null);
        }

        if (! $response) {
            throw new ApiException("Did not receive API response.", 0, null, $request);
        }

        return $this->parseResponseBody($response);
    }


    /**
     * Parse the PSR-7 Response body
     *
     * @param ResponseInterface $response
     * @return \stdClass|null
     * @throws ApiException
     */
    private function parseResponseBody(ResponseInterface $response)
    {
        $body = (string) $response->getBody();
        if (empty($body)) {
            if ($response->getStatusCode() === self::HTTP_NO_CONTENT) {
                return null;
            }

            throw new ApiException("No response body found.");
        }

        $object = @json_decode($body);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new ApiException("Unable to decode Mollie response: '{$body}'.");
        }

        if ($response->getStatusCode() >= 400) {
            throw ApiException::createFromResponse($response, null);
        }

        return $object;
    }

    public function versionString()
    {
        return "Guzzle/" . ClientInterface::VERSION;
    }
}
