<?php

namespace App\servecs\sms;

abstract class SmsAbstract
{
    /**
     * @param $url
     * @param $data
     * @return mixed
     */
    protected abstract function execute($url, $data = null);

    /**
     * @param $method
     * @param $base
     * @return mixed
     */
    protected abstract function get_path($method, $base = 'sms');

    /**
     * @param $receptor
     * @param $token
     * @param $template
     * @param $type
     * @return mixed
     */
    protected abstract function VerifyLookup($receptor, $token, $template, $type = "sms");


}
