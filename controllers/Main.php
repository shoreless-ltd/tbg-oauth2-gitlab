<?php

namespace thebuggenie\modules\oauth2_gitlab\controllers;

use Omines\OAuth2\Client\Provider\Gitlab;
use thebuggenie\core\entities\User;
use thebuggenie\core\framework\Action;
use thebuggenie\core\framework\Context;
use thebuggenie\core\framework\Request;
use thebuggenie\core\framework\Settings;
use thebuggenie\modules\oauth2_gitlab\Oauth2_gitlab;

/**
 * Main controller of the GitLab OAuth2 module.
 *
 * @Routes(name_prefix="oauth2_gitlab_", url_prefix="/oauth2_gitlab")
 *
 * @author SHORELESS Limited <mailto:contact@shoreless.limited>
 * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
 * @package thebuggenie
 * @subpackage oauth2-gitlab
 * @category controller
 */
class Main extends Action
{

    /**
     * Login action
     *
     * @Route(url="/login", name="login")
     * @AnonymousRoute
     *
     * @param \thebuggenie\core\framework\Request $request
     *   Request object.
     */
    public function runLogin(Request $request)
    {
        $settings      = Oauth2_gitlab::getModule()->getSettings();
        $domain        = $settings['domain'];
        $client_id     = $settings['client_id'];
        $client_secret = $settings['client_secret'];

        if (!$client_id || !$client_secret) {
            // Not configured.
            Context::setMessage('login_error', $this->getI18n()->__('GitLab authentication not configured'));
            $this->forward('login');
        }

        $provider = new Gitlab([
            'clientId'     => $client_id,
            'clientSecret' => $client_secret,
            'redirectUri'  => Context::getRouting()->generate('oauth2_gitlab_login', [], false),
            'domain' => $domain,
        ]);

        if ($request->hasParameter('error')) {
            // Got an error, probably user denied access
            unset($_SESSION['oauth2state']);
            Context::setMessage('login_error', htmlspecialchars($_GET['error'], ENT_QUOTES, 'UTF-8'));
            $this->forward('login');
        }
        elseif (!$request->hasParameter('code')) {
            // If we don't have an authorization code then get one.
            $authUrl = $provider->getAuthorizationUrl();
            $_SESSION['oauth2state'] = $provider->getState();
            $this->forward($authUrl);
        }
        elseif (!$request->hasParameter('state') || ($request->getParameter('state') !== $_SESSION['oauth2state'])) {
            // State is invalid, possible CSRF attack in progress.
            unset($_SESSION['oauth2state']);
            Context::setMessage('login_error', $this->getI18n()->__('Invalid login request'));
            $this->forward('login');
        }
        else {
            unset($_SESSION['oauth2state']);

            // Try to get an access token (using the authorization code grant).
            $token = $provider->getAccessToken('authorization_code', [
                'code' => $request->getParameter('code')
            ]);

            // Optional: Look up user profile data.
            try {
                // We got an access token, let's now get the owner details
                $ownerDetails = $provider->getResourceOwner($token);
                $user = Oauth2_gitlab::getModule()->getOrCreateUserByOwnerDetails($ownerDetails);

                if ($user instanceof User) {
                    Context::getResponse()->setCookie('tbg3_password', $user->getPassword());
                    Context::getResponse()->setCookie('tbg3_username', $user->getUsername());
                    $user->setOnline();
                    $user->save();
                    $this->verifyScopeMembership($user);

                    if (Settings::get('returnfromlogin') == 'referer') {
                        $forward_url = Context::getRouting()->generate('dashboard');
                    }
                    else {
                        $forward_url = Context::getRouting()->generate(Settings::get('returnfromlogin'));
                    }

                    $forward_url = htmlentities($forward_url, ENT_COMPAT, Context::getI18n()->getCharset());
                    return $this->forward($forward_url);
                }
            }
            catch (\Exception $e) {
                // Failed to get user details.
                Context::setMessage('login_error', $e->getMessage());
                $this->forward('login');
            }

            unset($_SESSION['oauth2state']);

            Context::setMessage('login_error', $this->getI18n()->__('An unknown error occurred'));
            $this->forward('login');
        }
    }

    /**
     * ConfigureSettings action
     *
     * @param \thebuggenie\core\framework\Request $request
     *   Request object.
     */
    public function runConfigureSettings(Request $request)
    {
        if ($request->isPost()) {
            Oauth2_gitlab::getModule()->saveSetting('domain', trim($request['domain']));
            Oauth2_gitlab::getModule()->saveSetting('client_id', trim($request['client_id']));
            Oauth2_gitlab::getModule()->saveSetting('client_secret', trim($request['client_secret']));
            // Oauth2_gitlab::getModule()->saveSetting('btn_label', trim($request['btn_label']));

            Context::setMessage('oauth2_gitlab_settings_saved', true);
        }

        $this->forward($this->getRouting()->generate('configure_module', ['config_module' => 'oauth2_gitlab']));
    }

}
