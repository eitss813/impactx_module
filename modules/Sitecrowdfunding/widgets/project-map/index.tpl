<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.php 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
$baseUrl = $this->layout()->staticBaseUrl;
$this->headLink()->appendStylesheet($baseUrl . 'application/modules/Seaocore/externals/styles/styles.css');
$this->headLink()->appendStylesheet($baseUrl . 'application/modules/Sitecrowdfunding/externals/styles/style_sitecrowdfunding.css');
?>
<?php if (!$this->is_ajax): ?>
<script type="text/javascript">

    var pageAction = function (page) {
        $('page').value = page;
        submiForm();
    }

    var searchSitecrowdfundings = function () {

        var formElements = $('filter_form').getElements('li');
        formElements.each(function (el) {
            var field_style = el.style.display;
            if (field_style == 'none') {
                el.destroy();
            }
        });

        if (Browser.Engine.trident) {
            document.getElementById('filter_form').submit();
        } else {
            $('filter_form').submit();
        }
    }

    en4.core.runonce.add(function () {
        $$('#filter_form input[type=text]').each(function (f) {
            if (f.value == '' && f.id.match(/\min$/)) {
                new OverText(f, {'textOverride': 'min', 'element': 'span'});
            }
            if (f.value == '' && f.id.match(/\max$/)) {
                new OverText(f, {'textOverride': 'max', 'element': 'span'});
            }
        });
    });

    window.addEvent('onChangeFields', function () {
        var firstSep = $$('li.browse-separator-wrapper')[0];
        var lastSep;
        var nextEl = firstSep;
        var allHidden = true;
        do {
            nextEl = nextEl.getNext();
            if (nextEl.get('class') == 'browse-separator-wrapper') {
                lastSep = nextEl;
                nextEl = false;
            } else {
                allHidden = allHidden && (nextEl.getStyle('display') == 'none');
            }
        } while (nextEl);
        if (lastSep) {
            lastSep.setStyle('display', (allHidden ? 'none' : ''));
        }
    });
</script>
<?php
    /* Include the common user-end field switching javascript */
    echo $this->partial('_jsSwitch.tpl', 'fields', array());
?>


<?php if ($this->form): ?>
<?php echo $this->form->setAttrib('class', 'global_form_box sitecrowdfunding_advanced_search_form')->render($this) ?>

<div class="mleft5" id="page_location_pops_loding_image" style="display: none;">
    <img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Core/externals/images/loading.gif' />
    <?php //echo $this->translate("Loading ...")  ?>
</div>
<?php endif ?>

