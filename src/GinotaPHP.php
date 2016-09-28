<?php

namespace instantjay\ginotaphp;

class GinotaPHP {
    private $apiKey;
    private $secret;
    private $endpoint;
    private $timeout;

    private $guzzleClient;

    const CONTENT_MAX_LENGTH = 1500;

    /**
     * GinotaPHP constructor.
     * @param $apiKey string Your API key provided by Ginota
     * @param $secret string Your API secret provided by Ginota
     * @param string $endpoint The URL to Ginota's API endpoint
     * @param int $timeout Timeout in seconds before our client should give up.
     */
    public function __construct($apiKey, $secret, $endpoint = 'https://www.ginota.com/gemp/sms/json', $timeout = 4) {
        //
        $this->apiKey = $apiKey;
        $this->secret = $secret;
        $this->endpoint = $endpoint;
        $this->timeout = $timeout;

        //
        $this->guzzleClient = new \GuzzleHttp\Client([
            'timeout' => $this->timeout
        ]);
    }

    /**
     * @param $content string Your message
     * @param $destinationAddress string Phone number including country/area codes.
     * @param $sourceAddress string Your sender ID. Can be anything.
     * @return int Returns API call's status code. 0 on success, anything else means a failure.
     * @throws \Exception Thrown if sending fails, with an explanation in the Exception's message field.
     */
    public function send($content, $destinationAddress, $sourceAddress) {
        if(strlen($content) > self::CONTENT_MAX_LENGTH)
            throw new \Exception('Content is too long. Content must not be longer than '.self::CONTENT_MAX_LENGTH.' characters.');

        $payload = [
            'apiKey' => $this->apiKey,
            'apiSecret' => $this->secret,
            'srcAddr' => $sourceAddress,
            'dstAddr' => $destinationAddress,
            'content' => $content
        ];

        $response = $this->guzzleClient->request('POST', $this->endpoint, ['form_params' => $payload]);

        if($response->getStatusCode() != 200)
            throw new \Exception('Connection to third-party SMS provider failed: HTTP '.$response->getStatusCode());

        $json = $response->getBody();
        $data = json_decode($json, JSON_NUMERIC_CHECK);

        return $data['status'];
    }
}