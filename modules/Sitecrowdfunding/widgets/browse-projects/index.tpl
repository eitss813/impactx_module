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
$this->params['identity'] = $this->identity;
if (!$this->id)
    $this->id = $this->identity;
?>
<?php
$baseUrl = $this->layout()->staticBaseUrl;
$this->headLink()->appendStylesheet($baseUrl . 'application/modules/Seaocore/externals/styles/styles.css');
$this->headLink()->appendStylesheet($baseUrl . 'application/modules/Sitecrowdfunding/externals/styles/style_sitecrowdfunding.css');
?>
<?php if (empty($this->is_ajax)): ?>
    <div class="layout_core_container_tabs">    
        <div class="sitecrowdfunding_browse_lists_view_options txt_right" id='projectViewFormat'>
            <div class="fleft">
                <?php if (empty($this->heading)) : ?>
                    <?php echo $this->translate(array('%s project found', '%s projects found', $this->totalCount), $this->totalCount); ?>
                <?php else : ?>
                    <h3>
                        <?php echo $this->translate($this->heading); ?>
                    </h3>
                <?php endif; ?>
                <?php if (!empty($this->tagName)) : ?>
                    <?php echo ' for '.$this->tagName; ?>
                <?php endif; ?>
            </div>
            <?php if (count($this->viewType) > 1 && empty($this->isSiteMobileView)): ?>
                <div class="fright">
                    <?php if (in_array('gridView', $this->viewType)) : ?>
                        <span class="seaocore_tab_select_wrapper fright">
                            <div class="seaocore_tab_select_view_tooltip"><?php echo $this->translate("Grid View"); ?></div>
                            <span class="seaocore_tab_icon tab_icon_grid_view active seaocore_tab_icon_<?php echo $this->identity ?>" onclick="siteProjectTabSwitchview($(this));" id="gridView" rel='grid_view' ></span>
                        </span>
                    <?php endif; ?>
                    <?php if (in_array('listView', $this->viewType)) : ?>
                        <span class="seaocore_tab_select_wrapper fright">
                            <div class="seaocore_tab_select_view_tooltip"><?php echo $this->translate("List View"); ?></div>
                            <span class="seaocore_tab_icon tab_icon_list_view seaocore_tab_icon_<?php echo $this->identity ?>" onclick="siteProjectTabSwitchview($(this));" id="listView" rel='list_view' ></span>
                        </span>
                    <?php endif; ?>
                    <?php if (in_array('mapView', $this->viewType)) : ?>
                        <span class="seaocore_tab_select_wrapper fright">
                            <div class="seaocore_tab_select_view_tooltip"><?php echo $this->translate("Map View"); ?></div>
                            <span class="seaocore_tab_icon tab_icon_map_view seaocore_tab_icon_<?php echo $this->identity ?>" onclick="siteProjectTabSwitchview($(this));" id="mapView" rel='map_view' ></span>
                        </span>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>

        <div id="dynamic_app_info_sitecrowdfunding_<?php echo $this->identity; ?>">
        <?php endif; ?>
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
        <?php if (in_array('mapView', $this->viewType) && empty($this->isSiteMobileView)): ?>
            <div class="sitecrowdfunding_container" id="map_view_sitecrowdfunding_" style="<?php echo $this->viewFormat == 'mapView' ? $this->viewFormat : 'display:none;'; ?>">
                <?php include APPLICATION_PATH . '/application/modules/Sitecrowdfunding/views/scripts/project/_map_view.tpl'; ?>
            </div>
        <?php endif; ?>
        <?php if ($this->showViewMore): ?>
            <div class="seaocore_view_more mtop10">
                <?php
                echo $this->htmlLink('javascript:void(0);', $this->translate('View More'), array(
                    'id' => '',
                    'class' => 'buttonlink icon_viewmore'
                ))
                ?>
            </div>
        <?php endif; ?>    
        <div class="seaocore_loading" id="" style="display: none;">
            <img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/core/loading.gif' style='margin-right: 5px;' />
            <?php echo $this->translate("Loading ...") ?>
        </div>
        <?php if (empty($this->is_ajax)) : ?>
        </div>
    </div>
    <script lang="javascript">

        var View = function ()
        {
            this.selectedViewFormat = '';
            this.addBoldClass = function ()
            {
                $$('.seaocore_tab_icon_<?php echo $this->identity ?>').each(function (el) {
                    el.removeClass('active');
                });
                if ($(this.selectedViewFormat))
                    $(this.selectedViewFormat).addClass('active');
            }
        }
        viewObj = new View();
        viewObj.selectedViewFormat = '<?php echo $this->viewFormat ?>';
        if ($('viewFormat')) {
            $('viewFormat').set('value', viewObj.selectedViewFormat);
        }
        viewObj.addBoldClass();
    </script>
    <script type="text/javascript">
        function sendAjaxRequestSiteproject(params) {
            var url = en4.core.baseUrl + 'widget';
            viewType = viewObj.selectedViewFormat;
            if (params.requestUrl)
                url = params.requestUrl;

            var request = new Request.HTML({
                url: url,
                data: $merge(params.requestParams, {
                    format: 'html',
                    subject: en4.core.subject.guid,
                    is_ajax: true,
                    loaded_by_ajax: false,
                }),
                evalScripts: true,
                onSuccess: function (responseTree, responseElements, responseHTML, responseJavaScript) {
                    if (params.requestParams.page == 1) {
                        params.responseContainer.empty();
                        Elements.from(responseHTML).inject(params.responseContainer);
                    } else {
                        var element = new Element('div', {
                            'html': responseHTML
                        });
                        params.responseContainer.getElements('.seaocore_loading').setStyle('display', 'none');

                        if (fillAjaxData('gridView') && $$('.sitecrowdfunding_projects_grid_view') && element.getElement('.sitecrowdfunding_projects_grid_view')) {
                            Elements.from(element.getElement('.sitecrowdfunding_projects_grid_view').innerHTML).inject(params.responseContainer.getElement('.sitecrowdfunding_projects_grid_view'));
                        }
                        if (fillAjaxData('listView') && $$('.sitecrowdfunding_projects_list_view') && element.getElement('.sitecrowdfunding_projects_list_view')) {
                            Elements.from(element.getElement('.sitecrowdfunding_projects_list_view').innerHTML).inject(params.responseContainer.getElement('.sitecrowdfunding_projects_list_view'));
                        } 
                        viewObj.selectedViewFormat = viewType;
                        viewObj.addBoldClass();
                    }
                    en4.core.runonce.trigger();
                    Smoothbox.bind(params.responseContainer);
                    fundingProgressiveBarAnimation();
                }
            });
            en4.core.request.send(request);
        }
        function siteProjectTabSwitchview(element) {
            var identity = '<?php echo $this->identity; ?>';
            viewObj.selectedViewFormat = element.get('id');
            if(viewObj.selectedViewFormat =='mapView'){
                window.addEvent('domready', function () {
                    initialize();
                    setMarkers();
                });
            }
            if ($('viewFormat')) {
                $('viewFormat').set('value', viewObj.selectedViewFormat);
            }
            viewObj.addBoldClass();
            var type = element.get('rel');
            $('dynamic_app_info_sitecrowdfunding_' + identity).getElements('.sitecrowdfunding_container').setStyle('display', 'none');
            $('dynamic_app_info_sitecrowdfunding_' + identity).getElement("#" + type + "_sitecrowdfunding_").style.display = 'block';
            showNHideViewMore(viewObj.selectedViewFormat);
        }
    </script>
