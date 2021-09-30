<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<ul class="sitepage_sidebar_list sitepage_sidebar_insights">
  <li>
    <div><?php echo $this->translate(array('<span> %s </span> Monthly Active User', '<span> %s </span> Monthly Active Users', $this->total_users), $this->locale()->toNumber($this->total_users)) ?></div>
  </li>
  <li>  
    <div><?php echo $this->translate(array('<span> %s </span> Like', '<span> %s </span> Likes', $this->sitepage->like_count), $this->locale()->toNumber($this->sitepage->like_count)) ?></div>
  </li>
  <?php $showComment = Engine_Api::_()->sitepage()->displayCommentInsights(); if(!empty($showComment)): ?>
    <li>  
      <div><?php echo $this->translate(array('<span> %s </span> Comment', '<span> %s </span> Comments', $this->sitepage->comment_count), $this->locale()->toNumber($this->sitepage->comment_count)) ?></div>
    </li>	
  <?php endif; ?>
  
  <?php if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemember')): ?>
		<?php $memberTitle = Engine_Api::_()->getApi('settings', 'core')->getSetting( 'pagemember.member.title' , 1);
		if ($this->sitepage->member_title && $memberTitle) : ?>
			 <li><div><span><?php echo $this->sitepage->member_count ?></span><?php  echo  ' ' . $this->sitepage->member_title; ?></div> </li>  
	<?php else : ?>
		<li>  
			<div><?php echo $this->translate(array('<span> %s </span> Member', '<span> %s </span> Members', $this->sitepage->member_count), $this->locale()->toNumber($this->sitepage->member_count)) ?></div>
		</li>
	<?php endif; ?>
	<?php endif; ?>

  
  <li>  
    <div><?php echo $this->translate(array('<span> %s </span> View', '<span> %s </span> Views', $this->sitepage->view_count), $this->locale()->toNumber($this->sitepage->view_count)) ?></div>
  </li>	
  <li>
    <?php
    echo $this->htmlLink(
            array('route' => 'sitepage_insights', 'page_id' => $this->sitepage->page_id), $this->translate('See All &raquo;')
    )
    ?>
  </li>
</ul>