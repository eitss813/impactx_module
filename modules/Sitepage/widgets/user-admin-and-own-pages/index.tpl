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

    <?php foreach ($this->user_and_admin_sitepages as $id): ?>
        <li>
            <?php $sitepage = Engine_Api::_()->getItem('sitepage_page', $id); ?>
            <?php echo $this->htmlLink(Engine_Api::_()->sitepage()->getHref($sitepage->page_id, $sitepage->owner_id, $sitepage->getSlug()), $this->itemPhoto($sitepage, 'thumb.icon'), array('title' => $sitepage->getTitle())) ?>
            <div class='sitepage_sidebar_list_info'>
                <div class='sitepage_sidebar_list_title'>
                    <?php echo $this->htmlLink(Engine_Api::_()->sitepage()->getHref($sitepage->page_id, $sitepage->owner_id, $sitepage->getSlug()), Engine_Api::_()->sitepage()->truncation($sitepage->getTitle()), array('title' => $sitepage->getTitle())) ?>
                </div>
                <div class='sitepage_sidebar_list_details'>
                    <?php $editURL = $this->url(array('action' => 'overview', 'page_id' => $sitepage->page_id), 'sitepage_dashboard', true);?>
                    <a target="_blank" href="<?php echo $editURL; ?>"><span ><?php echo $this->translate('Edit') ?></span></a>
                </div>
            </div>
        </li>
    <?php endforeach; ?>

    <?php if($this->user_and_admin_sitepages_count > 3):?>
        <?php $myPagesUrl = $this->url(array('action' => 'manage'), 'sitepage_general', true);?>
        <a target="_blank" class="viewlink" href="<?php echo $myPagesUrl; ?>">View All<i class="fa-angle-double-right fa"></i></a>
    <?php endif;?>

</ul>