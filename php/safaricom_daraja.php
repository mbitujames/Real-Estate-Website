<?php
class Safaricom_Daraja {
    private $consumerKey;
    private $consumerSecret;
    private $baseUrl;

    public function __construct($consumerKey, $consumerSecret) {
        $this->consumerKey = $consumerKey;
        $this->consumerSecret = $consumerSecret;
        $this->baseUrl = "https://sandbox.safaricom.co.ke"; // Use "https://api.safaricom.co.ke" for production
    }

    private function getAccessToken() {
        $url = $this->baseUrl . "/oauth/v1/generate?grant_type=client_credentials";
        $credentials = base64_encode($this->consumerKey . ":" . $this->consumerSecret);
        
        $headers = [
            "Authorization: Basic " . $credentials,
            "Content-Type: application/json"
        ];

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        
        $response = curl_exec($curl);
        curl_close($curl);
        
        $result = json_decode($response, true);
        return $result['access_token'];
    }

    public function stkPush($data) {
        $url = $this->baseUrl . "/mpesa/stkpush/v1/processrequest";
        $accessToken = $this->getAccessToken();

        $headers = [
            "Authorization: Bearer " . $accessToken,
            "Content-Type: application/json"
        ];

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
        
        $response = curl_exec($curl);
        curl_close($curl);
        
        return json_decode($response, true);
    }
}
?>
