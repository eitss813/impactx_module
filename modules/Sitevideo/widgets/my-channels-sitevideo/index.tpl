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
?>
<?php
$viewTypeChannel = isset($this->channelNavigationLink[0]) ? ($this->channelNavigationLink[0]) : 0;
if ($this->tab && in_array($this->tab, $this->channelNavigationLink)) {
    $viewTypeChannel = $this->tab;
}
$channelDefaultViewType = $this->defaultViewType;
?>
<?php if (empty($this->is_ajax)) : ?>
    <?php include APPLICATION_PATH . '/application/modules/Sitevideo/views/scripts/_managequicklinks.tpl'; ?>

    <?php
    $baseUrl = $this->layout()->staticBaseUrl;
    $this->headLink()->appendStylesheet($baseUrl . 'application/modules/Seaocore/externals/styles/styles.css');

    $baseUrl = $this->layout()->staticBaseUrl;
    $this->headLink()->appendStylesheet($baseUrl . 'application/modules/Sitevideo/externals/styles/style_sitevideo.css');
    ?>
    <?php $showToplink = count($this->channelNavigationLink) == 1 && count($this->viewType) == 1 && $this->searchButton == 0; ?>
    <div class="sitevideo_myvideos_top_links b_medium" style="display:<?php echo $showToplink ? 'none' : 'inline-block' ?> ">
        <div class="sitevideo_myvideos_top_filter_links txt_center sitevideo_myvideos_top_filter_links_<?php echo $this->identity; ?>" style="display: <?php echo (count($this->channelNavigationLink) > 1) ? 'block' : 'none' ?>">
            <?php if (in_array('channel', $this->channelNavigationLink)) : ?>
                <a href="javascript:void(0);" id='channel' onclick="clearSearchBox();
                                filter_rsvp_channel('channel', '<?php echo $channelDefaultViewType; ?>', '')"><?php echo $this->translate('Channels'); ?></a> 
               <?php endif; ?>
               <?php if (in_array('liked', $this->channelNavigationLink)) : ?>   
                <a href="javascript:void(0);" id='liked'  onclick="clearSearchBox();
                                filter_rsvp_channel('liked', '<?php echo $channelDefaultViewType; ?>', '')" ><?php echo $this->translate('Liked'); ?></a> 
               <?php endif; ?>
               <?php if (in_array('favourite', $this->channelNavigationLink)) : ?>
                <a href="javascript:void(0);" id='favourite'  onclick="clearSearchBox();
                                filter_rsvp_channel('favourite', '<?php echo $channelDefaultViewType; ?>', '')" ><?php echo $this->translate('Favourites'); ?></a> 
               <?php endif; ?>
               <?php if (in_array('subscribed', $this->channelNavigationLink)) : ?>
                <a href="javascript:void(0);" id='subscribed' onclick="clearSearchBox();
                                filter_rsvp_channel('subscribed', '<?php echo $channelDefaultViewType; ?>', '')" ><?php echo $this->translate('Subscribed'); ?></a>  
               <?php endif; ?>
               <?php if (in_array('rated', $this->channelNavigationLink)) : ?>
                <a href="javascript:void(0);" id='rated' onclick="clearSearchBox();
                                filter_rsvp_channel('rated', '<?php echo $channelDefaultViewType; ?>', '')" ><?php echo $this->translate('Rated'); ?></a>
               <?php endif; ?>
        </div>

        <?php if ($this->searchButton) : ?>
            <div class="sitevideo_myvideos_tab_search fright">
                <a href="javascript:void(0);" onclick="shownhidesearch()"></a>

            </div>
        <?php endif; ?>
        <div class="sitevideo_myvideos_top_filter_views txt_right fright" id='videoViewFormat' style="display: <?php echo (count($this->viewType) > 1) ? 'block' : 'none' ?>" >
            <?php if (in_array('gridView', $this->viewType)) : ?>
                <span class="seaocore_tab_select_wrapper fright">
                    <div class="seaocore_tab_select_view_tooltip"><?php echo $this->translate("Grid View"); ?></div>
                    <span class="seaocore_tab_icon tab_icon_grid_view seaocore_tab_icon_<?php echo $this->identity ?>" onclick="clearSearchBox();
                                    changeViewChannel('gridView', '')" id="gridView" ></span>
                </span>
            <?php endif; ?>
            <?php if (in_array('listView', $this->viewType)) : ?>
                <span class="seaocore_tab_select_wrapper fright">
                    <div class="seaocore_tab_select_view_tooltip"><?php echo $this->translate("List View"); ?></div>
                    <span class="seaocore_tab_icon tab_icon_list_view seaocore_tab_icon_<?php echo $this->identity ?>" onclick="clearSearchBox();
                                    changeViewChannel('listView', '')" id="listView" ></span>
                </span>
            <?php endif; ?>
            <?php if (in_array('videoView', $this->viewType)) : ?>
                <span class="seaocore_tab_select_wrapper fright">
                    <div class="seaocore_tab_select_view_tooltip"><?php echo $this->translate("Card View"); ?></div>
                    <span class="seaocore_tab_icon tab_icon_card_view seaocore_tab_icon_<?php echo $this->identity ?>" onclick="clearSearchBox();
                                    changeViewChannel('videoView', '')" id="videoView" ></span>
                </span>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>
