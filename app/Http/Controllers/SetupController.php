<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Http\Redirect;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;

use App\Helpers\CryptoHelper;
use App\Models\User;
use App\Helpers\UserHelper;
use App\Factories\UserFactory;
use Cache;

class SetupController extends Controller {
    protected static function parseExitCode($exitCode) {
        if ($exitCode == 0) {
            return true;
        }
        else {
            return false;
        }
    }

    private static function setupAlreadyRan() {
        return view('error', [
            'message' => 'Sorry, but you have already completed the setup process.'
        ]);
    }

    private function resetDatabase() {
        $exitCode = Artisan::call('migrate:refresh', [
            '--force' => true,
        ]);
        return self::parseExitCode($exitCode);
    }

    private static function updateGeoIP() {
        // Output GeoIP database for advanced
        // analytics
        $exitCode = Artisan::call('geoip:update', []);
        return self::parseExitCode($exitCode);
    }

    private static function createDatabase() {
        $exitCode = Artisan::call('migrate', [
            '--force' => true,
        ]);
        return self::parseExitCode($exitCode);
    }

    public static function displaySetupPage(Request $request) {
        if (env('POLR_SETUP_RAN')) {
            return self::setupAlreadyRan();
        }

        return view('setup');
    }

    public static function performSetup(Request $request) {
        if (env('POLR_SETUP_RAN')) {
            return self::setupAlreadyRan();
        }

        $app_key = CryptoHelper::generateRandomHex(16);
        $setup_auth_key = CryptoHelper::generateRandomHex(16);

        $app_name = $request->input('app:name');
        $app_protocol = $request->input('app:protocol');

        $app_address = $request->input('app:external_url');
        $app_protocol = $request->input('app:protocol');
        $app_stylesheet = $request->input('app:stylesheet');

        date_default_timezone_set('UTC');
        $date_today = date('F jS, Y');

        $polr_setup_ran = 'true';
        $db_url = $request->input('db:url');
        $db_unix_socket = $db_url ? '' : $request->input('db:unix_socket');
        $db_host = $db_url || $db_unix_socket ? '' : $request->input('db:host');
        $db_port = $db_url || $db_unix_socket ? '' : $request->input('db:port');
        $db_name = $db_url ? '' : $request->input('db:name');
        $db_username = $db_url ? '' : $request->input('db:username');
        $db_password = $db_url ? '' : $request->input('db:password');

        $st_public_interface = $request->input('setting:public_interface');

        $polr_registration_setting = $request->input('setting:registration_permission');

        if ($polr_registration_setting == 'no-verification') {
            $polr_acct_activation = false;
            $polr_allow_acct_creation = true;
        }
        else if ($polr_registration_setting == 'none') {
            $polr_acct_activation = false;
            $polr_allow_acct_creation = false;
        }
        else if ($polr_registration_setting == 'email') {
            $polr_acct_activation = true;
            $polr_allow_acct_creation = true;
        }
        else {
            return view('error', [
                'message' => 'Invalid registration settings'
            ]);
        }

        $polr_acct_creation_recaptcha = $request->input('setting:acct_registration_recaptcha');
        $polr_recaptcha_site_key = $request->input('setting:recaptcha_site_key');
        $polr_recaptcha_secret_key = $request->input('setting:recaptcha_secret_key');

        $maxmind_license_key = $request->input('maxmind:license_key');

        $acct_username = $request->input('acct:username');
        $acct_email = $request->input('acct:email');
        $acct_password = $request->input('acct:password');
        $acct_group = UserHelper::$USER_ROLES['admin'];

        // if true, only logged in users can shorten
        $st_shorten_permission = $request->input('setting:shorten_permission');
        $st_index_redirect = $request->input('setting:index_redirect');
        $st_redirect_404 = $request->input('setting:redirect_404');
        $st_password_recov = $request->input('setting:password_recovery');
        $st_restrict_email_domain = $request->input('setting:restrict_email_domain');
        $st_allowed_email_domains = $request->input('setting:allowed_email_domains');

        $st_base = $request->input('setting:base');
        $st_auto_api_key = $request->input('setting:auto_api_key');
        $st_anon_api = $request->input('setting:anon_api');
        $st_anon_api_quota = $request->input('setting:anon_api_quota');
        $st_pseudor_ending = $request->input('setting:pseudor_ending');
        $st_adv_analytics = $request->input('setting:adv_analytics');

        $mail_host = $request->input('app:smtp_server');
        $mail_port = $request->input('app:smtp_port');
        $mail_username = $request->input('app:smtp_username');
        $mail_password = $request->input('app:smtp_password');
        $mail_from = $request->input('app:smtp_from');
        $mail_from_name = $request->input('app:smtp_from_name');

        if ($mail_host) {
            $mail_enabled = true;
        }
        else {
            $mail_enabled = false;
        }

        $login_modes = array();
        if ($request->input('app:saml_enable')) {
            $login_modes[] = 'SAML';
        }
        $login_modes = implode(',', $login_modes);

        $saml_debug = $request->input('saml:saml_debug') ? 'true' : 'false';
        $saml_primary = $request->input('saml:saml_primary') ? 'true' : 'false';
        $saml_sp_entityid = $request->input('saml:sp_entityid');
        $saml_sp_servicename = $request->input('saml:sp_servicename');
        $saml_sp_desc = $request->input('saml:sp_desc');
        $saml_idp_entityid = $request->input('saml:idp_entityid');
        $saml_idp_name = $request->input('saml:idp_name');
        $saml_idp_x509cert = $request->input('saml:idp_x509cert');
        $saml_idp_sso = $request->input('saml:idp_sso');
        $saml_idp_slo = $request->input('saml:idp_slo');
        $saml_user_attr = $request->input('saml:admin_attr');
        $saml_user_regex = $request->input('saml:admin_regex');
        $saml_admin_attr = $request->input('saml:admin_attr');
        $saml_admin_regex = $request->input('saml:admin_regex');

        $compiled_configuration = view('env', [
            'APP_KEY' => $app_key,
            'APP_NAME' => $app_name,
            'APP_PROTOCOL' => $app_protocol,
            'APP_ADDRESS' => $app_address,
            'APP_STYLESHEET' => $app_stylesheet,
            'POLR_GENERATED_AT' => $date_today,
            'POLR_SETUP_RAN' => $polr_setup_ran,
            'MAXMIND_LICENSE_KEY' => $maxmind_license_key,

            'DB_URL' => $db_url,
            'DB_HOST' => $db_host,
            'DB_PORT' => $db_port,
            'DB_SOCKET' => $db_unix_socket,
            'DB_USERNAME' => $db_username,
            'DB_PASSWORD' => $db_password,
            'DB_DATABASE' => $db_name,

            'ST_PUBLIC_INTERFACE' => $st_public_interface,
            'POLR_ALLOW_ACCT_CREATION' => $polr_allow_acct_creation,
            'POLR_ACCT_ACTIVATION' => $polr_acct_activation,
            'POLR_ACCT_CREATION_RECAPTCHA' => $polr_acct_creation_recaptcha,
            'ST_SHORTEN_PERMISSION' => $st_shorten_permission,
            'ST_INDEX_REDIRECT' => $st_index_redirect,
            'ST_REDIRECT_404' => $st_redirect_404,
            'ST_PASSWORD_RECOV' => $st_password_recov,
            'ST_RESTRICT_EMAIL_DOMAIN' => $st_restrict_email_domain,
            'ST_ALLOWED_EMAIL_DOMAINS' => $st_allowed_email_domains,
            'POLR_RECAPTCHA_SITE_KEY' => $polr_recaptcha_site_key,
            'POLR_RECAPTCHA_SECRET' => $polr_recaptcha_secret_key,

            'MAIL_ENABLED' => $mail_enabled,
            'MAIL_HOST' => $mail_host,
            'MAIL_PORT' => $mail_port,
            'MAIL_USERNAME' => $mail_username,
            'MAIL_PASSWORD' => $mail_password,
            'MAIL_FROM_ADDRESS' => $mail_from,
            'MAIL_FROM_NAME' => $mail_from_name,

            'ST_BASE' => $st_base,
            'ST_AUTO_API' => $st_auto_api_key,
            'ST_ANON_API' => $st_anon_api,
            'ST_ANON_API_QUOTA' => $st_anon_api_quota,
            'ST_PSEUDOR_ENDING' => $st_pseudor_ending,
            'ST_ADV_ANALYTICS' => $st_adv_analytics,

            'POLR_LOGIN_MODES' => $login_modes,
            'SAML_DEBUG' => $saml_debug,
            'SAML_PRIMARY_LOGIN' => $saml_primary,
            'SAML_ENTITYID' => $saml_sp_entityid,
            'SAML_SERVICE_NAME' => $saml_sp_servicename,
            'SAML_SERVICE_DESC' => $saml_sp_desc,
            'SAML_IDP_ENTITYID' => $saml_idp_entityid,
            'SAML_IDP_NAME' => $saml_idp_name,
            'SAML_IDP_X509CERT' => $saml_idp_x509cert,
            'SAML_IDP_SSO_URL' => $saml_idp_sso,
            'SAML_IDP_SLO_URL' => $saml_idp_slo,            
            'SAML_USER_ATTR' => $saml_user_attr,
            'SAML_USER_REGEX' => $saml_user_regex,
            'SAML_ADMIN_ATTR' => $saml_admin_attr,
            'SAML_ADMIN_REGEX' => $saml_admin_regex,
            'TMP_SETUP_AUTH_KEY' => $setup_auth_key
        ])->render();

        $handle = fopen('../.env', 'w');
        if (fwrite($handle, $compiled_configuration) === FALSE) {
            $response = view('error', [
                'message' => 'Could not write configuration to disk.'
            ]);
        } else {
            Cache::flush();

            $setup_finish_arguments = json_encode([
                'acct_username' => $acct_username,
                'acct_email' => $acct_email,
                'acct_password' => $acct_password,
                'setup_auth_key' => $setup_auth_key
            ]);

            $response = redirect(route('setup_finish'));

            // set cookie with information needed for finishSetup, expire in 60 seconds
            // we use PHP's setcookie rather than Laravel's cookie capabilities because
            // our app key changes and Laravel encrypts cookies.
            setcookie('setup_arguments', $setup_finish_arguments, time()+60);
        }

        fclose($handle);
        return $response;

    }

