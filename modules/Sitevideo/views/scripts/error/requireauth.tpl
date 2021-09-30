<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: requireauth.tpl 9747 2012-07-26 02:08:08Z john $
 * @author     John
 */
?>

<?php echo $this->form; ?> <br />

<a class='buttonlink icon_back' href='javascript:void(0);' onClick='history.go(-1);'>
    <?php echo $this->translate('Go Back') ?>
</a>

<script type="text/javascript">

    function checkPasswordProtection(obj) {

        var flag = true;
        if ($('password_error'))
            $('password_error').destroy();

        if (obj['password'] && obj['password'].value == '') {
            liElement = new Element('span', {'html': '<?php echo $this->translate("* Please complete this field - it is required."); ?>', 'class': 'sitevideo_protection_error', 'id': 'password_error'}).inject($('password-element'));
            flag = false;
        }

        if (flag) {
            url = '<?php echo $this->url(array('action' => 'check-password-protection', 'video_id' => $this->video_id), "sitevideo_video_general"); ?>';
            var request = new Request.JSON({
                url: url,
                method: 'post',
                data: {
                    format: 'html',
                    video_id: '<?php echo $this->video_id; ?>',
                    password: obj['password'].value
                },
                //responseTree, responseElements, responseHTML, responseJavaScript
                onSuccess: function (responseJSON) {
                    if (responseJSON.status == 0) {

                        if ($('password_error'))
                            $('password_error').destroy();

                        liElement = new Element('span', {'html': '<?php echo $this->translate("This is not valid password. Please try again."); ?>', 'class': 'sitevideo_protection_error', 'id': 'password_error'}).inject($('password-element'));
                        flag = false;
                    } else {
                        window.location.href = '<?php echo $this->video->getHref(); ?>';
                    }
                }});

            request.send();
        }
        return false;
    }
</script>

<style type="text/css">
    .sitevideo_protection_error {
        color:#FF0000;
        display:block;
        font-size:11px;
        padding-top:5px;
    }
</style>