<script type="text/javascript">
    var flag = '<?php echo $this->advanced_search; ?>';
    var mapGetDirection;
    var myLatlng;
    var globalContentElement = en4.seaocore.getDomElements('content');

    window.addEvent('domready', function () {

        if (document.getElementById('location').value == '') {
            submiForm();
        }

        if ($$('.browse-separator-wrapper')) {
            $$('.browse-separator-wrapper').setStyle("display", 'none');
        }

        $('page_location_pops_loding_image').injectAfter($('done-element'));


        locationAutoSuggest('<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.countrycities'); ?>', 'location', 'project_city');

        var params = {
            'detactLocation': <?php echo $this->locationDetection; ?>,
        'fieldName': 'location',
            'locationmiles': <?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.locationdefaultmiles', 1000); ?>,
    };
        params.callBack = function () {
            submiForm();
            advancedSearchLists(flag);
        };
        en4.seaocore.locationBased.startReq(params);
//}
    });

    function submiForm() {
        if ($('category_id')) {
            if ($('category_id').options[$('category_id').selectedIndex].value == 0) {
                $('category_id').value = 0;
            }
        }
        var formElements = document.getElementById('filter_form');
        var url = en4.core.baseUrl + 'widget/index/content_id/<?php echo sprintf('%d', $this->identity) ?>';
        var parms = formElements.toQueryString();

        var param = (parms ? parms + '&' : '') + 'is_ajax=1&format=html&viewFormat='+getCurrentViewFormat();
        document.getElementById('page_location_pops_loding_image').style.display = '';
        en4.core.request.send(new Request.HTML({
            method: 'post',
            'url': url,
            'data': param,
            onSuccess: function (responseTree, responseElements, responseHTML, responseJavaScript) {

                document.getElementById('page_location_pops_loding_image').style.display = 'none';
                hiddenElement = $('sitecrowdfunding_location_hidden_ajax_resopnse');
                hiddenElement.innerHTML = responseHTML;
                document.getElementById('list_content_content').innerHTML = hiddenElement.getElement('#list_content_content').innerHTML;
                hiddenElement.innerHTML = '';
                setMarker();
                en4.core.runonce.trigger();
                if ($('list_map_container'))
                    $('list_map_container').style.visibility = 'visible';
                if ($('seaocore_browse_list')) {
                    var elementStartY = $('listlocation_map').getPosition().x;
                    var offsetWidth = $('list_map_container').offsetWidth;
                    var actualRightPostion = window.getSize().x - (elementStartY + offsetWidth);
                }
                if (document.getElementById('list_location_map_anchor'))
                    Smoothbox.bind(document.getElementById('list_location_map_anchor').getParent());
                fundingProgressiveBarAnimation();
                var viewSpanSelected = $(globalContentElement).getElement('#<?php echo $this->viewFormat; ?>');
                addActiveClass(viewSpanSelected);
            }
        }), {
            "force": true
        });
    }

    function locationPage() {
        var list_location = document.getElementById('location');

        if (document.getElementById('Latitude').value) {
            document.getElementById('Latitude').value = 0;
        }

        if (document.getElementById('Longitude').value) {
            document.getElementById('Longitude').value = 0;
        }
    }

    function locationSearch() {

        var formElements = document.getElementById('filter_form');
        formElements.addEvent('submit', function (event) {
            event.stop();
            submiForm();
        });
    }

    function advancedSearchLists() {

        if (flag == 0) {
            if ($('fieldset-grp2'))
                $('fieldset-grp2').style.display = 'none';

            if ($('fieldset-grp1'))
                $('fieldset-grp1').style.display = 'none';

            flag = 1;
            $('advanced_search').value = 0;

            if ($('project_street'))
                $('project_street').value = '';
            if ($('project_country'))
                $('project_country').value = '';
            if ($('project_state'))
                $('project_state').value = '';
            if ($('project_city'))
                $('project_city').value = '';

            if ($('profile_type'))
                $('profile_type').value = '';
            changeFields($('profile_type'));
            if ($('orderby'))
                $('orderby').value = 'starttime';
            if ($('category_id'))
                $('category_id').value = 0;

        }
        else {
            if ($('fieldset-grp2'))
                $('fieldset-grp2').style.display = 'block';
            if ($('fieldset-grp1'))
                $('fieldset-grp1').style.display = 'block';
            flag = 0;
            $('advanced_search').value = 1;

        }
    }
</script>

