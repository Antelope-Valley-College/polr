<?php
namespace App\Factories;

use App\Models\Link;
use App\Helpers\CryptoHelper;
use App\Helpers\LinkHelper;


class SamlFactory {
    public static function GetSettings() {
        $settings = array(
            // If 'strict' is True, then the PHP Toolkit will reject unsigned
            // or unencrypted messages if it expects them signed or encrypted
            // Also will reject the messages if not strictly follow the SAML
            // standard: Destination, NameId, Conditions ... are validated too.
            'strict' => true,

            // Enable debug mode (to print errors)
            'debug' => in_array(strtolower(env('SAML_DEBUG')), array('true', '1')),

            // Set a BaseURL to be used instead of try to guess
            // the BaseURL of the view that process the SAML Message.
            // Ex. http://sp.example.com/
            //     http://example.com/sp/
            'baseurl' => env('APP_PROTOCOL') . env('APP_ADDRESS') . '/saml/sp/',

            // Service Provider Data that we are deploying
            'sp' => array(
                // Identifier of the SP entity  (must be a URI)
                'entityId' => env('SAML_ENTITYID'),
                // Specifies info about where and how the <AuthnResponse> message MUST be
                // returned to the requester, in this case our SP.
                'assertionConsumerService' => array(
                    // URL Location where the <Response> from the IdP will be returned
                    'url' => env('APP_PROTOCOL') . env('APP_ADDRESS') . '/saml/sp/acs',
                    // SAML protocol binding to be used when returning the <Response>
                    // message.  Onelogin Toolkit supports for this endpoint the
                    // HTTP-POST binding only
                    'binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST',
                ),
                // If you need to specify requested attributes, set a
                // attributeConsumingService. nameFormat, attributeValue and
                // friendlyName can be omitted. Otherwise remove this section.
                "attributeConsumingService"=> array(
                        "serviceName" => env('SAML_SERVICE_NAME'),
                        "serviceDescription" => env('SAML_SERVICE_DESC'),
                        "requestedAttributes" => array(
                            array(
                                "name" => "username",
                                "isRequired" => true,
                                "nameFormat" => "",
                                "friendlyName" => "username",
                                "attributeValue" => ""
                            ),
                            array(
                                "name" => "email",
                                "isRequired" => true,
                                "nameFormat" => "",
                                "friendlyName" => "email",
                                "attributeValue" => ""
                            )
                        )
                ),
                // Specifies info about where and how the <Logout Response> message MUST be
                // returned to the requester, in this case our SP.
                'singleLogoutService' => array(
                    // URL Location where the <Response> from the IdP will be returned
                    'url' => env('APP_PROTOCOL') . env('APP_ADDRESS') . '/saml/sp/sls',
                    // SAML protocol binding to be used when returning the <Response>
                    // message.  Onelogin Toolkit supports for this endpoint the
                    // HTTP-Redirect binding only
                    'binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect',
                ),
                // Specifies constraints on the name identifier to be used to
                // represent the requested subject.
                // Take a look on lib/Saml2/Constants.php to see the NameIdFormat supported
                'NameIDFormat' => 'urn:oasis:names:tc:SAML:1.1:nameid-format:emailAddress',

                // Usually x509cert and privateKey of the SP are provided by files placed at
                // the certs folder. But we can also provide them with the following parameters
                'x509cert' => '',
                'privateKey' => '',

                /*
                 * Key rollover
                 * If you plan to update the SP x509cert and privateKey
                 * you can define here the new x509cert and it will be 
                 * published on the SP metadata so Identity Providers can
                 * read them and get ready for rollover.
                 */
                // 'x509certNew' => '',
            ),

            // Identity Provider Data that we want connect with our SP
            'idp' => array(
                // Identifier of the IdP entity  (must be a URI)
                'entityId' => env('SAML_IDP_ENTITYID'),
                // SSO endpoint info of the IdP. (Authentication Request protocol)
                'singleSignOnService' => array(
                    // URL Target of the IdP where the SP will send the Authentication Request Message
                    'url' => env('SAML_IDP_SSO_URL'),
                    // SAML protocol binding to be used when returning the <Response>
                    // message.  Onelogin Toolkit supports for this endpoint the
                    // HTTP-Redirect binding only
                    'binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect',
                ),
                // Public x509 certificate of the IdP
                'x509cert' => env('SAML_IDP_X509CERT'),
                /*
                 *  Instead of use the whole x509cert you can use a fingerprint in
                 *  order to validate the SAMLResponse, but we don't recommend to use
                 *  that method on production since is exploitable by a collision
                 *  attack.
                 *  (openssl x509 -noout -fingerprint -in "idp.crt" to generate it,
                 *   or add for example the -sha256 , -sha384 or -sha512 parameter)
                 *
                 *  If a fingerprint is provided, then the certFingerprintAlgorithm is required in order to
                 *  let the toolkit know which Algorithm was used. Possible values: sha1, sha256, sha384 or sha512
                 *  'sha1' is the default value.
                 */
                // 'certFingerprint' => '',
                // 'certFingerprintAlgorithm' => 'sha1',

                /* In some scenarios the IdP uses different certificates for
                 * signing/encryption, or is under key rollover phase and more 
                 * than one certificate is published on IdP metadata.
                 * In order to handle that the toolkit offers that parameter.
                 * (when used, 'x509cert' and 'certFingerprint' values are
                 * ignored).
                 */
                // 'x509certMulti' => array(
                //      'signing' => array(
                //          0 => '<cert1-string>',
                //      ),
                //      'encryption' => array(
                //          0 => '<cert2-string>',
                //      )
                // ),
            ),
        );

        function findAttr($arr, $key) {
            return array_filter($arr, function($v) use ($key) { return $v['name'] == $key;});
        }
        if (env('SAML_USER_ATTR') && env('SAML_USER_REGEX') && 
            empty(findAttr($settings['sp']['attributeConsumingService']['requestedAttributes'], env('SAML_USER_ATTR')))) {
            $settings['sp']['attributeConsumingService']['requestedAttributes'][]=
                array(
                    "name" => env('SAML_USER_ATTR'),
                    "isRequired" => false,
                    "nameFormat" => "",
                    "friendlyName" => env('SAML_USER_ATTR'),
                    "attributeValue" => ""
                );
        }

        if (env('SAML_ADMIN_ATTR') && env('SAML_ADMIN_REGEX') && 
            empty(findAttr($settings['sp']['attributeConsumingService']['requestedAttributes'], env('SAML_ADMIN_ATTR')))) {
            $settings['sp']['attributeConsumingService']['requestedAttributes'][]=
                array(
                    "name" => env('SAML_ADMIN_ATTR'),
                    "isRequired" => false,
                    "nameFormat" => "",
                    "friendlyName" => env('SAML_ADMIN_ATTR'),
                    "attributeValue" => ""
                );
        }

        if (env('SAML_SLO_URL')) {
            // SLO endpoint info of the IdP.
            $settings['sp']['singleLogoutService'] = array(
                // URL Location of the IdP where the SP will send the SLO Request
                'url' => env('SAML_IDP_SSO_URL'),
                // URL location of the IdP where the SP SLO Response will be sent (ResponseLocation)
                // if not set, url for the SLO Request will be used
                'responseUrl' => '',
                // SAML protocol binding to be used when returning the <Response>
                // message.  Onelogin Toolkit supports for this endpoint the
                // HTTP-Redirect binding only
                'binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect',
            );
        }

        return $settings;
    }
}
