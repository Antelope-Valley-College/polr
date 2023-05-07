<?php

namespace App\servecs\sms\adapter;
use GuzzleHttp\Client;
use http\Client\Request;
use Illuminate\Support\Facades\Http;
use torfeh\modules\bases\Http\servecs\sms\SmsInterface;

class Sms implements SmsInterface
{

    protected mixed $sender;
    protected mixed $gateway_url;

    protected mixed $api_key;
    /**
     * This file passes the items to the item variables before calling the settings that you read from the config folder
     * ParsgreenSms constructor.
     */
    public function __construct()
    {
        $this->gateway_url = config('client.parsgreenSms.gateway');
        $this->api_key = config('client.parsgreenSms.api_key');
        $this->sender = config('client.parsgreenSms.sender');
    }

    /**
     * This method is called from the interface file and is responsible for passing the information given to it in the control
     * @param array $number
     * @param string $text
     * @return array|\Illuminate\Contracts\Foundation\Application|null
     */
    public function send(array $number, string $text)
    {
        return $this->_send($number, $this->sender, $text);
    }

    /**
     * * This method is responsible for receiving this information, pass it to the sending method, and also checks
     * if the given value is passed to the executable method in the form of a presentation template.
     * @param $receptor
     * @param $sender
     * @param $message
     * @return null
     */
    private function _send($receptor, $sender, $message)
    {
        ///The condition checks to see if there is a presentation
        return $this->execute($this->get_path(), ["SmsBody" => $message, "Mobiles" => $receptor, "SmsNumber" => $sender]);
    }

    private function execute(string $url, array $body)
    {
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => 'basic apikey:7C4C9C5C-960B-4F4A-8862-B8DF6A7DFA2E'
        ])->post($url, [
            "SmsBody" => $body['SmsBody'],
            "Mobiles" => [$body['Mobiles']],
            "SmsNumber" => $body['Mobiles']
        ]);
        return $response->successful();
    }

    /**
     * @param string $method
     * @param string $type
     * @return string
     */
    private function get_path(string $method = 'Message', string $type = 'SendSms')
    {
        return sprintf($this->gateway_url, $method, $type);
    }

}

