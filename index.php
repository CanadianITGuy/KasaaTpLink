<?php
/**
 * Generic Kasa Controller (PHP 5.4 compatible)
 * Works without any specific framework
 */
class KasaController
{
    // === CONFIG ===
    private $email = "xxx";
    private $password = "xxx!";
    private $url = "https://use1-wap.tplinkcloud.com";
    private $deviceId = null; // If null, will redirect to listDevices()

    /**
     * Call Kasa API
     * @param array $payload
     * @param string|null $token
     * @return array|null
     */
    private function kasaApi($payload, $token = null)
    {
        $ch = curl_init();
        $headers = array('Content-Type: application/json');

        $fullUrl = $this->url;
        if ($token) {
            $fullUrl .= "?token=" . $token;
        }

        curl_setopt($ch, CURLOPT_URL, $fullUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));

        // Disable SSL verification (needed for older PHP with outdated certs)
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response, true);
    }

    /**
     * Get authentication token from Kasa cloud
     * @return string|null
     */
    private function getToken()
    {
        $loginPayload = array(
            "method" => "login",
            "params" => array(
                "appType" => "Kasa_Android",
                "cloudUserName" => $this->email,
                "cloudPassword" => $this->password,
                "terminalUUID" => uniqid('', true)
            )
        );
        $login = $this->kasaApi($loginPayload);
        return isset($login["result"]["token"]) ? $login["result"]["token"] : null;
    }

    /**
     * Turn the device ON
     */
    public function turnOn()
    {
        if (empty($this->deviceId)) {
            return $this->listDevices();
        }

        $token = $this->getToken();
        if (!$token) {
            return $this->jsonResponse(array("error" => "Unable to get token"));
        }

        $onPayload = array(
            "method" => "passthrough",
            "params" => array(
                "deviceId" => $this->deviceId,
                "requestData" => json_encode(array(
                    "system" => array("set_relay_state" => array("state" => 1))
                ))
            )
        );

        $action = $this->kasaApi($onPayload, $token);

        return $this->jsonResponse(array(
            "message" => "Device turned ON successfully",
            "datetime" => date("Y-m-d H:i:s"),
            "result" => $action
        ));
    }

    /**
     * Turn the device OFF
     */
    public function turnOff()
    {
        if (empty($this->deviceId)) {
            return $this->listDevices();
        }

        $token = $this->getToken();
        if (!$token) {
            return $this->jsonResponse(array("error" => "Unable to get token"));
        }

        $offPayload = array(
            "method" => "passthrough",
            "params" => array(
                "deviceId" => $this->deviceId,
                "requestData" => json_encode(array(
                    "system" => array("set_relay_state" => array("state" => 0))
                ))
            )
        );

        $action = $this->kasaApi($offPayload, $token);

        return $this->jsonResponse(array(
            "message" => "Device turned OFF successfully",
            "datetime" => date("Y-m-d H:i:s"),
            "result" => $action
        ));
    }

    /**
     * List all devices linked to the Kasa account
     */
    public function listDevices()
    {
        $token = $this->getToken();
        if (!$token) {
            return $this->jsonResponse(array("error" => "Unable to get token"));
        }

        $listPayload = array("method" => "getDeviceList");
        $list = $this->kasaApi($listPayload, $token);

        return $this->jsonResponse($list);
    }

    /**
     * Utility method to send JSON response
     * @param array $data
     */
    private function jsonResponse($data)
    {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}

// Example of usage (like a router)
$action = isset($_GET['action']) ? $_GET['action'] : 'listDevices';
$controller = new KasaController();

if (method_exists($controller, $action)) {
    $controller->$action();
} else {
    $controller->listDevices();
}