<div id="tbl_search" style="display: none;" class="sitevideo_myvideos_tab_search_panel">
    <span><?php echo $this->translate("Search within these results :"); ?></span>
    <input type="text" name="search" id="search" placeholder="<?php echo $this->translate("Start Typing Here..."); ?>" >
    <button onclick="filter_data()" > <?php echo $this->translate("Search"); ?> </button> <?php echo $this->translate("or"); ?> <a href="javascript:void(0);" onclick="shownhidesearch()"><?php echo $this->translate("Cancel"); ?></a>
</div>
<div id='siteevideo_manage_channel'>
</div>
<script>
    viewTypeChannel = '<?php echo $viewTypeChannel; ?>';
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
    addBoldClassChannel = function (reqType, viewFormat)
    {
        $$('div.sitevideo_myvideos_top_filter_links_<?php echo $this->identity; ?> > a').each(function (el) {
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
        changeViewChannel(viewFormatG, search);
    }
    filter_rsvp_channel = function (req_type, viewFormat, search)
    {
        if (req_type == '0')
            return false;
        viewFormatG = viewFormat;
        addBoldClassChannel(req_type, viewFormat);
        viewTypeChannel = req_type;
        switch (req_type)
        {
            case 'channel':
                var url = en4.core.baseUrl + 'widget/index/mod/sitevideo/name/channels-sitevideo/viewFormat/' + viewFormat + '/search/' + search;
                break;
            case 'liked':
                var url = en4.core.baseUrl + 'widget/index/mod/sitevideo/name/my-likedchannels-sitevideo/viewFormat/' + viewFormat + '/search/' + search;
                break;
            case 'favourite':
                var url = en4.core.baseUrl + 'widget/index/mod/sitevideo/name/my-favouritechannels-sitevideo/viewFormat/' + viewFormat + '/search/' + search;
                break;
            case 'subscribed':
                var url = en4.core.baseUrl + 'widget/index/mod/sitevideo/name/my-subscribedchannels-sitevideo/viewFormat/' + viewFormat + '/search/' + search;
                break;
            case 'rated':
                var url = en4.core.baseUrl + 'widget/index/mod/sitevideo/name/my-ratedchannels-sitevideo/viewFormat/' + viewFormat + '/search/' + search;
                break;
        }
        $('siteevideo_manage_channel').innerHTML = '<div class="seaocore_content_loader"></div>';

        var params = {
            requestParams:<?php echo json_encode($this->params) ?>
        };
        params.requestParams.is_ajax = 0;
        var request = new Request.HTML({
            url: url,
            data: $merge(params.requestParams, {
                format: 'html',
                subject: en4.core.subject.guid,
                is_ajax: 0,
                pagination: 0,
                page: 0,
            }),
            evalScripts: true,
            onSuccess: function (responseTree, responseElements, responseHTML, responseJavaScript) {
                $('siteevideo_manage_channel').innerHTML = '';
                $('siteevideo_manage_channel').innerHTML = responseHTML;
                Smoothbox.bind($('siteevideo_manage_channel'))
                en4.core.runonce.trigger();
            }
        });
        request.send();
    }
    videoTypeChannel = '<?php echo $viewTypeChannel; ?>';
    filter_rsvp_channel(videoTypeChannel, viewFormatG, '');
    changeViewChannel = function (viewFormat, search)
    { 
        viewFormatG = viewFormat;
        filter_rsvp_channel(viewTypeChannel, viewFormat, search);
    }
</script>