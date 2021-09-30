<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: meta-detail.tpl 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<script type="text/javascript" >
    var submitformajax = 1;
</script>

<?php include_once APPLICATION_PATH . '/application/modules/Sitecrowdfunding/views/scripts/_DashboardNavigation.tpl'; ?>

<div class="sitecrowdfunding_dashboard_content">
    <?php echo $this->partial('application/modules/Sitecrowdfunding/views/scripts/dashboard/header.tpl', array('project' => $this->project, 'sectionTitle' => 'Meta Keywords', 'sectionDescription' => "Meta keywords are a great way to provide search engines with information about your project so that search engines populate your project in search results. Below, you can add meta keywords for this project. (The tags entered by you for this project will also be added to the meta keywords.)")); ?>
    <?php echo $this->form->render($this); ?>
    <div id="show_tab_content_child"></div>
</div>
</div>
</div>