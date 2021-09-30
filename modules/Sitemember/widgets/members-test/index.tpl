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
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitemember/externals/scripts/core.js'); ?>
<?php
$this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/styles/style_infotooltip.css');
?>
<?php if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('advancedactivity')): ?>
  <script type="text/javascript">
      var gm = google.maps;
        var map = new gm.Map(document.getElementById('map_canvas'), {
          mapTypeId: gm.MapTypeId.SATELLITE,
          center: new gm.LatLng(50, 0), 
          zoom: 6
        });
      var oms = new OverlappingMarkerSpiderfier(map);
      
      var iw = new gm.InfoWindow();
        oms.addListener('click', function(marker, event) {
          iw.setContent(marker.desc);
          iw.open(map, marker);
        });
        
        oms.addListener('spiderfy', function(markers) {
            iw.close();
          });

    var CommentLikesTooltips;
    en4.core.runonce.add(function() {
      // Add hover event to get tool-tip
      var feedToolTipAAFEnable = true;
      if (feedToolTipAAFEnable) {
        var show_tool_tip = false;
        var counter_req_pendding = 0;
        $$('.sea_add_tooltip_link').addEvent('mouseover', function(event) {
          var el = $(event.target);
          ItemTooltips.options.offset.y = el.offsetHeight;
          ItemTooltips.options.showDelay = 0;
          if (!el.hasAttribute("rel")) {
            el = el.parentNode;
          }
          show_tool_tip = true;
          if (!el.retrieve('tip-loaded', false)) {
            counter_req_pendding++;
            var resource = '';
            if (el.hasAttribute("rel"))
              resource = el.rel;
            if (resource == '')
              return;

            el.store('tip-loaded', true);
            el.store('tip:title', '<div class="" style="">' +
                    ' <div class="uiOverlay info_tip" style="width: 300px; top: 0px; ">' +
                    '<div class="info_tip_content_wrapper" ><div class="info_tip_content"><div class="info_tip_content_loader">' +
                    '<img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Core/externals/images/loading.gif" alt="Loading" /><?php echo $this->translate("Loading ...") ?></div>' +
                    '</div></div></div></div>'
                    );
            el.store('tip:text', '');
            // Load the likes
            var url = '<?php echo $this->url(array('module' => 'seaocore', 'controller' => 'feed', 'action' => 'show-tooltip-info'), 'default', true) ?>';
            el.addEvent('mouseleave', function() {
              show_tool_tip = false;
            });

            var req = new Request.HTML({
              url: url,
              data: {
                format: 'html',
                'resource': resource
              },
              evalScripts: true,
              onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
                el.store('tip:title', '');
                el.store('tip:text', responseHTML);
                ItemTooltips.options.showDelay = 0;
                ItemTooltips.elementEnter(event, el); // Force it to update the text 
                counter_req_pendding--;
                if (!show_tool_tip || counter_req_pendding > 0) {
                  //ItemTooltips.hide(el);
                  ItemTooltips.elementLeave(event, el);
                }
                var tipEl = ItemTooltips.toElement();
                tipEl.addEvents({
                  'mouseenter': function() {
                    ItemTooltips.options.canHide = false;
                    ItemTooltips.show(el);
                  },
                  'mouseleave': function() {
                    ItemTooltips.options.canHide = true;
                    ItemTooltips.hide(el);
                  }
                });
                Smoothbox.bind($$(".sea_add_tooltip_link_tips"));
              }
            });
            req.send();
          }
        });
        // Add tooltips
        var window_size = window.getSize()
        var ItemTooltips = new SEATips($$('.sea_add_tooltip_link'), {
          fixed: true,
          title: '',
          className: 'sea_add_tooltip_link_tips',
          hideDelay: 200,
          offset: {'x': 0, 'y': 0},
          windowPadding: {'x': 370, 'y': (window_size.y / 2)}
        });
      }
    });
  </script>
<?php endif; ?>

<?php
$this->headScript()
        ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/pinboard/mooMasonry.js')
        ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/pinboard/pinboard.js');

$this->headLink()->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/styles/style_board.css')
        ->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/styles/styles.css')
        ->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitemember/externals/styles/style_sitemember.css');
?>

<?php if ($this->map_view && $this->paginator->count() > 0): ?>

  <?php 
  $apiKey = Engine_Api::_()->seaocore()->getGoogleMapApiKey();
  $this->headScript()->appendFile("https://maps.googleapis.com/maps/api/js?libraries=places&key=$apiKey");
  $this->headScript()->appendFile("http://jawj.github.io/OverlappingMarkerSpiderfier/bin/oms.min.js");
  ?>
  
  <?php
$this->headScript()->appendFile($this->layout()->staticBaseUrl . "application/modules/Seaocore/externals/scripts/markerclusterer.js");
?>
<?php endif; ?>

<div id="sitemember_location_map_none" style="display: none;"></div>

