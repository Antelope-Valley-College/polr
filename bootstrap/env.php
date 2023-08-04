<?php

use Dotenv\Dotenv;

    if (!isset($_SERVER['HTTP_HOST'])) {
        $dotenv = Dotenv::createMutable(dirname(__DIR__).'/envs/', '.env');
        $dotenv->load();
    }

    // STARTCUSTOMLOGIC
    // This logic can be used to have a different .env file for each subdomain on the main domain
    $pos = mb_strpos($_SERVER['HTTP_HOST'], '.');

    $prefix = '';

    if ($pos) {
        $prefix = mb_substr($_SERVER['HTTP_HOST'], 0, $pos);
    }

    $file = '.' . $prefix . '.env';
    // ENDCUSTOMLOGIC

    // This can be used to have a different environment file for each domain
    if($_SERVER['HTTP_HOST'] == "www.example1.com" || $_SERVER['HTTP_HOST'] == "example1.com"){
        $file = '.example1.env';
    }

    if($_SERVER['HTTP_HOST'] == "www.example2.com" || $_SERVER['HTTP_HOST'] == "example2.com"){
        $file = '.example2.env';
    }

    // Load the custom env file if it exists, otherwise load the default .env file
    if (!file_exists(dirname(__DIR__).'/envs/' . $file)) {
        $file = '.env';
    }

    $dotenv = Dotenv::createMutable(dirname(__DIR__).'/envs/', $file);
    $dotenv->load();
