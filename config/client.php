<?php
return [

    ///The settings are related to the dandelion messenger
    'parsgreenSms' => [
        'gateway' => env('PARSGREENSMS_GATEWAY', 'http://sms.parsgreen.ir/Apiv2/%s/%s'),
        "api_key" => env('GhASEDAKSMS_APIKEY', '1496B7C0-788D-4594-AC79-6B0CBE2F3E06'),
        "sender" => env('PARSGREENSMS_SENDER', '10003784'), /// 10004004040 , 10003784
    ],
];