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
$apiKey = Engine_Api::_()->seaocore()->getGoogleMapApiKey();
$this->headScript()->appendFile("https://maps.googleapis.com/maps/api/js?libraries=places&key=$apiKey");
?>

<?php $this->headLink()->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/styles/styles.css'); ?>
<?php include APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/Adintegration.tpl';?>

<?php
	$sitepageOfferEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepageoffer');
    if ($sitepageOfferEnabled) { 
        $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitepageoffer/externals/styles/style_sitepageoffer.css');
    }
    $verifyTableObj = Engine_Api::_()->getDbtable('verifies', 'sitepage');
    $verify_limit = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.verify.limit', 3);
    include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/common_style_css.tpl';

    $this->headLink()->prependStylesheet($this->layout()->staticBaseUrl.'application/modules/Sitepage/externals/styles/sitepage-tooltip.css');

    $viewer = Engine_Api::_()->user()->getViewer()->getIdentity();
    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
    $MODULE_NAME = 'sitepage';
    $RESOURCE_TYPE = 'sitepage_page';

?>

<?php
if ($this->is_ajax_load):
  ?>
<?php  $postedBy = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.postedby', 1); ?>
<?php  $enableBouce=Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.map.sponsored', 1); ?>
<?php  $latitude=Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.map.latitude', 0); ?>
<?php  $longitude=Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.map.longitude', 0); ?>
<?php  $defaultZoom=Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.map.zoom', 1); ?>

<?php if(empty($this->is_ajax)): ?>
  <script>
          function owner(thisobj) {
              var Obj_Url = thisobj.href;
              Smoothbox.open(Obj_Url);
          }

    var sitepages_likes = function(resource_id, resource_type) {
    var content_type = 'sitepage';
      //var error_msg = '<?php //echo $this->result['0']['like_id']; ?>';

    // SENDING REQUEST TO AJAX
    var request = createLikepage(resource_id, resource_type,content_type);

    // RESPONCE FROM AJAX
    request.addEvent('complete', function(responseJSON) {
     if (responseJSON.error_mess == 0) {
      $(resource_id).style.display = 'block';
      if(responseJSON.like_id )
      {
       $('backgroundcolor_'+ resource_id).className ="sitepage_browse_thumb sitepage_browse_liked";
       $('sitepage_like_'+ resource_id).value = responseJSON.like_id;
       $('sitepage_most_likes_'+ resource_id).style.display = 'none';
       $('sitepage_unlikes_'+ resource_id).style.display = 'block';
       $('show_like_button_child_'+ resource_id).style.display='none';
      }
      else
      {  $('backgroundcolor_'+ resource_id).className ="sitepage_browse_thumb";
       $('sitepage_like_'+ resource_id).value = 0;
       $('sitepage_most_likes_'+ resource_id).style.display = 'block';
       $('sitepage_unlikes_'+ resource_id).style.display = 'none';
       $('show_like_button_child_'+ resource_id).style.display='none';
      }

     }
     else {
      en4.core.showError('An error has occurred processing the request. The target may no longer exist.' + '<br /><br /><button onclick="Smoothbox.close()">Close</button>');
      return;
     }
    });
   }
    var createLikepage = function( resource_id, resource_type, content_type ){
    if($('sitepage_most_likes_'+ resource_id).style.display == 'block')
     $('sitepage_most_likes_'+ resource_id).style.display='none';


    if($('sitepage_unlikes_'+ resource_id).style.display == 'block')
     $('sitepage_unlikes_'+ resource_id).style.display='none';
     $(resource_id).style.display='none';
     $('show_like_button_child_'+ resource_id).style.display='block';

    if (content_type == 'sitepage') {
     var like_id = $(content_type + '_like_'+ resource_id).value
    }
    var url = '<?php echo $this->url(array('action' => 'global-likes' ), 'sitepage_like', true);?>';
    var request = new Request.JSON({

     url : url,
     data : {
      format : 'json',
      'resource_id' : resource_id,
      'resource_type' : resource_type,
      'like_id' : like_id
     }
    });
    request.send();
    return request;
   }
    var pageAction = function(page){

          var form;
          if($('filter_form')) {
              form=document.getElementById('filter_form');
          }else if($('filter_form_page')){
              form=$('filter_form_page');
          }
          form.elements['page'].value = page;
      <?php if($this->tag):?>
          form.elements['tag'].value = '<?php echo $this->tag?>';
      <?php endif;?>
          form.elements['category'].value = '<?php echo $this->category?>';
          form.elements['categoryname'].value = '<?php echo $this->categoryname?>';
          form.elements['subsubcategory'].value = '<?php echo $this->subsubcategory?>';
          form.elements['subcategory'].value = '<?php echo $this->subcategory?>';
          form.elements['subcategoryname'].value = '<?php echo $this->subcategoryname?>';
          form.elements['subsubcategoryname'].value = '<?php echo $this->subsubcategoryname?>';
      <?php if($this->sitepage_location): ?>
          form.elements['sitepage_location'].value = '<?php echo $this->sitepage_location?>';
      <?php  endif; ?>
          form.submit();
      }
  </script>
