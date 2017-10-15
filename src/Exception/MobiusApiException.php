<?php


namespace ZuluCrypto\MobiusApi\Exception;


use Psr\Http\Message\ResponseInterface;

class MobiusApiException extends \ErrorException
{
    /**
     * Mobius-specific error type available in 'type'
     * @var string
     */
    protected $mobiusType;

    /**
     * Details about the error, available in 'message'
     * @var string
     */
    protected $mobiusDetails;

    /**
     * The URL that was requested
     *
     * @var string
     */
    protected $url;

    /**
     * @var array
     */
    protected $decoded;

    /**
     * @param ResponseInterface $response
     * @param string $requestedUrl The URL that this response is for
     * @return MobiusApiException
     */
    public static function fromResponse(ResponseInterface $response, $requestedUrl)
    {
        $body = $response->getBody();
        $decodedResponse = json_decode($body, true);
        $errorDetails = [
            'type' => 'unknown',
            'message' => $body,
        ];

        // Usually there's more data in the response body
        if (isset($decodedResponse['error'])) {
            if (isset($decodedResponse['error']['type'])) $errorDetails['type'] = $decodedResponse['error']['type'];
            if (isset($decodedResponse['error']['message'])) $errorDetails['message'] = $decodedResponse['error']['message'];
        }

        // Build exception and set internal properties
        $exception = new MobiusApiException(static::messageFromHttpStatusCode($response->getStatusCode()));
        $exception->decoded = $decodedResponse;
        $exception->mobiusType = $errorDetails['type'];
        $exception->mobiusDetails = $errorDetails['message'];
        $exception->url = $requestedUrl;

        return $exception;
    }

    /**
     * Maps HTTP status codes to more useful error descriptions
     *
     * @param $httpStatusCode
     * @return string
     */
    public static function messageFromHttpStatusCode($httpStatusCode)
    {
        $message = sprintf('HTTP %s', $httpStatusCode);
        switch ($httpStatusCode) {
            case 400:
                $message .= ' Bad Request (invalid parameters for the API endpoint)';
                break;
            case 401:
                $message .= ' Unauthorized (check that your API key is correct)';
                break;
            case 403:
                $message .= ' Forbidden (you do not have access to this API endpoint)';
                break;
            case 404:
                $message .= ' API Endpoint not found or parameters were incorrect. Check method and URL';
                break;
            case 429:
                $message .= ' Rate limit hit, decrease request speed';
                break;
            case 500:
                $message .= ' Server error (Mobius error)';
                break;
            case 503:
                $message .= ' Server temporarily offline for maintenance';
                break;
        }

        return $message;
    }

    /**
     * @return string
     */
    public function getMobiusType()
    {
        return $this->mobiusType;
    }

    /**
     * @return string
     */
    public function getMobiusDetails()
    {
        return $this->mobiusDetails;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * The json body as a decoded PHP array
     *
     * @return array
     */
    public function getDecodedBody()
    {
        return $this->decoded;
    }
}