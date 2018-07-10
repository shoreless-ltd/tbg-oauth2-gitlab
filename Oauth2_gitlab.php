<?php

namespace thebuggenie\modules\oauth2_gitlab;

use Omines\OAuth2\Client\Provider\GitlabResourceOwner;
use thebuggenie\core\entities\Module;
use thebuggenie\core\entities\tables\Users;
use thebuggenie\core\entities\User;
use thebuggenie\core\framework\ActionComponent;
use thebuggenie\core\framework\Context;
use thebuggenie\core\framework\Event;

/**
 * GitLab OAuth2 authentication module for The Bug Genie.
 *
 * @author SHORELESS Limited <mailto:contact@shoreless.limited>
 * @version 1.0
 * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
 * @package thebuggenie
 * @subpackage oauth2-gitlab
 * @category module
 *
 * @Table(name="\thebuggenie\core\entities\tables\Modules")
 */
class Oauth2_gitlab extends Module
{

    const VERSION = '1.0';

    protected $_has_config_settings = true;
    protected $_name = 'oauth2_gitlab';
    protected $_longname = 'GitLab OAuth2 support';
    protected $_description = 'Adds GitLab as an OAuth2 provider for user authentication and registration.';
    protected $_module_config_title = 'GitLab OAuth2 settings';
    protected $_module_config_description = 'Configure the GitLab OAuth2 authentication provider';

    /**
     * Return an instance of this module
     *
     * @return \thebuggenie\modules\oauth2_gitlab\Oauth2_gitlab
     *   Instance of the OAuth2 GitLab module.
     */
    public static function getModule()
    {
        return Context::getModule('oauth2_gitlab');
    }

    /**
     * Initialize module
     */
    protected function _initialize()
    {
        // Load all composer dependencies required by the OAuth2 GitLab module.
        require THEBUGGENIE_MODULES_PATH . 'oauth2_gitlab' . DS . 'vendor' . DS . 'autoload.php';
    }

    /**
     * Get module settings
     *
     * @return array
     *   Associative array of module settings.
     */
    public function getSettings()
    {
        return [
            'domain' => $this->getSetting('domain'),
            'client_id' => $this->getSetting('client_id'),
            'client_secret' => $this->getSetting('client_secret'),
        ];
    }

    /**
     * Event callback for core login_form_pane
     *
     * @Listener(module="core", identifier="login_form_pane")
     *
     * @param \thebuggenie\core\framework\Event $event
     */
    public function listenLoginButtons(Event $event)
    {
        ActionComponent::includeComponent('oauth2_gitlab/loginbutton');
    }

    /**
     * Get Font Awesome icon for module settings list
     *
     * @return string
     *   Font Awesome icon for module settings list.
     */
    public function getFontAwesomeIcon()
    {
        return 'gitlab';
    }

    /**
     * Get Font Awesome icon color for module settings list
     *
     * @return string
     *   Font Awesome icon color for module settings list.
     */
    public function getFontAwesomeColor()
    {
        return '#555';
    }

    public function getOrCreateUserByOwnerDetails(GitlabResourceOwner $ownerDetails)
    {
        $email = $ownerDetails->getEmail();
        $user = Users::getTable()->getByEmail($email);

        if (!$user instanceof User) {
            $user = new User();
            $user->setPassword(User::createPassword());
            $user->setUsername($email);
            $user->setRealname($ownerDetails->getName());
            $user->setEmail($email);
            $user->setOpenIdLocked();
            $user->setActivated();
            $user->setEnabled();
            $user->setValidated();
            $user->save();
        }

        return $user;
    }

}
