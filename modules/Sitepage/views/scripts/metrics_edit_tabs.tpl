<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: edit_tabs.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
	$front = Zend_Controller_Front::getInstance();
	$module = $front->getRequest()->getModuleName();
	$controller = $front->getRequest()->getControllerName();
	$action = $front->getRequest()->getActionName();
?>

<?php 
//GET SITEPAGE OBJECT
$sitepage = Engine_Api::_()->getItem('sitepage_page', $this->page_id);

$this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/styles/style_sitepage_dashboard.css');

$this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/styles/styles.css');

$this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/scripts/core.js');

include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/common_style_css.tpl'; ?>

<?php
$this->headScript()
		->appendFile($this->layout()->staticBaseUrl . 'externals/moolasso/Lasso.js')
		->appendFile($this->layout()->staticBaseUrl . 'externals/moolasso/Lasso.Crop.js')
		->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Observer.js')
		->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.js')
		->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Local.js')
		->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Request.js');
?>


<div class="sitepage seaocore_db_tabs">
    <div class="dashboard_info">
        <div class="dashboard_info_image">
            <?php $metrics = Engine_Api::_()->getItem('sitepage_metric', $this->metric_id);?>
            <?php if($metrics['logo']):?>
                <img class="metrics_logo thumb" src="<?php echo $metrics->getLogoUrl('thumb.cover'); ?>">
            <?php else:?>
                <img class="metrics_logo thumb" src="application/modules/Sitepage/externals/images/nophoto_metric_thumb_profile.png">
            <?php endif;?>
        </div>
    </div>
    <ul>
        <li class="seaocore_db_head">
            <h3>Overview
                <i id="metrics-overview"  class="fa fa-arrow-circle-up" style="float: right;"></i>
            </h3>
        </li>
        <li id="metrics-overview-sub" class="selected">
            <a href="<?php echo $this->url(array('action' => 'edit', 'metric_id' => $this->metric_id ), 'sitepage_metrics') ?>" target="_blank">Edit Metrics</a>
        </li>
    </ul>
</div>