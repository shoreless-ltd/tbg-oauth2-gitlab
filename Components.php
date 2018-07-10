<?php

namespace thebuggenie\modules\oauth2_gitlab;

use thebuggenie\core\framework\ActionComponent;
use thebuggenie\core\framework\Context;

/**
 * Component preprocessing of the GitLab OAuth2 module.
 *
 * @author SHORELESS Limited <mailto:contact@shoreless.limited>
 * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
 * @package thebuggenie
 * @subpackage oauth2-gitlab
 * @category component
 */
class Components extends ActionComponent
{

    /**
     * Settings component
     */
    public function componentSettings()
    {
        $settings = Oauth2_gitlab::getModule()->getSettings();
        $this->domain = ! empty($settings['domain']) ? $settings['domain'] : '';
        $this->client_id = ! empty($settings['client_id']) ? $settings['client_id'] : '';
        $this->client_secret = ! empty($settings['client_secret']) ? $settings['client_secret'] : '';

        if (Context::hasMessage('oauth2_gitlab_settings_saved')) {
            $this->settings_saved = Context::getMessageAndClear('oauth2_gitlab_settings_saved');
        }
    }

}
