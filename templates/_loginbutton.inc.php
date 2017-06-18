<style>
    #oauth2-gitlab-signin-button.persona-button span::after {
        background-image : url('<?= image_url('gitlab-normal.png', false, 'oauth2_gitlab'); ?>');
    }
</style>
<div class="logindiv openid_container" id="oauth2_gitlab_openid_container" style="display: block;">
    <div style="text-align: center;">
        <?php if (!\thebuggenie\core\framework\Settings::isOpenIDavailable()): ?>
        <fieldset style="border: 0; border-top: 1px dotted rgba(0, 0, 0, 0.3); padding: 10px 100px; width: 100px; margin: 0 auto;">
            <legend style="text-align: center; width: 100%; background-color: transparent;"><?php echo __('%regular_login or %persona_or_openid_login', array('%regular_login' => '', '%persona_or_openid_login' => '')); ?></legend>
        </fieldset>
        <?php endif; ?>
        <a class="persona-button large blue" id="oauth2-gitlab-signin-button" href="<?= make_url('oauth2_gitlab_login'); ?>"><span><?php echo empty($gitlab_btn_label) ? __('Use GitLab') : $gitlab_btn_label; ?></span></a>
    </div>
</div>