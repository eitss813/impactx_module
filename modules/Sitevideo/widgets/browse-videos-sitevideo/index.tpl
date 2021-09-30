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
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitevideo/externals/scripts/core.js'); ?>
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/favourite.js'); ?>
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/like.js'); ?>

<?php
$this->params['identity'] = $this->identity;
if (!$this->id)
    $this->id = $this->identity;
$params = '';
foreach ($this->params as $key => $param) {
    if (!empty($param) && in_array($key, array('search', 'category_id', 'subcategory_id', 'tag_id', 'location', 'orderby', 'video_street', 'video_state', 'video_country', 'locationmiles'))) {
        $params .= '/' . $key . '/' . $param;
    }
}
?>
<?php
$baseUrl = $this->layout()->staticBaseUrl;
$this->headLink()->appendStylesheet($baseUrl . 'application/modules/Seaocore/externals/styles/styles.css');
$this->headLink()->appendStylesheet($baseUrl . 'application/modules/Sitevideo/externals/styles/style_sitevideo.css');
?>
<?php if (empty($this->is_ajax)): ?>
<?php $doNotShowTopContent = 0;?>
  <?php if($this->categoryName && !empty($this->categoryObject->top_content)): ?>

    <h4 class="sr_browse_lists_view_options_head mbot10" style="display: inherit;">
      <?php echo $this->translate($this->categoryName); ?>
    </h4>

    <?php $doNotShowTopContent = 1;?>
  <?php endif; ?>

  <?php if($this->category_id && !$this->subcategory_id && !$this->subsubcategory_id && !empty($this->showTopBottomContent)):?>
    <?php $catTopContent = Engine_Api::_()->getItem('sitevideo_video_category', $this->category_id)->top_content;?>
    <?php if($catTopContent):?>
        <div class="sr_browse_cat_cont clr"><?php echo $catTopContent; ?></div>
    <?php endif; ?>
  <?php elseif($this->subcategory_id && $this->category_id && !$this->subsubcategory_id):?>
    <?php $subcatTopContent = Engine_Api::_()->getItem('sitevideo_video_category', $this->subcategory_id)->top_content;?>
    <?php if($subcatTopContent):?>    
        <div class="sr_browse_cat_cont clr"><?php echo $subcatTopContent; ?></div>
    <?php endif; ?>    
  <?php elseif($this->subsubcategory_id && $this->category_id && $this->subcategory_id):?>
    <?php $subsubcatTopContent = Engine_Api::_()->getItem('sitevideo_video_category', $this->subsubcategory_id)->top_content;?>
    <?php if($subsubcatTopContent):?>
        <div class="sr_browse_cat_cont clr"><?php echo $subsubcatTopContent; ?></div>
    <?php endif; ?>    
  <?php endif;?> 
    <div class="layout_core_container_tabs">    
        <div class="sitevideo_browse_lists_view_options txt_right" id='videoViewFormat' style="display:<?php echo count($this->viewType) > 1 ? 'block' : 'none' ?>">
            <div class="fleft">
                <?php if (empty($this->heading)) : ?>
                    <?php echo $this->translate(array('%s video found.', '%s videos found.', $this->totalCount), $this->totalCount); ?>
                <?php else : ?>
                    <h3>
                        <?php echo $this->heading; ?>
                    </h3>
                <?php endif; ?>
            </div>
            <div class="fright">
                <?php if (in_array('gridView', $this->viewType)) : ?>
                    <span class="seaocore_tab_select_wrapper fright">
                        <div class="seaocore_tab_select_view_tooltip"><?php echo $this->translate("Grid View"); ?></div>
                        <span class="seaocore_tab_icon tab_icon_grid_view seaocore_tab_icon_<?php echo $this->identity ?>" onclick="sitevideoTabSwitchview($(this));" id="gridView" rel='grid_view' ></span>
                    </span>
                <?php endif; ?>
                <?php if (in_array('listView', $this->viewType)) : ?>
                    <span class="seaocore_tab_select_wrapper fright">
                        <div class="seaocore_tab_select_view_tooltip"><?php echo $this->translate("List View"); ?></div>
                        <span class="seaocore_tab_icon tab_icon_list_view seaocore_tab_icon_<?php echo $this->identity ?>" onclick="sitevideoTabSwitchview($(this));" id="listView" rel='list_view' ></span>
                    </span>
                <?php endif; ?>
                <?php if (in_array('videoView', $this->viewType)) : ?>
                    <span class="seaocore_tab_select_wrapper fright">
                        <div class="seaocore_tab_select_view_tooltip"><?php echo $this->translate("Card View"); ?></div>
                        <span class="seaocore_tab_icon tab_icon_card_view seaocore_tab_icon_<?php echo $this->identity ?>" onclick="sitevideoTabSwitchview($(this));" id="videoView" rel='video_view' ></span>
                    </span>
                <?php endif; ?>
            </div>
        </div>
        <div id="dynamic_app_info_sitevideo_<?php echo $this->identity; ?>">
        <?php endif; ?>
        <?php if (in_array('videoView', $this->viewType)) : ?>
            <div class="sitevideo_container" id="video_view_sitevideo_" style="<?php echo $this->viewFormat == 'videoView' ? $this->viewFormat : 'display:none;'; ?>">
                <?php include APPLICATION_PATH . '/application/modules/Sitevideo/views/scripts/video/_video_view.tpl'; ?>
            </div>
        <?php endif; ?>
        <?php if (in_array('gridView', $this->viewType)) : ?>
            <div class="sitevideo_container" id="grid_view_sitevideo_" style="<?php echo $this->viewFormat == 'gridView' ? $this->viewFormat : 'display:none;'; ?>">
                <?php include APPLICATION_PATH . '/application/modules/Sitevideo/views/scripts/video/_grid_view.tpl'; ?>
            </div>
        <?php endif; ?>
        <?php if (in_array('listView', $this->viewType)) : ?>
            <div class="sitevideo_container" id="list_view_sitevideo_" style="<?php echo $this->viewFormat == 'listView' ? $this->viewFormat : 'display:none;'; ?>">
                <?php include APPLICATION_PATH . '/application/modules/Sitevideo/views/scripts/video/_list_view.tpl'; ?>
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
                        viewObj.addBoldClass();
                    }
                    en4.core.runonce.trigger();
                    Smoothbox.bind(params.responseContainer);
                }
            });
            en4.core.request.send(request);
        }
        function sitevideoTabSwitchview(element) {
            var identity = '<?php echo $this->identity; ?>';
            viewObj.selectedViewFormat = element.get('id');
            if ($('viewFormat')) {
                $('viewFormat').set('value', viewObj.selectedViewFormat);
            }
            viewObj.addBoldClass();
            var type = element.get('rel');
            $('dynamic_app_info_sitevideo_' + identity).getElements('.sitevideo_container').setStyle('display', 'none');
            $('dynamic_app_info_sitevideo_' + identity).getElement("#" + type + "_sitevideo_").style.display = 'block';
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
            var view_more_content = $('dynamic_app_info_sitevideo_<?php echo $this->identity ?>').getElements('.seaocore_view_more');
            view_more_content.setStyle('display', 'none');
        });
    </script>
    <?php
    echo $this->paginationControl($this->paginator, null, array("pagination/pagination.tpl", "sitevideo"), array("orderby" => $this->orderby));
    ?>
