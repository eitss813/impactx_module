<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitealbum
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2011-08-026 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php $this->headLink()->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/styles/style_board.css'); ?>
<?php
$this->headScript()
        ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitealbum/externals/scripts/core.js')
        ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/pinboard/pinboard.js')
        ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/pinboard/mooMasonry.js');
?>
<?php $this->headLink()->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitealbum/externals/styles/style_sitealbum.css'); ?>

<?php if ($this->autoload): ?>
  <div id="pinboard_<?php echo $this->identity ?>">
    <?php if (isset($this->params['defaultLoadingImage']) && $this->params['defaultLoadingImage']): ?>
      <div class="sitealbum_profile_loading_image"></div>
    <?php endif; ?>
  </div>
  <script type="text/javascript">
    var layoutColumn = 'middle';
    if ($("pinboard_<?php echo $this->identity ?>").getParent('.layout_left')) {
      layoutColumn = 'left';
    } else if ($("pinboard_<?php echo $this->identity ?>").getParent('.layout_right')) {
      layoutColumn = 'right';
    }
    PinBoardSeaoObject[layoutColumn].add({
      contentId: 'pinboard_<?php echo $this->identity ?>',
      widgetId: '<?php echo $this->identity ?>',
      totalCount: '<?php echo $this->totalCount ?>',
      requestParams:<?php echo json_encode($this->params) ?>,
      detactLocation: <?php echo $this->detactLocation; ?>,
      responseContainerClass: 'layout_sitealbum_pinboard_albums_sitealbum'
    });

  </script>
