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
<?php if (!empty($this->is_ajax_load) && !empty($this->titleLink) && ($this->titleLinkPosition == 'top')) : ?>
    <span class="sitepage_top_link">
        <?php echo $this->titleLink; ?>
    </span>
<?php endif; ?>
<?php
//GET API KEY
$apiKey = Engine_Api::_()->seaocore()->getGoogleMapApiKey();
$this->headScript()->appendFile("https://maps.googleapis.com/maps/api/js?libraries=places&key=$apiKey");
?>
<?php
$this->headScript()
        ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/scripts/core.js');
?>
<?php $this->headLink()->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/styles/styles.css'); ?>
<?php include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/common_style_css.tpl'; ?>
<?php
$this->headLink()
        ->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/styles/sitepage-tooltip.css');
?>
<?php
$enableBouce = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.map.sponsored', 1);
 ?>

<?php if ($this->is_ajax_load): ?>

    <div class="layout_core_container_tabs">
        <?php if (($this->list_view && $this->grid_view) || ($this->map_view && $this->grid_view) || ($this->list_view && $this->map_view) || count($this->tabs) > 1): ?>
            <?php if (count($this->tabs) > 1): ?>
                <div class="tabs_alt tabs_parent">
                <?php else: ?>
                    <div class="sitepage_view_select">
                    <?php endif; ?>
                    <?php if (count($this->sitepagesitepage)): ?>
                        <ul id="main_tabs">
                            <?php if (count($this->tabs) > 1): ?>
                                <?php $active = true; ?> 
                                <?php foreach ($this->tabs as $key => $tab): ?>
                                    <?php $class = $active ? 'active' : '' ?>
                                    <?php $active = false; ?> 
                                    <li class = '<?php echo $class ?>'  id = '<?php echo 'sitepage_home_page_' . $key . '_tab' ?>'>
                                        <a href='javascript:void(0);'  onclick="showListSitepage('<?php echo $tab['tabShow']; ?>', '<?php echo $key; ?>');"><?php echo $this->translate($tab['title']) ?></a>
                                    </li>
                                <?php endforeach; ?>
                            <?php endif; ?>  

                            <?php if (($this->list_view && $this->grid_view) || ($this->map_view && $this->grid_view) || ($this->list_view && $this->map_view)): ?>
                                <?php if ($this->enableLocation && $this->map_view): ?>
                                    <?php $latitude = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.map.latitude', 0); ?>
                                    <?php $longitude = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.map.longitude', 0); ?>
                                    <?php $defaultZoom = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.map.zoom', 1); ?>
                                    <li class="seaocore_tab_select_wrapper fright">
                                        <div class="seaocore_tab_select_view_tooltip"><?php echo $this->translate("Map View") ?></div>
                                        <span id="seaocore_tab_icon_map_view" class="seaocore_tab_icon tab_icon_map_view" onclick="rswitchviewPage(2)"></span>
                                    </li>
                                <?php endif; ?>
                                <?php if ($this->grid_view): ?> 
                                    <li class="seaocore_tab_select_wrapper fright">
                                        <div class="seaocore_tab_select_view_tooltip"><?php echo $this->translate("Grid View") ?></div>
                                        <span id="seaocore_tab_icon_grid_view" class="seaocore_tab_icon tab_icon_grid_view" onclick="rswitchviewPage(1)"></span>
                                    </li> 
                                <?php endif; ?>
                                <?php if ($this->list_view): ?>
                                    <li class="seaocore_tab_select_wrapper fright">
                                        <div class="seaocore_tab_select_view_tooltip"><?php echo $this->translate("List View") ?></div>
                                        <span id="seaocore_tab_icon_list_view" class="seaocore_tab_icon tab_icon_list_view" onclick="rswitchviewPage(0)"></span>
                                    </li>
                                <?php endif; ?>
                            <?php endif; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            <div id="dynamic_app_info_page">
                <?php
                    include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/_recently_popular_random_page.tpl';
                ?>
            </div>
        </div>

        <?php if (!empty($this->is_ajax_load) && !empty($this->titleLink) && ($this->titleLinkPosition == 'bottom')) : ?>
            <span class="sitepage_bottom_link">
                <?php echo $this->titleLink; ?>
            </span>
        <?php endif; ?>

        <script type="text/javascript" >
            function rswitchviewPage(flage) {
                if (flage == 2) {
                    if ($('rmap_canvas_view_page')) {
                        $('rmap_canvas_view_page').style.display = 'block';
                        google.maps.event.trigger(rmap_page, 'resize');
                        rmap_page.setZoom(<?php echo $defaultZoom; ?>);
                        rmap_page.setCenter(new google.maps.LatLng(<?php echo $latitude; ?>,<?php echo $longitude; ?>));
                    }
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
        </script>

        <script type="text/javascript">

            /* moo style */
            en4.core.runonce.add(function () {
                if ($('rimage_view_page')) {
                    //showtooltipPage();
                }
                <?php if ($this->enableLocation && $this->map_view): ?>
                                rinitializePage();
                <?php endif; ?>
                rswitchviewPage(<?php echo $this->defaultView ?>);
                $$('.tab_layout_sitepage_recently_popular_random_sitepage').addEvent('click', function () {

                    google.maps.event.trigger(rmap_page, 'resize');
                    rmap_page.setZoom(<?php echo $defaultZoom; ?>);
                    rmap_page.setCenter(new google.maps.LatLng(<?php echo $latitude; ?>,<?php echo $longitude; ?>));
                });
            });

            //var showtooltipPage = function (){
            //  if($('rimage_view_page')){
            //  //opacity / display fix
            //	$$('.sitepage_tooltip').setStyles({
            //		opacity: 0,
            //		display: 'block'
            //	});
            //	//put the effect in place
            //	$$('.jq-sitepage_tooltip li').each(function(el,i) {
            //		el.addEvents({
            //			'mouseenter': function() {
            //				el.getElement('div').fade('in');
            //			},
            //			'mouseleave': function() {
            //				el.getElement('div').fade('out');
            //			}
            //		});
            //	});
            //  }
            //}
        </script>
        <script type="text/javascript">

            var showListSitepage = function (tabshow, tabName) {
    <?php foreach ($this->tabs as $key => $tab): ?>
                    if ($('<?php echo 'sitepage_home_page_' . $key . '_tab' ?>'))
                        $('<?php echo 'sitepage_home_page_' . $key . '_tab' ?>').erase('class');
    <?php endforeach; ?>
                if ($('sitepage_home_page_' + tabName + '_tab'))
                    $('sitepage_home_page_' + tabName + '_tab').set('class', 'active');

                if ($('dynamic_app_info_page') != null) {

                    $('dynamic_app_info_page').innerHTML = '<center><img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitepage/externals/images/loader.gif" class="sitepage_tabs_loader_img" /></center>';
                }

                var request = new Request.HTML({
                    'url': '<?php echo $this->url(array(), 'sitepage_ajaxhomelist', true) ?>',
                    'data': {
                        'format': 'html',
                        'task': 'ajax',
                        'tab_show': tabshow,
                        'list_limit':<?php echo $this->active_tab_list; ?>,
                        'grid_limit':<?php echo $this->active_tab_image; ?>,
                        'list_view':<?php echo $this->list_view; ?>,
                        'grid_view':<?php echo $this->grid_view; ?>,
                        'map_view':<?php echo $this->map_view; ?>,
                        'category_id':<?php echo $this->category_id; ?>,
                        'defaultView':<?php echo $this->defaultView; ?>,
                        'columnWidth':<?php echo $this->columnWidth; ?>,
                        'columnHeight':<?php echo $this->columnHeight; ?>,
                        'showlikebutton':<?php echo $this->showlikebutton; ?>,
                        'turncation':<?php echo $this->turncation; ?>,
                        'listview_turncation':<?php echo $this->listview_turncation; ?>,
                        'showfeaturedLable':<?php echo $this->showfeaturedLable; ?>,
                        'showsponsoredLable':<?php echo $this->showsponsoredLable; ?>,
                        'showlocation':<?php echo $this->showlocation; ?>,
                        'show_name':<?php echo $this->show_name;?>,
                        'showprice':<?php echo $this->showprice; ?>,
                        'showpostedBy':<?php echo $this->showpostedBy; ?>,
                        'showdate':<?php echo $this->showdate; ?>,
                        'statistics': '<?php echo json_encode($this->statistics) ?>',
                        'detactLocation': '<?php echo $this->detactLocation ?>',
                        'defaultLocationDistance': '<?php echo $this->defaultLocationDistance ?>',
                        'latitude': window.locationsParamsSEAO && window.locationsParamsSEAO.latitude ? window.locationsParamsSEAO.latitude : 0,
                        'longitude': window.locationsParamsSEAO && window.locationsParamsSEAO.longitude ? window.locationsParamsSEAO.longitude : 0,
                        'showgetdirection':'<?php echo $this->showgetdirection; ?>'
                    },
                    onSuccess: function (responseTree, responseElements, responseHTML, responseJavaScript) {
                        $('dynamic_app_info_page').innerHTML = responseHTML;

                        // showtooltipPage();
    <?php if ($this->enableLocation && $this->map_view): ?>
                            rinitializePage();
    <?php endif; ?>
                        rswitchviewPage(<?php echo $this->defaultView ?>);
                    }
                });

                request.send();
            }
        </script>
        <style type="text/css">
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
    <?php else: ?>

        <div id="layout_sitepage_recently_popular_random_sitepage_<?php echo $this->identity; ?>">
            <!--    <div class="seaocore_content_loader"></div>-->
        </div>

        <?php if ($this->detactLocation): ?>
            <script type="text/javascript">
                var requestParams = $merge(<?php echo json_encode($this->paramsLocation); ?>, {'content_id': '<?php echo $this->identity; ?>'})
                var params = {
                    'detactLocation': <?php echo $this->detactLocation; ?>,
                    'responseContainer': 'layout_sitepage_recently_popular_random_sitepage_<?php echo $this->identity; ?>',
                    requestParams: requestParams
                };

                en4.seaocore.locationBased.startReq(params);
            </script>  
        <?php else: ?>
            <script type="text/javascript">
                window.addEvent('domready', function () {
                    en4.sitepage.ajaxTab.sendReq({
                        loading: true,
                        requestParams: $merge(<?php echo json_encode($this->paramsLocation); ?>, {'content_id': '<?php echo $this->identity; ?>'}),
                        responseContainer: [$('layout_sitepage_recently_popular_random_sitepage_<?php echo $this->identity; ?>')]
                    });
                });
            </script>  
        <?php endif; ?>


    <?php endif; ?>

<style>
    .sitepage_browse_thumb .sitepage_browse_title{
        background: transparent !important;
    }
</style>