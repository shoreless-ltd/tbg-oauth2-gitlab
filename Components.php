<?php

    namespace thebuggenie\modules\oauth2_gitlab;

    use thebuggenie\core\framework;

    /**
     * actions for the oauth2_gitlab module
     */
    class Components extends framework\ActionComponent
    {

        public function componentSettings()
        {
            $settings = Oauth2_gitlab::getModule()->getSettings();
            $this->domain = ! empty($settings['domain']) ? $settings['domain'] : '';
            $this->client_id = ! empty($settings['client_id']) ? $settings['client_id'] : '';
            $this->client_secret = ! empty($settings['client_secret']) ? $settings['client_secret'] : '';
            //$this->btn_label = ! empty($settings['btn_label']) ? $settings['btn_label'] : '';

            if (framework\Context::hasMessage('oauth2_gitlab_settings_saved')) {
                $this->settings_saved = framework\Context::getMessageAndClear('oauth2_gitlab_settings_saved');
            }
        }

    }