<script type="text/javascript">

    var profile_type = 0;
    var previous_mapped_level = 0;

    var project_categories_slug = <?php echo json_encode($this->categories_slug); ?>;

    function showFields(cat_value, cat_level) {

        if (cat_level == 1 || (previous_mapped_level >= cat_level && previous_mapped_level != 1) || (profile_type == null || profile_type == '' || profile_type == 0)) {
            profile_type = getProfileType(cat_value);
            if (profile_type == 0) {
                profile_type = '';
            } else {
                previous_mapped_level = cat_level;
            }
            $('profile_type').value = profile_type;
            changeFields($('profile_type'));
        }
    }

    var getProfileType = function (category_id) {

        var mapping = <?php echo Zend_Json_Encoder::encode(Engine_Api::_()->getDbTable('categories', 'sitecrowdfunding')->getMapping('profile_type')); ?>;

        for (i = 0; i < mapping.length; i++) {
            if (mapping[i].category_id == category_id)
                return mapping[i].profile_type;
        }
        return 0;
    }

    function addOptions(element_value, element_type, element_updated, domready) {

        var element = $(element_updated);
        if (domready == 0) {
            switch (element_type) {
                case 'cat_dependency':
                    $('subcategory_id' + '-wrapper').style.display = 'none';
                    clear($('subcategory_id'));
                    $('subcategory_id').value = 0;

                    $('categoryname').value = project_categories_slug[element_value];


                case 'subcat_dependency':
                    $('subsubcategory_id' + '-wrapper').style.display = 'none';
                    clear($('subsubcategory_id'));
                    $('subsubcategory_id').value = 0;
                    $('subsubcategoryname').value = '';
                    if (element_type == 'subcat_dependency')

                        $('subcategoryname').value = project_categories_slug[element_value];

                    else
                        $('subcategoryname').value = '';
            }
        }

        if (element_value <= 0)
            return;


        var url = '<?php echo $this->url(array('module' => 'sitecrowdfunding', 'controller' => 'project', 'action' => 'categories'), "default", true); ?>';

        en4.core.request.send(new Request.JSON({
            url: url,
            data: {
                format: 'json',
                element_value: element_value,
                element_type: element_type
            },
            onSuccess: function (responseJSON) {
                var categories = responseJSON.categories;
                var option = document.createElement("OPTION");
                option.text = "";
                option.value = 0;
                element.options.add(option);
                for (i = 0; i < categories.length; i++) {
                    var option = document.createElement("OPTION");
                    option.text = categories[i]['category_name'];
                    option.value = categories[i]['category_id'];
                    element.options.add(option);

                    project_categories_slug[categories[i]['category_id']] = categories[i]['category_slug'];

                }

                if (categories.length > 0)
                    $(element_updated + '-wrapper').style.display = 'inline-block';
                else
                    $(element_updated + '-wrapper').style.display = 'none';

                if (domready == 1) {
                    var value = 0;
                    if (element_updated == 'category_id') {
                        value = search_category_id;
                    } else if (element_updated == 'subcategory_id') {
                        value = search_subcategory_id;
                    } else {
                        value = search_subsubcategory_id;
                    }
                    $(element_updated).value = value;
                }
            }

        }), {'force': true});
    }

    function clear(element)
    {
        for (var i = (element.options.length - 1); i >= 0; i--) {
            element.options[ i ] = null;
        }
    }

    var search_category_id, search_subcategory_id, search_subsubcategory_id;
    window.addEvent('domready', function () {

        search_category_id = '<?php echo $this->category_id ?>';

        if (search_category_id != 0) {

            addOptions(search_category_id, 'cat_dependency', 'subcategory_id', 1);

            search_subcategory_id = '<?php echo $this->subcategory_id ?>';

            if (search_subcategory_id != 0) {
                search_subsubcategory_id = '<?php echo $this->subsubcategory_id ?>';
                addOptions(search_subcategory_id, 'subcat_dependency', 'subsubcategory_id', 1);
            }
        }
    });

    function show_subcat(cat_id)
    {
        if (document.getElementById('subcat_' + cat_id)) {
            if (document.getElementById('subcat_' + cat_id).style.display == 'block') {
                document.getElementById('subcat_' + cat_id).style.display = 'none';

                document.getElementById('img_' + cat_id).src = '<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitecrowdfunding/externals/images/bullet-right.png';
            }
            else if (document.getElementById('subcat_' + cat_id).style.display == '') {
                document.getElementById('subcat_' + cat_id).style.display = 'none';
                document.getElementById('img_' + cat_id).src = '<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitecrowdfunding/externals/images/bullet-right.png';
            }
            else {
                document.getElementById('subcat_' + cat_id).style.display = 'block';
                document.getElementById('img_' + cat_id).src = '<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitecrowdfunding/externals/images/bullet-bottom.png';

            }
        }
    }
</script>

<div id="project_location_map_none" style="display: none;"></div>
<?php endif; ?>


<?php
$apiKey = Engine_Api::_()->seaocore()->getGoogleMapApiKey();
$this->headScript()
->appendFile("https://maps.googleapis.com/maps/api/js?libraries=places&sensor=true&key=$apiKey");

$latitude = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.map.latitude', 0);

$longitude = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.map.longitude', 0);
?>
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitecrowdfunding/externals/scripts/core.js') ?>


