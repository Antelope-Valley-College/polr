<?php
return [

    ///The settings are related to the dandelion messenger
    'parsgreenSms' => [
        'gateway' => env('PARSGREENSMS_GATEWAY', 'http://sms.parsgreen.ir/Apiv2/%s/%s'),
        "api_key" => env('GhASEDAKSMS_APIKEY', '1496B7C0-788D-4594-AC79-6B0CBE2F3E06'),
        "sender" => env('PARSGREENSMS_SENDER', '10003784'), /// 10004004040 , 10003784
    ],

    'Kavenegar' => [
        'gateway' => env('PARSGREENSMS_GATEWAY', "%s://api.kavenegar.com/v1/%s/%s/%s.json?receptor=%s&sender=%s&message=%s"),
        "api_key" => env('GhASEDAKSMS_APIKEY', '4954352F383964723255736C4D6C36507A3465496A4E6E594D61384D34474F36452B30685635474A577A6F3D'),
        "sender" => env('PARSGREENSMS_SENDER', '200023067'), /// 10004004040 , 10003784
    ],
];