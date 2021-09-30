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
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitecrowdfunding/externals/scripts/core.js'); ?>
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/favourite.js'); ?>
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/like.js'); ?>
<?php

if($this->identity)
    $this->params['identity'] = $this->identity;
else
    $this->identity = $this->params['identity'];
?>
<?php 
$apiKey = Engine_Api::_()->seaocore()->getGoogleMapApiKey();
$this->headScript()->appendFile("https://maps.googleapis.com/maps/api/js?libraries=places&key=$apiKey");
?>
<?php
$currentLink = 'all';
if(isset($this->params['link']) && !empty($this->params['link']))
    $currentLink = $this->params['link'];
$viewFormat = $this->params['viewFormat'];
$viewType = isset($this->projectNavigationLink[0]) ? ($this->projectNavigationLink[0]) : 0;

$projectDefaultViewType = $this->defaultViewType;
?>
<?php if (empty($this->is_ajax)) : ?>
    <?php
    $baseUrl = $this->layout()->staticBaseUrl;
    $this->headLink()->appendStylesheet($baseUrl . 'application/modules/Seaocore/externals/styles/styles.css');
    $this->headLink()->appendStylesheet($baseUrl . 'application/modules/Sitecrowdfunding/externals/styles/style_sitecrowdfunding.css');
    ?>
    <?php $showToplink = count($this->projectNavigationLink) == 1 && count($this->viewType) == 1 && $this->searchButton == 0; ?>
        <div class="sitecrowdfunding_myprojects_top o_hidden b_medium"> 
            <div class="fright my_projects_top_links_right" >
                <?php if(in_array('createProject',$this->topNavigationLink) && $this->canCreateProject) : ?>
                <span class="sitecrowdfunding_link_wrap" style="display: none">
                    <i class="sitecrowdfunding_icon item_icon_sitecrowdfunding_project"></i> 
                        <a href="<?php echo $this->url(array('action' => 'step-one', 'controller' => 'project-create'), 'sitecrowdfunding_create', true); ?>" class="bold upload_new_project seaocore_icon_add" id="list_projects_link">
                        <?php
                            echo $this->translate('Create a Project') . '</a>';
                        ?> 
                </span>&nbsp;&nbsp;
                <?php endif; ?>
            </div>
        </div>
        <div class="sitecrowdfunding_myprojects_top_links b_medium" style="display:<?php echo $showToplink ? 'none' : 'inline-block' ?>">
            <div class="sitecrowdfunding_myprojects_top_filter_links txt_center sitecrowdfunding_myprojects_top_filter_links_<?php echo $this->identity; ?>" style="display:<?php echo (count($this->projectNavigationLink) > 0) ? 'block' : 'none'; ?>" >
                <?php if (in_array('all', $this->projectNavigationLink)) : ?>
                     <a href="javascript:void(0);" id='all' onclick="clearSearchBox();
                                    filter_rsvp('all',currentViewFormat(), '')"><?php echo $this->translate('All Projects'); ?></a>
                 <?php endif; ?>
                  <?php if (in_array('all', $this->projectNavigationLink)) : ?>
                    <a href="javascript:void(0);" id='all-projects' onclick="clearSearchBox();
                                    filter_rsvp('all-projects',currentViewFormat(), '')"><?php echo $this->translate('My Projects'); ?></a>
                   <?php endif; ?>
                   <?php if (in_array('backed', $this->projectNavigationLink)) : ?>
                    <a href="javascript:void(0);" id='backed' onclick="clearSearchBox();
                                    filter_rsvp('backed', '<?php echo $this->viewFormat; ?>', '')" ><?php echo $this->translate('Funded'); ?></a>
                   <?php endif; ?>
                <!--
            <?php if (in_array('liked', $this->projectNavigationLink)) : ?>
              <a href="javascript:void(0);" id='liked'  onclick="clearSearchBox();
                              filter_rsvp('liked',currentViewFormat(), '')" ><?php echo $this->translate('Liked'); ?></a>
             <?php endif; ?> -->
             <?php if (in_array('favourite', $this->projectNavigationLink)) : ?>
               <a href="javascript:void(0);" id='favourites'  onclick="clearSearchBox();
                              filter_rsvp('favourites',currentViewFormat(), '')" ><?php echo $this->translate('Followed'); ?></a>
            <?php endif; ?>
            <!--
              <?php if (in_array('favourite', $this->projectNavigationLink)) : ?>
                 <a href="javascript:void(0);" id='favourite'  onclick="clearSearchBox();
                              filter_rsvp('favourite',currentViewFormat(), '')" ><?php echo $this->translate('Followed'); ?></a>
              <?php endif; ?>
             -->
                 <?php if (in_array('favourite', $this->projectNavigationLink)) : ?>
                      <a href="javascript:void(0);" id='admin'  onclick="clearSearchBox();
                                    filter_rsvp('admin',currentViewFormat(), '')" ><?php echo $this->translate('Projects I Admin'); ?></a>
                  <?php endif; ?>

            </div>
            <?php if ($this->searchButton) : ?>
                <div class="sitecrowdfunding_myprojects_tab_search fright">
                    <a href="javascript:void(0);" onclick="shownhidesearch()"></a>
                </div>
            <?php endif; ?>
            <div class="sitecrowdfunding_myprojects_top_filter_views txt_right fright" id='projectViewFormat' style="display:<?php echo (count($this->viewType) > 1 && empty($this->isSiteMobileView)) ? 'block' : 'none'; ?>">
                <?php if (in_array('gridView', $this->viewType)) : ?>
                    <span class="seaocore_tab_select_wrapper fright">
                        <div class="seaocore_tab_select_view_tooltip"><?php echo $this->translate("Grid View"); ?></div>
                        <span class="seaocore_tab_icon tab_icon_grid_view seaocore_tab_icon_<?php echo $this->identity ?> <?php echo ($this->viewFormat == 'gridView')?'active': '' ?>" onclick="clearSearchBox();changeView('gridView', '')" id="gridView" ></span>
                    </span>
                <?php endif; ?>
                <?php if (in_array('listView', $this->viewType)) : ?>
                    <span class="seaocore_tab_select_wrapper fright">
                        <div class="seaocore_tab_select_view_tooltip"><?php echo $this->translate("List View"); ?></div>
                        <span class="seaocore_tab_icon tab_icon_list_view seaocore_tab_icon_<?php echo $this->identity ?> <?php echo ($this->viewFormat == 'listView')?'active': '' ?>" onclick="clearSearchBox();changeView('listView', '')" id="listView" ></span>
                    </span>
                <?php endif; ?>
                <?php if (in_array('mapView', $this->viewType)) : ?>
                    <span class="seaocore_tab_select_wrapper fright">
                        <div class="seaocore_tab_select_view_tooltip"><?php echo $this->translate("Map View"); ?></div>
                        <span class="seaocore_tab_icon tab_icon_map_view seaocore_tab_icon_<?php echo $this->identity ?> <?php echo ($this->viewFormat == 'mapView')?'active': '' ?>" onclick="clearSearchBox();changeView('mapView', '')" id="mapView" ></span>
                    </span>
                <?php endif; ?>
            </div>
        </div>
