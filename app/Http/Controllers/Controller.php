<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    
    protected static function currIsAdmin() {
        $role = session('role');
        if ($role == 'admin') {
            return true;
        }
        else {
            return false;
        }
    }

    protected static function isLoggedIn() {
        $username = session('username');
        if (!isset($username)) {
            return false;
        }
        else {
            return true;
        }
    }

    protected static function checkRequiredArgs($required_args=[]) {
        foreach($required_args as $arg) {
            if ($arg == NULL) {
                return false;
            }
        }
        return true;
    }

    protected static function ensureAdmin() {
        if (!self::currIsAdmin()) {
            abort(401, 'User not admin.');
        }
        return true;
    }

    protected static function ensureLoggedIn() {
        if (!self::isLoggedIn()) {
            abort (401, 'User must be authenticated.');
        }
        return true;
    }
}
