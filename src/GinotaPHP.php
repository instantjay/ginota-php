<?php

namespace instantjay\ginotaphp;

class GinotaPHP {
    private $apiKey;
    private $secret;
    private $endpoint;
    private $timeout;

    private $guzzleClient;

    public function __construct($apiKey, $secret, $endpoint = 'https://www.ginota.com/gemp/sms/json', $timeout = 3) {
        //
        $this->apiKey = $apiKey;
        $this->secret = $secret;
        $this->endpoint = $endpoint;
        $this->timeout = $timeout;

        //
        $this->guzzleClient = new \GuzzleHttp\Client([
            'timeout' => 3
        ]);
    }

    public function send($content, $destinationAddress, $sourceAddress) {
        $response = $this->guzzleClient->request('GET', $this->endpoint, [
            'apiKey' => $this->apiKey,
            'apiSecret' => $this->secret,
            'srcAddr' => $sourceAddress,
            'dstAddr' => $destinationAddress,
            'content' => $content
        ]);

        return $response->getStatusCode();
    }
}