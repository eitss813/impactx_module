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
<?php 
  include APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/Adintegration.tpl';
?>
<?php 
	$this->headLink()
  ->appendStylesheet($this->layout()->staticBaseUrl
    . 'application/modules/Sitepagediscussion/externals/styles/style_sitepagediscussion.css')
?>
<?php include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/payment_navigation_views.tpl'; ?>

<div class="sitepage_viewpages_head">
	<?php echo $this->htmlLink($this->sitepage->getHref(), $this->itemPhoto($this->sitepage, 'thumb.icon', '' , array('align'=>'left'))) ?>
	<h2>	
	  <?php echo $this->sitepage->__toString() ?>	
	  <?php echo $this->translate('&raquo; ');?>
    <?php echo $this->htmlLink(array( 'route' => 'sitepage_entry_view', 'page_url' => Engine_Api::_()->sitepage()->getPageUrl($this->sitepage->page_id), 'tab' => $this->tab_selected_id), $this->translate('Discussions')) ?>
  </h2>  
</div>
<!--RIGHT AD START HERE-->
<?php if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad') && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.communityads', 1) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.addiscussionview', 3) && $page_communityad_integration && Engine_Api::_()->sitepage()->showAdWithPackage($this->sitepage)):?>
	<div class="layout_right" id="communityad_topicindex">
      <?php echo $this->content()->renderWidget("communityad.ads", array( "itemCount"=>Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.addiscussionview', 3),"loaded_by_ajax"=>1,'widgetId'=>'page_topicindex'))?>
	</div>
<?php endif;?>
<!--RIGHT AD END HERE-->
<div class="sitepage_discussion_view">
	<?php if( $this->paginator->count() > 1 ): ?>
	  <div>
	    <br />
	    <?php echo $this->paginationControl($this->paginator) ?>
	    <br />
	  </div>
	<?php endif; ?>
	
	<ul class="sitepage_sitepages">
	  <?php foreach( $this->paginator as $topic ):
	      $lastpost = $topic->getLastPost();
	      $lastposter = $topic->getLastPoster();
	      ?>
	    <li>
	      <div class="sitepage_sitepages_replies">
	        <span>
	          <?php echo $this->locale()->toNumber($topic->post_count - 1) ?>
	        </span>
	        <?php echo $this->translate(array('reply', 'replies', $topic->post_count - 1)) ?>
	      </div>
	      <div class="sitepage_sitepages_lastreply">
	        <?php echo $this->htmlLink($lastposter->getHref(), $this->itemPhoto($lastposter, 'thumb.icon')) ?>
	        <div class="sitepage_sitepages_lastreply_info">
	          <?php echo $this->htmlLink($lastpost->getHref(), $this->translate('Last Post')) ?> <?php echo $this->translate('by');?> <?php echo $lastposter->__toString() ?>
	          <br />
	          <?php echo $this->timestamp(strtotime($topic->modified_date), array('tag' => 'div', 'class' => 'sitepage_sitepages_lastreply_info_date')) ?>
	        </div>
	      </div>
	      <div class="sitepage_sitepages_info">
	        <h3<?php if( $topic->sticky ): ?> class='sitepage_sitepages_sticky'<?php endif; ?>>
	          <?php echo $this->htmlLink($topic->getHref(), $topic->getTitle()) ?>
            <?php if(($resource=$topic->getResource())!=null):?>
            <span style="float: right;">
            <?php echo $this->translate("In ".$resource->getMediaType().":") ?>
            <?php echo $this->htmlLink($resource->getHref(), $resource->getTitle()) ?>
          </span>
          <?php endif;?>
	        </h3>
          
	        <div class="sitepage_sitepages_blurb">
	          <?php echo $this->viewMore(strip_tags($topic->getDescription())) ?>
	        </div>
	      </div>
	    </li>
	  <?php endforeach; ?>
	</ul>
	
	<?php if( $this->paginator->count() > 1 ): ?>
	  <div>
	    <?php echo $this->paginationControl($this->paginator) ?>
	  </div>
	<?php endif; ?>
</div>	