<?php endif; ?>

<script type="text/javascript">
    var getRequestParameter = function () {
        return '<?php echo $params; ?>';
    }
    var pageAction = function (page) {
        window.location.href = en4.core.baseUrl + 'sitevideo/video/browse/page/' + page + '/viewFormat/' + viewObj.selectedViewFormat + getRequestParameter();
    }

    function getNextPage() {
        return <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() + 1) ?>
    }

    function hideViewMoreLink(showContent) {
        if (showContent == 3) {
            var view_more_content = $('dynamic_app_info_sitevideo_<?php echo $this->identity ?>').getElements('.seaocore_view_more');
            view_more_content.setStyle('display', 'none');
            var totalCount = '<?php echo $this->paginator->count(); ?>';
            var currentPageNumber = '<?php echo $this->paginator->getCurrentPageNumber(); ?>';

            function doOnScrollLoadChannel()
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
                            responseContainer: $('dynamic_app_info_sitevideo_' +<?php echo sprintf('%d', $this->identity) ?>)
                        }
                        params.requestParams.page =<?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() + 1) ?>;
                        params.requestParams.content_id = '<?php echo $this->identity ?>';
                        view_more_content.setStyle('display', 'none');
                        params.responseContainer.getElements('.seaocore_loading').setStyle('display', '');
                        sendAjaxRequestSitevideo(params);
                    }
                }
            }
            window.onscroll = doOnScrollLoadChannel;

        } else if (showContent == 2) {
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
                params.requestParams.page =<?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() + 1) ?>;
                params.requestParams.content_id = '<?php echo $this->identity ?>';
                view_more_content.setStyle('display', 'none');
                params.responseContainer.getElements('.seaocore_loading').setStyle('display', '');

                sendAjaxRequestSitevideo(params);
            });
        }
    }
</script>
<?php if(!empty($this->showTopBottomContent)):?>
    <?php if($this->category_id && !$this->subcategory_id && !$this->subsubcategory_id):?>
      <div class="sr_browse_cat_cont clr"><?php echo Engine_Api::_()->getItem('sitevideo_video_category', $this->category_id)->bottom_content;?></div>
    <?php elseif($this->subcategory_id && $this->category_id && !$this->subsubcategory_id):?>
      <div class="sr_browse_cat_cont clr"><?php echo Engine_Api::_()->getItem('sitevideo_video_category', $this->subcategory_id)->bottom_content;?></div>
    <?php elseif($this->subsubcategory_id && $this->category_id && $this->subcategory_id):?>
      <div class="sr_browse_cat_cont clr"><?php echo Engine_Api::_()->getItem('sitevideo_video_category', $this->subsubcategory_id)->bottom_content;?></div>
    <?php endif;?> 
  <?php endif;?> 
