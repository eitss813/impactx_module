<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
$baseUrl = $this->layout()->staticBaseUrl;
$this->headLink()
        ->prependStylesheet($baseUrl . 'application/modules/Seaocore/externals/styles/styles.css')
        ->prependStylesheet($baseUrl . 'application/modules/Sitevideo/externals/styles/style_sitevideo.css');

$this->headScript()
        ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitevideo/externals/scripts/core.js');
?>
<?php $this->headScript()->appendFile($baseUrl . 'application/modules/Seaocore/externals/scripts/favourite.js'); ?>
<?php $this->headScript()->appendFile($baseUrl . 'application/modules/Seaocore/externals/scripts/like.js'); ?>
<?php $this->headScript()->appendFile($baseUrl . 'application/modules/Sitevideo/externals/scripts/core.js'); ?>

<?php if ($this->is_ajax_load): ?>
    <?php if (empty($this->is_ajax)): ?>
        <div class="layout_core_container_tabs">

            <?php if ($this->tabCount > 1 || count($this->viewType) > 1): ?>
                <div class="tabs_alt tabs_parent tabs_parent_sitevideo_home">
                    <ul id="main_tabs" identity='<?php echo $this->identity ?>'>
                        <?php if (!empty($this->heading)) : ?>
                            <li>
                                <div class="" style="margin-top: 5px;">
                                    <h3>
                                        <?php echo $this->heading; ?>
                                    </h3>
                                </div>
                            </li>
                        <?php endif; ?>
                        <?php if ($this->tabCount > 1): ?>
                            <?php foreach ($this->tabs as $key => $tab): ?>
                                <li class="tab_li_<?php echo $this->identity ?> <?php echo $key == 0 ? 'active' : ''; ?>" rel="<?php echo $tab; ?>">
                                    <a  href='javascript:void(0);' ><?php echo $this->translate(ucwords(str_replace('_', ' ', $tab))); ?> </a>
                                </li>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        <?php
                        for ($i = count($this->viewType) - 1; $i >= 0; $i--):
                            ?>
                            <li class="seaocore_tab_select_wrapper fright tab_select_wrapper_<?php echo $this->identity; ?>" rel='<?php echo $this->viewType[$i] ?>'>
                                <div class="seaocore_tab_select_view_tooltip"><?php echo $this->translate(ucwords(str_replace('_', ' ', $this->viewType[$i]))) ?></div>
                                <span id="<?php echo $this->viewType[$i] . "_" . $this->identity ?>"class="seaocore_tab_icon tab_icon_<?php echo $this->viewType[$i] == 'video_view' ? 'card_view' : $this->viewType[$i] ?> <?php echo $this->viewFormat == $this->viewType[$i] ? 'active' : ''; ?>" onclick="sitevideoTabSwitchview($(this));" ></span>
                            </li>
                        <?php endfor; ?>
                    </ul>
                </div>
            <?php endif; ?>
            <div id="dynamic_app_info_sitevideo_<?php echo $this->identity; ?>">
            <?php endif; ?>
            <?php if (in_array('video_view', $this->viewType)): ?> 
                <div class="sitevideo_container" id="video_view_sitevideo_" style="<?php echo $this->viewFormat == 'video_view' ? $this->viewFormat : 'display:none;'; ?>">
                    <?php include APPLICATION_PATH . '/application/modules/Sitevideo/views/scripts/channel/_video_view.tpl'; ?>
                </div>
            <?php endif; ?>

            <?php if (in_array('grid_view', $this->viewType)): ?> 
                <div class="sitevideo_container" id="grid_view_sitevideo_" style="<?php echo $this->viewFormat == 'grid_view' ? $this->viewFormat : 'display:none;'; ?>">
                    <?php include APPLICATION_PATH . '/application/modules/Sitevideo/views/scripts/channel/_grid_view.tpl'; ?>
                </div>
            <?php endif; ?>
            <?php if (in_array('list_view', $this->viewType)): ?> 
                <div class="sitevideo_container" id="list_view_sitevideo_" style="<?php echo $this->viewFormat == 'list_view' ? $this->viewFormat : 'display:none;'; ?>">
                    <?php include APPLICATION_PATH . '/application/modules/Sitevideo/views/scripts/channel/_list_view.tpl'; ?>
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
        <script type="text/javascript">
            function sendAjaxRequestSitevideo(params) {
                var url = en4.core.baseUrl + 'widget';

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
                            $$('li.tab_select_wrapper_<?php echo $this->identity; ?> > span').each(function (el) {
                                el.removeClass("active");
                            });
                            defaultFormat = '<?php echo $this->viewFormat ?>';
                            id = '<?php echo $this->identity; ?>';
                            $(defaultFormat + "_" + id).addClass('active');
                        } else {
                            var element = new Element('div', {
                                'html': responseHTML
                            });
                            params.responseContainer.getElements('.seaocore_loading').setStyle('display', 'none');
                            if ($$('.siteevideo_videos_view') && element.getElement('.siteevideo_videos_view')) {
                                Elements.from(element.getElement('.siteevideo_videos_view').innerHTML).inject(params.responseContainer.getElement('.siteevideo_videos_view'));
                            }
                            if ($$('.siteevideo_videos_grid_view') && element.getElement('.siteevideo_videos_grid_view')) {
                                Elements.from(element.getElement('.siteevideo_videos_grid_view').innerHTML).inject(params.responseContainer.getElement('.siteevideo_videos_grid_view'));
                            }
                            if ($$('.siteevideo_list_view') && element.getElement('.siteevideo_list_view')) {
                                Elements.from(element.getElement('.siteevideo_list_view').innerHTML).inject(params.responseContainer.getElement('.siteevideo_list_view'));
                            }

                            if ($$('.sitevideo_img_view') && element.getElement('.sitevideo_img_view')) {
                                Elements.from(element.getElement('.sitevideo_img_view').innerHTML).inject(params.responseContainer.getElement('.sitevideo_img_view'));
                            }
                        }
                        en4.core.runonce.trigger();
                        Smoothbox.bind(params.responseContainer);
                    }
                });
                en4.core.request.send(request);
            }
            en4.core.runonce.add(function () {
        <?php if (count($this->tabs) > 1): ?>
                    $$('.tab_li_<?php echo $this->identity ?>').addEvent('click', function (video) {
                        if (en4.core.request.isRequestActive())
                            return;
                        var element = $(video.target);
                        if (element.tagName.toLowerCase() == 'a') {
                            element = element.getParent('li');
                        }
                        var type = element.get('rel');

                        element.getParent('ul').getElements('li').removeClass("active")
                        element.addClass("active");
                        var params = {
                            requestParams:<?php echo json_encode($this->params) ?>,
                            responseContainer: $('dynamic_app_info_sitevideo_' + '<?php echo $this->identity ?>')
                        }
                        params.requestParams.content_type = type;
                        params.requestParams.page = 1;
                        params.requestParams.content_id = '<?php echo $this->identity ?>';
                        params.responseContainer.empty();
                        new Element('div', {
                            'class': 'seaocore_content_loader'
                        }).inject(params.responseContainer);
                        sendAjaxRequestSitevideo(params);
                    });
        <?php endif; ?>
            });
            function sitevideoTabSwitchview(element) {
                $$('li.tab_select_wrapper_<?php echo $this->identity; ?> > span').each(function (el) {
                    el.removeClass("active");
                });
                element.addClass("active");
                if (element.tagName.toLowerCase() == 'span') {
                    element = element.getParent('li');
                }
                var type = element.get('rel');
                var identity = element.getParent('ul').get('identity');
                $('dynamic_app_info_sitevideo_' + identity).getElements('.sitevideo_container').setStyle('display', 'none');
                $('dynamic_app_info_sitevideo_' + identity).getElement("#" + type + "_sitevideo_").style.display = 'block';
            }
        </script>
    <?php endif; ?>
    <script type="text/javascript">
        en4.core.runonce.add(function () {
            var view_more_content = $('dynamic_app_info_sitevideo_<?php echo $this->identity ?>').getElements('.seaocore_view_more');
            view_more_content.setStyle('display', '<?php echo ( $this->paginator->count() == $this->paginator->getCurrentPageNumber() || $this->totalCount == 0 ? 'none' : '' ) ?>');
            view_more_content.removeEvents('click');
            view_more_content.addEvent('click', function () {
                if (en4.core.request.isRequestActive())
                    return;
                var params = {
                    requestParams:<?php echo json_encode($this->params) ?>,
                    responseContainer: $('dynamic_app_info_sitevideo_' +<?php echo sprintf('%d', $this->identity) ?>)
                }
                params.requestParams.content_type = "<?php echo $this->content_type ?>";
                params.requestParams.page =<?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() + 1) ?>;
                params.requestParams.content_id = '<?php echo $this->identity ?>';
                view_more_content.setStyle('display', 'none');
                params.responseContainer.getElements('.seaocore_loading').setStyle('display', '');

                sendAjaxRequestSitevideo(params);
            });
        });
    </script>
