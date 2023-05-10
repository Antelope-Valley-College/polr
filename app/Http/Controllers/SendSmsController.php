<?php

namespace App\Http\Controllers;


use App\servecs\sms\SmsInterface;
use Illuminate\Http\Request;

class SendSmsController extends Controller
{
    protected $smss;

    public function __construct(SmsInterface $sms)
    {
        $this->smss = $sms;
    }

    public function send(Request $request)
    {
        if (!$this->isLoggedIn()) {
            return redirect(route('login'))->with('error', 'Please login to access your dashboard.');
        }

        $validator = \Validator::make($request->all(), [
            'phoneNumber' => 'required',
            'text' => 'required'

        ]);
        if ($validator->fails())
            return response()->json(['status' => false, 'error' => $validator->errors()->all()], 401);

          $this->smss->send($request->phoneNumber, $request->text);

        return response()->json(['status' => true, 'messages' => 'SMS sent successfully'], 200);
    }
}