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
<?php include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/common_style_css.tpl'; ?>

<?php $postedBy = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.postedby', 1);?>
<?php include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/payment_navigation_views.tpl'; ?>
<script type="text/javascript">
  en4.core.runonce.add(function(){

    <?php if( !$this->renderOne ): ?>
			var anchor = $('profile_sitepages_<?php echo $this->category_id?>').getParent();
			$('profile_sitepage_previous_<?php echo $this->category_id?>').style.display = '<?php echo ( $this->paginator->getCurrentPageNumber() == 1 ? 'none' : '' ) ?>';
			$('profile_sitepage_next_<?php echo $this->category_id?>').style.display = '<?php echo ( $this->paginator->count() == $this->paginator->getCurrentPageNumber() ? 'none' : '' ) ?>';

			$('profile_sitepage_previous_<?php echo $this->category_id?>').removeEvents('click').addEvent('click', function(){
				en4.core.request.send(new Request.HTML({
					url : en4.core.baseUrl + 'widget/index/content_id/' + <?php echo sprintf('%d', $this->identity) ?>,
					data : {
						format : 'html',
						subject : en4.core.subject.guid,
						page : <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() - 1) ?>,
            isajax: 1
					}
				}), {
					'element' : anchor
				})
			});

			$('profile_sitepage_next_<?php echo $this->category_id?>').removeEvents('click').addEvent('click', function(){
				en4.core.request.send(new Request.HTML({
					url : en4.core.baseUrl + 'widget/index/content_id/' + <?php echo sprintf('%d', $this->identity) ?>,
					data : {
						format : 'html',
						subject : en4.core.subject.guid,
						page : <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() + 1) ?>,
            isajax: 1
					}
				}), {
					'element' : anchor
				})
			});
    <?php endif; ?>
  });
</script>
<div class="sitepage_view_select">
<h3 class="fleft"><?php echo $this->translate('Pages I Joined'); ?></h3>
</div>
<ul id="profile_sitepages_<?php echo $this->category_id;?>"  class="sitepages_profile_tab">
<?php if ($this->paginator->getTotalItemCount() > 0) : ?>
  <?php foreach ($this->paginator as $item): ?>
  <?php //$isPageAdmin = $this->sitepage->isPageAdmin($item->owner_id); ?>
    				<li <?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.fs.markers', 1)):?><?php if($item->featured):?>class="lists_highlight"<?php endif;?><?php endif;?>>
			<?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.fs.markers', 1)):?>
					<?php if($item->featured):?>
						<?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/images/featured-label.png', '',  array('title' => 'Featured','class' => 'sitepage_featured_label')) ?>
				<?php endif;?>
			<?php endif;?>
      <div class='sitepages_profile_tab_photo'>
        <?php echo $this->htmlLink(Engine_Api::_()->sitepage()->getHref($item->page_id, $item->owner_id, $item->getSlug()), $this->itemPhoto($item, 'thumb.normal', '', array('align' => 'left'))) ?>
				<?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.fs.markers', 1)):?>
          <?php if (!empty($item->sponsored)): ?>
						<?php $sponsored = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.sponsored.image', 1);
						if (!empty($sponsored)) { ?>
							<div class="sitepage_sponsored_label" style='background: <?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.sponsored.color', '#fc0505'); ?>;'>
								<?php echo $this->translate('SPONSORED'); ?>                 
							</div>
						<?php } ?>
					<?php endif; ?>
				<?php endif; ?>
      </div>
      <div class='sitepages_profile_tab_info'>
        <div class='sitepages_profile_tab_title'>
          <?php echo $this->htmlLink(Engine_Api::_()->sitepage()->getHref($item->page_id, $item->owner_id, $item->getSlug()), $item->getTitle()) ?>
          <div class="fright">
            <?php if ($item->closed): ?>
              <span>
                <?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/images/close.png', '', array('class' => 'icon', 'title' => $this->translate('Closed'))) ?>
              </span>
            <?php endif; ?>
            <span>
                <?php if(!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.fs.markers', 1)) :?>
                  <?php if ($item->sponsored == 1): ?>
                    <?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/images/sponsored.png', '', array('class' => 'icon', 'title' => $this->translate('Sponsored'))) ?>
                  <?php endif; ?>
                  <?php if ($item->featured == 1): ?>
                    <?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/images/sitepage_goldmedal1.gif', '', array('class' => 'icon', 'title' => $this->translate('Featured'))) ?>
                  <?php endif; ?>
                <?php endif; ?>
            </span>
          </div>
          <div class="clr"></div>
        </div>
        <div class='sitepages_browse_info_date  seaocore_txt_light'>
          <?php echo $this->timestamp(strtotime($item->creation_date)) ?> 
          <?php if($postedBy):?>
            - <?php echo $this->translate('posted by'); ?>
            <?php echo $this->htmlLink($item->getOwner()->getHref(), $item->getOwner()->getTitle()) ?>,
          <?php endif; ?>
          <?php echo $this->translate(array('%s like', '%s likes', $item->like_count), $this->locale()->toNumber($item->like_count)) ?>,
          <?php echo $this->translate(array('%s comment', '%s comments', $item->comment_count), $this->locale()->toNumber($item->comment_count)) ?>,
          <?php echo $this->translate(array('%s view', '%s views', $item->view_count), $this->locale()->toNumber($item->view_count)) ?>
        </div>
        
        <?php if (!empty($item->page_owner_id)) : ?>
					<div class='sitepages_browse_info_date  seaocore_txt_light'>
						<?php if ($item->page_owner_id == $item->owner_id) : ?>
						<?php echo $this->translate("PAGEMEMBER_OWNER"); ?>
						<?php else: ?>
						<?php echo $this->translate("PAGEMEMBER_MEMBER"); ?>
						<?php endif; ?>
					<div>
        <?php endif; ?>
        
        <div class='sitepages_browse_info_blurb'>
          <?php
          // Not mbstring compat
          echo substr(strip_tags($item->body), 0, 350);
          if (strlen($item->body) > 349)
            echo $this->translate("...");
          ?>
        </div>
      </div>
    </li>
  <?php endforeach; ?>
  <?php else : ?>
  			<div class='tip'>
				<span>
					<?php echo $this->translate("There are no pages joined by you."); ?>
				</span>
			</div>
			<?php endif; ?>
</ul>
<div class="clr"></div>
<?php echo $this->paginationControl($this->paginator); ?>
<!--<div>
  <div id="profile_sitepage_previous_<?php //echo $this->category_id?>" class="paginator_previous">
    <?php //echo $this->htmlLink('javascript:void(0);', $this->translate('Previous'), array('onclick' => '', 'class' => 'buttonlink icon_previous')); ?>
  </div>
  <div id="profile_sitepage_next_<?php //echo $this->category_id?>" class="paginator_next">
    <?php //echo $this->htmlLink('javascript:void(0);', $this->translate('Next'), array(      'onclick' => '',      'class' => 'buttonlink_right icon_next'    )); ?>
  </div>
</div>-->