<?php else: ?>
  <?php if (!$this->autoload && !$this->is_ajax_load): ?> 
    <div id="pinboard_<?php echo $this->identity ?>"></div>
    <script type="text/javascript">
      en4.core.runonce.add(function() {
        var pinBoardViewMore = new PinBoardSeaoViewMore({
          contentId: 'pinboard_<?php echo $this->identity ?>',
          widgetId: '<?php echo $this->identity ?>',
          totalCount: '<?php echo $this->totalCount ?>',
          viewMoreId: 'seaocore_view_more_<?php echo $this->identity ?>',
          loadingId: 'seaocore_loading_<?php echo $this->identity ?>',
          requestParams:<?php echo json_encode($this->params) ?>,
          responseContainerClass: 'layout_sitealbum_pinboard_albums_sitealbum'
        });
        PinBoardSeaoViewMoreObjects.push(pinBoardViewMore);
      });
    </script>
  <?php endif; ?>
 
    <?php
      if ($this->params['itemWidth'] > $this->normalLargePhotoWidth):
        $photo_type = 'thumb.main';
      elseif ($this->params['itemWidth'] > $this->normalPhotoWidth):
        $photo_type = 'thumb.medium';
      else:
        $photo_type = 'thumb.normal';
      endif;
      ?>
  <?php $countButton = count($this->show_buttons); ?>
  <?php foreach ($this->events as $sitealbum): ?>
    <?php
    $noOfButtons = $countButton;
    if ($this->show_buttons):
      $alllowComment = (in_array('comment', $this->show_buttons) || in_array('like', $this->show_buttons)) && $sitealbum->authorization()->isAllowed($this->viewer(), "comment");
      if (in_array('comment', $this->show_buttons) && !$alllowComment) {
        $noOfButtons--;
      }
      if (in_array('like', $this->show_buttons) && !$alllowComment) {
        $noOfButtons--;
      }
    endif;
    ?>
    <div class="seaocore_list_wrapper" style="width:<?php echo $this->params['itemWidth'] ?>px;">
      <div class="seaocore_board_list b_medium" style="width:<?php echo $this->params['itemWidth'] - 18 ?>px;">
        <div>
          <div class="seaocore_board_list_thumb">
            <a href="<?php echo $sitealbum->getHref() ?>" class="seaocore_thumb">
              <table>
                <tr valign="middle">
                  <td>
                    <?php
                    $options = array('align' => 'center');

                    if (isset($this->params['withoutStretch']) && $this->params['withoutStretch']):
                      $options['style'] = 'width:auto; max-width:' . ($this->params['itemWidth'] - 18) . 'px;';
                    endif;
                    ?>  
                    <?php echo $this->itemPhoto($sitealbum, ($sitealbum->photo_id <= $this->sitealbum_last_photoid) ? 'thumb.main' : $photo_type, '', $options); ?>
                  </td> 
                </tr> 
              </table>
            </a>
          </div>
          
          <?php if ((!empty($this->statistics) && (in_array('categoryLink', $this->statistics))) || (!empty($this->userComment)) || (!empty($this->show_buttons))): ?>
						<?php if (!empty($this->statistics) && in_array('ownerName', $this->statistics)): ?>   
						<div class="seaocore_board_list_btm">
						<?php endif; ?>
						
              <?php if (!empty($this->statistics) && in_array('ownerName', $this->statistics)): ?>                
                <?php echo $this->itemPhoto($sitealbum->getOwner(), 'thumb.icon'); ?>                 
                <div class="o_hidden seaocore_stats seaocore_txt_light">            
                  <b> <?php echo $this->htmlLink($sitealbum->getOwner()->getHref(), $sitealbum->getOwner()->getTitle(), array('class' => 'thumbs_author')) ?> </b>
                </div>
              <?php endif; ?>  
                         
 						<?php if (!empty($this->statistics) && in_array('ownerName', $this->statistics)): ?>   
							</div>
						<?php endif; ?>
              
              <?php if (!empty($this->userComment)) : ?>
                <div class="seaocore_board_list_comments o_hidden">
                  <?php echo $this->action("list", "pin-board-comment", "seaocore", array("type" => $sitealbum->getType(), "id" => $sitealbum->album_id, 'widget_id' => $this->identity)); ?>
                </div>
              <?php endif; ?>

              <?php if (!empty($this->show_buttons)): ?>
                <div class="seaocore_board_list_action_links">
                  <?php $urlencode = urlencode(((!empty($_ENV["HTTPS"]) && 'on' == strtolower($_ENV["HTTPS"])) ? "https://" : "http://") . $_SERVER['HTTP_HOST'] . $sitealbum->getHref(array('showEventType' => $this->showEventType))); ?>

                  <?php if ((in_array('comment', $this->show_buttons) || in_array('like', $this->show_buttons)) && $alllowComment && !empty($this->userComment)): ?>
                    <?php if (in_array('comment', $this->show_buttons)): ?>
                      <a href='javascript:void(0);' onclick="en4.seaocorepinboard.comments.addComment('<?php echo $sitealbum->getGuid() . "_" . $this->identity ?>')" class="seaocore_board_icon comment_icon" title="Comment"><!--<?php echo $this->translate('Comment'); ?>--></a> 
                    <?php endif; ?>
                    <?php if (in_array('like', $this->show_buttons)): ?>
                      <a href="javascript:void(0)" title="Like" class="seaocore_board_icon like_icon <?php echo $sitealbum->getGuid() ?>like_link" id="<?php echo $sitealbum->getType() ?>_<?php echo $sitealbum->getIdentity() ?>like_link" <?php if ($sitealbum->likes()->isLike($this->viewer())): ?> style="display: none;" <?php endif; ?> onclick="en4.seaocorepinboard.likes.like('<?php echo $sitealbum->getType() ?>', '<?php echo $sitealbum->getIdentity() ?>');" ><!--<?php echo $this->translate('Like'); ?>--></a>

                      <a  href="javascript:void(0)" title="Unlike" class="seaocore_board_icon unlike_icon <?php echo $sitealbum->getGuid() ?>unlike_link" id="<?php echo $sitealbum->getType() ?>_<?php echo $sitealbum->getIdentity() ?>unlike_link" <?php if (!$sitealbum->likes()->isLike($this->viewer())): ?> style="display:none;" <?php endif; ?> onclick="en4.seaocorepinboard.likes.unlike('<?php echo $sitealbum->getType() ?>', '<?php echo $sitealbum->getIdentity() ?>');"><!--<?php echo $this->translate('Unlike'); ?>--></a> 
                    <?php endif; ?>
                  <?php endif; ?>

                  <?php if (in_array('facebook', $this->show_buttons)): ?>
                    <?php echo $this->htmlLink('http://www.facebook.com/share.php?u=' . $urlencode . '&t=' . $sitealbum->getTitle(), $this->translate(''), array('class' => 'pb_ch_wd seaocore_board_icon fb_icon' , 'title' => 'Facebook')) ?>
                  <?php endif; ?>

                  <?php if (in_array('twitter', $this->show_buttons)): ?>
                    <?php echo $this->htmlLink('http://twitter.com/share?url=' . $urlencode . '&text=' . $sitealbum->getTitle(), $this->translate(''), array('class' => 'pb_ch_wd seaocore_board_icon tt_icon' , 'title' => 'Twitter')) ?> 
                  <?php endif; ?>

                  <?php if (in_array('pinit', $this->show_buttons)): ?>
                    <a href="http://pinterest.com/pin/create/button/?url=<?php echo $urlencode; ?>&media=<?php echo urlencode((!preg_match("~^(?:f|ht)tps?://~i", $sitealbum->getPhotoUrl('thumb.profile')) ? (((!empty($_ENV["HTTPS"]) && 'on' == strtolower($_ENV["HTTPS"])) ? "https://" : "http://") . $_SERVER['HTTP_HOST'] ) : '') . $sitealbum->getPhotoUrl('thumb.profile')); ?>&description=<?php echo $sitealbum->getTitle(); ?>"  class="pb_ch_wd seaocore_board_icon pin_icon" title="Pin It" ><!--<?php echo $this->translate('Pin It') ?>--></a>
                  <?php endif; ?>
                </div>
              <?php endif; ?>
            
            
          <?php endif; ?>  
        
          <div class="seaocore_board_list_cont">
            <?php if (!empty($this->statistics) && in_array('albumTitle', $this->statistics)): ?>
              <div class="seaocore_title">
                <?php echo $this->htmlLink($sitealbum, Engine_Api::_()->seaocore()->seaocoreTruncateText($sitealbum->getTitle(), $this->albumTitleTruncation)) ?>
              </div>
            <?php endif; ?>
            
            <?php if ($this->truncationDescription): ?>
              <div class="seaocore_description">
                <?php echo Engine_Api::_()->seaocore()->seaocoreTruncateText($sitealbum->getDescription(), $this->truncationDescription) ?>
              </div>  
            <?php endif; ?>
            
            <?php if (!empty($this->statistics) && in_array('totalPhotos', $this->statistics)): ?>
              <div class="seao_listings_stats">
                <i class="seao_icon_strip seao_icon seao_icon_photo" title="Photos"></i>
                <div title="<?php echo $this->translate(array('%s photo', '%s photos', $sitealbum->photos_count), $this->locale()->toNumber($sitealbum->photos_count)) ?>" class="o_hidden"><?php echo $this->translate(array('%s photo', '%s photos', $sitealbum->photos_count), $this->locale()->toNumber($sitealbum->photos_count)) ?></div>
              </div>
            <?php endif; ?>
            
            <?php echo $this->albumInfo($sitealbum, $this->statistics, array('truncationLocation' => $this->truncationLocation)); ?>
            
            <?php
            if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitealbum.category.enabled', 1) && !empty($this->statistics) && in_array('profileField', $this->statistics)):
              //CUSTOM FIELD DISPLAY WORK
              $this->addHelperPath(APPLICATION_PATH . '/application/modules/Sitealbum/View/Helper', 'Sitealbum_View_Helper');
              $fieldStructure = Engine_Api::_()->fields()->getFieldsStructurePartial($sitealbum);
              ?>
              <?php
              if (Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) :
                $fieldvalues = $this->FieldValueLoopQuickInfoSitealbum($sitealbum, $fieldStructure);
                ?>
                <?php if (!empty($fieldvalues)): ?>
                  <div class="sitealbum_quick_specs">
                    <?php echo $fieldvalues; ?>
                  </div>
                <?php endif; ?>
              <?php endif; ?>
            <?php endif; ?>
          </div>

          
            
        </div>
      </div></div>
    <?php endforeach; ?>

    <?php if (!$this->autoload && !$this->is_ajax_load): ?>
      <div class="seaocore_view_more mtop10 dnone" id="seaocore_view_more_<?php echo $this->identity ?>">
        <a href="javascript:void(0);" id="" class="buttonlink icon_viewmore"><?php echo$this->translate('View More') ?></a>
      </div>
      <div class="seaocore_loading dnone" id="seaocore_loading_<?php echo $this->identity ?>" >
        <img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/core/loading.gif" style="margin-right: 5px;">
        <?php echo $this->translate('Loading...') ?>
      </div>
    <?php endif; ?>
  <?php endif; ?>

