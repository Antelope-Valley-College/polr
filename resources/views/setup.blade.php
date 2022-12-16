@extends('layouts.minimal')

@section('title')
Setup
@endsection

@section('css')
<link rel='stylesheet' href='/css/default-bootstrap.min.css'>
<link rel='stylesheet' href='/css/setup.css'>
@endsection

@section('content')
<div class="navbar navbar-default navbar-fixed-top">
    <a class="navbar-brand" href="/">Polr</a>
</div>

<div class="row" ng-controller="SetupCtrl" class="ng-root">
    <div class='col-md-3'></div>

    <div class='col-md-6 setup-body well'>
        <div class='setup-center'>
            <img class='setup-logo' src='/img/logo.png'>
        </div>

        <form class='setup-form' method='POST' action='/setup'>
            <h4>Database Configuration</h4>

            <p>
                Database URL:
                <setup-tooltip content="If supplied, this will override all other database fields. Enter the database URL in the format 'driver://username:password@host:port/database?options'"></setup-tooltip>
            </p>
            <input type='text' class='form-control' name='db:url' value=''>

            <p>Database Host:</p>
            <input type='text' class='form-control' name='db:host' value='localhost'>

            <p>Database Port:</p>
            <input type='text' class='form-control' name='db:port' value='3306'>

            <p>
                Database Unix Socket:
                <setup-tooltip content="If supplied, this will override the host and port fields below. Enter the absolute path to the unix file socket that will be used to access the database."></setup-tooltip>
            </p>
            <input type='text' class='form-control' name='db:unix_socket' value=''>

            <p>
                Database Username:
                <setup-tooltip content="For security reasons, we encourage you to not use the root account in production environments."></setup-tooltip>
            </p>
            <input type='text' class='form-control' name='db:username' value='root'>

            <p>Database Password:</p>
            <input type='password' class='form-control' name='db:password' value='password'>

            <p>
                Database Name:
                <setup-tooltip content="Name of existing database. You must create the Polr database manually."></setup-tooltip>
            </p>
            <input type='text' class='form-control' name='db:name' value='polr'>


            <h4>Application Settings</h4>

            <p>Application Name:</p>
            <input type='text' class='form-control' name='app:name' value='Polr'>

            <p>Application Protocol:</p>
            <input type='text' class='form-control' name='app:protocol' value='http://'>

            <p>Application URL (path to Polr; do not include http:// or trailing slash):</p>
            <input type='text' class='form-control' name='app:external_url' value='yoursite.com'>

            <p>
                Advanced Analytics:
                <button data-content="Enable advanced analytics to collect data such as referers, geolocation, and clicks over time. Enabling advanced analytics reduces performance and increases disk space usage."
                    type="button" class="btn btn-xs btn-default setup-qmark" data-toggle="popover">?</button>
            </p>
            <select name='setting:adv_analytics' class='form-control'>
                <option value='false' selected='selected'>Disable advanced analytics</option>
                <option value='true'>Enable advanced analytics</option>
            </select>

            <p>
                MaxMind GeoIP License Key (required for advanced analytics only):
            </p>
            <p>
            <input type='text' class='form-control' name='maxmind:license_key' value=''>

            <p class='text-muted'>
                To obtain a free MaxMind GeoIP license key, follow <a href="https://docs.polrproject.org/en/latest/user-guide/maxmind-license">these instructions</a> on Polr's documentation website.
            </p>

            <p>Shortening Permissions:</p>
            <select name='setting:shorten_permission' class='form-control'>
                <option value='false' selected='selected'>Anyone can shorten URLs</option>
                <option value='true'>Only logged in users may shorten URLs</option>
            </select>

            <p>Public Interface:</p>
            <select name='setting:public_interface' class='form-control'>
                <option value='true' selected='selected'>Show public interface (default)</option>
                <option value='false'>Redirect index page to redirect URL</option>
            </select>

            <p>404s and Disabled Short Links:</p>
            <select name='setting:redirect_404' class='form-control'>
                <option value='false' selected='selected'>Show an error message (default)</option>
                <option value='true'>Redirect to redirect URL</option>
            </select>

            <p>
                Redirect URL:
                <setup-tooltip content="Required if you wish to redirect the index page or 404s to a different website. To use Polr, login by directly heading to yoursite.com/login first."></setup-tooltip>
            </p>
            <input type='text' class='form-control' name='setting:index_redirect' placeholder='http://your-main-site.com'>
            <p class='text-muted'>
                If a redirect is enabled, you will need to go to
                http://yoursite.com/login before you can access the index
                page.
            </p>

            <p>
                Default URL Ending Type:
                <setup-tooltip content="If you choose to use pseudorandom strings, you will not have the option to use a counter-based ending."></setup-tooltip>
            </p>
            <select name='setting:pseudor_ending' class='form-control'>
                <option value='false' selected='selected'>Use base62 or base32 counter (shorter but more predictable, e.g 5a)</option>
                <option value='true'>Use pseudorandom strings (longer but less predictable, e.g 6LxZ3j)</option>
            </select>

            <p>
                URL Ending Base:
                <setup-tooltip content="This will have no effect if you choose to use pseudorandom endings."></setup-tooltip>
            </p>
            <select name='setting:base' class='form-control'>
                <option value='32' selected='selected'>32 -- lowercase letters & numbers (default)</option>
                <option value='62'>62 -- lowercase, uppercase letters & numbers</option>
            </select>

            <h4>
                Admin Account Settings
                <setup-tooltip content="These credentials will be used for your admin account in Polr."></setup-tooltip>
            </h4>

            <p>Admin Username:</p>
            <input type='text' class='form-control' name='acct:username' value='polr'>

            <p>Admin Email:</p>
            <input type='text' class='form-control' name='acct:email' value='polr@admin.tld'>

            <p>Admin Password:</p>
            <input type='password' class='form-control' name='acct:password' value='polr'>

            <h4>
                SMTP Settings
                <setup-tooltip content="Required only if the email verification or password recovery features are enabled."></setup-tooltip>
            </h4>

            <p>SMTP Server:</p>
            <input type='text' class='form-control' name='app:smtp_server' placeholder='smtp.gmail.com'>

            <p>SMTP Port:</p>
            <input type='text' class='form-control' name='app:smtp_port' placeholder='25'>

            <p>SMTP Username:</p>
            <input type='text' class='form-control' name='app:smtp_username' placeholder='example@gmail.com'>

            <p>SMTP Password:</p>
            <input type='password' class='form-control' name='app:smtp_password' placeholder='password'>

            <p>SMTP From:</p>
            <input type='text' class='form-control' name='app:smtp_from' placeholder='example@gmail.com'>
            <p>SMTP From Name:</p>
            <input type='text' class='form-control' name='app:smtp_from_name' placeholder='noreply'>

            <h4>API Settings</h4>

            <p>Anonymous API:</p>
            <select name='setting:anon_api' class='form-control'>
                <option selected value='false'>Off -- only registered users can use API</option>
                <option value='true'>On -- empty key API requests are allowed</option>
            </select>

            <p>
                Anonymous API Quota:
                <setup-tooltip content="API quota for non-authenticated users per minute per IP."></setup-tooltip>
            </p>
            <input type='text' class='form-control' name='setting:anon_api_quota' placeholder='10'>

            <p>Automatic API Assignment:</p>
            <select name='setting:auto_api_key' class='form-control'>
                <option selected value='false'>Off -- admins must manually enable API for each user</option>
                <option value='true'>On -- each user receives an API key on signup</option>
            </select>

            <h4>
                SAML2 Settings
                <setup-tooltip content="Enables use of a SAML2-compliant IdP (Identity Provider)."></setup-tooltip>
            </h4>
            <p>To use this, the IdP must send over the following attributes:</p>
            <table class="table">
            <tr><th scope="row">NameID</th><td>The NameID must be an immutable value that uniquely identifies a user. If a NameID does not have an associated account, a new one is created. If the username exists, it is converted int a SSO account and can no longer login using a password.</td></tr>
            <tr><th scope="row">username</th><td>This must be the username of the person logging in.</td></tr>
            <tr><th scope="row">email</th><td>The current email address of the user.</td></tr>
            </table>
            <p>The IdP may send over an additional attribute you specify to see if the account should be an admin account or not.</p>
            <p>If SAML SSO is turned off, users may request a password reset to gain access once again.</p>

            <p>
                <input type='checkbox' class='form-check-input' name='saml:saml_primary'> Enable SAML
            </p>            

            <p>
                <input type='checkbox' class='form-check-input' name='saml:saml_primary'> Use SAML As Primary Login
                <setup-tooltip content="If SAML is enabled and this is checked, when users are redirected to login or click on the login menu link, they are redirected to their SSO login page. Local logins can still be used by adding ?local_login=true to the login URL."></setup-tooltip>
            </p>            

            <p>
                <input type='checkbox' class='form-check-input' name='saml:saml_debug'> Enable SAML Debugging
                <setup-tooltip content="Turns on the debug functionality of the SAML library. Recommended for test environments."></setup-tooltip>
            </p>            

            <p>
                EntityID (This website):
                <setup-tooltip content="Defines the EntityID value for this website. The IdP needs this value to know which service is asking for a login. This is usually the base URL to this website or application."></setup-tooltip>
            </p>
            <input type='text' class='form-control' name='saml:sp_entityid' placeholder='https://polr.example.com'>

            <p>Service Name:</p>
            <input type='text' class='form-control' name='saml:sp_servicename' placeholder='Polr'>

            <p>Service Description:</p>
            <input type='text' class='form-control' name='saml:sp_desc' placeholder='URL shortening application'>

            <p>
                IdP EntityID:
                <setup-tooltip content="Provide the EntityID of the remote IdP system. This is not always the site URL."></setup-tooltip>
            </p>
            <input type='text' class='form-control' name='saml:idp_entityid' placeholder='https://idp.shibboleth.org'>

            <p>
                IdP Name:
                <setup-tooltip content="This text is displayed on a button when when logging in on the login page or through the login menu. Clicking it will direct the user to their IdP."></setup-tooltip>
            </p>
            <input type='text' class='form-control' name='saml:idp_name' placeholder='My IdP'>

            <p>
                IdP Certificate:
                <setup-tooltip content="Provide the signing certificate in PEM (base64) format. This is contained in the metadata file."></setup-tooltip>
            </p>
            <textarea class='form-control' name='saml:idp_x509cert' placeholder='MIID...'></textarea>

            <p>
                IdP Single Sign-On URL:
                <setup-tooltip content="This is the URL that users are directed to for login. It is provided in the IdP metadata file. This SAML library only supports HTTP-Redirect, as opposed to HTTP-Post. This is required for SAML to work."></setup-tooltip>
            </p>
            <input type='text' class='form-control' name='saml:idp_sso' placeholder='IdP Single Sign-On URL'>

            <p>
                IdP Single Log-Off URL:
                <setup-tooltip content="This is the URL that users are directed to for SAML logout. It is provided in the IdP metadata file. This is optional and not supported by some IdPs."></setup-tooltip>
            </p>
            <input type='text' class='form-control' name='saml:idp_slo' placeholder='IdP Single Log-Off URL'>

            <p>
                User Attribute:
                <setup-tooltip content="If present with the user regular expression, the IdP must be configured to pass this attribute. In order to be allowed to use this application, the specified attribute of the user must match the regular expression. The attribute may be but is not limited to the username or email attribute."></setup-tooltip>
            </p>
            <input type='text' class='form-control' name='saml:user_attr' placeholder='role'>

            <p>
                User Regular Expression:
                <setup-tooltip content="If the user attribute specified above matches this regular expression, the the account is turned into an admin, otherwise it is demoted to a regular user. If not provided, no chages are made to whether an account is an admin or not."></setup-tooltip>
            </p>
            <input type='text' class='form-control' name='saml:user_regex' placeholder='/user/'>
            <p>
                Admin Attribute:
                <setup-tooltip content="If present with the admin regular expression, the IdP must be configured to pass this attribute. The attribute may be but is not limited to the username or email attribute."></setup-tooltip>
            </p>
            <input type='text' class='form-control' name='saml:admin_attr' placeholder='role'>

            <p>
                Admin Regular Expression:
                <setup-tooltip content="If the admin attribute specified above matches this regular expression, the account is turned into an admin, otherwise it is demoted to a regular user. If not provided, no chages are made to whether an account is an admin or not."></setup-tooltip>
            </p>
            <input type='text' class='form-control' name='saml:admin_regex' placeholder='/admin/'>


            <h4>Other Settings</h4>

            <p>
                Registration:
                <setup-tooltip content="Enabling registration allows any user to create an account."></setup-tooltip>
            </p>
            <select name='setting:registration_permission' class='form-control'>
                <option value='none'>Registration disabled</option>
                <option value='email'>Enabled, email verification required</option>
                <option value='no-verification'>Enabled, no email verification required</option>
            </select>

            <p>
                Restrict Registration Email Domains:
                <setup-tooltip content="Restrict registration to certain email domains."></setup-tooltip>
            </p>
            <select name='setting:restrict_email_domain' class='form-control'>
                <option value='false'>Allow any email domain to register</option>
                <option value='true'>Restrict email domains allowed to register</option>
            </select>

            <p>
                Permitted Email Domains:
                <setup-tooltip content="A comma-separated list of emails permitted to register."></setup-tooltip>
            </p>
            <input type='text' class='form-control' name='setting:allowed_email_domains' placeholder='company.com,company-corp.com'>

            <p>
                Password Recovery:
                <setup-tooltip content="Password recovery allows users to reset their password through email."></setup-tooltip>
            </p>
            <select name='setting:password_recovery' class='form-control'>
                <option value='false'>Password recovery disabled</option>
                <option value='true'>Password recovery enabled</option>
            </select>
            <p class='text-muted'>
                Please ensure SMTP is properly set up before enabling password recovery.
            </p>

            <p>
                Require reCAPTCHA for Registrations
                <setup-tooltip content="You must provide your reCAPTCHA keys to use this feature."></setup-tooltip>
            </p>
            <select name='setting:acct_registration_recaptcha' class='form-control'>
                <option value='false'>Do not require reCAPTCHA for registration</option>
                <option value='true'>Require reCATPCHA for registration</option>
            </select>

            <p>
                reCAPTCHA Configuration:
                <setup-tooltip content="You must provide reCAPTCHA keys if you intend to use any reCAPTCHA-dependent features."></setup-tooltip>
            </p>

            <p>
                reCAPTCHA Site Key
            </p>
            <input type='text' class='form-control' name='setting:recaptcha_site_key'>

            <p>
                reCAPTCHA Secret Key
            </p>
            <input type='text' class='form-control' name='setting:recaptcha_secret_key'>

            <p class='text-muted'>
                You can obtain reCAPTCHA keys from <a href="https://www.google.com/recaptcha/admin">Google's reCAPTCHA website</a>.
            </p>

            <p>Theme (<a href='https://github.com/cydrobolt/polr/wiki/Themes-Screenshots'>screenshots</a>):</p>
            <select name='app:stylesheet' class='form-control'>
                <option value=''>Modern (default)</option>
                <option value='//maxcdn.bootstrapcdn.com/bootswatch/3.3.7/cyborg/bootstrap.min.css'>Midnight Black</option>
                <option value='//maxcdn.bootstrapcdn.com/bootswatch/3.3.7/united/bootstrap.min.css'>Orange</option>
                <option value='//maxcdn.bootstrapcdn.com/bootswatch/3.3.7/simplex/bootstrap.min.css'>Crisp White</option>
                <option value='//maxcdn.bootstrapcdn.com/bootswatch/3.3.7/darkly/bootstrap.min.css'>Cloudy Night</option>
                <option value='//maxcdn.bootstrapcdn.com/bootswatch/3.3.7/cerulean/bootstrap.min.css'>Calm Skies</option>
                <option value='//maxcdn.bootstrapcdn.com/bootswatch/3.3.7/paper/bootstrap.min.css'>Google Material Design</option>
                <option value='//maxcdn.bootstrapcdn.com/bootswatch/3.3.7/superhero/bootstrap.min.css'>Blue Metro</option>
                <option value='//maxcdn.bootstrapcdn.com/bootswatch/3.3.7/sandstone/bootstrap.min.css'>Sandstone</option>
                <option value='//maxcdn.bootstrapcdn.com/bootswatch/3.3.7/lumen/bootstrap.min.css'>Newspaper</option>
                <option value='//maxcdn.bootstrapcdn.com/bootswatch/3.3.7/solar/bootstrap.min.css'>Solar</option>
                <option value='//maxcdn.bootstrapcdn.com/bootswatch/3.3.7/cosmo/bootstrap.min.css'>Cosmo</option>
                <option value='//maxcdn.bootstrapcdn.com/bootswatch/3.3.7/flatly/bootstrap.min.css'>Flatly</option>
                <option value='//maxcdn.bootstrapcdn.com/bootswatch/3.3.7/yeti/bootstrap.min.css'>Yeti</option>
            </select>

            <div class='setup-form-buttons'>
                <input type='submit' class='btn btn-success' value='Install'>
                <input type='reset' class='btn btn-warning' value='Clear Fields'>
            </div>
            <input type="hidden" name='_token' value='{{csrf_token()}}' />
        </form>
    </div>

    <div class='col-md-3'></div>
</div>

<div class='setup-footer well'>
    Polr is <a href='https://opensource.org/osd' target='_blank'>open-source
    software</a> licensed under the <a href='//www.gnu.org/copyleft/gpl.html'>GPLv2+
    License</a>.

    <div>
        Polr Version {{env('VERSION')}} released {{env('VERSION_RELMONTH')}} {{env('VERSION_RELDAY')}}, {{env('VERSION_RELYEAR')}} -
        <a href='//github.com/cydrobolt/polr' target='_blank'>Github</a>

        <div class='footer-well'>
            &copy; Copyright {{env('VERSION_RELYEAR')}}
            <a class='footer-link' href='//cydrobolt.com' target='_blank'>Chaoyi Zha</a> &amp;
            <a class='footer-link' href='//github.com/Cydrobolt/polr/graphs/contributors' target='_blank'>other Polr contributors</a>
        </div>
    </div>
</div>

@endsection

@section('js')
<script src="/js/bootstrap.min.js"></script>
<script src='/js/angular.min.js'></script>
<script src='/js/base.js'></script>
<script src='/js/SetupCtrl.js'></script>
@endsection
