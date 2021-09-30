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
$apiKey = Engine_Api::_()->seaocore()->getGoogleMapApiKey();
$this->headScript()->appendFile("https://maps.googleapis.com/maps/api/js?libraries=places&key=$apiKey");
?>
<?php
$viewTypeVideo = isset($this->videoNavigationLink[0]) ? ($this->videoNavigationLink[0]) : 0;
if ($this->tab && in_array($this->tab, $this->videoNavigationLink)) {
    $viewTypeVideo = $this->tab;
}
$videoDefaultViewType = $this->defaultViewType;
?>
<?php if (empty($this->is_ajax)) : ?>
    <?php include APPLICATION_PATH . '/application/modules/Sitevideo/views/scripts/_managequicklinks.tpl'; ?>
    <?php
    $baseUrl = $this->layout()->staticBaseUrl;
    $this->headLink()->appendStylesheet($baseUrl . 'application/modules/Seaocore/externals/styles/styles.css');
    $this->headLink()->appendStylesheet($baseUrl . 'application/modules/Sitevideo/externals/styles/style_sitevideo.css');
    ?>
    <?php $showToplink = count($this->videoNavigationLink) == 1 && count($this->viewType) == 1 && $this->searchButton == 0 && count($this->playlistViewType) <= 1; ?>
    <div class="sitevideo_myvideos_top_links b_medium" style="display:<?php echo $showToplink ? 'none' : 'inline-block' ?>">
        <div class="sitevideo_myvideos_top_filter_links txt_center sitevideo_myvideos_top_filter_links_<?php echo $this->identity; ?>" style="display:<?php echo (count($this->videoNavigationLink) > 1) ? 'block' : 'none'; ?>" >
            <?php if (in_array('video', $this->videoNavigationLink)) : ?>
                <a href="javascript:void(0);" id='video' onclick="clearSearchBox();
                                filter_rsvp_video('video', '<?php echo $videoDefaultViewType; ?>', '')"><?php echo $this->translate('Videos'); ?></a>
               <?php endif; ?>
               <?php if (in_array('playlist', $this->videoNavigationLink) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.playlist.allow', 1)) : ?>
                <a href="javascript:void(0);" id='playlist' onclick="clearSearchBox();
                                filter_rsvp_video('playlist', '<?php echo $this->playlistDefaultViewType; ?>', '')" ><?php echo $this->translate('Playlists'); ?></a> 
               <?php endif; ?>
               <?php if (in_array('watchlater', $this->videoNavigationLink) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.watchlater.allow', 1)) : ?>
                <a href="javascript:void(0);" id='watchlater' onclick="clearSearchBox();
                                filter_rsvp_video('watchlater', '<?php echo $videoDefaultViewType; ?>', '')" ><?php echo $this->translate('Watch Later'); ?></a>
               <?php endif; ?>
               <?php if (in_array('liked', $this->videoNavigationLink)) : ?>   
                <a href="javascript:void(0);" id='liked'  onclick="clearSearchBox();
                                filter_rsvp_video('liked', '<?php echo $videoDefaultViewType; ?>', '')" ><?php echo $this->translate('Liked'); ?></a>
               <?php endif; ?>
               <?php if (in_array('favourite', $this->videoNavigationLink)) : ?>
                <a href="javascript:void(0);" id='favourite'  onclick="clearSearchBox();
                                filter_rsvp_video('favourite', '<?php echo $videoDefaultViewType; ?>', '')" ><?php echo $this->translate('Favourites'); ?></a>
               <?php endif; ?>
               <?php if (in_array('rated', $this->videoNavigationLink) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.rating', 1)) : ?>
                <a href="javascript:void(0);" id='rated' onclick="clearSearchBox();
                                filter_rsvp_video('rated', '<?php echo $videoDefaultViewType; ?>', '')" ><?php echo $this->translate('Rated'); ?></a>               <?php endif; ?>
        </div>
        <?php if ($this->searchButton) : ?>
            <div class="sitevideo_myvideos_tab_search fright">
                <a href="javascript:void(0);" onclick="shownhidesearch()"></a>
            </div>
        <?php endif; ?>
        <div class="sitevideo_myvideos_top_filter_views txt_right fright" id='videoViewFormat' style="display:<?php echo (count($this->viewType) > 1) ? 'block' : 'none'; ?>">
            <?php if (in_array('gridView', $this->viewType)) : ?>
                <span class="seaocore_tab_select_wrapper fright">
                    <div class="seaocore_tab_select_view_tooltip"><?php echo $this->translate("Grid View"); ?></div>
                    <span class="seaocore_tab_icon tab_icon_grid_view seaocore_tab_icon_<?php echo $this->identity ?>" onclick="clearSearchBox();
                                    changeViewVideo('gridView', '')" id="gridView" ></span>
                </span>
            <?php endif; ?>
            <?php if (in_array('listView', $this->viewType)) : ?>
                <span class="seaocore_tab_select_wrapper fright">
                    <div class="seaocore_tab_select_view_tooltip"><?php echo $this->translate("List View"); ?></div>
                    <span class="seaocore_tab_icon tab_icon_list_view seaocore_tab_icon_<?php echo $this->identity ?>" onclick="clearSearchBox();
                                    changeViewVideo('listView', '')" id="listView" ></span>
                </span>
            <?php endif; ?>
            <?php if (in_array('videoView', $this->viewType)) : ?>
                <span class="seaocore_tab_select_wrapper fright">
                    <div class="seaocore_tab_select_view_tooltip"><?php echo $this->translate("Card View"); ?></div>
                    <span class="seaocore_tab_icon tab_icon_card_view seaocore_tab_icon_<?php echo $this->identity ?>" onclick="clearSearchBox();
                                    changeViewVideo('videoView', '')" id="videoView" ></span>
                </span>
            <?php endif; ?>
        </div>
        <div class="sitevideo_myvideos_top_filter_views txt_right fright"  id='playlistViewFormat' style="float:right;display:<?php echo (count($this->playlistViewType) > 1) ? 'block' : 'none'; ?>">
            <?php if (in_array('gridView', $this->playlistViewType)) : ?>
                <span class="seaocore_tab_select_wrapper fright">
                    <div class="seaocore_tab_select_view_tooltip"><?php echo $this->translate("Grid View"); ?></div>
                    <span class="seaocore_tab_icon tab_icon_grid_view seaocore_tab_icon_<?php echo $this->identity ?>" onclick="clearSearchBox();
                                    changeViewVideo('gridView', '')" id="p_gridView" ></span>
                </span>
            <?php endif; ?>
            <?php if (in_array('listView', $this->playlistViewType)) : ?>
                <span class="seaocore_tab_select_wrapper fright">
                    <div class="seaocore_tab_select_view_tooltip"><?php echo $this->translate("List View"); ?></div>
                    <span class="seaocore_tab_icon tab_icon_list_view seaocore_tab_icon_<?php echo $this->identity ?>" onclick="clearSearchBox();
                                    changeViewVideo('listView', '')" id="p_listView" ></span>
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
<div id='siteevideo_manage_video'>

</div>
<script>
    viewTypeVideo = '<?php echo $viewTypeVideo; ?>';
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
    addBoldClassVideo = function (reqType, viewFormat)
    {
        $$('div.sitevideo_myvideos_top_filter_links_<?php echo $this->identity; ?> > a').each(function (el) {
            
            el.removeClass('active');
        });
        
        $$('.seaocore_tab_icon_<?php echo $this->identity ?>').each(function (el) {
            el.removeClass('active');
        });
        $(reqType).addClass('active');
        if (reqType == 'playlist')
            $('p_' + viewFormat).addClass('active');
        else
            $(viewFormat).addClass('active');
    }
    filter_data = function ()
    {
        search = $('search').value;
        changeViewVideo(viewFormatG, search);
    }
    filter_rsvp_video = function (req_type, viewFormat, search)
    {
        if (req_type == '0')
            return false;
        viewFormatG = viewFormat;
        addBoldClassVideo(req_type, viewFormat);
        if (req_type == 'playlist')
        {
            $('videoViewFormat').style.display = 'none';
            $('playlistViewFormat').style.display = 'block';
        }
        else
        {
            $('videoViewFormat').style.display = 'block';
            $('playlistViewFormat').style.display = 'none';

        }
        viewTypeVideo = req_type;
        switch (req_type)
        {
            case 'video':
                var url = en4.core.baseUrl + 'widget/index/mod/sitevideo/name/videos-sitevideo/viewFormat/' + viewFormat + '/search/' + search;
                break;
            case 'playlist':
                var url = en4.core.baseUrl + 'widget/index/mod/sitevideo/name/my-playlists-sitevideo/viewFormat/' + viewFormat + '/search/' + search;
                break;
            case 'watchlater':
                var url = en4.core.baseUrl + 'widget/index/mod/sitevideo/name/my-watchlaters-sitevideo/viewFormat/' + viewFormat + '/search/' + search;
                break;
            case 'liked':
                var url = en4.core.baseUrl + 'widget/index/mod/sitevideo/name/my-likedvideos-sitevideo/viewFormat/' + viewFormat + '/search/' + search;
                break;
            case 'favourite':
                var url = en4.core.baseUrl + 'widget/index/mod/sitevideo/name/my-favouritevideos-sitevideo/viewFormat/' + viewFormat + '/search/' + search;
                break;
            case 'rated':
                var url = en4.core.baseUrl + 'widget/index/mod/sitevideo/name/my-ratedvideos-sitevideo/viewFormat/' + viewFormat + '/search/' + search;
                break;
        }
        $('siteevideo_manage_video').innerHTML = '<div class="seaocore_content_loader"></div>';

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
                $('siteevideo_manage_video').innerHTML = '';
                $('siteevideo_manage_video').innerHTML = responseHTML;
                Smoothbox.bind($('siteevideo_manage_video'));
                en4.core.runonce.trigger();
                en4.sitevideolightboxview.attachClickEvent(Array('sitevideo_thumb_viewer'));
            }
        });
        request.send();
    }
    videoType = '<?php echo $viewTypeVideo; ?>';
    if (videoType == 'playlist')
        viewFormatG = '<?php echo $this->playlistDefaultViewType; ?>';
    filter_rsvp_video(videoType, viewFormatG, '');
    changeViewVideo = function (viewFormat, search)
    {
        viewFormatG = viewFormat;
        filter_rsvp_video(viewTypeVideo, viewFormat, search);
    }
</script>