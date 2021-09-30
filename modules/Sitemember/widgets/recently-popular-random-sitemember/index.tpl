<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemember
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2014-07-20 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
$this->headLink()->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/styles/styles.css')
        ->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitemember/externals/styles/style_sitemember.css');
include_once APPLICATION_PATH . '/application/modules/Sitemember/views/scripts/infotooltip.tpl';

?>
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitemember/externals/scripts/core.js'); ?>
<?php
if ($this->is_ajax_load):
  ?>
  <?php if (empty($this->is_ajax)):
    ?>
    <div class="layout_core_container_tabs">
      <?php if ($this->tabCount > 1 || count($this->layouts_views) > 1): ?>
        <div class="tabs_alt tabs_parent tabs_parent_sitemember_home">
          <ul id="main_tabs" identity='<?php echo $this->identity ?>'>
            <?php if ($this->tabCount > 1): ?>
              <?php foreach ($this->tabs as $key => $tab): ?>
                <li class="tab_li_<?php echo $this->identity ?> <?php echo $key == 0 ? 'active' : ''; ?>" rel="<?php echo $tab; ?>">
                  <a  href='javascript:void(0);' ><?php echo $this->translate(ucwords(str_replace('_', ' ', $tab))); ?> </a>
                </li>
              <?php endforeach; ?>
            <?php endif; ?>
            <?php if (count($this->layouts_views) > 2) : ?>
              <?php
              for ($i = count($this->layouts_views) - 1; $i >= 0; $i--):
                ?>
                <li class="seaocore_tab_select_wrapper fright" rel='<?php echo $this->layouts_views[$i] ?>'>
                  <div class="seaocore_tab_select_view_tooltip"><?php echo $this->translate(ucwords(str_replace('_', ' ', $this->layouts_views[$i]))) ?></div>
                  <span id="<?php echo $this->layouts_views[$i] . "_" . $this->identity ?>"class="seaocore_tab_icon tab_icon_<?php echo $this->layouts_views[$i] ?>" onclick="sitememberTabSwitchview($(this));" ></span>

                </li>
              <?php endfor; ?>
            <?php endif; ?>
          </ul>
        </div>
      <?php endif; ?>
      <div id="dynamic_app_info_sitemember_<?php echo $this->identity; ?>">
      <?php endif; ?>
      <?php
      if (in_array('list_view', $this->layouts_views)):
        ?>
        <div class="sitemember_container <?php if(!empty($this->circularImage)):?> sitemember_circular_container <?php endif;?>" id="list_view_sitemember_" style="<?php echo $this->defaultLayout !== 'list_view' ? 'display: none;' : '' ?>">
          <ul class="sitemember_browse_list sitemember_list_view">
            <?php if ($this->totalCount): ?>
              <?php foreach ($this->paginator as $sitemember): ?>
                <?php $rel = 'user' . ' ' . $sitemember->user_id; ?>

                <?php
                if ($this->listViewType == 'list'):
                  ?>
                  <li class="b_medium" style="height:<?php echo $this->commonColumnHeight; ?>px;<?php if($this->listFullWidthElement):?>width:98%;<?php endif;?>">
                    <div class='sitemember_browse_list_photo b_medium'>

                      <?php if (!empty($this->statistics) && in_array('featuredLabel', $this->statistics) && $sitemember->featured):
                        ?>
                        <i class="seaocore_list_featured_label" title="<?php echo $this->translate('Featured'); ?>"><?php echo $this->translate('Featured'); ?></i>
                      <?php endif; ?>
