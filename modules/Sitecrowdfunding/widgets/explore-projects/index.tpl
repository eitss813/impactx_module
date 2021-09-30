<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagemember
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2013-03-18 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/common_style_css.tpl';
?>

<ul class="sitepage_sidebar_list">

    <?php foreach ($this->projects as $project): ?>
        <li>
            <?php $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project->project_id); ?>
            <?php echo $this->htmlLink($project->getHref(), $this->itemPhoto($project, 'thumb.icon'), array('title' => $project->getTitle())) ?>
            <div class='sitepage_sidebar_list_info'>
                <div class='sitepage_sidebar_list_title'>
                    <?php echo $this->htmlLink($project->getHref(), $project->getTitle(), array('title' => $project->getTitle())) ?>
                </div>
                <div class='sitepage_sidebar_list_details'>
                    <?php $editURL = $this->url(array('action' => 'overview', 'project_id' => $project->project_id), 'sitecrowdfunding_dashboard', true);?>
                    <a target="_blank" href="<?php echo $editURL; ?>"><span ><?php echo $this->translate('Edit') ?></span></a>
                </div>
            </div>
        </li>
    <?php endforeach; ?>

    <?php if($this->total_projects > 3):?>
        <?php $myProjectsUrl = $this->url(array('action' => 'index'), 'sitecrowdfunding_general', true);?>
        <a target="_blank" class="viewlink" href="<?php echo $myProjectsUrl; ?>">View All<i class="fa-angle-double-right fa"></i></a>
    <?php endif;?>
</ul>