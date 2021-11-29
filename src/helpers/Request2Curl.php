<?php declare(strict_types=1);

namespace hiqdev\hiart\helpers;

use hiqdev\hiart\RequestInterface;

final class Request2Curl
{
    public const DOUBLE_QOUTE = '"';

    private string $method;

    private string $url;

    private string $body;

    private array $headers;

    private $guessedContentType;

    // not the full list, just special cases
    public const CONTENT_TYPE_FORM_DATA = 'multipart/form-data';

    public const CONTENT_TYPE_FORM_URL_ENCODED = 'x-www-form-urlencoded';

    public const CONTENT_TYPE_UNKNOWN = 'unknown';

    public function __construct(RequestInterface $request)
    {
        $this->method = $request->getMethod();
        $this->url = $request->getFullUri();
        $this->headers = $request->getHeaders();
        $this->body = $request->getBody();

        $this->guessedContentType = $this->guessContentTypeFromHeaders();
        $this->removeContentLengthFromHeaders();
    }

    private function removeContentLengthFromHeaders(): void
    {
        $targetKey = 'content-length';
        $arrayFilterClosure = static fn($key) => !(strtolower($key) === $targetKey);
        $this->headers = array_filter($this->headers, $arrayFilterClosure, ARRAY_FILTER_USE_KEY);
    }

    private function guessContentTypeFromHeaders(): string
    {
        foreach ($this->getHeadersArray() as $header => $value) {

            if (strtolower($header) === 'content-type') {

                if (stripos($value, 'multipart/form-data') !== false) {
                    return self::CONTENT_TYPE_FORM_DATA;
                }

                if (stripos($value, 'www-form-urlencoded') !== false) {
                    return self::CONTENT_TYPE_FORM_URL_ENCODED;
                }

                return self::CONTENT_TYPE_UNKNOWN;
            }
        }

        return self::CONTENT_TYPE_UNKNOWN;
    }

    public function __toString(): string
    {
        return "curl --insecure "
            . '-X ' . $this->getMethod()
            . ' ' . self::DOUBLE_QOUTE . $this->getFullURLPart() . self::DOUBLE_QOUTE
            . $this->getHeadersPart()
            . $this->getRequestBodyPart();
    }

    private function getMethod(): string
    {
        return $this->method;
    }

    private function getFullURLPart(): string
    {
        return $this->url;
    }

    private function getHeadersPart(): string
    {
        $result = '';

        foreach ($this->getHeadersArray() as $key => $value) {

            $result .= " -H '$key: $value'";
        }

        return $result;
    }

    private function getRequestBodyPart(): string
    {
        switch ($this->getMethod()) {
            case 'POST':
            case 'PUT':
            case 'PATCH':
            case 'DELETE':
                switch ($this->guessedContentType) {
                    case self::CONTENT_TYPE_FORM_DATA:
                        return " --form '$this->body'";
                    case self::CONTENT_TYPE_FORM_URL_ENCODED:
                        $data = $this->body;

                        return " --data '$data'";
                }
                break;
            case 'OPTIONS':
                return '';
                break;
        }

        return '';
    }

    private function getHeadersArray(): array
    {
        return $this->headers;
    }
}
