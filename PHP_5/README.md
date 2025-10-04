Kasa Controller (PHP 5.4 Compatible) and PHP 8+

Adapt the code to your needs

This is a generic PHP controller (no framework required) to control TP-Link Kasa smart plugs through the Kasa Cloud API.
It works with PHP 5.4 or later and uses cURL to communicate with the Kasa cloud service.

✨ Features

Authenticate with Kasa Cloud and retrieve a token.

Turn a smart plug ON or OFF.

List all devices linked to your Kasa account.

Redirects automatically to listDevices if no deviceId is set.

📂 File Structure
kasa-controller.php   # Main controller

⚙️ Configuration

Edit the top of the KasaController class:
```php
private $email = "your-kasa-email";
private $password = "your-kasa-password";
private $url = "https://use1-wap.tplinkcloud.com"; 
private $deviceId = null; // Leave null to always list devices first
```
email / password → your TP-Link Kasa account credentials

url → depends on your Kasa region (common: use1-wap, eu-wap)
🌐 Common TP-Link Kasa Cloud API Endpoints

North America (USA, Canada, etc.):
https://use1-wap.tplinkcloud.com

Europe (EU):
https://euw1-wap.tplinkcloud.com

Asia-Pacific (APAC):
https://aps1-wap.tplinkcloud.com

Global (Fallback):
https://wap.tplinkcloud.com

deviceId → optional. If not set, you will be redirected to listDevices.

🚀 Usage

The script includes a simple router at the bottom.
Call the controller through a browser or CLI:

List devices
http://yourserver/kasa-controller.php?action=listDevices

Turn ON device
http://yourserver/kasa-controller.php?action=turnOn

Turn OFF device
http://yourserver/kasa-controller.php?action=turnOff

📦 Example JSON Responses
List devices
```
{
  "error_code": 0,
  "result": {
    "deviceList": [
      {
        "deviceId": "8006A1B2345678",
        "alias": "Living Room Plug",
        "status": 1
      }
    ]
  }
}
```
Turn ON
```
{
  "message": "Device turned ON successfully",
  "datetime": "2025-10-04 12:30:00",
  "result": { "error_code": 0 }
}
```
Turn OFF
```
{
  "message": "Device turned OFF successfully",
  "datetime": "2025-10-04 12:31:00",
  "result": { "error_code": 0 }
}
```
🔒 Security Notes

The script disables SSL verification for compatibility with PHP 5.4 and old cURL certificates.
⚠️ This is not recommended for production. If possible, enable SSL verification.

Store your Kasa credentials securely (e.g. in environment variables, not in the source code).

🛠 Requirements

PHP 5.4+

cURL enabled in PHP (php_curl extension)

TP-Link Kasa Cloud account

📖 License

This project is free to use and modify.