<?php endif;?>


<?php if ($this->paginator->count() > 0): ?>

    <?php if(empty($this->is_ajax)):?>
        <form id='filter_form_page' class='global_form_box' method='get' action='<?php echo $this->url(array('action' => 'index'), 'sitepage_general', true) ?>' style='display: none;'>
          <input type="hidden" id="page" name="page"  value=""/>
          <input type="hidden" id="tag" name="tag"  value=""/>
          <input type="hidden" id="sitepage_location" name="sitepage_location"  value=""/>
          <input type="hidden" id="category" name="category"  value=""/>
          <input type="hidden" id="categoryname" name="categoryname"  value=""/>
          <input type="hidden" id="subsubcategory" name="subsubcategory" value=""/>
          <input type="hidden" id="subcategory" name="subcategory"  value=""/>
          <input type="hidden" id="subcategoryname" name="subcategoryname"  value=""/>
          <input type="hidden" id="subsubcategoryname" name="subsubcategoryname" value=""/>
        </form>
    <?php endif;?>

    <?php if(empty($this->is_ajax)):?>
        <div class="sitepage_view_select">
        <div class="fleft">
            <?php echo $this->translate(array('%s page found.', '%s pages found.', $this->paginator->getTotalItemCount()),$this->locale()->toNumber($this->paginator->getTotalItemCount())); ?>
        </div>

        <?php if ((($this->list_view && $this->grid_view) || ($this->map_view && $this->grid_view) || ($this->list_view && $this->map_view))): ?>
            <?php if( $this->enableLocation  && $this->map_view): ?>
                <span class="seaocore_tab_select_wrapper fright">
                    <div class="seaocore_tab_select_view_tooltip"><?php echo $this->translate("Map View"); ?></div>
                    <span id="seaocore_tab_icon_map_view" class="seaocore_tab_icon tab_icon_map_view" onclick="switchview(2)"></span>
                </span>
            <?php endif;?>

            <?php  if( $this->grid_view): ?>
              <span class="seaocore_tab_select_wrapper fright">
                    <div class="seaocore_tab_select_view_tooltip"><?php echo $this->translate("Grid View"); ?></div>
                    <span id="seaocore_tab_icon_grid_view" class="seaocore_tab_icon tab_icon_grid_view" onclick="switchview(1)"></span>
              </span>
            <?php endif;?>

            <?php  if( $this->list_view): ?>
                <span class="seaocore_tab_select_wrapper fright">
                    <div class="seaocore_tab_select_view_tooltip"><?php echo $this->translate("List View"); ?></div>
                    <span id="seaocore_tab_icon_list_view" class="seaocore_tab_icon tab_icon_list_view" onclick="switchview(0)"></span>
                </span>
            <?php endif; ?>
        <?php endif; ?>
    </div>
    <?php endif;?>

    <div id="dynamic_app_info_page">
        <?php include APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/_recently_popular_random_page.tpl'; ?>
    </div>

    <div class="clr" id="scroll_bar_height"></div>
    <?php if (empty($this->is_ajax)) : ?>
        <div class = "seaocore_view_more mtop10" id="seaocore_view_more" style="display: none;">
          <?php
          echo $this->htmlLink('javascript:void(0);', $this->translate('View More'), array(
              'id' => '',
              'class' => 'buttonlink icon_viewmore'
          ))
          ?>
        </div>

        <div class="seaocore_view_more" id="loding_image" style="display: none;">
          <img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/core/loading.gif' style='margin-right: 5px;' />
          <?php echo $this->translate("Loading ...") ?>
        </div>

        <div id="hideResponse_div"> </div>
    <?php endif; ?>

