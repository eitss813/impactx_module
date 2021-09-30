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
include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/common_style_css.tpl';
$this->headScript()->appendFile($this->layout()->staticBaseUrl.'application/modules/Sitepage/externals/scripts/core.js');
?>
<?php $postedBy = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.postedby', 1);?>
<script type="text/javascript" >
	function owner(thisobj) {
		var Obj_Url = thisobj.href ;
		Smoothbox.open(Obj_Url);
	}
        
        $$('.layout_sitepage_profile_joined_sitepage').removeClass('layout_sitepage_profile_joined_sitepage').addClass('layout_sitepage_profile_joined_sitepage<?php echo $this->identity ?>');
</script>

<?php if($this->loaded_by_ajax):?>
  <script type="text/javascript">
    var params = {
      requestParams :<?php echo json_encode($this->params) ?>,
      responseContainer :$$('.layout_sitepage_profile_joined_sitepage<?php echo $this->identity ?>')
    };
    en4.sitepage.ajaxTab.attachEvent('<?php echo $this->identity ?>',params);
  </script>
<?php endif;?>
  
<?php if($this->showContent): ?> 
    <script type="text/javascript">
      en4.core.runonce.add(function(){

        <?php if( !$this->renderOne ): ?>
          var anchor = $('profile_sitepagemembers_<?php echo $this->identity?>').getParent();
          $('profile_sitepagemember_previous_<?php echo $this->identity?>').style.display = '<?php echo ( $this->paginator->getCurrentPageNumber() == 1 ? 'none' : '' ) ?>';
          $('profile_sitepagemember_next_<?php echo $this->identity?>').style.display = '<?php echo ( $this->paginator->count() == $this->paginator->getCurrentPageNumber() ? 'none' : '' ) ?>';

          $('profile_sitepagemember_previous_<?php echo $this->identity?>').removeEvents('click').addEvent('click', function(){
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
            });
          });

          $('profile_sitepagemember_next_<?php echo $this->identity?>').removeEvents('click').addEvent('click', function(){
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
            });
          });
        <?php endif; ?>
      });
    </script>
    <ul id="profile_sitepagemembers_<?php echo $this->identity;?>"  class="sitepages_profile_tab">
      <?php if ($this->subject_id == $this->viewer_id) : ?>
      <li>
        <?php echo $this->htmlLink(array('route' => 'sitepage_profilepagemember', 'action' => 'joined-more-pages', 'user_id' => $this->viewer_id), $this->translate('Join More Pages'), array('onclick' => 'owner(this);return false', ' class' => 'buttonlink icon_sitepage_join_page')); ?>
      </li>
      <?php endif; ?>
      <?php foreach ($this->paginator as $item): //print_r($item->toArray());die; ?>
      <?php //$isPageAdmin = $this->sitepage->isPageAdmin($item->owner_id); ?>
                <li <?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.fs.markers', 1)):?><?php if($item->featured):?> class="lists_highlight"<?php endif;?><?php endif;?>>
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
                <?php if ($this->ratngShow): ?>
                  <?php if (($item->rating > 0)): ?>

                    <?php
                    $currentRatingValue = $item->rating;
                    $difference = $currentRatingValue - (int) $currentRatingValue;
                    if ($difference < .5) {
                      $finalRatingValue = (int) $currentRatingValue;
                    } else {
                      $finalRatingValue = (int) $currentRatingValue + .5;
                    }
                    ?>

                    <span title="<?php echo $finalRatingValue . $this->translate(' rating'); ?>">
                      <?php for ($x = 1; $x <= $item->rating; $x++): ?>
                        <span class="rating_star_generic rating_star" ></span>
                      <?php endfor; ?>
                      <?php if ((round($item->rating) - $item->rating) > 0): ?>
                        <span class="rating_star_generic rating_star_half" ></span>
                      <?php endif; ?>
                    </span>
                  <?php endif; ?>
                <?php endif; ?>

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

                <?php if (!empty($item->page_owner_id)) : ?>
                  <span>
                  <?php if ($this->viewer_id != $item->owner_id && $this->subject_id == $this->viewer_id) : ?>
                    <?php echo $this->htmlLink(array('route' => 'sitepage_profilepagemember', 'action' => 'leave', 'page_id' => $item->page_id), $this->translate('Leave Page'), array('onclick' => 'owner(this);return false', ' class' => 'buttonlink icon_sitepage_leave')); ?>
                  <?php endif; ?>
                  </span>
                <?php endif; ?>
              </div>
              <div class="clr"></div>
            </div>
            <div class='sitepages_browse_info_date  seaocore_txt_light'>
                <?php echo $this->timestamp(strtotime($item->creation_date)) ?> 
                <?php if($postedBy && $item->show_name):?>
                  - <?php echo $this->translate('posted by'); ?>
                  <?php echo $this->htmlLink($item->getOwner()->getHref(), $item->getOwner()->getTitle()) ?>,
              <?php endif; ?>
              <?php echo $this->translate(array('%s comment', '%s comments', $item->comment_count), $this->locale()->toNumber($item->comment_count)) ?>,

              <?php if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemember')) : ?>
                <?php $memberTitle = Engine_Api::_()->getApi('settings', 'core')->getSetting( 'pagemember.member.title' , 1);
                if ($item->member_title && $memberTitle) : ?>
                      <?php echo $item->member_count . ' ' . $item->member_title; ?>,
                <?php else : ?>
                    <?php echo $this->translate(array('%s member', '%s members', $item->member_count), $this->locale()->toNumber($item->member_count)) ?>,
                <?php endif; ?>
              <?php endif; ?>

              <?php $sitepagereviewEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagereview'); ?>
              <?php if ($sitepagereviewEnabled): ?>
                <?php echo $this->translate(array('%s review', '%s reviews', $item->review_count), $this->locale()->toNumber($item->review_count)) ?>,
              <?php endif; ?>

              <?php echo $this->translate(array('%s view', '%s views', $item->view_count), $this->locale()->toNumber($item->view_count)) ?>,
              <?php echo $this->translate(array('%s like', '%s likes', $item->like_count), $this->locale()->toNumber($item->like_count)) ?>
            </div>
            <div class='sitepages_browse_info_blurb clr'>
                <?php if (!empty($item->page_owner_id)) : ?>
                  <div class='sitepages_profile_info_member'>
                      <?php if ($this->viewer_id == $item->owner_id) : ?>
                      <i class="icon_sitepages_page-owner"><?php echo $this->translate("PAGEMEMBER_OWNER"); ?></i>
                      <?php else: ?>
                      <?php if(!empty($this->showMemberText)) : ?>
                        <i class="icon_sitepage_page-member" title="Members be called">
                        <?php if (empty($item->member_title)) : ?>
                          <?php echo $this->translate("PAGEMEMBER_MEMBER"); ?>
                        <?php else: ?>
                          <?php echo $item->member_title; ?>
                        <?php endif ?>
                        </i>
                      <?php endif; ?>
                      <?php endif; ?>
                  </div>
                <?php endif; ?>
                <?php if (!empty($this->textShow)) : ?>
                  <?php if (!empty($item->page_owner_id)) : ?>
                    <div class='sitepages_profile_info_member'>
                        <?php if ($this->viewer_id != $item->owner_id && empty($item->member_approval)) : ?>
                            <i class="icon_sitepage_verified_page"><?php echo $this->translate($this->textShow); ?></i>
                        <?php endif; ?>
                    </div>
                  <?php endif; ?>
                <?php endif; ?>
            </div>


            <?php $roleName = Engine_Api::_()->getDbtable('membership', 'sitepage')->userRoles(array('page_id' => $item->page_id, 'user_id' => Engine_Api::_()->core()->getSubject('user')->user_id));
            $roleCount = count(json_decode($roleName)) ?>
            <?php if($roleName && Engine_Api::_()->core()->getSubject('user')->user_id): ?>
              <div class='sitepages_browse_info_blurb clr'>
                <div class='sitepages_profile_info_member' >
                 <?php if($roleName): ?><i class="icon_sitepage_page-member-role" <?php if($roleCount == 1) : ?> title="Role" <?php else: ?> title="Roles" <?php endif; ?>><?php echo implode(", ", json_decode($roleName)); ?></i><?php endif; ?>
                </div>
              </div>
            <?php endif; ?>
            <div class='sitepages_browse_info_blurb clr'>
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
    </ul>
    <div>
      <div id="profile_sitepagemember_previous_<?php echo $this->identity?>" class="paginator_previous">
        <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Previous'), array(
          'onclick' => '',
          'class' => 'buttonlink icon_previous'
        )); ?>
      </div>
      <div id="profile_sitepagemember_next_<?php echo $this->identity?>" class="paginator_next">
        <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Next'), array(
          'onclick' => '',
          'class' => 'buttonlink_right icon_next'
        )); ?>
      </div>
    </div>
<?php endif; ?>