<script type="text/javascript">

    var current_page = '<?php echo $this->current_page; ?>';

    var paginatePageLocations = function (page) {

        var formElements = document.getElementById('filter_form');
        var parms = formElements.toQueryString();
        var param = (parms ? parms + '&' : '') + 'is_ajax=1&format=html&page=' + page;
        document.getElementById('page_location_loding_image').style.display = '';

        var url = en4.core.baseUrl + 'widget/index/content_id/<?php echo sprintf('%d', $this->identity) ?>';
        clearOverlays();
        gmarkers = [];

        en4.core.request.send(new Request.HTML({
            method: 'post',
            'url': url,
            'data': param,
            onSuccess: function (responseTree, responseElements, responseHTML, responseJavaScript) {

                document.getElementById('page_location_loding_image').style.display = 'none';
                document.getElementById('list_location_map_anchor').getParent().innerHTML = responseHTML;
                setMarker();
            }
        }));
    };

</script>

<?php $locationEnabled = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.location', 1); ?>
<?php if (empty($this->is_ajax)) : ?>
<div class="list_browse_location sitecrowdfunding_list_browse_location" id="list_browse_location" >
    <div class="list_map_container_right sitecrowdfunding_list_map_container_right" id ="list_map_container_right"></div>
    <div id="list_map_container" class="list_map_container sitecrowdfunding_list_map_container absolute" style="visibility:hidden;">

        <div class="list_map_container_map_area sitecrowdfunding_list_map_container_map_area fleft seaocore_map" id="listlocation_map">
            <div class="list_map_content" id="listlocation_browse_map_canvas" ></div>
            <?php $siteTitle = Engine_Api::_()->getApi('settings', 'core')->core_general_site_title; ?>
            <?php if (!empty($siteTitle)) : ?>
            <div class="seaocore_map_info"><?php echo $this->translate("Locations on %s", "<a href='' target='_blank'>$siteTitle</a>"); ?></div>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>
</div>
<div class="list_map_container_list" id="list_content_content">
    <a id="list_location_map_anchor" class="pabsolute"></a>
    <?php if (count($this->paginator) > 0): ?>
    <div class="sitecrowdfunding_browse_list sitecrowdfunding_location_browse_box" id="seaocore_browse_list"><?php if (!empty($this->is_ajax)) : ?>
        <div class="sitecrowdfunding_location_top_links" style="margin-top: 0;">
            <?php echo $this->translate(array('%s project found.', '%s projects found.', $this->totalresults), $this->locale()->toNumber($this->totalresults)) ?>
            <?php if (count($this->viewType) > 1 && empty($this->isSiteMobileView)): ?>
            <div class="fright">
                <?php if (in_array('gridView', $this->viewType)) : ?>
                <span class="seaocore_tab_select_wrapper fright">
                                <div class="seaocore_tab_select_view_tooltip"><?php echo $this->translate("Grid View"); ?></div>
                                <span class="seaocore_tab_icon tab_icon_grid_view seaocore_tab_icon_<?php echo $this->identity ?>" onclick="siteProjectTabSwitchview($(this));" id="gridView" rel='grid_view' ></span>
                            </span>
                <?php endif; ?>
                <?php if (in_array('listView', $this->viewType)) : ?>
                <span class="seaocore_tab_select_wrapper fright">
                                <div class="seaocore_tab_select_view_tooltip"><?php echo $this->translate("List View"); ?></div>
                                <span class="seaocore_tab_icon tab_icon_list_view seaocore_tab_icon_<?php echo $this->identity ?>" onclick="siteProjectTabSwitchview($(this));" id="listView" rel='list_view' ></span>
                            </span>
                <?php endif; ?>
            </div>
            <?php endif ?>
        </div>
        <div id="dynamic_app_info_sitecrowdfunding_<?php echo $this->identity; ?>">
            <?php if (in_array('gridView', $this->viewType) || $this->isSiteMobileView) : ?>
            <div class="sitecrowdfunding_container" id="grid_view_sitecrowdfunding_" style="<?php echo $this->viewFormat == 'gridView' ? $this->viewFormat : 'display:none;'; ?>">
                <?php include APPLICATION_PATH . '/application/modules/Sitecrowdfunding/views/scripts/project/_grid_view.tpl'; ?>
            </div>
            <?php endif; ?>
            <?php if (in_array('listView', $this->viewType) && empty($this->isSiteMobileView)) : ?>
            <div class="sitecrowdfunding_container" id="list_view_sitecrowdfunding_" style="<?php echo $this->viewFormat == 'listView' ? $this->viewFormat : 'display:none;'; ?>">
                <?php include APPLICATION_PATH . '/application/modules/Sitecrowdfunding/views/scripts/project/_list_view.tpl';
                        ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>
    <div class="clr sitecrowdfunding_browse_location_paging mtop10">
        <?php if (count($this->paginator) > 1): ?>
        <div class="fleft" id="page_location_loding_image" style="display: none;margin:5px;">
            <img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Core/externals/images/loading.gif' alt="" />
        </div>
        <?php endif; ?>
    </div>
    <?php else: ?>
    <div class="tip">
        <span><?php echo $this->translate($this->message); ?></span>
    </div>
    <?php endif; ?>
    <?php if(empty($this->is_ajax)) : ?>