<?php endif; ?>
<div id="tbl_search" style="display: none;" class="sitecrowdfunding_myprojects_tab_search_panel">

    <span><?php echo $this->translate("Search within these results :"); ?></span>
    <input type="text" name="search" id="search" placeholder="<?php echo $this->translate("Start Typing Here..."); ?>" >
    <button onclick="filter_data()" > <?php echo $this->translate("Search"); ?> </button> <?php echo $this->translate("or"); ?> <a href="javascript:void(0);" onclick="shownhidesearch()"><?php echo $this->translate("Cancel"); ?></a>
</div>
<div id='sitecrowdfunding_manage_project'>

<?php 

if($this->viewFormat == 'gridView' || $this->isSiteMobileView)
    include APPLICATION_PATH . '/application/modules/Sitecrowdfunding/views/scripts/project/_grid_view.tpl';
else if($this->viewFormat == 'listView' && empty($this->isSiteMobileView))
    include APPLICATION_PATH . '/application/modules/Sitecrowdfunding/views/scripts/project/_list_view.tpl';
else if($this->viewFormat == 'mapView' && empty($this->isSiteMobileView))
    include APPLICATION_PATH . '/application/modules/Sitecrowdfunding/views/scripts/project/_map_view.tpl';
?>
</div>
<div id="hidden_ajax_data" style="display: none;"></div>
<script>
    viewType = '<?php echo $viewType; ?>';
    viewFormatG = '<?php echo $this->viewFormat ?>';
    isSearchButton = <?php echo $this->searchButton; ?>;
    $('search').addEvent('keypress', function (e) {
        if (e.key == 'enter') {
            e.stop();
            filter_data();
        }
    });
    shownhidesearch = function ()
    {
        if ($('tbl_search').style.display == 'none')
        {
            $('tbl_search').style.display = 'block';
            $('search').focus();
        }
        else
            $('tbl_search').style.display = 'none';
    }
    clearSearchBox = function ()
    {
        if (!isSearchButton)
            return false;
        if ($('tbl_search').style.display == 'block')
        {
            $('tbl_search').style.display = 'none';
            $('search').value = '';
        }
    }
    addBoldClass = function (reqType, viewFormat)
    {
        $$('div.sitecrowdfunding_myprojects_top_filter_links_<?php echo $this->identity; ?> > a').each(function (el) {
            el.removeClass('active');
        });
        $$('.seaocore_tab_icon_<?php echo $this->identity ?>').each(function (el) {
            el.removeClass('active');
        });
        $(reqType).addClass('active');
        $(viewFormat).addClass('active');
    }
    filter_data = function ()
    {
        search = $('search').value;
        changeView(viewFormatG, search);
    }
    filter_rsvp = function (req_type, viewFormat, search)
    {
            if (req_type == '0')
            return false;
        viewFormatG = viewFormat;
        addBoldClass(req_type, viewFormat);
            $('projectViewFormat').style.display = $('projectViewFormat').style.display;
        viewType = req_type;

      console.log('req_type',req_type);
        switch (req_type)
        {
            case 'all-projects':
                var url = en4.core.baseUrl + 'widget/index/mod/sitecrowdfunding/name/my-projects/link/all-projects/viewFormat/' + viewFormat + '/search/' + search;
                break;
            case 'all':
                var url = en4.core.baseUrl + 'widget/index/mod/sitecrowdfunding/name/my-projects/link/all/viewFormat/' + viewFormat + '/search/' + search;
                break;
            case 'backed':
                var url = en4.core.baseUrl + 'widget/index/mod/sitecrowdfunding/name/my-projects/link/backed/viewFormat/' + viewFormat + '/search/' + search;
                break;
            case 'liked':
                var url = en4.core.baseUrl + 'widget/index/mod/sitecrowdfunding/name/my-projects/link/liked/viewFormat/' + viewFormat + '/search/' + search;
                break;
            case 'favourites':
                var url = en4.core.baseUrl + 'widget/index/mod/sitecrowdfunding/name/my-projects/link/favourites/viewFormat/' + viewFormat + '/search/' + search;
                break;
            case 'favourite':
                var url = en4.core.baseUrl + 'widget/index/mod/sitecrowdfunding/name/my-projects/link/favourite/viewFormat/' + viewFormat + '/search/' + search;
            case 'admin':
                var url = en4.core.baseUrl + 'widget/index/mod/sitecrowdfunding/name/my-projects/link/admin/viewFormat/' + viewFormat + '/search/' + search;
                break;
        }
        $('sitecrowdfunding_manage_project').innerHTML = '<div class="clr"></div><div class="seaocore_content_loader"></div>';

        var params = {
            requestParams:<?php echo json_encode($this->params) ?>
        };
        params.requestParams.is_ajax = 0;

       if(req_type =='favourite') {
           params.requestParams.link ='favourite' ;
       }
        console.log('req_type req_type',params.requestParams);

        var request = new Request.HTML({
            url: url,
            data: $merge(params.requestParams, {
                format: 'html',
                subject: en4.core.subject.guid,
                is_ajax: 0,
                pagination: 0,
                page: 0,
                link:req_type
            }),
            evalScripts: true,
            onSuccess: function (responseTree, responseElements, responseHTML, responseJavaScript) {
                $('hidden_ajax_data').innerHTML = responseHTML;
                $('sitecrowdfunding_manage_project').innerHTML = $('hidden_ajax_data').getElement('#sitecrowdfunding_manage_project').innerHTML;
                $('hidden_ajax_data').innerHTML = '';
                fundingProgressiveBarAnimation();
                Smoothbox.bind($('sitecrowdfunding_manage_project'));
                en4.core.runonce.trigger();
                if(viewFormat=='mapView'){
                    initialize();
                    setMarkers();
                }
            }
        });
        request.send();
    }

    changeView = function (viewFormat, search)
    {
      console.log('viewFormat',viewFormat);
        viewFormatG = viewFormat;
        var currentLink = "<?php echo $currentLink; ?>";
        if(search == "")
        search = "<?php echo $this->currentSearch ?>";
        filter_rsvp(currentLink, viewFormat, search);
    }
    currentViewFormat = function()
    {
        currentView = "<?php  echo $viewFormat ?>";
        return currentView;
    }

    var currentLink = "<?php echo $currentLink; ?>";
    var allLinks = $$('div.sitecrowdfunding_myprojects_top_filter_links_<?php echo $this->identity; ?> > a');
    allLinks.removeClass('active');
    $(currentLink).addClass('active');
</script>

<style>

    @media (max-width: 767px) {
        .seaocore_tab_icon {
            margin: 15px 3px 3px !important;
        }
    }
</style>