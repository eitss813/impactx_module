<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: change-photo.tpl 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php include_once APPLICATION_PATH . '/application/modules/Sitevideo/views/scripts/_DashboardNavigation.tpl'; ?>

<div class="sitevideo_dashboard_content">
    <?php echo $this->partial('application/modules/Sitevideo/views/scripts/dashboard/header.tpl', array('channel' => $this->channel)); ?>
    <?php echo $this->form->render($this); ?>
</div>
</div>
<script type="text/javascript">
    function removePhotoChannel(url) {
        window.location.href = url;
    }
</script>