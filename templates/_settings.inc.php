<style>
    .address-settings dl { font-size: 1em; }
    .address-settings dt, .address-settings dd { width: auto; display: block; float: none; }
    .address-settings dd { margin-bottom: 10px; }

    .address-settings form { display: block; clear: both; float: none; margin-top: 15px; }

    .address-settings ul, .address-settings ul li { list-style: none; margin: 0; padding: 0; }
    .address-settings ul li { padding: 5px 0; }
    .address-settings form label { display: block; width: auto; }
    .address-settings form input { font-size: 1.05em; padding: 5px; }
    .address-settings form input[name="domain"] { width: 80%; }
    .address-settings form input[name="client_id"] { width: 80%; }
    .address-settings form input[name="client_secret"] { width: 80%; }
</style>
<div class="address-settings">
    <p><?= __('The Bug Genie can use %gitlab_icon GitLab OAuth2 (%link_to_gitlab_oauth) as an identity provider for user authentication and registration.', ['%gitlab_icon' => image_tag('gitlab-normal.png', ['style' => 'display: inline-block; width: 16px; vertical-align: middle; margin-left: 3px;'], false, 'oauth2_gitlab'), '%link_to_gitlab_oauth' => '<a href="https://docs.gitlab.com/ce/integration/oauth_provider.html">GitLab Integration &raquo; OAuth2</a>']); ?></p>
    <p><?= __('To authenticate with GitLab OAuth2, GitLab requires that you create a client key specific to this installation. To do this, follow the instructions under "Adding an application through the profile", here: %link_to_gitlab_oauth', ['%link_to_gitlab_oauth' => link_tag('https://docs.gitlab.com/ce/integration/oauth_provider.html#adding-an-application-through-the-profile', null, ['target' => '_blank'])]); ?></p>
    <p><?= __('When prompted, use the values listed below, then input the values of the client id and client secret here.'); ?></p>
    <dl>
        <dt>Name</dt>
        <dd><i>&lt;e.g. My Company TBG installation&gt;</i></dd>
        <dt>Redirect URI</dt>
        <dd><?= make_url('oauth2_gitlab_login', [], false); ?></dd>
    </dl>
    <?php if (isset($settings_saved) && $settings_saved): ?>
        <div class="greenbox" style="margin: 5px 0px;">
            <div><?= __('Settings saved'); ?></div>
        </div>
    <?php endif; ?>
    <form action="<?= make_url('configure_oauth2_gitlab_settings'); ?>" accept-charset="<?= \thebuggenie\core\framework\Context::getI18n()->getCharset(); ?>" method="post">
        <ul>
            <li>
                <label><?= __('GitLab Server Domain'); ?></label>
                <input type="text" name="domain" placeholder="https://mygitlab.example.com" value="<?= $domain; ?>">
            </li>
            <li>
                <label><?= __('Client ID'); ?></label>
                <input type="text" name="client_id" value="<?= $client_id; ?>">
            </li>
            <li>
                <label><?= __('Client Secret'); ?></label>
                <input type="text" name="client_secret" value="<?= $client_secret; ?>">
            </li>
<?php /*            <li>
                <label><?= __('Login Button Label'); ?></label>
                <input type="text" name="btn_label" placeholder="Use GitLab" value="<?= $btn_label; ?>">
            </li> */ ?>
        </ul>
        <input type="submit" class="button" value="<?= __('Save'); ?>">
    </form>
</div>