</div>
</div>
<div id="sitecrowdfunding_location_hidden_ajax_resopnse" style="display: none;"></div>

<script type="text/javascript" >

    /* moo style */
    window.addEvent('domready', function () {
//smallLargeMap(1);

        var Clientwidth = $(globalContentElement).getElement(".layout_sitecrowdfunding_browselocation_sitecrowdfunding").clientWidth;

        var offsetWidth = '';
        if ($('list_map_container'))
            offsetWidth = $('list_map_container').offsetWidth;
        if ($('listlocation_browse_map_canvas'))
            $('listlocation_browse_map_canvas').setStyle("height", offsetWidth);

        if ($('list_map_right'))
            $('list_map_right').style.display = 'none';

    <?php if ($this->enableLocation): ?>
        initialize();
    <?php endif; ?>
    });

    if ($('seaocore_browse_list')) {

        var elementStartY = $('listlocation_map').getPosition().x;
        var offsetWidth = $('list_map_container').offsetWidth;
        var actualRightPostion = window.getSize().x - (elementStartY + offsetWidth);

        function setMapContent() {

            if (!$('seaocore_browse_list')) {
                return;
            }

            var element = $("list_map_container");
            if (element.offsetHeight > $('seaocore_browse_list').offsetHeight) {
                if (!element.hasClass('absolute')) {
                    element.addClass('absolute');
                    element.removeClass('fixed');
                    if (element.hasClass('bottom'))
                        element.removeClass('bottom');
                }
                return;
            }

            var elementPostionStartY = $('seaocore_browse_list').getPosition().y;
            var elementPostionStartX = $('list_map_container').getPosition().x;
            var elementPostionEndY = elementPostionStartY + $('seaocore_browse_list').offsetHeight - element.offsetHeight;

            if (((elementPostionEndY) < window.getScrollTop())) {
                if (element.hasClass('absolute'))
                    element.removeClass('absolute');
                if (element.hasClass('fixed'))
                    element.removeClass('fixed');
                if (!element.hasClass('bottom'))
                    element.addClass('bottom');
            }
            else if (((elementPostionStartY) < window.getScrollTop())) {
                if (element.hasClass('absolute'))
                    element.removeClass('absolute');
                if (!element.hasClass('fixed'))
                    element.addClass('fixed');
                if (element.hasClass('bottom'))
                    element.removeClass('bottom');
                element.setStyle("right", actualRightPostion);
                element.setStyle("width", offsetWidth);
            }
            else if (!element.hasClass('absolute')) {
                element.addClass('absolute');
                element.removeClass('fixed');
                if (element.hasClass('bottom'))
                    element.removeClass('bottom');
            }
        }

    }

    function smallLargeMap(option) {
        if (option == '1') {
            $('listlocation_browse_map_canvas').setStyle("height", '400px');
            if (!$('list_map_container').hasClass('list_map_container_exp'))
                $('list_map_container').addClass('list_map_container_exp');
        } else {
            $('listlocation_browse_map_canvas').setStyle("height", offsetWidth);
            if ($('list_map_container').hasClass('list_map_container_exp'))
                $('list_map_container').removeClass('list_map_container_exp');

        }
        // setMapContent();
        google.maps.event.trigger(map, 'resize');
    }
