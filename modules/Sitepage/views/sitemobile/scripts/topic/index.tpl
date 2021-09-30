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
// include APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/Adintegration.tpl';
?>
<?php
$this->headLink()
        ->appendStylesheet($this->layout()->staticBaseUrl
                . 'application/modules/Sitepagediscussion/externals/styles/style_sitepagediscussion.css')
?>
<?php //include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/payment_navigation_views.tpl'; ?>


<div data-role="navbar" role="navigation" data-iconpos="right">
	<ul>
		<li><a href="<?php echo $this->sitepage->getHref(); ?>"  data-icon="arrow-r"><?php echo $this->sitepage->getTitle();?></a></li>
		<li><a class="ui-btn-active ui-state-persist" data-icon="arrow-d"><?php echo $this->translate('Discussions');?></a></li>
	</ul>
</div>
<!--photo * breadcrumb apply widget here-->
<!--<div class="sitepage_viewpages_head">
  <?php echo $this->htmlLink($this->sitepage->getHref(), $this->itemPhoto($this->sitepage, 'thumb.icon', '', array('align' => 'left'))) ?>
  <h2>	
    <?php echo $this->sitepage->__toString() ?>	
    <?php echo $this->translate('&raquo; '); ?>
    <?php echo $this->htmlLink(array('route' => 'sitepage_entry_view', 'page_url' => Engine_Api::_()->sitepage()->getPageUrl($this->sitepage->page_id), 'tab' => $this->tab_selected_id), $this->translate('Discussions')) ?>
  </h2>  
</div>-->


  <?php if ($this->paginator->count() > 1): ?>
    <div>
      <br />
      <?php echo $this->paginationControl($this->paginator) ?>
      <br />
    </div>
  <?php endif; ?>

  <ul  data-role="listview" data-inset="false" data-icon="false">
    <?php
    foreach ($this->paginator as $topic):
      $lastpost = $topic->getLastPost();
      $lastposter = $topic->getLastPoster();
      ?>
      <li id="sitepagenote-item-<?php echo $topic->topic_id ?>">
<!--        <a href="<?php echo $topic->getHref(); ?>">
            <?php //echo $this->itemPhoto($lastposter, 'thumb.icon') ?>
          <strong<?php if ($topic->sticky): ?> class='sitepage_sitepages_sticky'<?php endif; ?>>
            <?php echo $topic->getTitle() ?>
              <?php if (($resource = $topic->getResource()) != null): ?>
              <span style="float: right;">
                <?php echo $this->translate("In " . $resource->getMediaType() . ":") ?>
              <?php echo $resource->getTitle() ?>
              </span>
  <?php endif; ?>
          </strong>
          <p>
            <?php echo $this->locale()->toNumber($topic->post_count - 1) ?>       
            <?php echo $this->translate(array('reply', 'replies', $topic->post_count - 1)) ?>
            -
  <?php echo $this->translate('Last Post by') ?> <?php echo $lastposter->__toString() ?>
          </p>
          <p><?php echo $this->timestamp(strtotime($topic->modified_date), array('tag' => 'div', 'class' => 'sitepage_sitepages_lastreply_info_date')) ?>
          </p>
  <?php //echo $this->viewMore(strip_tags($topic->getDescription()))  ?>
</a>-->
        
        <a href="<?php echo $topic->getHref();?>">
           <?php echo $this->itemPhoto($lastposter, 'thumb.icon') ?>
					<h3<?php if( $topic->sticky ): ?> class='sitepage_sitepages_sticky'<?php endif; ?>>
						<?php echo $topic->getTitle() ?>
            <?php if (($resource = $topic->getResource()) != null): ?>
<!--              <span style="float: right;">-->
                <?php echo $this->translate("In " . $resource->getMediaType() . ":") ?>
              <?php echo $resource->getTitle() ?>
<!--              </span>-->
  <?php endif; ?>
					</h3>
					<p class="ui-li-aside"><strong> <?php echo $this->translate(array('%s reply', '%s replies', $topic->post_count-1),$this->locale()->toNumber($topic->post_count-1)) ?></strong></p>
					<p><?php echo $this->translate('Last Post') ?> <?php echo $this->translate('by');?> <strong><?php echo $lastposter->getTitle() ?></strong></p>
          <p><?php echo $this->timestamp(strtotime($topic->modified_date)) ?>
          </p>
				</a>
      </li>
<?php endforeach; ?>
  </ul>

    <?php if ($this->paginator->count() > 1): ?>
    <div>
    <?php echo $this->paginationControl($this->paginator) ?>
    </div>
<?php endif; ?>