<?php elseif ($this->search):  ?>

    <div class="tip">
         <?php  if (Engine_Api::_()->sitepage()->hasPackageEnable()):
          $createUrl=$this->url(array('action'=>'index'), 'sitepage_packages');
           else:
           $createUrl=$this->url(array('action'=>'create'), 'sitepage_general');
         endif; ?>
		<span>
            <?php echo $this->translate('Nobody has created a page with that criteria. Be the first to %1$screate%2$s one!', '<a href="' . $createUrl . '">', '</a>'); ?>
        </span>
	</div>

<?php else: ?>
  <div class="tip">
        <span>
          <?php echo $this->translate('No Pages have been created yet.'); ?>
            <?php if ($this->can_create): ?>
              <?php  if (Engine_Api::_()->sitepage()->hasPackageEnable()):
              $createUrl=$this->url(array('action'=>'index'), 'sitepage_packages');
               else:
               $createUrl=$this->url(array('action'=>'create'), 'sitepage_general');
             endif; ?>
                <?php echo $this->translate('Be the first to %1$screate%2$s one!', '<a href="' . $createUrl. '">', '</a>'); ?>
            <?php endif; ?>
        </span>
	</div>
<?php endif; ?>

<?php if(empty($this->is_ajax)):?>
    <script type="text/javascript" >
	    function switchview(flage) {
            if (flage == 2) {
                  if ($('rmap_canvas_view_page'))
                    $('rmap_canvas_view_page').style.display = 'block';
                  if ($('rgrid_view_page'))
                      $('rgrid_view_page').style.display = 'none';
                  if ($('rimage_view_page'))
                      $('rimage_view_page').style.display = 'none';
                  var mapIconId = document.getElementById("seaocore_tab_icon_map_view");
                  var listIconId = document.getElementById("seaocore_tab_icon_list_view");
                  var gridIconId = document.getElementById("seaocore_tab_icon_grid_view");
                  mapIconId.classList.add("active");
                  listIconId.classList.remove("active");
                  gridIconId.classList.remove("active");


              } else if (flage == 1) {
                  if ($('rmap_canvas_view_page'))
                      $('rmap_canvas_view_page').style.display = 'none';
                  if ($('rgrid_view_page'))
                      $('rgrid_view_page').style.display = 'none';
                  if ($('rimage_view_page'))
                      $('rimage_view_page').style.display = 'block';
                  var mapIconId = document.getElementById("seaocore_tab_icon_map_view");
                  var listIconId = document.getElementById("seaocore_tab_icon_list_view");
                  var gridIconId = document.getElementById("seaocore_tab_icon_grid_view");
                  mapIconId.classList.remove("active");
                  gridIconId.classList.add("active");
                  listIconId.classList.remove("active");
              } else {
                  if ($('rmap_canvas_view_page'))
                      $('rmap_canvas_view_page').style.display = 'none';
                  if ($('rgrid_view_page'))
                      $('rgrid_view_page').style.display = 'block';
                  if ($('rimage_view_page'))
                      $('rimage_view_page').style.display = 'none';
                  var mapIconId = document.getElementById("seaocore_tab_icon_map_view");
                  var listIconId = document.getElementById("seaocore_tab_icon_list_view");
                  var gridIconId = document.getElementById("seaocore_tab_icon_grid_view");
                  mapIconId.classList.remove("active");
                  gridIconId.classList.remove("active");
                  listIconId.classList.add("active");
              }
	    }

        en4.core.runonce.add(function() {
              $$('.sitepage_tooltip').setStyles({
                opacity: 0,
                display: 'block'
              });
              $$('.jq-sitepage_tooltip li').each(function(el,i) {
                el.addEvents({
                  'mouseenter': function() {
                    el.getElement('div').fade('in');
                  },
                  'mouseleave': function() {
                    el.getElement('div').fade('out');
                  }
                });
              });
            <?php if($this->paginator->count()>0):?>
                switchview(<?php echo $this->defaultView ?>);
            <?php endif;?>

            <?php if ($this->enableLocation && $this->map_view): ?>
                rinitializePage();
            <?php endif; ?>
        });
    </script>
 <?php endif;?>