</script>

<script type="text/javascript" >
    function owner(thisobj) {
        var Obj_Url = thisobj.href;
        Smoothbox.open(Obj_Url);
    }
</script>

<script type="text/javascript">
    var script = '<script type="text/javascript" src="http://google-maps-' +
        'utility-library-v3.googlecode.com/svn/trunk/infobubble/src/infobubble';
    if (document.location.search.indexOf('compiled') !== -1) {
        script += '-compiled';
    }
    script += '.js"><' + '/script>';
    document.write(script);
</script>

<?php
$this->headScript()->appendFile($this->layout()->staticBaseUrl . "application/modules/Seaocore/externals/scripts/infobubble.js");
?>

<script type="text/javascript" >
    //<![CDATA[
    // this variable will collect the html which will eventually be placed in the side_bar
    var side_bar_html = "";

    // arrays to hold copies of the markers and html used by the side_bar
    // because the function closure trick doesnt work there
    var gmarkers = [];
    var infoBubbles;
    var markerClusterer = null;
    // global "map" variable
    var map = null;
    // A function to create the marker and set up the event window function
    function createMarker(latlng, name, html, title_page, page_id) {
        var contentString = html;
        if (name == 0) {
            var marker = new google.maps.Marker({
                position: latlng,
                map: map,
                title: title_page,
// page_id : page_id,
                animation: google.maps.Animation.DROP,
                zIndex: Math.round(latlng.lat() * -100000) << 5
            });
        }
        else {
            var marker = new google.maps.Marker({
                position: latlng,
                map: map,
                title: title_page,
//page_id: page_id,
                draggable: false,
                animation: google.maps.Animation.BOUNCE
            });
        }

        gmarkers.push(marker);
        var infowindow = new google.maps.InfoWindow({
            content: contentString,
            maxWidth: 400,
            maxHeight : 400
        });
        google.maps.event.addListener(marker, 'click', function () {
            google.maps.event.trigger(map, 'resize');
            map.setCenter(marker.position);
            infowindow.open(map, marker);
//infoBubbles.open(map, marker);
//infoBubbles.setContent(contentString);
        });

//Show tooltip on the mouse over.
        $$('.marker_' + page_id).each(function (locationMarker) {
            locationMarker.addEvent('mouseover', function (event) {
                google.maps.event.trigger(map, 'resize');
                map.setCenter(marker.position);
                infoBubbles.open(map, marker);
                infoBubbles.setContent(contentString);
            });
        });

//Show tooltip on the mouse over.
        $$('.marker_photo_' + page_id).each(function (locationMarker) {
            locationMarker.addEvent('mouseover', function (event) {
                google.maps.event.trigger(map, 'resize');
                map.setCenter(marker.position);
                infoBubbles.open(map, marker);
                infoBubbles.setContent(contentString);
            });
        });
    }

    function initialize() {

// create the map
        var myOptions = {
            zoom: <?php echo '1'; ?>,
        center: new google.maps.LatLng(<?php echo $latitude ?>,<?php echo $longitude ?>),
//  mapTypeControl: true,
// mapTypeControlOptions: {style: google.maps.MapTypeControlStyle.DROPDOWN_MENU},
        navigationControl: true,
            mapTypeId: google.maps.MapTypeId.ROADMAP
    }

        map = new google.maps.Map(document.getElementById("listlocation_browse_map_canvas"),
            myOptions);

        google.maps.event.addListener(map, 'click', function () {

        <?php  if ($this->enableLocation): ?>

            infoBubbles.close();
        <?php endif; ?>
        });
        setMarker();
        smallLargeMap(1);
    }

    function clearOverlays() {
        infoBubbles.close();
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

    function setMapCenterZoomPoint(bounds, maplocation) {
        if (bounds && bounds.min_lat && bounds.min_lng && bounds.max_lat && bounds.max_lng) {
            var bds = new google.maps.LatLngBounds(new google.maps.LatLng(bounds.min_lat, bounds.min_lng), new google.maps.LatLng(bounds.max_lat, bounds.max_lng));
        }
        if (bounds && bounds.center_lat && bounds.center_lng) {
            maplocation.setCenter(new google.maps.LatLng(bounds.center_lat, bounds.center_lng), 4);
        } else {
//maplocation.setCenter(new google.maps.LatLng(lat, lng), 4);
        }
        if (bds) {
            maplocation.setCenter(bds.getCenter());
            maplocation.fitBounds(bds);
        }
    }

    infoBubbles = new InfoBubble({
        maxWidth: 400,
        maxHeight: 400,
        shadowStyle: 1,
        padding: 0,
        backgroundColor: '<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.tooltip.bgcolor', '#ffffff'); ?>',
        borderRadius: 5,
        arrowSize: 10,
        borderWidth: 1,
        borderColor: '#2c2c2c',
        disableAutoPan: true,
        hideCloseButton: false,
        arrowPosition: 50,
//backgroundClassName: 'sitetag_checkin_map_tip',
        arrowStyle: 0
    });
