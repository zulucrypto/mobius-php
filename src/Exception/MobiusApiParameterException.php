<?php


namespace ZuluCrypto\MobiusApi\Exception;

use Psr\Http\Message\ResponseInterface;

class MobiusApiParameterException extends MobiusApiException
{
    /**
     * @param ResponseInterface $response
     * @param string $requestedUrl The URL that this response is for
     * @return MobiusApiException
     */
    public static function fromResponse(ResponseInterface $response, $requestedUrl)
    {
        $exception = parent::fromResponse($response, $requestedUrl);
        $decoded = $exception->getDecodedBody();

        // Build a better message
        $exception->message = sprintf('Parameter "%s" is invalid (%s)',
            $decoded['error']['param'],
            $decoded['error']['message']
        );

        return $exception;
    }
}