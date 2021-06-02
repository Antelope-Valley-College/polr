<?php
namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;

use OneLogin\Saml2\Auth;
use OneLogin\Saml2\Utils;
use OneLogin\Saml2\Error;

use App\Helpers\UserHelper;

use App\Factories\UserFactory;
use App\Factories\SamlFactory;

class SamlController extends Controller {
    /**
     * Show pages related to SAML processing.
     *
     * @return Response
     */


    public function processLogout(Request $request) {
        $requestID = $request->session()->get('LogoutRequestID', null);

        $auth = new Auth(SamlFactory::GetSettings());
        $auth->processSLO(false, $requestID);
        $errors = $auth->getErrors();

        if (!empty($errors)) {
            if ($auth->getSettings()->isDebugActive()) {
                $errors[] = $auth->getLastErrorReason();
            }
            return redirect()->route('index')->with('error', implode(', ', $errors));
        }

        $request->session()->forget('username');
        $request->session()->forget('role');
        return redirect()->route('index')->with('success', 'Sucessfully logged out');
    }

    public function processLogin(Request $request) {
        $auth = new Auth(SamlFactory::GetSettings());
        $auth->processResponse();

        $errors = $auth->getErrors();
        if (!empty($errors)) {
            if ($auth->getSettings()->isDebugActive()) {
                $errors[] = $auth->getLastErrorReason();
            }
            return abort(500, 'Error with login: ' . implode(', ', $errors));
        }

        if (!$auth->isAuthenticated()) {
            return abort(500, 'Unable to authenticate.');            
        }
        
        $attributes = $auth->getAttributes();
        $sessionIndex = $auth->getSessionIndex();

        if (env('SAML_USER_ATTR') && env('SAML_USER_REGEX')) {
            $roleAttr = $attributes[env('SAML_USER_ATTR')][0];
            if (!preg_match(env('SAML_USER_REGEX'), $roleAttr)) {
                // This user may not login.
                return redirect('login')->with('error', 'Sorry, but that account is not allowed to use this service.');
            }
        }

        $nameid = $auth->getNameId();
        $username = $attributes['username'][0];
        $email = $attributes['email'][0];

        $role = UserHelper::$USER_ROLES['default'];
        if (env('SAML_ADMIN_ATTR') && env('SAML_ADMIN_REGEX')) {
            $roleAttr = $attributes[env('SAML_ADMIN_ATTR')][0];
            if (preg_match(env('SAML_ADMIN_REGEX'), $roleAttr)) {
                $role = UserHelper::$USER_ROLES['admin'];
            }
        }

        // Check if this user exists.
        $user = UserHelper::getSamlUser($nameid);
        if (!empty($user)) {
            // Ensure that the username and email match what was sent.
            if ($user->username != $username || $user->email != $email || $user->role != $role) {
                $user->username = $username;
                $user->email = $email;
                if (env('SAML_ADMIN_ATTR') && env('SAML_ADMIN_REGEX')) {
                    $user->role = $role;
                }
                $user->save();
            }
        }
        else {
            // If the user already has an account, convert it to a SAML account.
            if (UserHelper::userExists($username)){
                $user = UserHelper::convertToSamlUser($username, $nameid);
            }
            else {
                // Create an account for this user, based on nameid.
                $ip = $request->ip();

                $api_active = false;
                $api_key = null;

                if (env('SETTING_AUTO_API')) {
                    // if automatic API key assignment is on
                    $api_active = 1;
                    $api_key = CryptoHelper::generateRandomHex(env('_API_KEY_LENGTH'));
                }

                $user = UserFactory::createSamlUser($username, $email, $nameid, $ip, $api_key, $api_active, $role);
            }
        }

        $request->session()->put('username', $username);
        $request->session()->put('role', $role);
        $request->session()->put('saml', true);

        return redirect()->route('index');
    }

    public function sendMetadata(Request $request) {
        try {
            $auth = new Auth(SamlFactory::GetSettings());
            $settings = $auth->getSettings();
            $metadata = $settings->getSPMetadata();
            $errors = $settings->validateMetadata($metadata);
            if (empty($errors)) {
                header('Content-Type: text/xml');
                echo $metadata;
                exit;
            } else {
                throw new Error(
                    'Invalid SP metadata: '.implode(', ', $errors),
                    Error::METADATA_SP_INVALID
                );
            }
        } catch (Exception $e) {
            echo $e->getMessage();
            exit;
        }
    }
}
