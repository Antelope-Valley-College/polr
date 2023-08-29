<?php

namespace App\servecs\sms;


interface SmsInterface
{
    /**
     * @param mixed $number
     * @param string $text
     * @param $date
     * @param $type
     * @param $localid
     * @return mixed
     */
    public function send(mixed $number, string $text , $date=null , $type =null , $localid =null ): mixed;
}