<?php $rel = 'user' . ' ' . $sitemember->user_id; ?>
                        <?php if($this->circularImage):?>
                        <?php
                        $url = $sitemember->getPhotoUrl('thumb.profile');
                        if (empty($url)): $url = $this->layout()->staticBaseUrl . 'application/modules/Sitemember/externals/images/nophoto_user_thumb_normal.png';
                        endif;
                        ?>
                        

                        <a href="<?php echo $sitemember->getHref() ?>" class ="sitemember_thumb sea_add_tooltip_link" rel="<?php echo $rel ?>" >
                        <span style="background-image: url(<?php echo $url; ?>);"></span>
                        </a>
                    <?php else:?>

                      <?php echo $this->htmlLink($sitemember->getHref(), $this->itemPhoto($sitemember, 'thumb.profile', '', array('align' => 'center', 'class' => 'sea_add_tooltip_link', 'rel' => "$rel"))); ?>
                    <?php endif;?>
                      <?php if (!empty($this->statistics) && in_array('sponsoredLabel', $this->statistics) && !empty($sitemember->sponsored)): ?>
                        <div class="seaocore_list_sponsored_label" style="background: <?php echo $this->settings->getSetting('sitemember_sponsoredcolor', '#FC0505'); ?>">
                          <?php echo $this->translate('Sponsored'); ?>
                        </div>
                      <?php endif;
                      ?>
                    </div>

                    <div class='sitemember_browse_list_info'>
                      <div class='sitemember_browse_list_info_header'>
                        <div class="seaocore_browse_list_info_title o_hidden">
                          <?php echo $this->htmlLink($sitemember->getHref(), Engine_Api::_()->seaocore()->seaocoreTruncateText($sitemember->getTitle(), $this->title_truncationList), array('title' => $sitemember->getTitle(), 'class' => 'sea_add_tooltip_link', 'rel' => "$rel")); ?>
                          <?php
                          if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteverify') && Engine_Api::_()->authorization()->getPermission($sitemember->level_id, 'siteverify', 'allow_verify') && !empty($this->statistics) && in_array('verifyLabel', $this->statistics)) :
                            $verify_count = Engine_Api::_()->getDbtable('verifies', 'siteverify')->getVerifyCount($sitemember->user_id);
                            $user = Engine_Api::_()->getItem('user', $sitemember->user_id);
                            $verify_limit = Engine_Api::_()->authorization()->getPermission($user->level_id, 'siteverify', 'verify_limit');
                            ?>
                            <?php if ($verify_count >= $verify_limit): ?>
                              <span class="siteverify_tip_wrapper">
                                <i class="sitemember_list_verify_label mleft5"></i>
                                <span class="siteverify_tip"><?php echo $this->translate('Verified'); ?><i></i></span>
                              </span>
                              <?php
                            endif;
                          endif;
                          ?>
                          <?php
                          if (!empty($this->statistics) && in_array('memberStatus', $this->statistics)) :

                            $online_status = Engine_Api::_()->sitemember()->isOnline($sitemember->user_id);
                            ?>
                            <span class="fright seaocore_txt_light">
                              <?php if (!empty($online_status)) : ?>
                                <img title="Online" src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/online.png' alt="" class="fleft" />
                                <?php echo $this->translate("Online"); ?>
                              <?php endif; ?>
                            </span>
                          <?php endif; ?>
                        </div>
                      </div>

                      <div class='sitemember_browse_list_info'>
                        <?php if (!empty($this->statistics)) : ?>
                          <?php echo $this->memberInfo($sitemember, $this->statistics, array('customParams' => $this->customParams, 'custom_field_title' => $this->custom_field_title, 'custom_field_heading' => $this->custom_field_heading)); ?>
                        <?php endif; ?>     
                      </div>

                      <?php if(!empty($this->links)):?>
                        <div class="clr sitemember_action_link_options sitemember_action_links">
                        <?php $items = Engine_Api::_()->getItem('user', $sitemember->user_id); ?>
                        <?php if (Engine_Api::_()->seaocore()->canSendUserMessage($items) && !empty($this->viewer_id) && !empty($this->links) && in_array('messege', $this->links)): ?>
                          <a href="<?php echo $this->baseUrl() ?>/messages/compose/to/<?php echo $sitemember->user_id ?>" target="_parent" class="buttonlink sitemember_action_links_message"><?php echo $this->translate('Message'); ?></a>
                        <?php endif; ?>

                        <?php if (!empty($this->links) && in_array('addfriend', $this->links)): ?>
                          <?php $uaseFRIENFLINK = $this->userFriendshipAjax($this->user($sitemember->user_id)); ?>
                          <?php if (!empty($uaseFRIENFLINK)) : ?>
                            <?php echo $uaseFRIENFLINK; ?>
                          <?php endif; ?>
                        <?php endif; ?>
                        </div>
                      <?php endif; ?>
                      <?php if ($this->showDetailLink) : ?>
                        <div class="clr dblock fright sitemember_browse_list_info_btn">
                          <a href="<?php echo $sitemember->getHref(); ?>" class="sitemember_buttonlink"><?php echo $this->translate('Details &raquo;'); ?></a>
                        </div>
                      <?php endif; ?>
                    
                  </li>
                <?php else: ?>
                  <?php if (!empty($sitemember->sponsored)): ?>
                    <li class="list_sponsered b_medium">
                    <?php else: ?>
                    <li class="b_medium">
                    <?php endif; ?>

                    <div class='sitemember_browse_list_photo b_medium'>
                      <?php if (!empty($this->statistics) && in_array('featuredLabel', $this->statistics) && $sitemember->featured): ?>
                        <i class="seaocore_list_featured_label" title="<?php echo $this->translate('Featured'); ?>"><?php echo $this->translate('Featured'); ?></i>
                      <?php endif; ?>
                        <?php if($this->circularImage):?>
                        <?php
                        $url = $sitemember->getPhotoUrl('thumb.normal');
                        if (empty($url)): $url = $this->layout()->staticBaseUrl . 'application/modules/Sitemember/externals/images/nophoto_user_thumb_normal.png';
                        endif;
                        ?>
                        <a href="<?php echo $sitemember->getHref() ?>" class="sitemember_thumb"><span style="background-image: url(<?php echo $url; ?>);"></span></a>
                    <?php else:?>
                      <?php echo $this->htmlLink($sitemember->getHref(), $this->itemPhoto($sitemember, 'thumb.normal', '', array('align' => 'center'))) ?>
                        <?php endif;?>
                      <?php if (!empty($this->statistics) && in_array('sponsoredLabel', $this->statistics) && !empty($sitemember->sponsored)): ?>
                        <div class="seaocore_list_sponsored_label" style="background: <?php echo $this->settings->getSetting('sitemember_sponsoredcolor', '#FC0505'); ?>">
                          <?php echo $this->translate('Sponsored'); ?>
                        </div>
                      <?php endif; ?>
                    </div>

                    <div class="sitemember_browse_list_info">
                      <div class="sitemember_browse_list_info_header">
                        <div class="seaocore_browse_list_info_title">
                          <?php echo $this->htmlLink($sitemember->getHref(), Engine_Api::_()->seaocore()->seaocoreTruncateText($sitemember->getTitle(), $this->title_truncationList), array('title' => $sitemember->getTitle(), 'class' => 'sea_add_tooltip_link', 'rel' => "$rel")); ?>
                          <?php
                          if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteverify') && Engine_Api::_()->authorization()->getPermission($sitemember->level_id, 'siteverify', 'allow_verify') && !empty($this->statistics) && in_array('verifyLabel', $this->statistics)) :
                            $verify_count = Engine_Api::_()->getDbtable('verifies', 'siteverify')->getVerifyCount($sitemember->user_id);
                            $user = Engine_Api::_()->getItem('user', $sitemember->user_id);
                            $verify_limit = Engine_Api::_()->authorization()->getPermission($user->level_id, 'siteverify', 'verify_limit');
                            ?>
                            <?php if ($verify_count >= $verify_limit): ?>
                              <span class="siteverify_tip_wrapper">
                                <i class="sitemember_list_verify_label mleft5"></i>
                                <span class="siteverify_tip"><?php echo $this->translate('Verified'); ?><i></i></span>
                              </span>
                              <?php
                            endif;
                          endif;
                          ?>
                          <?php
                          if (!empty($this->statistics) && in_array('memberStatus', $this->statistics)) :

                            $online_status = Engine_Api::_()->sitemember()->isOnline($sitemember->user_id);
                            ?>
                            <span class="fright seaocore_txt_light">
                              <?php if (!empty($online_status)) : ?>
                                <img title="Online" src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/online.png' alt="" class="fleft" />
                                <?php echo $this->translate("Online"); ?>
                              <?php endif; ?>
                            </span>
                          <?php endif; ?>
                        </div>
                      </div>

                      <?php if (!empty($this->statistics)) : ?>
                        <?php echo $this->memberInfo($sitemember, $this->statistics, array('customParams' => $this->customParams, 'custom_field_title' => $this->custom_field_title, 'custom_field_heading' => $this->custom_field_heading)); ?>
                      <?php endif; ?>
                     <?php if(!empty($this->links)):?>
                        <div class="clr sitemember_action_link_options sitemember_action_links">  
                        <?php $items = Engine_Api::_()->getItem('user', $sitemember->user_id); ?>
                        <?php if (Engine_Api::_()->seaocore()->canSendUserMessage($items) && !empty($this->viewer_id) && in_array('messege', $this->links)): ?>
                          <a href="<?php echo $this->baseUrl() ?>/messages/compose/to/<?php echo $sitemember->user_id ?>" target="_parent" class="buttonlink sitemember_action_links_message"><?php echo $this->translate('Message'); ?></a>
                        <?php endif; ?>

                        <?php if ( in_array('addfriend', $this->links)): ?>
                          <?php $uaseFRIENFLINK = $this->userFriendshipAjax($this->user($sitemember->user_id)); ?>
                          <?php if (!empty($uaseFRIENFLINK)) : ?>
                            <?php echo $uaseFRIENFLINK; ?>
                          <?php endif; ?>
                        <?php endif; ?>
                          </div>
                       <?php endif; ?>
                    </div>
                    </div>
                  </li>
                <?php endif; ?>
              <?php endforeach; ?>
            <?php else: ?>
              <div class="tip">
                <span>
                  <?php echo $this->translate('No matching results were found for members.'); ?>
                </span>
              </div>
            <?php endif; ?>
          </ul>
        </div>
      <?php endif; ?>

      <?php if (in_array('grid_view', $this->layouts_views)): ?>

        <div class="image_view sitemember_container <?php if(!empty($this->circularImage)):?> sitemember_circular_container <?php endif;?>" id="grid_view_sitemember_" style="<?php echo $this->defaultLayout !== 'grid_view' ? 'display: none;' : '' ?>">
          <div class="sitemember_img_view">
            <?php if ($this->totalCount): ?>
              <?php $isLarge = ($this->columnWidth > 170); ?>
              <?php foreach ($this->paginator as $sitemember): ?>
                <?php $rel = 'user' . ' ' . $sitemember->user_id; ?>
                <div class="sitemember_grid_view <?php if (empty($this->titlePosition) || !empty($this->links) || !empty($this->statistics)) : ?> <?php if($this->circularImage):?> sitemember_grid_withhover <?php endif;?><?php endif;?>" style="width: <?php echo $this->columnWidth; ?>px;height:<?php echo $this->columnHeight; ?>px;">

                  <div class="sitemember_grid_thumb">
 
                    <?php if (!empty($this->statistics) && in_array('featuredLabel', $this->statistics) && $sitemember->featured): ?>
                      <i class="seaocore_list_featured_label" title="<?php echo $this->translate('Featured'); ?>"><?php echo $this->translate('Featured'); ?></i>
                    <?php endif; ?>

                    <a href="<?php echo $sitemember->getHref() ?>" class ="sitemember_thumb">
                      <?php
                      $url = $sitemember->getPhotoUrl($isLarge ? 'thumb.profile' : 'thumb.profile'); 
                      if (empty($url)): $url = $this->layout()->staticBaseUrl . 'application/modules/Sitemember/externals/images/nophoto_user_thumb_normal.png';
                      endif;
                      ?>
                      <span style="background-image: url(<?php echo $url; ?>); <?php if($this->circularImage):?>height:<?php echo $this->circularImageHeight; ?>px;<?php endif;?>"></span>
                    </a>
  
                    <?php if (!empty($this->titlePosition)) : ?>
                      <div class="sitemember_grid_title">
                        <?php echo $this->htmlLink($sitemember->getHref(), Engine_Api::_()->seaocore()->seaocoreTruncateText($sitemember->getTitle(), $this->title_truncationGrid), array('title' => $sitemember->getTitle(), 'class' => 'sea_add_tooltip_link', 'rel' => "$rel")); ?>
                        <?php
                        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteverify') && Engine_Api::_()->authorization()->getPermission($sitemember->level_id, 'siteverify', 'allow_verify') && !empty($this->statistics) && in_array('verifyLabel', $this->statistics)) :
                          $verify_count = Engine_Api::_()->getDbtable('verifies', 'siteverify')->getVerifyCount($sitemember->user_id);
                          $user = Engine_Api::_()->getItem('user', $sitemember->user_id);
                          $verify_limit = Engine_Api::_()->authorization()->getPermission($user->level_id, 'siteverify', 'verify_limit');
                          ?>
                          <?php if ($verify_count >= $verify_limit): ?>
                            <span class="siteverify_tip_wrapper">
                                <i class="sitemember_list_verify_label mleft5"></i>
                                <span class="siteverify_tip"><?php echo $this->translate('Verified'); ?><i></i></span>
                            </span>
                            <?php
                          endif;
                        endif;
                        ?>
                        <?php
                        if (!empty($this->statistics) && in_array('memberStatus', $this->statistics)) :
                          $online_status = Engine_Api::_()->sitemember()->isOnline($sitemember->user_id);
                          ?>
                          <span class="fright seaocore_txt_light">
                            <?php if (!empty($online_status)) : ?>
                              <img title="Online" src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/online.png' alt="" class="fleft" />
                              <!--<?php echo $this->translate("Online"); ?>-->
                            <?php endif; ?>
                          </span>
                        <?php endif; ?>
                      </div>
                    <?php endif; ?>
                  </div>

                  <?php if (!empty($this->statistics) && in_array('sponsoredLabel', $this->statistics) && !empty($sitemember->sponsored)): ?>
                    <div class="seaocore_list_sponsored_label" style='background: <?php echo $this->settings->getSetting('sitemember_sponsoredcolor', '#fc0505'); ?>;'>
                      <?php echo $this->translate('Sponsored'); ?>
                    </div>
                  <?php endif; ?>
                  <?php if (empty($this->titlePosition) || !empty($this->links) || !empty($this->statistics)) : ?>  
                  <div class="sitemember_grid_info">
                    <?php if (empty($this->titlePosition)) : ?>
                      <div class="sitemember_grid_title">
                        <?php echo $this->htmlLink($sitemember->getHref(), Engine_Api::_()->seaocore()->seaocoreTruncateText($sitemember->getTitle(), $this->title_truncationGrid), array('title' => $sitemember->getTitle(), 'class' => 'sea_add_tooltip_link', 'rel' => "$rel")) ?>
                        <?php
                        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteverify') && Engine_Api::_()->authorization()->getPermission($sitemember->level_id, 'siteverify', 'allow_verify') && !empty($this->statistics) && in_array('verifyLabel', $this->statistics)) :
                          $verify_count = Engine_Api::_()->getDbtable('verifies', 'siteverify')->getVerifyCount($sitemember->user_id);
                          $user = Engine_Api::_()->getItem('user', $sitemember->user_id);
                          $verify_limit = Engine_Api::_()->authorization()->getPermission($user->level_id, 'siteverify', 'verify_limit');
                          ?>
                          <?php if ($verify_count >= $verify_limit): ?>
                            <span class="siteverify_tip_wrapper">
                                <i class="sitemember_list_verify_label mleft5"></i>
                                <span class="siteverify_tip"><?php echo $this->translate('Verified'); ?><i></i></span>
                            </span>
                            <?php
                          endif;
                        endif;
                        ?>

                        <?php
                        if (!empty($this->statistics) && in_array('memberStatus', $this->statistics)) :
                          $online_status = Engine_Api::_()->sitemember()->isOnline($sitemember->user_id);
                          ?>
                          <span class="fright seaocore_txt_light">
                            <?php if (!empty($online_status)) : ?>
                              <img title="Online" src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/online.png' alt="" class="fleft" />
                              <!--<?php echo $this->translate("Online"); ?>-->
                            <?php endif; ?>
                          </span>
                        <?php endif; ?>

                      </div>
                    <?php endif; ?>

                    <?php if (!empty($this->statistics)) : ?>
                      <?php echo $this->memberInfo($sitemember, $this->statistics, array('customParams' => $this->customParams, 'custom_field_title' => $this->custom_field_title, 'custom_field_heading' => $this->custom_field_heading)); ?>
                    <?php endif; ?>

                    <?php if(!empty($this->links)):?>
                      <div class="clr sitemember_action_link_options sitemember_action_links">
                        <?php $items = Engine_Api::_()->getItem('user', $sitemember->user_id); ?>
                        <?php if (Engine_Api::_()->seaocore()->canSendUserMessage($items) && !empty($this->viewer_id) && !empty($this->links) && in_array('messege', $this->links)): ?>
                          <a href="<?php echo $this->baseUrl() ?>/messages/compose/to/<?php echo $sitemember->user_id ?>" target="_parent" class="buttonlink sitemember_action_links_message"><?php echo $this->translate('Message'); ?></a>
                        <?php endif; ?>
                        <?php if (in_array('addfriend', $this->links)): ?>
                          <?php $uaseFRIENFLINK = $this->userFriendshipAjax($this->user($sitemember->user_id)); ?>
                          <?php if (!empty($uaseFRIENFLINK)) : ?>
                            <?php echo $uaseFRIENFLINK; ?>
                          <?php endif; ?>
                        <?php endif; ?>
                      </div>
                    <?php endif; ?>
                  </div>
                    <?php endif; ?>
                </div>
              <?php endforeach; ?>
            <?php else: ?>
              <div class="tip">
                <span>
                  <?php echo $this->translate("No member with that criteria."); ?>
                </span>
              </div>
            <?php endif; ?>
          </div>
        </div>
      <?php endif; ?>
      <?php if ($this->enableLocation): ?>
        <div class="sitemember_container siteevent_map_view o_hiddden <?php if(!empty($this->circularImage)):?> sitemember_circular_container <?php endif;?>" id="map_view_sitemember_" style="<?php echo $this->defaultLayout !== 'map_view' ? 'display: none;' : '' ?>">
          <div class="seaocore_map clr" style="overflow:hidden;">
            <div id="rmap_canvas_<?php echo $this->identity ?>" class="sitemember_list_map"> </div>
            <?php $siteTitle = $this->settings->core_general_site_title; ?>
            <?php if (!empty($siteTitle)) : ?>
              <div class="seaocore_map_info"><?php echo $this->translate("Locations on %s", "<a href='' target='_blank'>$siteTitle</a>"); ?></div>
            <?php endif; ?>
          </div>
          <a  href="javascript:void(0);" onclick="srToggleBounce(<?php echo $this->identity ?>);" class="fleft sitemember_list_map_bounce_link" style="<?php echo $this->flagSponsored ? '' : 'display:none' ?>"> <?php echo $this->translate('Stop Bounce'); ?></a>
        </div>
      <?php endif; ?>
      <div class="seaocore_view_more mtop10" id="seaocore_view_more_<?php echo $this->identity ?>">
        <?php
        echo $this->htmlLink('javascript:void(0);', $this->translate('View More'), array(
            'id' => '',
            'class' => 'buttonlink icon_viewmore'
        ))
        ?>
      </div>
      <div class="clr" id="scroll_bar_height"></div>
      <div class="seaocore_loading" id="seaocore_loading_<?php echo $this->identity ?>" style="display: none;">
        <img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/core/loading.gif' style='margin-right: 5px;' />
        <?php echo $this->translate("Loading ...") ?>
      </div>
      <?php if (empty($this->is_ajax)): ?>

      </div>
      <script type="text/javascript">

                    function sendAjaxRequestSitemember(params) {
                      if ($('seaocore_view_more_<?php echo $this->identity ?>'))
                        $('seaocore_view_more_<?php echo $this->identity ?>').style.display = 'none';
                      if (!params.requestParams.tabbed || params.requestParams.scroll) {
                        if ($('seaocore_loading_<?php echo $this->identity ?>'))
                          $('seaocore_loading_<?php echo $this->identity ?>').style.display = '';
                      }
                      var url = en4.core.baseUrl + 'widget';

                      if (params.requestUrl)
                        url = params.requestUrl;

                      var request = new Request.HTML({
                        method: 'get',
                        url: url,
                        data: $merge(params.requestParams, {
                          format: 'html',
                          subject: en4.core.subject.guid,
                          is_ajax: true,
                          loaded_by_ajax: true,
                        }),
                        evalScripts: true,
                        onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
                          if (params.requestParams.page == 1) {
                            $('dynamic_app_info_sitemember_' + '<?php echo $this->identity ?>').style.display = '';
                            params.responseContainer.empty();
                            Elements.from(responseHTML).inject(params.responseContainer);
    <?php if ($this->enableLocation): ?>
                              srInitializeMap(params.requestParams.content_id);
    <?php endif; ?>
                            if ($$('.seaocore_content_loader')) {
                              $$('.seaocore_content_loader').setStyle('display', 'none');
                            }
                          } else {
                            if ($$('.seaocore_content_loader')) {
                              $$('.seaocore_content_loader').setStyle('display', 'none');
                            }
                            var element = new Element('div', {
                              'html': responseHTML
                            });
                            params.responseContainer.getElements('.seaocore_loading').setStyle('display', 'none');


                            if (element.getElement('.sitemember_list_view') && $$('.sitemember_list_view'))
                              Elements.from(element.getElement('.sitemember_list_view').innerHTML).inject(params.responseContainer.getElement('.sitemember_list_view'));

                            if (element.getElement('.sitemember_img_view') && $$('.sitemember_grid_view'))
                              Elements.from(element.getElement('.sitemember_img_view').innerHTML).inject(params.responseContainer.getElement('.sitemember_img_view'));
                          }
                          en4.core.runonce.trigger();
                          Smoothbox.bind(params.responseContainer);
			  setGridHoverEffect('<?php echo $this->circularImage;?>');
                        }
						
                      });
                      en4.core.request.send(request);
                    }

                    en4.core.runonce.add(function() {
    <?php if (count($this->tabs) > 1): ?>
                        $$('.tab_li_<?php echo $this->identity ?>').addEvent('click', function(event) {

                          if (en4.core.request.isRequestActive())
                            return;
                          var element = $(event.target);
                          if (element.tagName.toLowerCase() == 'a') {
                            element = element.getParent('li');
                          }
                          var type = element.get('rel');

                          element.getParent('ul').getElements('li').removeClass("active")
                          element.addClass("active");
                          var params = {
                            requestParams:<?php echo json_encode($this->params) ?>,
                            responseContainer: $('dynamic_app_info_sitemember_' + '<?php echo $this->identity ?>')
                          }
                          params.requestParams.content_type = type;
                          params.requestParams.page = 1;
                          params.requestParams.tabbed = 1;
                          params.requestParams.content_id = '<?php echo $this->identity ?>';
                          params.responseContainer.empty();

                          new Element('div', {
                            'class': 'seaocore_content_loader'
                          }).inject(params.responseContainer);
                          //                          $('dynamic_app_info_sitemember_' + '<?php echo $this->identity ?>').style.display = 'none';
                          sendAjaxRequestSitemember(params);
                        });
    <?php endif; ?>
                    });

                    function sitememberTabSwitchview(element) {
                      if (element.tagName.toLowerCase() == 'span') {
                        element = element.getParent('li');
                      }
                      var type = element.get('rel');

                      var identity = element.getParent('ul').get('identity');
                      $('dynamic_app_info_sitemember_' + identity).getElements('.sitemember_container').setStyle('display', 'none');
                      $('dynamic_app_info_sitemember_' + identity).getElement("#" + type + "_sitemember_").style.display = 'block';
                      setGridHoverEffect('<?php echo $this->circularImage;?>');
                    }
      </script>

      <?php if ($this->enableLocation): ?>
        <?php $latitude = $this->settings->getSetting('sitemember.map.latitude', 0); ?>
        <?php $longitude = $this->settings->getSetting('sitemember.map.longitude', 0); ?>
        <?php $defaultZoom = $this->settings->getSetting('sitemember.map.zoom', 1); ?>
        <script type="text/javascript">
          // var rgmarkers = [];
          en4.sitemember = {
            maps: [],
            infowindow: [],
            markers: []
          };
          function srInitializeMap(element_id) {
            en4.sitemember.maps[element_id] = [];
            en4.sitemember.maps[element_id]['markers'] = [];
            // create the map
            var myOptions = {
              zoom: <?php echo $defaultZoom ?>,
              center: new google.maps.LatLng(<?php echo $latitude ?>,<?php echo $longitude ?>),
              navigationControl: true,
              mapTypeId: google.maps.MapTypeId.ROADMAP
            };

            en4.sitemember.maps[element_id]['map'] = new google.maps.Map(document.getElementById("rmap_canvas_" + element_id), myOptions);

            google.maps.event.addListener(en4.sitemember.maps[element_id]['map'], 'click', function() {
              en4.sitemember.maps[element_id]['infowindow'].close();
              google.maps.event.trigger(en4.sitemember.maps[element_id]['map'], 'resize');
              en4.sitemember.maps[element_id]['map'].setCenter(new google.maps.LatLng(<?php echo $latitude ?>,<?php echo $longitude ?>));
            });
            if ($("rmap_canvas_" + element_id)) {
              $("rmap_canvas_" + element_id).addEvent('click', function() {
                google.maps.event.trigger(en4.sitemember.maps[element_id]['map'], 'resize');
                en4.sitemember.maps[element_id]['map'].setZoom(<?php echo $defaultZoom ?>);
                en4.sitemember.maps[element_id]['map'].setCenter(new google.maps.LatLng(<?php echo $latitude ?>,<?php echo $longitude ?>));
              });
            }

            en4.sitemember.maps[element_id]['infowindow'] = new google.maps.InfoWindow(
                    {
                      size: new google.maps.Size(250, 50)
                    });

          }

          function setSRMarker(element_id, latlng, bounce, html, title_list) {
            var contentString = html;
            if (bounce == 0) {
              var marker = new google.maps.Marker({
                position: latlng,
                map: en4.sitemember.maps[element_id]['map'],
                title: title_list,
                animation: google.maps.Animation.DROP,
                zIndex: Math.round(latlng.lat() * -100000) << 5
              });
            }
            else {
              var marker = new google.maps.Marker({
                position: latlng,
                map: en4.sitemember.maps[element_id]['map'],
                title: title_list,
                draggable: false,
                animation: google.maps.Animation.BOUNCE
              });
            }
            en4.sitemember.maps[element_id]['markers'].push(marker);

            google.maps.event.addListener(marker, 'click', function() {
              en4.sitemember.maps[element_id]['infowindow'].setContent(contentString);
              google.maps.event.trigger(en4.sitemember.maps[element_id]['map'], 'resize');

              en4.sitemember.maps[element_id]['infowindow'].open(en4.sitemember.maps[element_id]['map'], marker);

            });
          }
          function srToggleBounce(element_id) {
            var markers = en4.sitemember.maps[element_id]['markers'];
            for (var i = 0; i < markers.length; i++) {
              if (markers[i].getAnimation() != null) {
                markers[i].setAnimation(null);
              }
            }
          }
          en4.core.runonce.add(function() {
            srInitializeMap("<?php echo $this->identity ?>");

            $$('.tab_icon_map_view').addEvent('click', function() {
              var rmap_member = en4.sitemember.maps["<?php echo $this->identity ?>"]['map'];
              google.maps.event.trigger(rmap_member, 'resize');
              rmap_member.setZoom(<?php echo $defaultZoom; ?>);
              rmap_member.setCenter(new google.maps.LatLng(<?php echo $latitude; ?>,<?php echo $longitude; ?>));
            });

          });
        </script>
      <?php endif; ?>
    <?php endif; ?>

    <script type="text/javascript">
      en4.core.runonce.add(function() {
  <?php if ($this->enableLocation && count($this->locations) > 0): ?>
    <?php foreach ($this->locations as $location) : ?>
            var point = new google.maps.LatLng(<?php echo $location->latitude ?>,<?php echo $location->longitude ?>);
            var contentString = "<?php
      echo $this->string()->escapeJavascript($this->partial('application/modules/Sitemember/views/scripts/_mapInfoWindowContent.tpl', array(
                  'sitemember' => $this->locationsMember[$location->locationitem_id],
                  'statistics' => $this->statistics,
                  'customParams' => $this->customParams,
                  'custom_field_title' => $this->custom_field_title,
                  'custom_field_heading' => $this->custom_field_heading
              )), false);
      ?>";

            setSRMarker(<?php echo $this->identity ?>, point, '<?php echo!empty($this->flagSponsored) ? $this->locationsMember[$location->locationitem_id]->sponsored : 0 ?>', contentString, "<?php echo $this->string()->escapeJavascript($this->locationsMember[$location->locationitem_id]->getTitle()) ?>");
    <?php endforeach; ?>
  <?php endif; ?>
      });
    </script>


    <?php if ($this->showContent == 2): ?>
      <script type="text/javascript">
        en4.core.runonce.add(function() {
          $('seaocore_view_more_<?php echo $this->identity ?>').style.display = 'block';
          hideViewMoreLink('<?php echo $this->showContent; ?>');
        });</script>
    <?php elseif ($this->showContent == 1): ?>
      <script type="text/javascript">
        en4.core.runonce.add(function() {
          $('seaocore_view_more_<?php echo $this->identity ?>').style.display = 'block';
          hideViewMoreLink('<?php echo $this->showContent; ?>');
        });
      </script>
    <?php endif; ?>

    <script type="text/javascript">

      function getNextPage() {
        return <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() + 1) ?>
      }

      function hideViewMoreLink(showContent) {

        if (showContent == 2) {
          $('seaocore_view_more_<?php echo $this->identity ?>').style.display = 'none';
          var totalCount = '<?php echo $this->paginator->count(); ?>';
          var currentPageNumber = '<?php echo $this->paginator->getCurrentPageNumber(); ?>';

          function doOnScrollLoadPage()
          {

            if ($('scroll_bar_height') && typeof($('scroll_bar_height').offsetParent) != 'undefined') {
              var elementPostionY = $('scroll_bar_height').offsetTop;
            } else {
              var elementPostionY = $('scroll_bar_height').y;
            }
            if (elementPostionY <= window.getScrollTop() + (window.getSize().y - 20)) {
              if ((totalCount != currentPageNumber) && (totalCount != 0)) {
                if (en4.core.request.isRequestActive())
                  return;
                var params = {
                  requestParams:<?php echo json_encode($this->params) ?>,
                  responseContainer: $('dynamic_app_info_sitemember_' +<?php echo sprintf('%d', $this->identity) ?>)
                };

                params.requestParams.content_type = "<?php echo $this->content_type ?>";
                params.requestParams.page = getNextPage();
                params.requestParams.content_id = '<?php echo $this->identity ?>';
                params.requestParams.scroll = 1;
                sendAjaxRequestSitemember(params);
              }
            }
          }
          window.onscroll = doOnScrollLoadPage;
        } else if (showContent == 1)
        {
          var view_more_content = $('seaocore_view_more_<?php echo $this->identity ?>');
          view_more_content.setStyle('display', '<?php echo ( $this->paginator->count() == $this->paginator->getCurrentPageNumber() || $this->totalCount == 0 ? 'none' : '' ) ?>');
          view_more_content.removeEvents('click');
          view_more_content.addEvent('click', function() {
            if (en4.core.request.isRequestActive())
              return;
            var params = {
              requestParams:<?php echo json_encode($this->params) ?>,
              responseContainer: $('dynamic_app_info_sitemember_' +<?php echo sprintf('%d', $this->identity) ?>)
            };

            params.requestParams.content_type = "<?php echo $this->content_type ?>";
            params.requestParams.page = getNextPage();
            params.requestParams.content_id = '<?php echo $this->identity ?>';

            sendAjaxRequestSitemember(params);
          });
        }
      }
    </script>
  <?php else: ?>

    <div id="layout_sitemember_recently_popular_random_sitemember<?php echo $this->identity; ?>">
      <div class="seaocore_content_loader"></div>
    </div>

    <script type="text/javascript">
      var requestParams = $merge(<?php echo json_encode($this->paramsLocation); ?>, {'content_id': '<?php echo $this->identity; ?>'});
      var params = {
        'detactLocation': <?php echo $this->detactLocation; ?>,
        'responseContainer': 'layout_sitemember_recently_popular_random_sitemember<?php echo $this->identity; ?>',
        requestParams: requestParams
      };

      en4.seaocore.locationBased.startReq(params);
    </script>

  <?php endif; ?>

<script type="text/javascript">
    setGridHoverEffect('<?php echo $this->circularImage;?>');
</script>

<style type="text/css" >
.sitemember_circular_container .sitemember_grid_view .seaocore_list_sponsored_label::before, .sitemember_circular_container .sitemember_grid_view .seaocore_list_sponsored_label::after {
    background: <?php echo $this->settings->getSetting('sitemember.sponsoredcolor', '#FC0505'); ?>;
	}
</style>