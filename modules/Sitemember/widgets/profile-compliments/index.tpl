<?php try{
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
                    '<img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/core/loading.gif" alt="Loading" /><?php echo $this->translate("Loading ...") ?></div>' +
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
$this->headLink()->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/styles/style_board.css')
        ->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/styles/styles.css')
        ->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitemember/externals/styles/style_sitemember.css');
?>



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
  
  <?php $viewer_id = $this->viewer->getIdentity(); ?>

  <?php if ($this->paginator->count() > 0): ?>
    
   <?php if (empty($this->is_ajax)): ?>

      <script>
        function sendAjaxRequestSM() {
          if (en4.core.request.isRequestActive())
                  return;
          var params = {
                  requestParams:<?php echo json_encode($this->params) ?>,
                  responseContainer: $('dynamic_app_info_sitemember_' +<?php echo sprintf('%d', $this->identity) ?>)
          }
          params.requestParams.page = getNextPage();
     
          params.requestParams.content_id = '<?php echo $this->identity ?>';
          $('seaocore_view_more').style.display = 'none';
          $('seaocore_loading').style.display = '';
          var url = en4.core.baseUrl + 'widget';
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

                if ($('list_view')) {
                  if (element.getElement('.sitemember_list_view') && $$('.sitemember_list_view')) {
                    Elements.from(element.getElement('.sitemember_list_view').innerHTML).inject(params.responseContainer.getElement('.sitemember_list_view'));
                  }
                }
              }
              en4.core.runonce.trigger();
              Smoothbox.bind(params.responseContainer);
              
            }
          });
          en4.core.request.send(request);
        }
      </script>

    <?php endif; ?>

    <div id="dynamic_app_info_sitemember_<?php echo $this->identity; ?>">
        <div class="sitemember_browse_lists_view_options b_medium">
          <div class="">
          <?php echo $this->translate(array('%s compliments.', '%s compliments.', $this->totalResults), $this->locale()->toNumber($this->totalResults)) ?>
                <div style="float: right">
                    <?php echo $this->content()->renderWidget('sitemember.compliment-me') ?>
                </div>
          </div>
       </div>
        <div id="list_view" class="<?php if(!empty($this->circularImage)):?> sitemember_circular_container <?php endif;?>">

           <ul class="sitemember_browse_list sitemember_list_view">
                
              <?php
              foreach ($this->paginator as $compliment):
                      $sitemember = Engine_Api::_()->user()->getUser($compliment->user_id);
                ?>
                <?php ?>
                
                <li class="b_medium">
                  <div class='sitemember_browse_list_photo b_medium'>
                  <a href="<?php echo $sitemember->getHref() ?>" class="sitemember_thumb">
                        <?php
                        $url = $sitemember->getPhotoUrl('thumb.profile');
                        if (empty($url)): $url = $this->layout()->staticBaseUrl . 'application/modules/Sitemember/externals/images/nophoto_user_thumb_normal.png';
                        endif;
                        ?>
                        <span style="background-image: url(<?php echo $url; ?>);"></span>
                        </a>
                  </div>

                  <div class='sitemember_browse_list_info'>
                    <div class="sitemember_browse_list_info_header">
                      <div class="seaocore_browse_list_info_title">
                        <?php echo $this->htmlLink($sitemember->getHref(), Engine_Api::_()->seaocore()->seaocoreTruncateText($sitemember->getTitle(), $this->title_truncation), array('title' => $sitemember->getTitle(), 'class' => 'sea_add_tooltip_link', 'rel' => 'user' . ' ' . $sitemember->getIdentity() )); ?>

                        <?php
                        if ($sitemember->getIdentity() && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteverify') && Engine_Api::_()->authorization()->getPermission($sitemember->level_id, 'siteverify', 'allow_verify') && !empty($this->statistics) && in_array('verifyLabel', $this->statistics)) :
                          $verify_count = Engine_Api::_()->getDbtable('verifies', 'siteverify')->getVerifyCount($sitemember->user_id);
                          $verify_limit = Engine_Api::_()->authorization()->getPermission($sitemember->level_id, 'siteverify', 'verify_limit');
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
                      </div>
                    </div> 
                    <?php if ($sitemember->getIdentity() && !empty($this->links)):?>
                      <div class="clr sitemember_action_link_options sitemember_action_links">
                        <?php
                        //FOR MESSAGE LINK
                        if ((Engine_Api::_()->seaocore()->canSendUserMessage($sitemember)) && (!empty($viewer_id)) && !empty($this->links) && in_array('messege', $this->links)) :
                          ?>
                          <span><a href="<?php echo $this->baseUrl() ?>/messages/compose/to/<?php echo $sitemember->user_id ?>" target="_parent" class="buttonlink sitemember_action_links_message"><?php echo $this->translate('Message'); ?></a></span>
                        <?php endif; ?>

                        <?php
                        if (!empty($this->links) && in_array('addfriend', $this->links)):
                          //Add friend link.
                          $uaseFRIENFLINK = $this->userFriendship($this->user($sitemember->user_id));
                          ?>
                          <?php if (!empty($uaseFRIENFLINK)) : ?>
                            <span><?php echo $uaseFRIENFLINK; ?></span>
                          <?php endif; ?>
                        <?php endif; ?>
                     </div>
                    <?php endif; ?>
 </div> 
                  <div class="sitemember_profile_compliment" >
                  <?php   $complimentItem = Engine_Api::_()->getItem('sitemember_compliment_category',$compliment->complimentcategory_id);
                   echo   $complimentItem ? $this->itemPhoto($complimentItem, '', '', array('align' => 'center')) : ''; 
                   ?>
                   <span class="sitemember_compliment_name">
                   <?php   echo $complimentItem->getTitle() ?>
                   </span>
                   <span class="sitemember_profile_compliment_date">
                   <i class="fa fa-calendar" aria-hidden="true"></i>
                      <?php    echo $this->timestamp(strtotime($compliment->date));  ?>
                  </span>
                  <?php if (!empty($compliment->body)):?>
                    <span class="sitemember_profile_compliment_body">
                      <q><?php    echo $compliment->body;  ?></q>
                    </span>
                  <?php endif;?>
                  </div>
                </li>
                <?php
              endforeach;
              ?>
            </ul>
          
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
        <img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/core/loading.gif' class='mright5' />
        <?php echo $this->translate("Loading ...") ?>
      </div> 
        </div>
    <?php else: ?>
      <br/>
      <div class="tip mtip10"> 
        <span> <?php echo $this->translate('No compliments yet.'); ?>
        </span>
        <div style="float: right">
                    <?php echo $this->content()->renderWidget('sitemember.compliment-me') ?>
        </div>
      </div>
    <?php endif; ?>
  

  <?php if ($this->showContent == 2): ?>
    <script type="text/javascript">
        en4.core.runonce.add(function() {
          $('seaocore_view_more').style.display = 'block';
          hideViewMoreLink('<?php echo $this->showContent; ?>');
        });</script>
  <?php elseif ($this->showContent == 1): ?>
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

      if (showContent == 2) {
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
      } else if (showContent == 1)
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
<?php }catch(Exception $e) { die(" Excepton ".$e);} ?>