<?php else : ?>
    <div id="layout_sitevideo_recently_view_random_channels_<?php echo $this->identity; ?>">
        <div class="layout_core_container_tabs">
            <?php if ($this->tabCount > 1 || count($this->viewType) > 1): ?>
                <div class="tabs_alt tabs_parent tabs_parent_sitevideo_home">
                    <ul id="main_tabs" identity='<?php echo $this->identity ?>'>
                        <?php if ($this->tabCount > 1): ?>
                            <?php foreach ($this->tabs as $key => $tab): ?>
                                <li class="tab_li_<?php echo $this->identity ?> <?php echo $key == 0 ? 'active' : ''; ?>" rel="<?php echo $tab; ?>">
                                    <a  href='javascript:void(0);' ><?php echo $this->translate(ucwords(str_replace('_', ' ', $tab))); ?> </a>
                                </li>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        <?php
                        for ($i = count($this->viewType) - 1; $i >= 0; $i--):
                            ?>
                            <li class="seaocore_tab_select_wrapper fright tab_select_wrapper_<?php echo $this->identity; ?>" rel='<?php echo $this->viewType[$i] ?>'>
                                <div class="seaocore_tab_select_view_tooltip"><?php echo $this->translate(ucwords(str_replace('_', ' ', $this->viewType[$i]))) ?></div>
                                <span id="<?php echo $this->viewType[$i] . "_" . $this->identity ?>"class="seaocore_tab_icon tab_icon_<?php echo $this->viewType[$i] == 'video_view' ? 'card_view' : $this->viewType[$i] ?> <?php echo $this->viewFormat == $this->viewType[$i] ? 'active' : ''; ?>"  ></span>
                            </li>
                        <?php endfor; ?>
                    </ul>
                </div>
            <?php endif; ?>
            <div class="seaocore_content_loader"></div>
        </div>
    </div>
    <script type="text/javascript">
        window.addEvent('domready', function () {
            en4.sitevideo.ajaxTab.sendReq({
                loading: false,
                requestParams: $merge(<?php echo json_encode($this->paramsLocation); ?>, {'content_id': '<?php echo $this->identity; ?>'}),
                responseContainer: [$('layout_sitevideo_recently_view_random_channels_<?php echo $this->identity; ?>')]
            });
        });
    </script>
<?php endif; ?>