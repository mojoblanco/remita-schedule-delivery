<?php

namespace Mojoblanco\ScheduleDelivery\Helpers;

use GuzzleHttp\Client;

class ApiHelper {
    /**
     * @param $url
     * @param null $body
     * @param string $type
     * @return mixed
     */
    public static function makeRequest($url, $body = null, $type = 'POST')
    {
        $headers = ['Content-Type' => 'application/json'];
        $options = ['verify' => false, 'headers' => $headers, 'json' => $body];

        $client = new Client();
        $response = $client->request($type, $url, $options);

        $data = (string) $response->getBody();
        
        return json_decode($data);
    }
}
