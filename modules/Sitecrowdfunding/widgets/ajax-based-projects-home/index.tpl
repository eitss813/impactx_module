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
$this->headLink()
        ->prependStylesheet($baseUrl . 'application/modules/Seaocore/externals/styles/styles.css')
        ->prependStylesheet($baseUrl . 'application/modules/Sitecrowdfunding/externals/styles/style_sitecrowdfunding.css');

$this->headScript()
        ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitecrowdfunding/externals/scripts/core.js');
?>
<?php $this->headScript()->appendFile($baseUrl . 'application/modules/Seaocore/externals/scripts/favourite.js'); ?>
<?php $this->headScript()->appendFile($baseUrl . 'application/modules/Seaocore/externals/scripts/like.js'); ?>
<?php $this->headScript()->appendFile($baseUrl . 'application/modules/Sitecrowdfunding/externals/scripts/core.js'); ?>
<?php if ($this->is_ajax_load): ?>
    <?php if (empty($this->is_ajax)): ?>
        <div class="layout_core_container_tabs">
            <?php if ($this->tabCount > 1 || count($this->viewType) > 1): ?>
                <div class="tabs_alt tabs_parent tabs_parent_sitecrowdfunding_home ajax_based_tabs">
                    <ul id="main_tabs" identity='<?php echo $this->identity ?>'>
                        <?php if ($this->tabCount > 1): ?>
                            <?php foreach ($this->tabs as $key => $tab): ?>
                                <li class="tab_li_<?php echo $this->identity ?> <?php echo $key == 0 ? 'active' : ''; ?>" rel="<?php echo $tab; ?>">
                                    <a  href='javascript:void(0);' >
                                        <?php $label = $this->translate(ucwords(str_replace('_', ' ', $tab))); ?>
                                        <?php echo ($label == 'Random' ? 'Explore' : $label); ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </ul>
                    <div class="grid_list_icon">
                        <ol>
                        <?php if (count($this->viewType) > 1 && empty($this->isSiteMobileView)): ?>
                            <?php for ($i = count($this->viewType) - 1; $i >= 0; $i--):
                                ?>
                                <li class="seaocore_tab_select_wrapper fright tab_select_wrapper_<?php echo $this->identity; ?>" rel='<?php echo $this->viewType[$i] ?>'>
                                    <div class="seaocore_tab_select_view_tooltip"><?php echo $this->translate(ucwords(str_replace('_', ' ', $this->viewType[$i]))) ?></div>
                                    <?php $viewName = '"'.$this->viewType[$i].'"' ?>
                                    <span id="<?php echo $this->viewType[$i] . "_" . $this->identity ?>"class="seaocore_tab_icon tab_icon_<?php echo $this->viewType[$i] ?> <?php echo $this->viewFormat == $this->viewType[$i] ? 'active' : ''; ?>" onclick='sitecrowdfundingTabSwitchview($(this));refreshView(<?php echo $viewName ?>);' ></span>
                                </li>
                            <?php endfor; ?>
                        <?php endif; ?>
                      </ol>
                   </div>
                </div>
            <?php endif; ?>

            <!-- show the browse_btn only in landing page -->
            <?php $path =  $_SERVER['REQUEST_URI']; ?>
            <?php if ($path == '/'): ?>
                <br/>
                <?php echo $this->htmlLink(array('route' => 'sitecrowdfunding_general', 'module' => 'sitecrowdfunding'), 'Browse All Projects', array('class' => 'button fright browse_project_btn')); ?>
                <br/>
            <?php endif; ?>

            <div id="dynamic_app_info_sitecrowdfunding_<?php echo $this->identity; ?>">
            <?php endif; ?> 
            <?php if (in_array('grid_view', $this->viewType) || $this->isSiteMobileView): ?> 
                <div class="sitecrowdfunding_container" id="grid_view_sitecrowdfunding_" style="<?php echo $this->viewFormat == 'grid_view' ? $this->viewFormat : 'display:none;'; ?>">
                    <?php include APPLICATION_PATH . '/application/modules/Sitecrowdfunding/views/scripts/project/_grid_view.tpl'; ?>
                </div>
            <?php endif; ?>
            <?php if (in_array('list_view', $this->viewType) && empty($this->isSiteMobileView)): ?> 
                <div class="sitecrowdfunding_container" id="list_view_sitecrowdfunding_" style="<?php echo $this->viewFormat == 'list_view' ? $this->viewFormat : 'display:none;'; ?>">
                    <?php include APPLICATION_PATH . '/application/modules/Sitecrowdfunding/views/scripts/project/_list_view.tpl'; ?>
                </div>
            <?php endif; ?>
            <?php if (in_array('map_view', $this->viewType) && empty($this->isSiteMobileView)): ?>
                <div class="sitecrowdfunding_container" id="map_view_sitecrowdfunding_" style="<?php echo $this->viewFormat == 'map_view' ? $this->viewFormat : 'display:none;'; ?>">
                    <?php include APPLICATION_PATH . '/application/modules/Sitecrowdfunding/views/scripts/project/_map_view.tpl'; ?>
                </div>
            <?php endif; ?>
            <?php if ($this->showViewMore): ?>
                <div class="seaocore_view_more mtop10" id="view_more_btn">
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
        <script type="text/javascript">
            function sendAjaxRequestSitecrowdfunding(params) {
                var url = en4.core.baseUrl + 'widget/index/mod/sitecrowdfunding/name/ajax-based-projects-home';

                if (params.requestUrl)
                    url = params.requestUrl;

                switch (params.requestParams.content_type) {
                    case 'random' :
                        params.requestParams.orderby = 'random';
                        break;
                    case 'most_recent' :
                        params.requestParams.orderby = 'startDate';
                        break;
                    case 'most_commented' :
                        params.requestParams.orderby  = 'commentCount';
                        break;
                    case 'most_backed' :
                        params.requestParams.orderby = 'backerCount';
                        break;
                    case 'most_funded' :
                        params.requestParams.orderby = 'mostFunded';
                        break;
                    case 'most_liked' :
                        params.requestParams.orderby = 'likeCount';
                        break;
                    case 'most_favourite' :
                        params.requestParams.orderby = 'favouriteCount';
                        break;
                    default :
                        params.requestParams.orderby = 'startDate';
                }

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
                            $$('li.tab_select_wrapper_<?php echo $this->identity; ?> > span').each(function (el) {
                                el.removeClass("active");
                            });
                            defaultFormat = '<?php echo $this->viewFormat ?>';
                            id = '<?php echo $this->identity; ?>';
                            if($(defaultFormat + "_" + id)){
                                $(defaultFormat + "_" + id).addClass('active');
                            }
                        } else {
                            var element = new Element('div', {
                                'html': responseHTML
                            });
                            params.responseContainer.getElements('.seaocore_loading').setStyle('display', 'none');
                            if ($$('.sitecrowdfunding_projects_grid_view') && element.getElement('.sitecrowdfunding_projects_grid_view')) {
                                Elements.from(element.getElement('.sitecrowdfunding_projects_grid_view').innerHTML).inject(params.responseContainer.getElement('.sitecrowdfunding_projects_grid_view'));
                            }
                            if ($$('.sitecrowdfunding_projects_list_view') && element.getElement('.sitecrowdfunding_projects_list_view')) {
                                Elements.from(element.getElement('.sitecrowdfunding_projects_list_view').innerHTML).inject(params.responseContainer.getElement('.sitecrowdfunding_projects_list_view'));
                            } 
                        }
                        fundingProgressiveBarAnimation();
                        en4.core.runonce.trigger();
                        Smoothbox.bind(params.responseContainer);
                    }
                });
                en4.core.request.send(request);
            }
            en4.core.runonce.add(function () {
        <?php if (count($this->tabs) > 1): ?>
                    $$('.tab_li_<?php echo $this->identity ?>').addEvent('click', function (project) {
                        if (en4.core.request.isRequestActive())
                            return;
                        var element = $(project.target);
                        if (element.tagName.toLowerCase() == 'a') {
                            element = element.getParent('li');
                        }
                        var type = element.get('rel');

                        element.getParent('ul').getElements('li').removeClass("active")
                        element.addClass("active");
                        var params = {
                            requestParams:<?php echo json_encode($this->params) ?>,
                            responseContainer: $('dynamic_app_info_sitecrowdfunding_' + '<?php echo $this->identity ?>')
                        }
                        params.requestParams.content_type = type;
                        params.requestParams.page = 1;
                        params.requestParams.content_id = '<?php echo $this->identity ?>';
                        params.responseContainer.empty();
                        new Element('div', {
                            'class': 'seaocore_content_loader'
                        }).inject(params.responseContainer);
                        sendAjaxRequestSitecrowdfunding(params);
                    });
        <?php endif; ?>
            });
            function sitecrowdfundingTabSwitchview(element) {
                $$('li.tab_select_wrapper_<?php echo $this->identity; ?> > span').each(function (el) {
                    el.removeClass("active");
                });
                element.addClass("active");
                if (element.tagName.toLowerCase() == 'span') {
                    element = element.getParent('li');
                }
                var type = element.get('rel');
                //var identity = element.getParent('ol').get('identity');
                $('dynamic_app_info_sitecrowdfunding_<?php echo $this->identity ?>').getElements('.sitecrowdfunding_container').setStyle('display', 'none');
                $('dynamic_app_info_sitecrowdfunding_<?php echo $this->identity ?>').getElement("#" + type + "_sitecrowdfunding_").style.display = 'block';
                fundingProgressiveBarAnimation();
            }
            function refreshView(viewName){
                if(viewName=='map_view'){
                    window.addEvent('domready', function () {
                        initialize();
                        setMarkers();
                        $('view_more_btn').style.display = 'none';
                    });
                }else{
                    $('view_more_btn').style.display = 'inherit';
                }
            }
        </script>
    <?php endif; ?>
    <script type="text/javascript">
        en4.core.runonce.add(function () {
            var view_more_content = $('dynamic_app_info_sitecrowdfunding_<?php echo $this->identity ?>').getElements('.seaocore_view_more');
            view_more_content.setStyle('display', '<?php echo ( $this->paginator->count() == $this->paginator->getCurrentPageNumber() || $this->totalCount == 0 ? 'none' : '' ) ?>');
            view_more_content.removeEvents('click');
            view_more_content.addEvent('click', function () {
                var totalCount =  parseInt('<?php echo $this->paginator->count(); ?>')
                var currentPageNumber = parseInt('<?php echo $this->paginator->getCurrentPageNumber(); ?>')
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
                params.requestParams.content_type = "<?php echo $this->content_type ?>";
                params.requestParams.page =<?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() + 1) ?>;
                params.requestParams.content_id = '<?php echo $this->identity ?>';
                view_more_content.setStyle('display', 'none');
                params.responseContainer.getElements('.seaocore_loading').setStyle('display', '');

                sendAjaxRequestSitecrowdfunding(params);
            });
        });
    </script>
