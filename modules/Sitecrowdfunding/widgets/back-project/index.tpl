<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.php 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php if($this->donationType):?>
	<a href="<?php echo $this->url(array('action'=>'reward-selection','project_id'=>$this->project->project_id, 'donationType' => $this->donationType), "sitecrowdfunding_backer", true) ?>">
	<div class="common_btn">
		<?php echo $this->translate($this->backTitle); ?>	
	</div>
	</a>
<?php else: ?>
	<a href="<?php echo $this->url(array('action'=>'reward-selection','project_id'=>$this->project->project_id), "sitecrowdfunding_backer", true) ?>">
	<div class="common_btn">
		<?php echo $this->translate($this->backTitle); ?>	
	</div>
	</a>
<?php endif; ?> 
