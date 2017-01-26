<?php

namespace hiqdev\hiart;

class ResponseDecodingException extends ResponseErrorException
{
    /**
     * @return mixed
     */
    public function getName()
    {
        return 'Failed to decode the response';
    }

    /**
     * {@inheritdoc}
     */
    protected function getErrorInfo()
    {
        $request = $this->getRequest();
        $response = $this->getResponse();

        return [
            'statusCode' => $response->getStatusCode(),
            'responseData' => $response->getRawData(),
            'request' => [
                'method' => $request->getMethod(),
                'uri' => $request->getFullUri(),
                'body' => $request->getBody(),
            ],
        ];
    }
}
