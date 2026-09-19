<?php

class ApiClient {
    private $baseUrl;
    private $timeout;

    public function __construct($baseUrl = API_BASE_URL, $timeout = API_TIMEOUT) {
        $this->baseUrl = rtrim($baseUrl, '/');
        $this->timeout = $timeout;
    }

    public function get($endpoint, $params = []) {
        $url = $this->baseUrl . '/' . ltrim($endpoint, '/');
        if (!empty($params)) {
            $url .= '?' . http_build_query($params);
        }
        return $this->request('GET', $url);
    }

    public function post($endpoint, $data = []) {
        $url = $this->baseUrl . '/' . ltrim($endpoint, '/');
        return $this->request('POST', $url, $data);
    }

    public function put($endpoint, $data = []) {
        $url = $this->baseUrl . '/' . ltrim($endpoint, '/');
        return $this->request('PUT', $url, $data);
    }

    public function delete($endpoint) {
        $url = $this->baseUrl . '/' . ltrim($endpoint, '/');
        return $this->request('DELETE', $url);
    }

    private function request($method, $url, $data = null) {
        $ch = curl_init();
        
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);

        if ($data !== null) {
            $jsonData = json_encode($data);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Content-Length: ' . strlen($jsonData)
            ]);
        }

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            return [
                'success' => false,
                'error' => $error,
                'http_code' => $httpCode
            ];
        }

        return [
            'success' => $httpCode >= 200 && $httpCode < 300,
            'http_code' => $httpCode,
            'data' => json_decode($response, true)
        ];
    }
}