</script>
<?php endif; ?>

<script type="text/javascript" >

    function setMarker() {

    <?php  if (count($this->locations) > 0) : ?>

    <?php foreach ($this->locations as $location) : ?>
// obtain the attribues of each marker
        var lat = <?php echo $location->latitude ?>;
        var lng =<?php echo $location->longitude ?>;
        var point = new google.maps.LatLng(lat, lng);

        var page_id = <?php echo $this->list[$location->project_id]->project_id ?>;
    <?php if (!empty($enableBouce)): ?>
        var sponsored = <?php echo $this->list[$location->project_id]->sponsored ?>

    <?php else: ?>
        var sponsored = 0;
    <?php endif; ?>
// create the marker
        var contentString = "<?php

        echo $this->string()->escapeJavascript($this->partial('application/modules/Sitecrowdfunding/views/scripts/_mapInfoWindowContent.tpl', array(
            'project' => $this->list[$location->project_id],
            'projectOption' => $this->projectOption,
    )), false);
            ?>";
        var marker = createMarker(point, sponsored, contentString, '', page_id);

    <?php endforeach; ?>
        if ($('list_map_container'))
            $('list_map_container').style.display = 'block';
        google.maps.event.trigger(map, 'resize');

    <?php endif; ?>
//  markerClusterer = new MarkerClusterer(map, gmarkers, {
//  });
    <?php if (count($this->locations) > 0): ?>
        setMapCenterZoomPoint(<?php echo json_encode(Engine_Api::_()->seaocore()->getProfileMapBounds($this->locations)); ?>, map);
    <?php endif; ?>

//$$('.un_location_list').each(function(el) {
        $$('.un_location_list').addEvent('mouseover', function (event) {
            infoBubbles.close();
        });
//  });
    }
</script>

<?php if (!empty($this->is_ajax)): ?>
<?php echo $this->paginationControl($this->paginator, null, array("pagination/pagination.tpl", "sitecrowdfunding"), array("orderby" => $this->orderby)); ?>
<?php endif; ?>

<script>
    var currentViewFormat = '<?php echo $this->viewFormat; ?>';
    function setCurrentViewFormat(viewSpan) {
        currentViewFormat = viewSpan.get('id');
    }
    function getCurrentViewFormat() {
        return currentViewFormat;
    }

    function addActiveClass(viewSpan) {
        $(globalContentElement).getElement('.sitecrowdfunding_browse_list .tab_icon_grid_view').removeClass('active');
        $(globalContentElement).getElement('.sitecrowdfunding_browse_list .tab_icon_list_view').removeClass('active');
        viewSpan.addClass('active');
    }
    function siteProjectTabSwitchview(viewSpan) {
        var identity = '<?php echo $this->identity; ?>';
        setCurrentViewFormat(viewSpan);
        addActiveClass(viewSpan);
        var type = viewSpan.get('rel');
        $('dynamic_app_info_sitecrowdfunding_' + identity).getElements('.sitecrowdfunding_container').setStyle('display', 'none');
        $('dynamic_app_info_sitecrowdfunding_' + identity).getElement("#" + type + "_sitecrowdfunding_").style.display = 'block';
        fundingProgressiveBarAnimation();
    }
</script>