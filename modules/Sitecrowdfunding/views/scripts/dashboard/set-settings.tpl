<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: set-settings.tpl 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<script type="text/javascript" >
    var submitformajax = 1;
</script>

<?php include_once APPLICATION_PATH . '/application/modules/Sitecrowdfunding/views/scripts/_DashboardNavigation.tpl'; ?>

<div class="sitecrowdfunding_dashboard_content">
    <?php echo $this->partial('application/modules/Sitecrowdfunding/views/scripts/dashboard/header.tpl', array('project' => $this->project, 'sectionTitle' => 'Edit Project Profile Settings', 'sectionDescription' => 'You can configure below settings related to the your Project’s profile page, like you can enable setting to display main photo / video at top on the Project’s profile page.')); ?>
    <?php  echo $this->form->render($this); ?>
    <div id="show_tab_content_child"></div>
</div>
</div>
</div>