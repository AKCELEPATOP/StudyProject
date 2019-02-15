<?php
/**
 * Created by PhpStorm.
 * User: Sasha
 * Date: 14.02.2019
 * Time: 16:22
 */

namespace App\Service;

use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\GuzzleException;


class HttpService
{

    private $client;

    public function __construct()
    {
        $this->client = new HttpClient();
    }

    public function sendGuzzleRequest(string $url, string $method, string $body)
    {
        $options = [];
        $message = '';
        $this->initMessage($message, $method, $url);

        if ($this->hasBody($method)) {
            $options['body'] = $body;
            $this->appendBodyToMessage($message, $body);
        }

        try {
            $this->client->request($method, $url, $options);
        } catch (GuzzleException $e) {
        }
        return $message;
    }


    private function hasBody(string $method)
    {
        return $method === "POST" || $method === "PUT";
    }

    private function initMessage(string &$message, string $method, string $url)
    {
        $message = 'Sent ' . $method . ' request to ' . $url;
    }

    private function appendBodyToMessage(string &$message, string $body)
    {
        $message .= ' with body ' . $body;
    }
}