<?php else : ?>
    <div id="layout_sitecrowdfunding_recently_view_random_projects_<?php echo $this->identity; ?>">
        <div class="layout_core_container_tabs">
            <?php if ($this->tabCount > 1 || count($this->viewType) > 1): ?>
                <div class="tabs_alt tabs_parent tabs_parent_sitecrowdfunding_home">
                    <ul id="main_tabs" identity='<?php echo $this->identity ?>'>
                        <?php if ($this->tabCount > 1): ?>
                            <?php foreach ($this->tabs as $key => $tab): ?>
                                <li class="tab_li_<?php echo $this->identity ?> <?php echo $key == 0 ? 'active' : ''; ?>" rel="<?php echo $tab; ?>">
                                    <!--<a  href='javascript:void(0);' ><?php echo $this->translate(ucwords(str_replace('_', ' ', $tab))); ?> </a>-->
                                    <a  href='javascript:void(0);' ><?php echo $this->tabLabel[$key]; ?> </a>
                                </li>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        <?php
                        for ($i = count($this->viewType) - 1; $i >= 0; $i--):
                            ?>
                            <li class="seaocore_tab_select_wrapper fright tab_select_wrapper_<?php echo $this->identity; ?>" rel='<?php echo $this->viewType[$i] ?>'>
                                <div class="seaocore_tab_select_view_tooltip"><?php echo $this->translate(ucwords(str_replace('_', ' ', $this->viewType[$i]))) ?></div>
                                <span id="<?php echo $this->viewType[$i] . "_" . $this->identity ?>"class="seaocore_tab_icon tab_icon_<?php echo $this->viewType[$i] ?> <?php echo $this->viewFormat == $this->viewType[$i] ? 'active' : ''; ?>"  ></span>
                            </li>
                        <?php endfor; ?>
                    </ul>
                </div>
            <?php endif; ?>
             <div class="clr"></div>
            <div class="seaocore_content_loader"></div>
        </div>
    </div>
    <script type="text/javascript">
        window.addEvent('domready', function () {
            en4.sitecrowdfunding.ajaxTab.sendReq({
                loading: false,
                requestParams: $merge(<?php echo json_encode($this->paramsLocation); ?>, {'content_id': '<?php echo $this->identity; ?>'}),
                responseContainer: [$('layout_sitecrowdfunding_recently_view_random_projects_<?php echo $this->identity; ?>')]
            });
        });
    </script>
<?php endif; ?>

<style>
    .layout_sitecrowdfunding_ajax_based_projects_home {
        background-color: #ffffff;
        padding: 15px;
        box-shadow: 0 1px 8px 0 rgba(0, 0, 0, .05);
        border-radius: 6px;
    }
</style>