<?php endif; ?>
<?php if ($this->showContent == 2 || $this->showContent == 3): ?>
    <script type="text/javascript">
        en4.core.runonce.add(function () {
            hideViewMoreLink('<?php echo $this->showContent; ?>');
        });
    </script>
<?php else: ?>
    <script type="text/javascript">
        en4.core.runonce.add(function () {
            var view_more_content = $('dynamic_app_info_sitecrowdfunding_<?php echo $this->identity ?>').getElements('.seaocore_view_more');
            view_more_content.setStyle('display', 'none');
        });
    </script>
    <?php
    echo $this->paginationControl($this->gPaginator, null, array("pagination/pagination.tpl", "sitecrowdfunding"), array("orderby" => $this->orderby));
    ?>
<?php endif; ?>

<script type="text/javascript">

    var pageAction = function (page) {
        window.location.href = en4.core.baseUrl + 'sitecrowdfunding/project/browse/page/' + page + '/viewFormat/' + viewObj.selectedViewFormat;
    }

    function getNextPage() {
        return <?php echo sprintf('%d', $this->gPaginator->getCurrentPageNumber() + 1) ?>
    }

    function hideViewMoreLink(showContent) {
        if (showContent == 3) {
            var view_more_content = $('dynamic_app_info_sitecrowdfunding_<?php echo $this->identity ?>').getElements('.seaocore_view_more');
            view_more_content.setStyle('display', 'none');
            var totalCount = '<?php echo $this->gPaginator->count(); ?>';
            var currentPageNumber = '<?php echo $this->gPaginator->getCurrentPageNumber(); ?>';

            function doOnScrollLoadProjects()
            {
                if (typeof (view_more_content[0].offsetParent) != 'undefined') {
                    var elementPostionY = view_more_content[0].offsetTop;
                } else {
                    var elementPostionY = view_more_content.y;
                }
                if (elementPostionY <= window.getScrollTop() + (window.getSize().y - 40)) {

                    if ((totalCount != currentPageNumber) && (totalCount != 0))
                    {
                        if (en4.core.request.isRequestActive())
                            return;
                        var params = {
                            requestParams:<?php echo json_encode($this->params) ?>,
                            responseContainer: $('dynamic_app_info_sitecrowdfunding_' +<?php echo sprintf('%d', $this->identity) ?>)
                        }
                        params.requestParams.page =<?php echo sprintf('%d', $this->gPaginator->getCurrentPageNumber() + 1) ?>;
                        params.requestParams.content_id = '<?php echo $this->identity ?>';
                        view_more_content.setStyle('display', 'none');
                        params.responseContainer.getElements('.seaocore_loading').setStyle('display', '');
                        sendAjaxRequestSiteproject(params);
                    }
                }
            }
            window.onscroll = doOnScrollLoadProjects;

        } else if (showContent == 2) {
            var view_more_content = $('dynamic_app_info_sitecrowdfunding_<?php echo $this->identity ?>').getElements('.seaocore_view_more');
            view_more_content.setStyle('display', '<?php echo ( $this->gPaginator->count() == $this->gPaginator->getCurrentPageNumber() || $this->totalCount == 0 ? 'none' : '' ) ?>');
            view_more_content.removeEvents('click');
            view_more_content.addEvent('click', function () {
                var totalCount =  parseInt('<?php echo $this->gPaginator->count(); ?>')
                var currentPageNumber = parseInt('<?php echo $this->gPaginator->getCurrentPageNumber(); ?>')
                var totalcountfull = parseInt('<?php echo $this->totalCount; ?>')
                if((totalCount == currentPageNumber) || totalcountfull == 0 ){
                    view_more_content.setStyle('display', 'none');
                    return;
                }
                if (en4.core.request.isRequestActive())
                    return;
                var params = {
                    requestParams:<?php echo json_encode($this->params) ?>,
                    responseContainer: $('dynamic_app_info_sitecrowdfunding_' +<?php echo sprintf('%d', $this->identity) ?>)
                }
                params.requestParams.page =<?php echo sprintf('%d', $this->gPaginator->getCurrentPageNumber() + 1) ?>;
                params.requestParams.content_id = '<?php echo $this->identity ?>';
                view_more_content.setStyle('display', 'none');
                params.responseContainer.getElements('.seaocore_loading').setStyle('display', '');

                sendAjaxRequestSiteproject(params);
            });
        }
    }
    function showNHideViewMore(viewType) {
        var view_more_content = $('dynamic_app_info_sitecrowdfunding_<?php echo $this->identity ?>').getElements('.seaocore_view_more');
        if (viewType == 'gridView') {
            view_more_content.setStyle('display', '<?php echo ( $this->paginatorGridView->count() <= $this->gPaginator->getCurrentPageNumber() || $this->totalCount == 0 ? 'none' : '' ) ?>');
        } else {
            view_more_content.setStyle('display', '<?php echo ( $this->paginatorListView->count() <= $this->gPaginator->getCurrentPageNumber() || $this->totalCount == 0 ? 'none' : '' ) ?>');
        }
    }
    function fillAjaxData(viewType) {
        grid = '<?php echo ( $this->paginatorGridView->count() < $this->gPaginator->getCurrentPageNumber() || $this->totalCount == 0 ? 'false' : 'true' ) ?>';
        list = '<?php echo ( $this->paginatorListView->count() < $this->gPaginator->getCurrentPageNumber() || $this->totalCount == 0 ? 'false' : 'true' ) ?>';
        if (viewType == 'gridView' && grid == 'false') {
            return false;
        } else if (viewType == 'listView' && list == 'false') {
            return false;
        }
        return true;
    }

    function clear(element){
        for (var i = (element.options.length - 1); i >= 0; i--) {
            element.options[ i ] = null;
        }
    }


    // pre-popluate the field values
    var requestedParams = <?php echo json_encode($this->params) ?>;
    if(requestedParams.page_id !== null && requestedParams.page_id !== ''){

        // if initiative is passed, then pre-populate initiatives
        if(requestedParams.initiative !== null && requestedParams.initiative !== ''){
            initiativeOptions(requestedParams.page_id,'initiative_names',requestedParams.initiative);

            // if initiative_galleries is passed, then pre-populate initiative_galleries
            if(requestedParams.initiative_galleries !== null && requestedParams.initiative_galleries !== ''){
                initiativeOptions(requestedParams.initiative,'initiative_project_galleries',requestedParams.initiative_galleries);
            }else{
                initiativeOptions(requestedParams.initiative,'initiative_project_galleries');
            }
        }else{
            initiativeOptions(requestedParams.page_id,'initiative_names');
            $('initiative_galleries-wrapper').style.display = 'none';
        }
    }else{
        // Hide the initiative fields in form
        $('initiative-wrapper').style.display = 'none';
        $('initiative_galleries-wrapper').style.display = 'none';
    }



    function initiativeOptions(id, type , defaultValue) {
        if(id){
            var url = '<?php echo $this->url(array('action' => 'get-initiatives'), "sitepage_initiatives") ?>';
            var elementWrapper = null;
            var element = null;
            var page_id = null;
            var initiative_id = null;

            if(type =='initiative_names'){
                page_id = id;
                initiative_id = null;
            }

            if(type =='initiative_project_galleries'){
                page_id = $('page_id').value;
                initiative_id = id;
            }

            if(page_id){
                en4.core.request.send(new Request.JSON({
                    url: url,
                    data: {
                        format: 'json',
                        page_id:page_id,
                        initiative_id:initiative_id
                    },
                    onSuccess: function (responseJSON) {
                        var initiatives = responseJSON.initiatives;

                        if(initiative_id == null){
                            elementWrapper = $('initiative-wrapper');
                            element = $('initiative');
                        }
                        if(initiative_id != null){
                            elementWrapper = $('initiative_galleries-wrapper');
                            element = $('initiative_galleries');
                        }

                        elementWrapper.style.display = 'inline-block';

                        var option = document.createElement("OPTION");
                        option.text = "";
                        option.value = 0;
                        clear(element);
                        element.options.add(option);

                        for (let i = 0; i < initiatives.length; i++) {
                            var option = document.createElement("OPTION");
                            if(
                                (initiatives[i]['text']!==null && initiatives[i]['value']!==null)
                                ||
                                (initiatives[i]['text']!=='' && initiatives[i]['value']!=='')
                            )
                            {
                                option.text = initiatives[i]['text'];
                                option.value = initiatives[i]['value'];
                                element.options.add(option);
                            }
                        }

                        // set default value
                        element.value = defaultValue;

                    }
                }), {'force': true});
            }

        }else{
            clear($('initiative'));
            clear($('initiative_galleries'));
        }


    }


</script>