<?php
if ($this->is_ajax_load):
  ?>
  <script type="text/javascript">
    var pageAction = function(page){

       var form;
       if($('filter_form')) {
         form=document.getElementById('filter_form');
        } else if($('searchBox')) {
         form=document.getElementById('searchBox');
        }
      form.elements['page'].value = page;
          <?php if($this->text_search): ?>
              form.elements['search'].value = '<?php echo $this->text_search ?>';
    <?php endif; ?>
      form.submit();
    } 
  </script>
  <?php $latitude = $this->settings->getSetting('sitemember.map.latitude', 0); ?>
  <?php $longitude = $this->settings->getSetting('sitemember.map.longitude', 0); ?>
  <?php $defaultZoom = $this->settings->getSetting('sitemember.map.zoom', 1); ?>
  <?php $enableBouce = $this->enableBounce; ?>
  <?php $viewer_id = $this->viewer->getIdentity(); ?>

  <?php if ($this->paginator->count() > 0): ?>
  
    <?php if(empty($this->is_ajax)):?>
      <form id='filter_form' class='global_form_box' method='get' action='<?php echo $this->url(array('action' => 'userby-locations'), 'sitemember_userbylocation', true) ?>' style='display: none;'>
        <input type="hidden" id="page" name="page"  value=""/>
        <input type="hidden" id="search" name="search"  value=""/>
      </form>
    <?php endif;?>
  
    <script type="text/javascript">

       en4.core.runonce.add(function() {
          if ($("pinboard_view")) {

            
                    for (var i=0; i<= 10;i++){
          (function(){
            $("pinboard_view").pinBoardSeaoMasonry({
                  singleMode: true,
                  itemSelector: '.seaocore_list_wrapper'
                });
          }).delay(500*i);
        }
          }
      });

      function childWindowOpen(event) {
        var element = event;
        if ((element.get('tag') == 'a' && element.href && !element.href.match(/^(javascript|[#])/))) {
          open(element.href, element.get('html'), 'width=700,height=350,resizable,toolbar,status');
          element.removeProperties('href');
        }
      }
    </script>

    <?php if (empty($this->is_ajax)): ?>

      <script>
        function sendAjaxRequestSM() {


          var viewType = 2;
          if ($('grid_view')) {
            if ($('grid_view').style.display == 'block')
              viewType = 0;
          }
          if ($('image_view')) {
            if ($('image_view').style.display == 'block')
              viewType = 1;
          }
          if ($('pinboard_view')) {
            if ($('pinboard_view').style.display == 'block')
              viewType = 3;
          }

          if (en4.core.request.isRequestActive())
            return;
          var params = {
            requestParams:<?php echo json_encode($this->params) ?>,
            responseContainer: $('dynamic_app_info_sitemember_' +<?php echo sprintf('%d', $this->identity) ?>)
          }
          params.requestParams.page = getNextPage();
      <?php //echo sprintf('%d', $this->paginator->getCurrentPageNumber() + 1)  ?>;
          params.requestParams.content_id = '<?php echo $this->identity ?>';
          $('seaocore_view_more').style.display = 'none';
          $('seaocore_loading').style.display = '';
          var url = en4.core.baseUrl + 'widget';
      //          if (params.requestUrl)
      //            url = params.requestUrl;
          var request = new Request.HTML({
            method: 'get',
            url: url,
            data: $merge(params.requestParams, {
              format: 'html',
              subject: en4.core.subject.guid,
              is_ajax: true,
              loaded_by_ajax: true
            }),
            evalScripts: true,
            onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
              if (params.requestParams.page == 1) {
                params.responseContainer.empty();
                Elements.from(responseHTML).inject(params.responseContainer);
                initialize();
              } else {
                var element = new Element('div', {
                  'html': responseHTML
                });

                params.responseContainer.getElements('.seaocore_loading').setStyle('display', 'none');

                if ($('grid_view')) {
                  if (element.getElement('.sitemember_list_view') && $$('.sitemember_list_view')) {
                    Elements.from(element.getElement('.sitemember_list_view').innerHTML).inject(params.responseContainer.getElement('.sitemember_list_view'));
                  }
                }
                if ($('image_view')) {
                  if (element.getElement('.sitemember_img_view').innerHTML && $$('.sitemember_img_view')) {
                    Elements.from(element.getElement('.sitemember_img_view').innerHTML).inject(params.responseContainer.getElement('.sitemember_img_view'));
                  }
                }
                if ($('pinboard_view')) {
                  if (element.getElement('.sitemember_pinboard_view') && $$('.sitemember_pinboard_view')) {
                    Elements.from(element.getElement('.sitemember_pinboard_view').innerHTML).inject(params.responseContainer.getElement('.sitemember_pinboard_view'));
                  }
                }
              }
              en4.core.runonce.trigger();
              Smoothbox.bind(params.responseContainer);
              switchview(viewType);
              setGridHoverEffect('<?php echo $this->circularImage;?>');
            }
          });
          en4.core.request.send(request);
        }
      </script>

    <?php endif; ?>

    <div id="dynamic_app_info_sitemember_<?php echo $this->identity; ?>">
      <?php if (($this->list_view && $this->grid_view) || ($this->map_view && $this->grid_view) || ($this->list_view && $this->map_view) || ($this->list_view && $this->pinboard_view) || ($this->grid_view && $this->pinboard_view) || ($this->map_view && $this->pinboard_view)): ?>
        <div class="sitemember_browse_lists_view_options b_medium">
          <div class="fleft">

            <?php echo $this->translate(array('%s member found.', '%s members found.', $this->totalResults), $this->locale()->toNumber($this->totalResults)) ?>
          </div>

          <?php if ($this->map_view): ?>
            <span class="seaocore_tab_select_wrapper fright">
              <div class="seaocore_tab_select_view_tooltip"><?php echo $this->translate("Map View"); ?></div>
              <span class="seaocore_tab_icon tab_icon_map_view" onclick="switchview(2);" ></span>
            </span>
          <?php endif; ?>
          <?php if ($this->pinboard_view): ?>
            <span class="seaocore_tab_select_wrapper fright">
              <div class="seaocore_tab_select_view_tooltip"><?php echo $this->translate("Pinboard View"); ?></div>
              <span class="seaocore_tab_icon tab_icon_pin_view" onclick="switchview(3);" ></span>
            </span>
          <?php endif; ?>
          <?php if ($this->grid_view): ?>
            <span class="seaocore_tab_select_wrapper fright">
              <div class="seaocore_tab_select_view_tooltip"><?php echo $this->translate("Grid View"); ?></div>
              <span class="seaocore_tab_icon tab_icon_grid_view" onclick="switchview(1);" ></span>
            </span>
          <?php endif; ?>

          <?php if ($this->list_view): ?>
            <span class="seaocore_tab_select_wrapper fright">
              <div class="seaocore_tab_select_view_tooltip"><?php echo $this->translate("List View"); ?></div>
              <span class="seaocore_tab_icon tab_icon_list_view" onclick="switchview(0);" ></span>
            </span>
          <?php endif; ?>
        </div>
      <?php elseif($this->memberfoundshow): ?>
      <div class="sitemember_browse_lists_view_options b_medium">
          <div class="fleft">
            <?php echo $this->translate(array('%s member found.', '%s members found.', $this->totalResults), $this->locale()->toNumber($this->totalResults)) ?>
          </div>
        </div>
      <?php endif; ?>

      <?php
      if ($this->list_view):
        ?>
        <div id="grid_view" class="<?php if(!empty($this->circularImage)):?> sitemember_circular_container <?php endif;?>" <?php if ($this->defaultView != 0): ?> style="display: none;" <?php endif; ?>>

          <?php if (empty($this->viewType)): ?>
            <ul class="sitemember_browse_list sitemember_list_view">
              <?php foreach ($this->paginator as $sitemember): ?>
                <?php ?>
                <?php if (!empty($sitemember->sponsored)): ?>
                  <li class="list_sponsered1 b_medium">
                  <?php else: ?>
                  <li class="b_medium">
                  <?php endif; ?>
                  <div class='sitemember_browse_list_photo b_medium'>
                    <?php if (!empty($this->statistics) && in_array('featuredLabel', $this->statistics) && $sitemember->featured): ?>
                      <i class="seaocore_list_featured_label" title="<?php echo $this->translate('Expert'); ?>"><?php echo $this->translate('Expert'); ?></i>
                    <?php endif; ?>
                    <?php if($this->circularImage):?>
                        <?php
                        $url = $sitemember->getPhotoUrl('thumb.profile');
                        if (empty($url)): $url = $this->layout()->staticBaseUrl . 'application/modules/Sitemember/externals/images/nophoto_user_thumb_normal.png';
                        endif;
                        ?>
                      <a href="<?php echo $sitemember->getHref() ?>" class ="sitemember_thumb"><span style="background-image: url(<?php echo $url; ?>); <?php if($this->circularImage):?>height:<?php echo $this->circularImageHeight; ?>px;<?php endif;?>"></span></a>
                    <?php else:?>
                        <?php echo $this->htmlLink($sitemember->getHref(), $this->itemPhoto($sitemember, 'thumb.profile', '', array('align' => 'center', 'class' => 'sea_add_tooltip_link', 'rel' => 'user' . ' ' . $sitemember->user_id))) ?>
                    <?php endif;?>

                    <?php if (!empty($this->statistics) && in_array('sponsoredLabel', $this->statistics) && !empty($sitemember->sponsored)): ?>
                      <div class="seaocore_list_sponsored_label" style="padding:initial; background: <?php echo $this->settings->getSetting('sitemember.sponsoredcolor', '#FC0505'); ?>">
                        <?php echo $this->translate('Practice Leader'); ?>
                      </div>
                    <?php endif; ?>
                  </div>

                  <div class='sitemember_browse_list_info'>

                    <div class='sitemember_browse_list_info_header o_hidden'>
                      <div class="sitemember_list_title">
                        <?php echo $this->htmlLink($sitemember->getHref(), Engine_Api::_()->seaocore()->seaocoreTruncateText($sitemember->getTitle(), $this->title_truncation), array('title' => $sitemember->getTitle(), 'class' => 'sea_add_tooltip_link', 'rel' => 'user' . ' ' . $sitemember->user_id)); ?>
                        <?php
                        //GET VERIFY COUNT AND VERIFY LIMIT
                        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteverify') && Engine_Api::_()->authorization()->getPermission($sitemember->level_id, 'siteverify', 'allow_verify') && !empty($this->statistics) && in_array('verifyLabel', $this->statistics)) :
                          $verify_count = Engine_Api::_()->getDbtable('verifies', 'siteverify')->getVerifyCount($sitemember->user_id);
                          $user = Engine_Api::_()->getItem('user', $sitemember->user_id);
                          $verify_limit = Engine_Api::_()->authorization()->getPermission($user->level_id, 'siteverify', 'verify_limit');
                          ?>
                          <?php if ($verify_count >= $verify_limit): ?>
                          	<span class="siteverify_tip_wrapper">
                            	<i class="sitemember_list_verify_label"></i>
                            	<span class="siteverify_tip"><?php echo $this->translate('Verified'); ?><i></i></span>
                            </span>
                            <?php
                          endif;
                        endif;
                        ?>

                      </div>
                      <div class="clear"></div>
                    </div>

                    <?php if (!empty($this->statistics)) : ?>
                      <?php echo $this->memberInfo($sitemember, $this->statistics, array('customParams' => $this->customParams, 'custom_field_title' => $this->custom_field_title, 'custom_field_heading' => $this->custom_field_heading)); ?>
                    <?php endif; ?>
                  </div>
                </li>
              <?php endforeach; ?>
            </ul>

          <?php else:
            ?>

            <ul class="sitemember_browse_list sitemember_list_view">
              <?php
                $pokeEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('poke');
                $suggestionEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('suggestion');              
              ?>  
              <?php
              foreach ($this->paginator as $sitemember):
                ?>
                <?php ?>
                
                <li class="b_medium" style="height:<?php echo $this->commonColumnHeight; ?>px;<?php if($this->listFullWidthElement):?>width:98%;<?php endif;?>">
                  <div class='sitemember_browse_list_photo b_medium'>
                    <?php if (!empty($this->statistics) && in_array('featuredLabel', $this->statistics) && $sitemember->featured):
                      ?>
                      <i class="seaocore_list_featured_label" title="<?php echo $this->translate('Expert'); ?>"><?php echo $this->translate('Expert'); ?></i>
                    <?php endif; ?>

                   
                    <?php if($this->circularImage):?>  
                        <a href="<?php echo $sitemember->getHref() ?>" class="sitemember_thumb">
                        <?php
                        $url = $sitemember->getPhotoUrl('thumb.profile');
                        if (empty($url)): $url = $this->layout()->staticBaseUrl . 'application/modules/Sitemember/externals/images/nophoto_user_thumb_normal.png';
                        endif;
                        ?>
                        <span style="background-image: url(<?php echo $url; ?>);"></span>
                        </a>
                      <?php else:?>
                       <?php
                              echo $this->itemPhoto($sitemember, 'thumb.profile', '', array('align' => 'center'));
                              ?>
                      <?php endif;?>
                    <?php if (!empty($this->statistics) && in_array('sponsoredLabel', $this->statistics) && !empty($sitemember->sponsored)): ?>
                      <div class="seaocore_list_sponsored_label" style="padding:initial; background: <?php echo $this->settings->getSetting('sitemember.sponsoredcolor', '#FC0505'); ?>">
                        <?php echo $this->translate('Practice Leader'); ?>
                      </div>
                    <?php endif; ?>
                  </div>

                  <div class='sitemember_browse_list_info'>
                    <div class="sitemember_browse_list_info_header">
                      <div class="seaocore_browse_list_info_title">
                        <?php echo $this->htmlLink($sitemember->getHref(), Engine_Api::_()->seaocore()->seaocoreTruncateText($sitemember->getTitle(), $this->title_truncation), array('title' => $sitemember->getTitle(), 'class' => 'sea_add_tooltip_link', 'rel' => 'user' . ' ' . $sitemember->user_id)); ?>

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
                    <div class="sitemember_browse_list_information fleft">
                      <?php
                      if (!empty($this->statistics)) :
                        ?>
                        <?php echo $this->memberInfo($sitemember, $this->statistics, array('customParams' => $this->customParams, 'custom_field_title' => $this->custom_field_title, 'custom_field_heading' => $this->custom_field_heading)); ?>
                      <?php endif; ?>
                    </div>
                    <?php $items = Engine_Api::_()->getItem('user', $sitemember->user_id); ?>
                    <?php
                    //POKE WORK
                    $pokeFlag = false;
                    $resource_id = $sitemember->user_id;
                    $user_subject = Engine_Api::_()->user()->getUser($resource_id);

                    if (!empty($pokeEnabled) && (!empty($viewer_id))) {
                      $subject = Engine_Api::_()->getItem('user', $resource_id);
                      $getpokeFriend = Engine_Api::_()->poke()->levelSettings($subject);
                    }
                    if (!empty($pokeEnabled) && (!empty($getpokeFriend)) && ($resource_id !=
                            $viewer_id) && !empty($this->links) && in_array("poke", $this->links) && (!Engine_Api::_()->user()->getViewer()->isBlockedBy($user_subject) || Engine_Api::_()->user()->getViewer()->isAdmin())) { $pokeFlag = true; }
                    ?>
                      
                        <?php
                        $suggestionFlag = false;
                        $getMemberFriend = Engine_Api::_()->seaocore()->isMember($resource_id);

                        if (!empty($suggestionEnabled)) {
                          $moduleNmae = 'friend';
                          $suggestion_frienf_link_show = Engine_Api::_()->suggestion()->getModSettings("$moduleNmae", "link");
                        }

                        if (!empty($suggestionEnabled) && !empty($getMemberFriend) && (!empty($suggestion_frienf_link_show)) && !empty($this->links) &&
                                in_array("suggestion", $this->links) && (!empty($viewer_id))) { $suggestionFlag = true; }
                          ?>   
                    <?php if (!empty($this->links) || $pokeFlag || $suggestionFlag):?>
                      <div class="clr sitemember_action_link_options sitemember_action_links">
                        <?php
                        //FOR MESSAGE LINK
                        if ((Engine_Api::_()->seaocore()->canSendUserMessage($items)) && (!empty($viewer_id)) && !empty($this->links) && in_array('messege', $this->links)) :
                          ?>
                          <span><a href="<?php echo $this->baseUrl() ?>/messages/compose/to/<?php echo $sitemember->user_id ?>" target="_parent" class="buttonlink sitemember_action_links_message"><?php echo $this->translate('Message'); ?></a></span>
                        <?php endif; ?>

                        <?php
                        if (!empty($this->links) && in_array('addfriend', $this->links)):
                          //Add friend link.
                          $uaseFRIENFLINK = $this->userFriendshipAjax($this->user($sitemember->user_id));
                          ?>
                          <?php if (!empty($uaseFRIENFLINK)) : ?>
                            <span><?php echo $uaseFRIENFLINK; ?></span>
                          <?php endif; ?>
                        <?php endif; ?>

                        <?php if ($pokeFlag) { ?>

                          <span><a href="javascript:void(0);" onclick="showSmoothBox('<?php
                            echo $this->escape($this->url(array('route' => 'poke_general', 'module' => 'poke', 'controller' => 'pokeusers',
                                        'action' => 'pokeuser', 'pokeuser_id' => $resource_id, 'format' => 'smoothbox'), 'default', true));
                            ?>');

                        return false;" class="buttonlink sitemember_action_links_poke"><?php echo $this->translate("Poke") ?></a></span>
                       <?php } //END POKE WORK.?>

                        <?php //FOR SUGGESTION LINK SHOW IF SUGGESTION PLUGIN INSTALL AT HIS SITE.       ?>
                        <?php if ($suggestionFlag) { ?>

                          <span><a href="javascript:void(0);" onclick="showSmoothBox('<?php
                            echo $this->escape($this->url(array('module' => 'suggestion', 'controller' => 'index', 'action' =>
                                        'switch-popup', 'modName' => 'user', 'modContentId' => $resource_id, 'modError' => 1, 'format' => 'smoothbox'), 'default', true));
                            ?>');
                        return false;" class="buttonlink sitemember_action_links_suggestion"><?php echo $this->translate("Suggest to Friends") ?></a></span>
                       <?php } //END SUGGESTION WORK.                          ?>
                      </div>
                    <?php endif; ?>


                    <?php if ($this->showDetailLink) : ?>
                      <div class=" dblock fright sitemember_browse_list_info_btn">
                        <a href="<?php echo $sitemember->getHref(); ?>" class="sitemember_buttonlink"><?php echo $this->translate('Details &raquo;'); ?></a>
                      </div>
                    <?php endif; ?>
                  </div>
                </li>
                <?php
              endforeach;
              ?>
            </ul>
          <?php endif; ?>
        </div>
        <?php
      endif;
      ?>

      <?php if ($this->grid_view): ?>
        <div id="image_view" class="sitemember_container <?php if(!empty($this->circularImage)):?> sitemember_circular_container <?php endif;?>" <?php if ($this->defaultView != 1): ?> style="display: none;" <?php endif; ?>>
          <div class="sitemember_img_view">
            <?php
            $isLarge = ($this->columnWidth > 170);
            ?>
            <?php
            foreach ($this->paginator as $sitemember):
              ?>
              <div class="sitemember_grid_view" style="width: <?php echo $this->columnWidth; ?>px;height:<?php echo $this->columnHeight; ?>px;">
                <div class="sitemember_grid_thumb">
                  <a href="<?php echo $sitemember->getHref() ?>" class="sitemember_thumb">
                    <?php
                    $url = $sitemember->getPhotoUrl('thumb.profile');
                    if (empty($url)): $url = $this->layout()->staticBaseUrl . 'application/modules/Sitemember/externals/images/nophoto_user_thumb_normal.png';
                    endif;
                    ?>
                    <span style="background-image: url(<?php echo $url; ?>); <?php if($this->circularImage):?>height:<?php echo $this->circularImageHeight; ?>px;<?php endif;?>"></span>
                  </a>

                  <?php if (!empty($this->statistics) && in_array('featuredLabel', $this->statistics) && $sitemember->featured): ?>
                    <i class="seaocore_list_featured_label" title="<?php echo $this->translate('Expert'); ?>"><?php echo $this->translate('Expert'); ?></i>
                  <?php endif; ?>

                  <?php if (!empty($this->titlePosition)) : ?>
                    <div class="sitemember_grid_title">
                      <?php echo $this->htmlLink($sitemember->getHref(), Engine_Api::_()->seaocore()->seaocoreTruncateText($sitemember->getTitle(), $this->title_truncationGrid), array('title' => $sitemember->getTitle(), 'class' => 'sea_add_tooltip_link', 'rel' => 'user' . ' ' . $sitemember->user_id)) ?>
                      <?php
                      if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteverify') && Engine_Api::_()->authorization()->getPermission($sitemember->level_id, 'siteverify', 'allow_verify') && !empty($this->statistics) && in_array('verifyLabel', $this->statistics)) :
                        $verify_count = Engine_Api::_()->getDbtable('verifies', 'siteverify')->getVerifyCount($sitemember->user_id);
                        $user = Engine_Api::_()->getItem('user', $sitemember->user_id);
                        $verify_limit = Engine_Api::_()->authorization()->getPermission($user->level_id, 'siteverify', 'verify_limit');
                        ?>
                        <?php if (($verify_count >= $verify_limit)): ?> 
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
                            <!-- <?php echo $this->translate("Online"); ?>-->
                            <?php //else: ?>
              <!--                            <img title="Offline" src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/offline.png' alt="" class="fleft" />-->
                            <!-- <?php //echo $this->translate("Offline");  ?>-->
                          <?php endif; ?>
                        </span>
                      <?php endif; ?>
                    </div>
                  <?php endif; ?>
                </div>

                <?php if (!empty($this->statistics) && in_array('sponsoredLabel', $this->statistics) && !empty($sitemember->sponsored)): ?>
                  <div class="seaocore_list_sponsored_label" style="padding:initial; background: <?php echo $this->settings->getSetting('sitemember.sponsoredcolor', '#FC0505'); ?>">
                    <?php echo $this->translate('Practice Leader'); ?>
                  </div>
                <?php endif; ?>

                <div class="sitemember_grid_info">
                  <?php if (empty($this->titlePosition)) : ?>
                    <div class="sitemember_grid_title">
                      <?php echo $this->htmlLink($sitemember->getHref(), Engine_Api::_()->seaocore()->seaocoreTruncateText($sitemember->getTitle(), $this->title_truncationGrid), array('title' => $sitemember->getTitle(), 'class' => 'sea_add_tooltip_link', 'rel' => 'user' . ' ' . $sitemember->user_id)) ?>
                      <?php if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteverify') && Engine_Api::_()->authorization()->getPermission($sitemember->level_id, 'siteverify', 'allow_verify') && !empty($this->statistics) && in_array('verifyLabel', $this->statistics)) :
                        $verify_count = Engine_Api::_()->getDbtable('verifies', 'siteverify')->getVerifyCount($sitemember->user_id);
                        $user = Engine_Api::_()->getItem('user', $sitemember->user_id);
                        $verify_limit = Engine_Api::_()->authorization()->getPermission($user->level_id, 'siteverify', 'verify_limit');
                        ?>
                        <?php if (($verify_count >= $verify_limit)): ?>
                            <span class="siteverify_tip_wrapper">
                                <i class="sitemember_list_verify_label mleft5"></i>
                                <span class="siteverify_tip"><?php echo $this->translate('Verified'); ?><i></i></span>
                            </span>
                          <?php
                        endif;
                      endif;
                      ?>
                      <?php if (!empty($this->statistics) && in_array('memberStatus', $this->statistics)) :
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
                  <?php endif; ?>
                  <?php if (!empty($this->statistics)) : ?>
                    <?php echo $this->memberInfo($sitemember, $this->statistics, array('customParams' => $this->customParams, 'custom_field_title' => $this->custom_field_title, 'custom_field_heading' => $this->custom_field_heading)); ?>
                  <?php endif; ?>
                    <?php if(!empty($this->links)) :?>
                  <div class="clr sitemember_action_link_options sitemember_action_links">
                    <?php
                    //FOR MESSAGE LINK
                    $items = Engine_Api::_()->getItem('user', $sitemember->user_id);
                    if ((Engine_Api::_()->seaocore()->canSendUserMessage($items)) && (!empty($viewer_id)) && !empty($this->links) && in_array('messege', $this->links)) : 
                      ?>
                      <a class="buttonlink sitemember_action_links_message" href="<?php echo $this->baseUrl() ?>/messages/compose/to/<?php echo $sitemember->user_id ?>" target="_parent" class="buttonlink"><?php echo $this->translate('Message'); ?></a>
                    <?php endif; ?>
                    <?php
                    if (!empty($this->links) && in_array('addfriend', $this->links)):
                      //Add friend link.
                      $uaseFRIENFLINK = $this->userFriendshipAjax($this->user($sitemember->user_id));
                      ?>
                      <?php if (!empty($uaseFRIENFLINK)) : ?>
                        <?php echo $uaseFRIENFLINK; ?>
                      <?php endif; ?>
                    <?php endif; ?>
                    <?php
                    //POKE WORK
                    $flag = false;
                    $resource_id = $sitemember->user_id;
                    $viewer_id = $this->viewer->getIdentity();
                    $user_subject = Engine_Api::_()->user()->getUser($resource_id);
                    $pokeEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('poke');
                    if (!empty($pokeEnabled) && (!empty($viewer_id))) {
                      $subject = Engine_Api::_()->getItem('user', $resource_id);
                      $getpokeFriend = Engine_Api::_()->poke()->levelSettings($subject);
                    }
                    if (!empty($pokeEnabled) && (!empty($getpokeFriend)) && ($resource_id !=
                            $viewer_id) && !empty($this->links) && in_array("poke", $this->links) && (!Engine_Api::_()->user()->getViewer()->isBlockedBy($user_subject) || Engine_Api::_()->user()->getViewer()->isAdmin())) {
                      ?>
                      <?php if (!$flag) : ?>
                        <?php
                        $flag = true;
                      endif;
                      ?>
                      <a href="javascript:void(0);" onclick="showSmoothBox('<?php
                      echo $this->escape($this->url(array('route' => 'poke_general', 'module' => 'poke', 'controller' => 'pokeusers',
                                  'action' => 'pokeuser', 'pokeuser_id' => $resource_id, 'format' => 'smoothbox'), 'default', true));
                      ?>');
                    return false;" class="buttonlink sitemember_action_links_poke" style="background-image: url(<?php echo $this->layout()->staticBaseUrl ?>application/modules/Poke/externals/images/poke_icon.png);"><?php echo $this->translate("Poke") ?></a>
                       <?php } //END POKE WORK.                      ?>
                    <?php
                    $suggestionEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('suggestion');
                    $getMemberFriend = Engine_Api::_()->seaocore()->isMember($resource_id);
                    if (!empty($suggestionEnabled)) {
                      $moduleNmae = 'friend';
                      $suggestion_frienf_link_show = Engine_Api::_()->suggestion()->getModSettings("$moduleNmae", "link");
                    }
                    if (!empty($suggestionEnabled) && !empty($getMemberFriend) &&
                            (!empty($suggestion_frienf_link_show)) && !empty($this->links) &&
                            in_array("suggestion", $this->links) && (!empty($viewer_id))) {
                      ?>
                      <?php if (!$flag) : ?>
                        <?php
                        $flag = true;
                      endif;
                      ?>
                      <a href="javascript:void(0);" onclick="showSmoothBox('<?php
                      echo $this->escape($this->url(array('module' => 'suggestion', 'controller' => 'index', 'action' => 'switch-popup', 'modName' => 'user', 'modContentId' => $resource_id, 'modError' => 1, 'format' => 'smoothbox'), 'default', true));
                      ?>');
                    return false;" class="buttonlink sitemember_action_links_suggestion" style="background-image: url(<?php echo $this->layout()->staticBaseUrl ?>application/modules/Suggestion/externals/images/sugg_blub.png);"><?php echo $this->translate("Suggest to Friends") ?></a><?php } //END SUGGESTION WORK.   ?>
                  </div>
                    <?php endif; ?>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
      <?php endif; ?>

      <?php
      if ($this->pinboard_view):
        ?>
        <div id="pinboard_view" class="sitemember_container <?php if(!empty($this->circularImage)):?> sitemember_circular_container <?php endif;?>" <?php if ($this->defaultView != 3): ?> style="display: none;" <?php endif; ?>  style="display: none; position: relative; height: 909px;"z>
          <div class="sitemember_pinboard_view">
            <?php
            $countButton = count($this->show_buttons);
            ?>
            <?php foreach ($this->paginator as $sitemember): ?>
              <?php $noOfButtons = $countButton;
              ?>
              <div class="seaocore_list_wrapper" style="width:<?php 237 //echo $this->pinboarditemWidth;                                                                            ?>px;">
                <div class="seaocore_board_list b_medium" style="width:<?php echo $this->pinboarditemWidth; ?>px;">
                  <?php if (!empty($this->statistics) && in_array('featuredLabel', $this->statistics) && $sitemember->featured): ?>
                    <i class="seaocore_list_featured_label" title="<?php echo $this->translate('Expert'); ?>"><?php echo $this->translate('Expert'); ?></i>
                  <?php endif; ?>
                  <div>

                    <div class="seaocore_board_list_thumb">
                      <a href="<?php echo $sitemember->getHref() ?>" class="seaocore_thumb">
                        <table style="height: <?php echo 30 * $noOfButtons ?>px;">
                          <tr valign="middle">
                            <td>
                              <?php $options = array('align' => 'center'); ?>
                             <?php if($this->circularImage):?>  
                        
                        <?php
                        $url = $sitemember->getPhotoUrl(($this->pinboarditemWidth > 300) ? 'thumb.main' : 'thumb.profile');
                        if (empty($url)): $url = $this->layout()->staticBaseUrl . 'application/modules/Sitemember/externals/images/nophoto_user_thumb_normal.png';
                        endif;
                        ?>
                        <span style="background-image: url(<?php echo $url; ?>); <?php if($this->circularImage):?>height:<?php echo $this->circularPinboardImageHeight; ?>px;<?php endif;?>"></span>
                        
                      <?php else:?>
                       <?php
                              echo $this->itemPhoto($sitemember, ($this->pinboarditemWidth > 300) ? 'thumb.main' : 'thumb.profile', '', $options);
                              ?>
                      <?php endif;?>   

                              
                            </td>
                          </tr>
                        </table>
                      </a>
                    </div>
                    <?php if (!empty($this->statistics) && in_array('sponsoredLabel', $this->statistics) && !empty($sitemember->sponsored)): ?>
                        <div class="seaocore_list_sponsored_label" style="padding:initial; background: <?php echo $this->settings->getSetting('sitemember.sponsoredcolor', '#FC0505'); ?>">
                          <?php echo $this->translate('Practice Leader'); ?>
                        </div>
                    <?php endif; ?>    
                    <div class="seaocore_board_list_cont">
                      <div class="seaocore_title">
                        <?php echo $this->htmlLink($sitemember->getHref(), Engine_Api::_()->seaocore()->seaocoreTruncateText($sitemember->getTitle(), $this->title_truncation), array('class' => 'sea_add_tooltip_link', 'rel' => 'user' . ' ' . $sitemember->user_id)); ?>

                        <?php
                        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteverify') && Engine_Api::_()->authorization()->getPermission($sitemember->level_id, 'siteverify', 'allow_verify') && !empty($this->statistics) && in_array('verifyLabel', $this->statistics)) :
                          $verify_count = Engine_Api::_()->getDbtable('verifies', 'siteverify')->getVerifyCount($sitemember->user_id);
                          $user = Engine_Api::_()->getItem('user', $sitemember->user_id);
                          $verify_limit = Engine_Api::_()->authorization()->getPermission($user->level_id, 'siteverify', 'verify_limit');
                          ?>
                          <?php if (($verify_count >= $verify_limit)): ?>
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
                              <?php //else: ?>
            <!--                              <img title="Offline" src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/offline.png' alt="" class="fleft" />-->
                              <!--<?php //echo $this->translate("Offline");  ?>-->
                            <?php endif; ?>
                          </span>
                        <?php endif; ?>
                      </div>

                      <div class='sitemember_browse_list_info'>
                        <?php if (!empty($this->statistics)) : ?>
                          <?php echo $this->memberInfo($sitemember, $this->statistics, array('customParams' => $this->customParams, 'custom_field_title' => $this->custom_field_title, 'custom_field_heading' => $this->custom_field_heading)); ?>
                        <?php endif; ?>

                          <div class="clr sitemember_action_link_options sitemember_action_links" >  
                        <?php
                        //FOR MESSAGE LINK
                        $items = Engine_Api::_()->getItem('user', $sitemember->user_id);
                        $viewer_id = $this->viewer->getIdentity();
                        if ((Engine_Api::_()->seaocore()->canSendUserMessage($items)) && (!empty($viewer_id)) && !empty($this->links) && in_array('messege', $this->links)) :
                          ?>
                          <a href="<?php echo $this->baseUrl() ?>/messages/compose/to/<?php echo $sitemember->user_id ?>" target="_parent" class="buttonlink sitemember_action_links_message"><?php echo $this->translate('Message'); ?></a>
                        <?php endif; ?>

                        <?php
                        if (!empty($this->links) && in_array('addfriend', $this->links)):
                          //Add friend link.
                          $uaseFRIENFLINK = $this->userFriendshipAjax($this->user($sitemember->user_id));
                          ?>
                          <?php if (!empty($uaseFRIENFLINK)) : ?>
                            <?php echo $uaseFRIENFLINK; ?>
                          <?php endif; ?>
                        <?php endif; ?>
                        <?php
                        //POKE WORK
                        $flag = false;
                        $resource_id = $sitemember->user_id;
                        $viewer_id = $this->viewer->getIdentity();
                        $user_subject = Engine_Api::_()->user()->getUser($resource_id);
                        $pokeEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('poke');
                        if (!empty($pokeEnabled) && (!empty($viewer_id))) {
                          $subject = Engine_Api::_()->getItem('user', $resource_id);
                          $getpokeFriend = Engine_Api::_()->poke()->levelSettings($subject);
                        }

                        if (!empty($pokeEnabled) && (!empty($getpokeFriend)) && ($resource_id !=
                                $viewer_id) && !empty($this->links) && in_array("poke", $this->links) && (!Engine_Api::_()->user()->getViewer()->isBlockedBy($user_subject) || Engine_Api::_()->user()->getViewer()->isAdmin())) {
                          ?>
                          <?php if (!$flag) : ?>
                            <?php
                            $flag = true;
                          endif;
                          ?>
                          <a href="javascript:void(0);" onclick="showSmoothBox('<?php
                          echo $this->escape($this->url(array('route' => 'poke_general', 'module' => 'poke', 'controller' => 'pokeusers',
                                      'action' => 'pokeuser', 'pokeuser_id' => $resource_id, 'format' => 'smoothbox'), 'default', true));
                          ?>');
                    return false;" class="buttonlink sitemember_action_links_poke" style="background-image: url(<?php echo $this->layout()->staticBaseUrl ?>application/modules/Poke/externals/images/poke_icon.png);"><?php echo $this->translate("Poke") ?></a>
                           <?php } //END POKE WORK.                                   ?>

                        <?php //FOR SUGGESTION LINK SHOW IF SUGGESTION PLUGIN INSTALL AT HIS SITE.          ?>
                        <?php
                        $suggestionEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('suggestion');
                        $getMemberFriend = Engine_Api::_()->seaocore()->isMember($resource_id);

                        if (!empty($suggestionEnabled)) {
                          $moduleNmae = 'friend';
                          $suggestion_frienf_link_show = Engine_Api::_()->suggestion()->getModSettings("$moduleNmae", "link");
                        }

                        if (!empty($suggestionEnabled) && !empty($getMemberFriend) &&
                                (!empty($suggestion_frienf_link_show)) && !empty($this->links) &&
                                in_array("suggestion", $this->links) && (!empty($viewer_id))) {
                          ?>
                          <?php if (!$flag) : ?>
                            <?php
                            $flag = true;
                          endif;
                          ?>
                          <a href="javascript:void(0);" onclick="showSmoothBox('<?php
                          echo $this->escape($this->url(array('module' => 'suggestion', 'controller' => 'index', 'action' =>
                                      'switch-popup', 'modName' => 'user', 'modContentId' => $resource_id, 'modError' => 1, 'format' => 'smoothbox'), 'default', true));
                          ?>');
                    return false;" class="buttonlink sitemember_action_links_suggestion" style="background-image: url(<?php echo $this->layout()->staticBaseUrl ?>application/modules/Suggestion/externals/images/sugg_blub.png);"><?php echo $this->translate("Suggest to Friends") ?></a>
                           <?php } //END SUGGESTION WORK.                                                 ?></div>
                      </div>

                    </div>
                    <?php if (!empty($this->show_buttons)): ?>
                      <!--                      <div class="seaocore_board_list_btm o_hidden">-->

                      <div class="seaocore_board_list_action_links">
                        <?php $urlencode = urlencode(((!empty($_ENV["HTTPS"]) && 'on' == strtolower($_ENV["HTTPS"])) ? "https://" : "http://") . $_SERVER['HTTP_HOST'] . $sitemember->getHref()); ?>

                        <?php if($this->viewer()->getIdentity()):?>
                            <?php if (in_array('like', $this->show_buttons)): ?>
                              <a href="javascript:void(0)" title="<?php echo $this->translate('Like'); ?>" class="seaocore_board_icon like_icon <?php echo $sitemember->getGuid() ?>like_link" id="<?php echo $sitemember->getType() ?>_<?php echo $sitemember->getIdentity() ?>like_link" <?php if ($sitemember->likes()->isLike($this->viewer())): ?>style="display: none;" <?php endif; ?>onclick="en4.seaocorepinboard.likes.like('<?php echo $sitemember->getType() ?>', '<?php echo $sitemember->getIdentity() ?>');" ><!--<?php echo $this->translate('Like'); ?>--></a>

                              <a  href="javascript:void(0)" title="<?php echo $this->translate('Unlike'); ?>" class="seaocore_board_icon unlike_icon <?php echo $sitemember->getGuid() ?>unlike_link" id="<?php echo $sitemember->getType() ?>_<?php echo $sitemember->getIdentity() ?>unlike_link" <?php if (!$sitemember->likes()->isLike($this->viewer())): ?>style="display:none;" <?php endif; ?> onclick="en4.seaocorepinboard.likes.unlike('<?php echo $sitemember->getType() ?>', '<?php echo $sitemember->getIdentity() ?>');"><!--<?php echo $this->translate('Unlike'); ?>--></a>
                            <?php endif; ?>
                        <?php endif; ?>

                        <?php if (in_array('facebook', $this->show_buttons)): ?>
                          <?php echo $this->htmlLink('http://www.facebook.com/share.php?u=' . $urlencode . '&t=' . $sitemember->getTitle(), $this->translate(''), array('id' => 'pb_ch_wd', 'class' => 'pb_ch_wd seaocore_board_icon fb_icon', 'onclick' => 'childWindowOpen(this);' , 'title' => 'Facebook')) ?>
                        <?php endif; ?>

                        <?php if (in_array('twitter', $this->show_buttons)): ?>
                          <?php echo $this->htmlLink('http://twitter.com/share?url=' . $urlencode . '&text=' . $sitemember->getTitle(), $this->translate(''), array('class' => 'pb_ch_wd seaocore_board_icon tt_icon', 'onclick' => 'childWindowOpen(this);' , 'title' => 'Twitter')) ?>
                        <?php endif; ?>

                        <?php if (in_array('pinit', $this->show_buttons)): ?>
                          <a href="http://pinterest.com/pin/create/button/?url=<?php echo $urlencode; ?>&media=<?php echo urlencode((!preg_match("~^(?:f|ht)tps?://~i", $sitemember->getPhotoUrl('thumb.profile')) ? (((!empty($_ENV["HTTPS"]) && 'on' == strtolower($_ENV["HTTPS"])) ? "https://" : "http://") . $_SERVER['HTTP_HOST'] ) : '') . $sitemember->getPhotoUrl('thumb.profile')); ?>&description=<?php echo $sitemember->getTitle(); ?>"  class="pb_ch_wd seaocore_board_icon pin_icon" onclick="childWindowOpen(this);" title="Pin It" ><!--<?php echo $this->translate('Pin It') ?>--></a>
                        <?php endif; ?>
                      </div>
                      <!--                      </div>-->
                    <?php endif; ?>
                  </div>
                </div></div>
            <?php endforeach; ?></div></div>
      <?php endif; ?>


      <div id="sitemember_map_canvas_view_browse" <?php if ($this->defaultView != 2): ?> style="display: none;" <?php endif; ?>>
        <div class="seaocore_map clr o_hidden">
          <div id="sitemember_browse_map_canvas" class="sitemember_list_map"> </div>
          <div class="clear mtop10"></div>
          <?php $siteTitle = $this->settings->core_general_site_title; ?>
          <?php if (!empty($siteTitle)) : ?>
            <div class="seaocore_map_info"><?php echo $this->translate("Locations on %s", "<a href='' target='_blank'>$siteTitle</a>"); ?></div>
          <?php endif; ?>
        </div>
        <?php if ($this->flageSponsored && $this->map_view && $enableBouce): ?>
          <a href="javascript:void(0);" onclick="toggleBounce();" class="fleft sitemember_list_map_bounce_link"> <?php echo $this->translate('Stop Bounce'); ?></a>
        <?php endif; ?>
      </div>
      <div class="clear"></div>
      <div class="seaocore_pagination"></div>
      <div class="clr" id="scroll_bar_height"></div>
      <div id="seaocore_view_more" class="seaocore_view_more mtop10" style="display: none;">
        <?php
        echo $this->htmlLink('javascript:void(0);', $this->translate('View More'), array(
            'id' => '',
            'class' => 'buttonlink icon_viewmore'
        ))
        ?>
      </div>
      <div class="seaocore_loading" id="seaocore_loading" style="display: none;">
        <img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Core/externals/images/loading.gif' class='mright5' />
        <?php echo $this->translate("Loading ...") ?>
      </div>
    <?php else: ?>
      <br/>
      <div class="tip mtip10">
        <span> <?php echo $this->translate('No matching results were found for members.'); ?>
        </span>
      </div>
    <?php endif; ?>
  </div>

  <?php if ($this->showContent == 3): ?>
    <script type="text/javascript">
        en4.core.runonce.add(function() {
          $('seaocore_view_more').style.display = 'block';
          hideViewMoreLink('<?php echo $this->showContent; ?>');
        });</script>
  <?php elseif ($this->showContent == 2): ?>
    <script type="text/javascript">
      en4.core.runonce.add(function() {
        $('seaocore_view_more').style.display = 'block';
        hideViewMoreLink('<?php echo $this->showContent; ?>');
      });</script>
  <?php else: ?>
    <script type="text/javascript">
      en4.core.runonce.add(function() {
        $('seaocore_view_more').style.display = 'none';
      });
    </script>
    <?php
    echo $this->paginationControl($this->result, null, array("pagination/pagination.tpl", "sitemember"), array("orderby" => $this->orderby, "query" => $this->formValues));
    ?>
  <?php endif; ?>

  <script type="text/javascript">
    function getNextPage() {
      return <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() + 1) ?>
    }
    function hideViewMoreLink(showContent) {

      if (showContent == 3) {
        $('seaocore_view_more').style.display = 'none';
        var totalCount = '<?php echo $this->paginator->count(); ?>';
        var currentPageNumber = '<?php echo $this->paginator->getCurrentPageNumber(); ?>';

        function doOnScrollLoadPage()
        {
          if ($('scroll_bar_height')) {
            if (typeof($('scroll_bar_height').offsetParent) != 'undefined') {
              var elementPostionY = $('scroll_bar_height').offsetTop;
            } else {
              var elementPostionY = $('scroll_bar_height').y;
            }
            if (elementPostionY <= window.getScrollTop() + (window.getSize().y - 20)) {
              if ((totalCount != currentPageNumber) && (totalCount != 0))
                sendAjaxRequestSM();
            }
          }
        }
        window.onscroll = doOnScrollLoadPage;
      } else if (showContent == 2)
      {
        var view_more_content = $('seaocore_view_more');
        view_more_content.setStyle('display', '<?php echo ( $this->paginator->count() == $this->paginator->getCurrentPageNumber() || $this->totalCount == 0 ? 'none' : '' ) ?>');
        view_more_content.removeEvents('click');
        view_more_content.addEvent('click', function() {
          sendAjaxRequestSM();
        });
      }
    }
  </script>

  <script type="text/javascript" >
    function switchview(flage) {

      if (flage == 2) {

        if ($('sitemember_map_canvas_view_browse')) {
          $('sitemember_map_canvas_view_browse').style.display = 'block';
  <?php if ($this->map_view && $this->paginator->count() > 0): ?>
            google.maps.event.trigger(map, 'resize');
            map.setZoom(<?php echo $defaultZoom ?>);
            map.setCenter(new google.maps.LatLng(<?php echo $latitude ?>,<?php echo $longitude ?>));
  <?php endif; ?>
          if ($('grid_view'))
            $('grid_view').style.display = 'none';
          if ($('image_view'))
            $('image_view').style.display = 'none';
          if ($('pinboard_view'))
            $('pinboard_view').style.display = 'none';
        }
      } else if (flage == 1) {

        if ($('image_view')) {
          if ($('sitemember_map_canvas_view_browse'))
            $('sitemember_map_canvas_view_browse').style.display = 'none';
          if ($('grid_view'))
            $('grid_view').style.display = 'none';
          if ($('pinboard_view'))
            $('pinboard_view').style.display = 'none';
          $('image_view').style.display = 'block';
        }
      } else if (flage == 0) {
        if ($('grid_view')) {
          if ($('sitemember_map_canvas_view_browse'))
            $('sitemember_map_canvas_view_browse').style.display = 'none';
          $('grid_view').style.display = 'block';
          if ($('image_view'))
            $('image_view').style.display = 'none';
          if ($('pinboard_view'))
            $('pinboard_view').style.display = 'none';
        }
      }
      else if (flage == 3) {
        if ($('pinboard_view')) {
          if ($('sitemember_map_canvas_view_browse'))
            $('sitemember_map_canvas_view_browse').style.display = 'none';
          if ($('grid_view'))
            $('grid_view').style.display = 'none';
          if ($('image_view'))
            $('image_view').style.display = 'none';
          $('pinboard_view').style.display = 'block';
          for (var i=0; i<= 10;i++){
          (function(){
            $("pinboard_view").pinBoardSeaoMasonry({
                  singleMode: true,
                  itemSelector: '.seaocore_list_wrapper'
                });
          }).delay(500*i);
        }
        }
      }
    }
  </script>

  <script type="text/javascript">

    /* moo style */
    en4.core.runonce.add(function() {
      //opacity / display fix
      $$('.siteevent_tooltip').setStyles({
        opacity: 0,
        display: 'block'
      });
      //put the effect in place
      $$('.jq-siteevent_tooltip li').each(function(el, i) {
        el.addEvents({
          'mouseenter': function() {
            el.getElement('div').fade('in');
          },
          'mouseleave': function() {
            el.getElement('div').fade('out');
          }
        });
      });
  <?php if ($this->paginator->count() > 0): ?>
        switchview(<?php echo $this->defaultView ?>);
  <?php endif; ?>
    });</script>

  <?php if (empty($this->is_ajax)): ?>
    <script type="text/javascript">

      var markerClusterer = null;
      var side_bar_html = "";
      // arrays to hold copies of the markers and html used by the side_bar
      // because the function closure trick doesnt work there
      var gmarkers = [];
      // global "map" variable
      var map = null;
      // A function to create the marker and set up the event window function
      function createMarker(latlng, name, html, location, count) {
        var image = ('https:' == document.location.protocol ? 'https://' : 'http://') + 'chart.googleapis.com/chart?chst=d_map_pin_letter&chco=FFFFFF,008CFF,000000&chld=' + count + '|008CFF|000000';

        var contentString = html;
        if (name == 0) {
          var marker = new google.maps.Marker({
            position: latlng,
            map: map,
            title: location,
            icon: image,
            count: count,
            animation: google.maps.Animation.DROP,
            zIndex: Math.round(latlng.lat() * -100000) << 5
          });
        }
        else {
          var marker = new google.maps.Marker({
            position: latlng,
            map: map,
            title: location,
            icon: image,
            count: count,
            draggable: false,
            animation: google.maps.Animation.BOUNCE
          });
        }
        gmarkers.push(marker);
        google.maps.event.addListener(marker, 'click', function() {
          infowindow.setContent(contentString);
          google.maps.event.trigger(map, 'resize');
          infowindow.open(map, marker);
        });
      }

      function initialize() {

        // create the map
        var myOptions = {
          zoom: <?php echo $defaultZoom ?>,
          center: new google.maps.LatLng(<?php echo $latitude ?>,<?php echo $longitude ?>),
          navigationControl: true,
          mapTypeId: google.maps.MapTypeId.ROADMAP
        }
        map = new google.maps.Map(document.getElementById("sitemember_browse_map_canvas"),
                myOptions);
        google.maps.event.addListener(map, 'click', function() {
          infowindow.close();
          google.maps.event.trigger(map, 'resize');
        });

        clearOverlays();
      }


      function clearOverlays() {
        infowindow.close();
        google.maps.event.trigger(map, 'resize');

        if (gmarkers) {
          for (var i = 0; i < gmarkers.length; i++) {
            gmarkers[i].setMap(null);
          }
        }

        if (markerClusterer) {
          markerClusterer.clearMarkers();
        }
      }

      /* moo style */
      en4.core.runonce.add(function() {
    <?php if ($this->paginator->count() > 0): ?>
      <?php if ($this->map_view): ?>
            initialize();
      <?php endif; ?>
    <?php endif; ?>
      });
    </script>
  <?php endif; ?>


  <?php if ($this->map_view && $this->paginator->count() > 0): ?>

    <script type="text/javascript">
      //<![CDATA[
      // this variable will collect the html which will eventually be placed in the side_bar
      en4.core.runonce.add(function() {
    <?php if (count($this->locations) > 0) : ?>
    <?php foreach ($this->locations as $location) : ?>
          // obtain the attribues of each marker
          var lat = <?php echo $location['latitude'] ?>;
          var lng =<?php echo $location['longitude'] ?>;
          var point = new google.maps.LatLng(lat, lng);
      <?php if (!empty($enableBouce)): ?>
            var sponsored = '<?php echo $this->sitemember[$location->locationitem_id]->sponsored ?>';
      <?php else: ?>
            var sponsored = 0;
      <?php endif; ?>
          var contentString = "<?php
      echo $this->string()->escapeJavascript($this->partial('application/modules/Sitemember/views/scripts/_mapInfoWindowContent.tpl', array(
                  'sitemember' => $this->sitemember[$location->locationitem_id],
                  'statistics' => $this->statistics,
                  'customParams' => $this->customParams,
                  'custom_field_title' => $this->custom_field_title,
                  'custom_field_heading' => $this->custom_field_heading,
              )), false);
      ?>";
          var marker = createMarker(point, sponsored, contentString, "<?php echo $location->location ?>", 1);
          oms.addMarker(marker);

    <?php endforeach; ?>
    <?php endif; ?>

        markerClusterer = new MarkerClusterer(map, gmarkers, {
          zoomOnClick: true
        });

        google.maps.event.addListener(markerClusterer, 'clusterclick', function(cluster) {
          var info = new google.maps.MVCObject;
          info.set('position', cluster.center_);
          var markers = cluster.getMarkers();

          for (var i = 1; i < markers.length; i++) {
            if (info.position != markers[i].position) {
              return;
            }
          }
          if (marker) {
            marker.setMap(null);
            marker = null;
          }
        });
      });
      var infowindow = new google.maps.InfoWindow(
              {
                size: new google.maps.Size(250, 50)
              });
      function toggleBounce() {
        for (var i = 0; i < gmarkers.length; i++) {
          if (gmarkers[i].getAnimation() != null) {
            gmarkers[i].setAnimation(null);
          }
        }
      }
    </script>
  <?php endif; ?>

<?php else: ?>
  <div id="layout_sitemember_browse_members_<?php echo $this->identity; ?>">
  </div>

  <script type="text/javascript">
    var requestParams = $merge(<?php echo json_encode($this->paramsLocation); ?>, {'content_id': '<?php echo $this->identity; ?>'});
    var params = {
      'detactLocation': <?php echo $this->detactLocation; ?>,
      'responseContainer': 'layout_sitemember_browse_members_<?php echo $this->identity; ?>',
      'locationmiles': <?php echo $this->settings->getSetting('seaocore.locationdefaultmiles', 1000); ?>,
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

<script type="text/javascript">
    $$('.core_main_user').getParent().addClass('active');
</script>
