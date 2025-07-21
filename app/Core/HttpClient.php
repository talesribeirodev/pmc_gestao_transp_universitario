<?php
namespace App\Core;

class HttpClient {
    private $baseUrl;
    private $timeout;

    public function __construct() {
        $this->baseUrl = Env::get('API_BASE_URL');
        $this->timeout = 10;
    }

    public function get($endpoint) {
        return $this->request('GET', $endpoint);
    }

    public function post($endpoint, $data = []) {
        return $this->request('POST', $endpoint, $data);
    }

    public function put($endpoint, $data = []) {
        return $this->request('PUT', $endpoint, $data);
    }

    public function delete($endpoint) {
        return $this->request('DELETE', $endpoint);
    }

    private function request($method, $endpoint, $data = []) {
        $url = $this->baseUrl . $endpoint;

        $headers = [
            'Content-Type: application/json',
        ];

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => $this->timeout,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_HTTPHEADER => $headers,
        ]);

        if (in_array($method, ['POST', 'PUT']) && !empty($data)) {
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
        }

        $response = curl_exec($curl);
        $error    = curl_error($curl);

        curl_close($curl);

        if ($error) {
            return ['error' => $error];
        }

        return json_decode($response, true);
    }
}
