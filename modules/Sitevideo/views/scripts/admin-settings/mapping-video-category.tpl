<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: mapping-video-category.tpl 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<div class="global_form_popup">
    <?php echo $this->form->render($this) ?>
</div>

<?php if (@$this->closeSmoothbox || $this->close_smoothbox): ?>
    <?php $baseurl = Zend_Controller_Front::getInstance()->getBaseUrl(); ?>
    <script type="text/javascript">
        window.parent.location.href = '<?php echo $baseurl ?>' + '/admin/sitevideo/settings/video-categories';
        window.parent.Smoothbox.close();
    </script>
<?php endif; ?>

<script type="text/javascript">
    function closeSmoothbox() {
        window.parent.Smoothbox.close();
    }
</script>