    public static function finishSetup(Request $request) {
        if (!isset($_COOKIE['setup_arguments'])) {
            // Abort if setup arguments are missing.
            abort(404);
        }

        $setup_finish_args_raw = $_COOKIE['setup_arguments'];
        $setup_finish_args = json_decode($setup_finish_args_raw);

        // unset cookie
        setcookie('setup_arguments', '', time()-3600);

        $transaction_authorised = env('TMP_SETUP_AUTH_KEY') === $setup_finish_args->setup_auth_key;

        if ($transaction_authorised != true) {
            abort(403, 'Transaction unauthorised.');
        }

        $usersTableExists = Schema::hasTable('users');

        if ($usersTableExists) {
            // If the users table exists, then the setup process may have already been completed before.
            abort(403, 'Setup has been completed already.');
        }

        $database_created = self::createDatabase();
        if (!$database_created) {
            return redirect(route('setup'))->with('error', 'Could not create database. Perhaps your credentials were incorrect?');
        }

        if (env('SETTING_ADV_ANALYTICS')) {
            $geoip_db_created = self::updateGeoIP();
            if (!$geoip_db_created) {
                return redirect(route('setup'))->with('error', 'Could not fetch GeoIP database for advanced analytics. Perhaps your server is not connected to the internet or your MAXMIND_LICENSE_KEY is incorrect?');
            }
        }

        $user = UserFactory::createUser($setup_finish_args->acct_username, $setup_finish_args->acct_email, $setup_finish_args->acct_password, 1, $request->ip(), false, 0, UserHelper::$USER_ROLES['admin']);

        return view('setup_thanks')->with('success', 'Set up completed! Thanks for using Polr!');
    }
}