<?php if (empty($this->is_ajax)) : ?>
  <script type="text/javascript">
    function viewMorePage() {
      var viewType = 2;
      if($('grid_view')) {
        if($('grid_view').style.display== 'block')
          viewType = 0;
      }
      if($('image_view')) {
      if($('image_view').style.display== 'block')
        viewType = 1;
      }

      $('seaocore_view_more').style.display = 'none';
      $('loding_image').style.display = '';
      var params = {
        requestParams:<?php echo json_encode($this->params) ?>
      };
      setTimeout(function() {
        en4.core.request.send(new Request.HTML({
          method: 'get',
          'url': en4.core.baseUrl + 'widget/index/mod/sitepage/name/pages-sitepage',
          data: $merge(params.requestParams, {
            format: 'html',
            subject: en4.core.subject.guid,
            page: getNextPage(),
            isajax: 1,
            show_content: '<?php echo $this->showContent;?>',
            view_type: viewType,
            loaded_by_ajax: true
          }),
          evalScripts: true,
          onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
            $('hideResponse_div').innerHTML = responseHTML;
            if($('grid_view')) {
              $('grid_view').getElement('.seaocore_browse_list').innerHTML = $('grid_view').getElement('.seaocore_browse_list').innerHTML + $('hideResponse_div').getElement('.seaocore_browse_list').innerHTML;
            }
            if($('image_view')) {
              $('image_view').getElement('.sitepage_img_view').innerHTML = $('image_view').getElement('.sitepage_img_view').innerHTML + $('hideResponse_div').getElement('.sitepage_img_view').innerHTML;
            }
            $('loding_image').style.display = 'none';
            switchview(viewType);
          }
        }));
      }, 800);

      return false;
    }
  </script>
<?php endif; ?>

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
  <?php echo $this->paginationControl($this->result, null, array("pagination/pagination.tpl", "sitepage"), array("orderby" => $this->orderby, "query" => $this->formValues)); ?>
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
        if($('scroll_bar_height')) {
          if (typeof($('scroll_bar_height').offsetParent) != 'undefined') {
            var elementPostionY = $('scroll_bar_height').offsetTop;
          } else {
            var elementPostionY = $('scroll_bar_height').y;
          }
          if (elementPostionY <= window.getScrollTop() + (window.getSize().y - 40)) {
            if ((totalCount != currentPageNumber) && (totalCount != 0)) {
              viewMorePage();
            }
          }
        }
      }

      window.onscroll = doOnScrollLoadPage;

    }
    else if (showContent == 2)
    {
      var view_more_content = $('seaocore_view_more');
      view_more_content.setStyle('display', '<?php echo ( $this->paginator->count() == $this->paginator->getCurrentPageNumber() || $this->totalCount == 0 ? 'none' : '' ) ?>');
      view_more_content.removeEvents('click');
      view_more_content.addEvent('click', function() {
        viewMorePage();
      });
    }
  }
</script>

<?php else: ?>
  <div id="layout_sitepage_pages_sitepage_<?php echo $this->identity; ?>">
  </div>

  <script type="text/javascript">
    var requestParams = $merge(<?php echo json_encode($this->paramsLocation); ?>, {'content_id': '<?php echo $this->identity; ?>'});
    var params = {
      'detactLocation': <?php echo $this->detactLocation; ?>,
      'responseContainer': 'layout_sitepage_pages_sitepage_<?php echo $this->identity; ?>',
      'locationmiles': <?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.locationdefaultmiles', 1000); ?>,
      requestParams: requestParams,
      'method': 'get'
    };

    en4.seaocore.locationBased.startReq(params);
  </script>
<?php endif; ?>

<script type="text/javascript">
    en4.core.runonce.add(function () {
       showPageShareLinks();
    });
</script>

<style>
    .sitepage_browse_thumb .sitepage_browse_title{
        background: transparent !important;
    }
    #rmap_canvas_page {
        width: 100% !important;
        height: 400px;
        float: left;
    }
    #rmap_canvas_page > div{
        height: 300px;
    }
    #infoPanel {
        float: left;
        margin-left: 10px;
    }
    #infoPanel div {
        margin-bottom: 5px;
    }
</style>