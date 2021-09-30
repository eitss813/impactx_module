
(function () { // START NAMESPACE
    var $ = 'id' in document ? document.id : window.$;
    en4.sitevideolightboxview = {
        active_request: 0,
        count: 0,
        ads: '',
        locationHref: window.location.href,
        attach_event_classes: Array('item_photo_video', 'item_photo_sitepagevideo_video', 'sitepagevideo_thumb_wrapper', 'item_photo_sitebusinessvideo_video', 'sitebusinessvideo_thumb_wrapper', 'ynvideo_featured_title', 'ynvideo_title', 'video_title', 'sitebusinessvideo_profile_title', 'sitepagevideo_profile_title', 'sitepagevideo_title', 'sitebusinessvideo_title', 'layout_video_show_also_liked', 'layout_video_show_same_poster', 'layout_video_show_same_tags', 'layout_ynvideo_show_also_liked', 'message_view', 'item_photo_avp_video', 'avp_videos_title', 'avp_title', 'sees_related_video_title', 'sees_related_video_thumb', 'videofeed_entry_title', 'videofeed_entry_photo', 'item_photo_sitereview_video', 'sitereview_video_title', 'item_photo_sitegroupvideo_video', 'sitegroupvideo_profile_title', 'sitegroupvideo_title', 'item_photo_sitestorevideo_video', 'sitestorevideo_profile_title', 'sitestorevideo_title', 'item_photo_sitestoreproduct_video', 'sitestoreproduct_video_title', 'sitevideo_thumb_viewer', 'feed_video_title'),
        types: {
            /*video*/
            layout_video_list_recent_videos: 'recent',
            layout_video_list_most_view_videos: 'most-viewed',
            layout_video_list_liked_videos: 'most-liked',
            layout_video_show_same_tags: 'same-tags',
            layout_video_show_same_poster: 'same-poster',
            layout_video_list_most_rating_videos: 'top-rated',
            layout_video_list_most_comment_videos: 'most-commented',
            layout_video_show_also_liked: 'also-liked',
            layout_video_list_recent_creation_videos: 'recent',
            layout_video_list_recent_modified_videos: 'modified',
            /*ynvideo*/
            layout_ynvideo_list_recent_videos: 'recent',
            layout_ynvideo_list_most_view_videos: 'most-viewed',
            layout_ynvideo_list_liked_videos: 'most-liked',
            layout_ynvideo_list_favorite_videos: 'most-favorite',
            layout_ynvideo_show_same_categories: 'same-categories',
            layout_ynvideo_show_same_tags: 'same-tags',
            layout_ynvideo_show_same_poster: 'same-poster',
            layout_ynvideo_list_most_rating_videos: 'top-rated',
            layout_ynvideo_list_most_comment_videos: 'most-commented',
            layout_ynvideo_show_also_liked: 'also-liked',
            layout_ynvideo_list_my_favorite_videos: 'my-favorite',
            layout_ynvideo_list_my_watch_later_videos: 'my-watch-later',
            layout_ynvideo_list_featured_videos: 'featured',
            layout_ynvideo_profile_favorite_videos: 'profile-favorite',
            layout_advgroup_recent_group_videos: 'subject_recent',
            /* listing*/
            layout_list_video_list: 'profile-listing',
            /* review*/
            layout_sitereview_video_sitereview: 'profile-sitereview',
            /* product*/
            layout_sitereview_video_sitestoreproduct: 'profile-sitestoreproduct',
            /* profile recipe*/
            layout_recipe_video_recipe: 'profile-recipe',
            /*sitepagevideo*/
            layout_sitepagevideo_homerecent_sitepagevideos: 'recent',
            layout_sitepagevideo_homeview_sitepagevideos: 'most-viewed',
            layout_sitepagevideo_homerate_sitepagevideos: 'top-rated',
            layout_sitepagevideo_homelike_sitepagevideos: 'most-liked',
            layout_sitepagevideo_homefeaturelist_sitepagevideos: 'featured',
            layout_sitepagevideo_featured_videos_carousel: 'featured',
            layout_sitepagevideo_homehighlightlist_sitepagevideos: 'highlight',
            layout_sitepagevideo_list_videos_tabs_recent_pagevideos: 'recent',
            layout_sitepagevideo_list_videos_tabs_liked_pagevideos: 'most-liked',
            layout_sitepagevideo_list_videos_tabs_viewed_pagevideos: 'most-viewed',
            layout_sitepagevideo_list_videos_tabs_commented_pagevideos: 'most-commented',
            layout_sitepagevideo_list_videos_tabs_featured_pagevideos: 'featured',
            /*sitepage video page profile*/
            layout_sitepagevideo_comment_sitepagevideos: 'most-comment',
            layout_sitepagevideo_view_sitepagevideos: 'most-viewed',
            layout_sitepagevideo_recent_sitepagevideos: 'recent',
            layout_sitepagevideo_rate_sitepagevideos: 'top-rated',
            layout_sitepagevideo_like_sitepagevideos: 'most-liked',
            layout_sitepagevideo_featurelist_sitepagevideos: 'featured',
            layout_sitepagevideo_highlightlist_sitepagevideos: 'highlight',
            layout_sitepagevideo_profile_sitepagevideos: 'page-profile',
            /*sitepage video view page*/
            layout_sitepagevideo_show_same_poster: 'same-poster',
            layout_sitepagevideo_show_same_tags: 'same-tags',
            layout_sitepagevideo_show_also_liked: 'also-liked',
            /*sitebusinessvideo*/
            layout_sitebusinessvideo_homerecent_sitebusinessvideos: 'recent',
            layout_sitebusinessvideo_homeview_sitebusinessvideos: 'most-viewed',
            layout_sitebusinessvideo_homerate_sitebusinessvideos: 'top-rated',
            layout_sitebusinessvideo_homelike_sitebusinessvideos: 'most-liked',
            layout_sitebusinessvideo_homefeaturelist_sitebusinessvideos: 'featured',
            layout_sitebusinessvideo_featured_videos_carousel: 'featured',
            layout_sitebusinessvideo_homehighlightlist_sitebusinessvideos: 'highlight',
            layout_sitebusinessvideo_list_videos_tabs_recent_businessvideos: 'recent',
            layout_sitebusinessvideo_list_videos_tabs_liked_businessvideos: 'most-liked',
            layout_sitebusinessvideo_list_videos_tabs_viewed_businessvideos: 'most-viewed',
            layout_sitebusinessvideo_list_videos_tabs_commented_businessvideos: 'most-commented',
            layout_sitebusinessvideo_list_videos_tabs_featured_businessvideos: 'featured',
            /*sitebusiness video page profile*/
            layout_sitebusinessvideo_comment_sitebusinessvideos: 'most-comment',
            layout_sitebusinessvideo_view_sitebusinessvideos: 'most-viewed',
            layout_sitebusinessvideo_recent_sitebusinessvideos: 'recent',
            layout_sitebusinessvideo_rate_sitebusinessvideos: 'top-rated',
            layout_sitebusinessvideo_like_sitebusinessvideos: 'most-liked',
            layout_sitebusinessvideo_featurelist_sitebusinessvideos: 'featured',
            layout_sitebusinessvideo_highlightlist_sitebusinessvideos: 'highlight',
            layout_sitebusinessvideo_profile_sitebusinessvideos: 'business-profile',
            /*sitebusiness video view page*/
            layout_sitebusinessvideo_show_same_poster: 'same-poster',
            layout_sitebusinessvideo_show_same_tags: 'same-tags',
            layout_sitebusinessvideo_show_also_liked: 'also-liked',
            /*sitegroupvideo*/
            layout_sitegroupvideo_homerecent_sitegroupvideos: 'recent',
            layout_sitegroupvideo_homeview_sitegroupvideos: 'most-viewed',
            layout_sitegroupvideo_homerate_sitegroupvideos: 'top-rated',
            layout_sitegroupvideo_homelike_sitegroupvideos: 'most-liked',
            layout_sitegroupvideo_homefeaturelist_sitegroupvideos: 'featured',
            layout_sitegroupvideo_featured_videos_carousel: 'featured',
            layout_sitegroupvideo_homehighlightlist_sitegroupvideos: 'highlight',
            layout_sitegroupvideo_list_videos_tabs_recent_groupvideos: 'recent',
            layout_sitegroupvideo_list_videos_tabs_liked_groupvideos: 'most-liked',
            layout_sitegroupvideo_list_videos_tabs_viewed_groupvideos: 'most-viewed',
            layout_sitegroupvideo_list_videos_tabs_commented_groupvideos: 'most-commented',
            layout_sitegroupvideo_list_videos_tabs_featured_groupvideos: 'featured',
            /*sitegroup video group profile*/
            layout_sitegroupvideo_comment_sitegroupvideos: 'most-comment',
            layout_sitegroupvideo_view_sitegroupvideos: 'most-viewed',
            layout_sitegroupvideo_recent_sitegroupvideos: 'recent',
            layout_sitegroupvideo_rate_sitegroupvideos: 'top-rated',
            layout_sitegroupvideo_like_sitegroupvideos: 'most-liked',
            layout_sitegroupvideo_featurelist_sitegroupvideos: 'featured',
            layout_sitegroupvideo_highlightlist_sitegroupvideos: 'highlight',
            layout_sitegroupvideo_profile_sitegroupvideos: 'group-profile',
            /*sitegroup video view group*/
            layout_sitegroupvideo_show_same_poster: 'same-poster',
            layout_sitegroupvideo_show_same_tags: 'same-tags',
            layout_sitegroupvideo_show_also_liked: 'also-liked',
            /*sitestorevideo*/
            layout_sitestorevideo_homerecent_sitestorevideos: 'recent',
            layout_sitestorevideo_homeview_sitestorevideos: 'most-viewed',
            layout_sitestorevideo_homerate_sitestorevideos: 'top-rated',
            layout_sitestorevideo_homelike_sitestorevideos: 'most-liked',
            layout_sitestorevideo_homefeaturelist_sitestorevideos: 'featured',
            layout_sitestorevideo_featured_videos_carousel: 'featured',
            layout_sitestorevideo_homehighlightlist_sitestorevideos: 'highlight',
            layout_sitestorevideo_list_videos_tabs_recent_storevideos: 'recent',
            layout_sitestorevideo_list_videos_tabs_liked_storevideos: 'most-liked',
            layout_sitestorevideo_list_videos_tabs_viewed_storevideos: 'most-viewed',
            layout_sitestorevideo_list_videos_tabs_commented_storevideos: 'most-commented',
            layout_sitestorevideo_list_videos_tabs_featured_storevideos: 'featured',
            /*sitestore video store profile*/
            layout_sitestorevideo_comment_sitestorevideos: 'most-comment',
            layout_sitestorevideo_view_sitestorevideos: 'most-viewed',
            layout_sitestorevideo_recent_sitestorevideos: 'recent',
            layout_sitestorevideo_rate_sitestorevideos: 'top-rated',
            layout_sitestorevideo_like_sitestorevideos: 'most-liked',
            layout_sitestorevideo_featurelist_sitestorevideos: 'featured',
            layout_sitestorevideo_highlightlist_sitestorevideos: 'highlight',
            layout_sitestorevideo_profile_sitestorevideos: 'store-profile',
            /*sitestore video view store*/
            layout_sitestorevideo_show_same_poster: 'same-poster',
            layout_sitestorevideo_show_same_tags: 'same-tags',
            layout_sitestorevideo_show_also_liked: 'also-liked',
            /*sitelike*/
            layout_sitelike_list_like_items: 'most-liked'
        },
        addclasstypes: {
            layout_video_view_videos: "layout_video_list_most_view_videos",
            layout_video_rating_videos: "layout_video_list_most_rating_videos",
            layout_video_comment_videos: "layout_video_list_most_comment_videos",
            layout_video_recent_creation_videos: "layout_video_list_recent_creation_videos",
            layout_video_recent_modified_videos: "layout_video_list_recent_modified_videos",
            layout_ynvideo_view_videos: "layout_ynvideo_list_most_view_videos",
            layout_ynvideo_rating_videos: "layout_ynvideo_list_most_rating_videos",
            layout_ynvideo_comment_videos: "layout_ynvideo_list_most_comment_videos"
        },
        scrollPosition: {
            left: 0,
            top: 0
        },
        attachClickEvent: function (classnames) {
            if (DetectMobileQuick() || DetectIpad()) {
                return;
            }

            classnames.each(function (classname) {
                classname = '.' + classname;
                if (classname == '.layout_video_show_also_liked' || classname == '.layout_video_show_same_poster' || classname == '.layout_video_show_same_tags' || classname == '.layout_ynvideo_show_also_liked') {
                    $$(classname).each(function (el) {
                        en4.sitevideolightboxview.addElemntsClickEvent(el.getElements('.title'));
                    });

                } else if (classname == '.feed' || classname == '.message_view') {
                    $$(classname).each(function (el) {
                        en4.sitevideolightboxview.addElemntsClickEvent(el.getElements('.video_info'));
                    });
                } else {
                    en4.sitevideolightboxview.addElemntsClickEvent($$(classname));
                }
            });
        },
        addElemntsClickEvent: function (elements) {
            elements.each(function (el) {
                if (el.get('tag') != 'a' && el.getFirst('a')) {
                    el = el.getFirst('a');
                }
                if (el.get('tag') != 'a') {
                    el = el.getParent('a');
                }


                if (en4.sitevideolightboxview.shouldAttach(el)) {
                    if (el.retrieve('video_viewer_attached', false))
                        return;

                    el.store('video_viewer_attached', true);
                    el.removeEvents('click').addEvent('click', function (e) {
                        e.stop();
                        en4.sitevideolightboxview.open(el);

                    });
                }
            });
        },
        shouldAttach: function (element) {
            if (element) {
                return (
                        element.get('tag') == 'a' &&
                        !element.onclick &&
                        element.href &&
                        !element.href.match(/^(javascript|[#])/) &&
                        !element.hasClass('no-vidoviewer')
                        );
            }
        },
        getType: function (element) {
            var el = element.getParent('.generic_layout_container');
            var type = '';
            if (!el)
                return type;

            el.get('class').split(' ').each(function (className) {
                className = className.trim();
                if (en4.sitevideolightboxview.types[className]) {
                    type = en4.sitevideolightboxview.types[className];
                }
            });
            return type;
        },
        defaultContent: function (element) {
            var main_div = new Element('div', {
                'id': 'sitevideolightboxview_main_content',
                'class': 'photo_lightbox',
                'styles': {
                    'display': 'block'
                }
            }).inject(element);

            new Element('div', {
                'class': 'video_lightbox_black_overlay'
            }).inject(main_div);
            new Element('div', {
                'id': 'photo_lightbox_close',
                'class': 'photo_lightbox_close',
                'onclick': "en4.sitevideolightboxview.close()",
                'title': en4.core.language.translate("Press Esc to Close")
            }).inject(main_div);

            var videoContentDiv = new Element('div', {
                'id': 'white_content_default_sea_lightbox_video',
                'class': 'photo_lightbox_content_wrapper'
            });
            var photolbCont = new Element('div', {
                'class': 'photo_lightbox_cont'
            }).inject(videoContentDiv);

            var photolbContLeft = new Element('div', {
                'id': 'video_lightbox_seaocore_left',
                'class': 'photo_lightbox_left video_viewer_lightbox_left_bg',
                'styles': {
                    'right': '1px'
                }
            }).inject(photolbCont);

            new Element('img', {
                'src': en4.core.staticBaseUrl + 'application/modules/Seaocore/externals/images/icons/loader-large.gif',
                'class': 'photo_lightbox_loader'

            }).inject(photolbContLeft);

            new Element('div', {
                'class': 'lightbox_btm_bl'
            }).inject(videoContentDiv);
            videoContentDiv.inject(main_div);
            //      videoContentDiv.addEvent('click', function(event) {
            //        event.stopPropagation();
            //      });
        },
        open: function (el, duration) {

            if (typeof duration == 'undefined')
                duration = 0;

            en4.sitevideolightboxview.scrollPosition.top = window.getScrollTop();
            en4.sitevideolightboxview.scrollPosition.left = window.getScrollLeft();
            en4.sitevideolightboxview.setHtmlScroll("hidden");
            if ($('arrowchat_base'))
                $('arrowchat_base').style.display = 'none';
            if ($('wibiyaToolbar'))
                $('wibiyaToolbar').style.display = 'none';
            en4.sitevideolightboxview.defaultContent(document.body);
            if (typeof el != 'function') {
                en4.sitevideolightboxview.showVideo(el, {
                    is_ajax_lightbox: 0,
                    type: '',
                    subject_guid: en4.core.subject.guid,
                    duration: duration
                });
            } else {
                en4.sitevideolightboxview.showVideo(el.get('href'), {
                    is_ajax_lightbox: 0,
                    type: en4.sitevideolightboxview.getType(el),
                    subject_guid: en4.core.subject.guid,
                    duration: duration
                });
            }


            document.addEvents({
                'keydown': en4.sitevideolightboxview.keyDownEvent
            });
        },
        setHtmlScroll: function (cssCode) {
            $$('html').setStyle('overflow', cssCode);
        },
        keyDownEvent: function (e) {

            if (e.target.get('tag') == 'html' ||
                    e.target.get('tag') == 'body' ||
                    e.target.get('tag') == 'div' ||
                    e.target.get('tag') == 'span' ||
                    e.target.get('tag') == 'a') {
                if (en4.sitevideolightboxview.active_request == 0 && en4.sitevideolightboxview.count > 1) {
                    if (e.key == 'right') {
                        getSiteviewNextVideo();
                    } else if (e.key == 'left') {
                        getSiteviewPrevVideo();
                    }
                }
                if (e.key == 'esc') {
                    en4.sitevideolightboxview.close();
                }
            }
        },
        close: function () {
            if ($("sitevideolightboxview_main_content"))
                $("sitevideolightboxview_main_content").destroy();
            if ($('arrowchat_base'))
                $('arrowchat_base').style.display = 'block';
            if ($('wibiyaToolbar'))
                $('wibiyaToolbar').style.display = 'block';
            if (history.pushState)
                history.pushState({}, document.title, en4.sitevideolightboxview.locationHref);
            en4.sitevideolightboxview.setHtmlScroll("auto");
            window.scroll(en4.sitevideolightboxview.scrollPosition.left, en4.sitevideolightboxview.scrollPosition.top);
            en4.sitevideolightboxview.active_request = 0;
            en4.sitevideolightboxview.count = 0;
            if (typeof (keyUpLikeEventSitevideoView) != 'undefined')
                document.removeEvent("keyup", keyUpLikeEventSitevideoView);
            document.removeEvent("keydown", en4.sitevideolightboxview.keyDownEvent);
        },
        showVideo: function (href, params) {
            if (params.is_ajax_lightbox == 1) {
                $$(".lightbox_btm_bl").each(function (el) {
                    el.innerHTML = "<center><img src='" + en4.core.staticBaseUrl + "application/modules/Seaocore/externals/images/icons/loader-large.gif' style='height:30px;' /> </center>";
                });
            }

            if (history.pushState)
                history.pushState({}, document.title, href);


            if ($('ads') && $('ads').innerHTML != '')
                en4.sitevideolightboxview.ads = $('ads').innerHTML;

            if (params.type == 'page-profile') {
                if ($('sitepagevideo_videos_search_input_text'))
                    params.search_text = $('sitepagevideo_videos_search_input_text').value;

                if ($('sitepagevideo_videos_search_input_checkbox'))
                    params.my_video = $('sitepagevideo_videos_search_input_checkbox').checked;

                if ($('sitepagevideo_videos_search_input_selectbox'))
                    params.browse = $('sitepagevideo_videos_search_input_selectbox').value;
            } else if (params.type == 'business-profile') {
                if ($('sitebusinessvideo_videos_search_input_text'))
                    params.search_text = $('sitebusinessvideo_videos_search_input_text').value;

                if ($('sitebusinessvideo_videos_search_input_checkbox'))
                    params.my_video = $('sitebusinessvideo_videos_search_input_checkbox').checked;

                if ($('sitebusinessvideo_videos_search_input_selectbox'))
                    params.browse = $('sitebusinessvideo_videos_search_input_selectbox').value;
            } else if (params.type == 'group-profile') {
                if ($('sitegroupvideo_videos_search_input_text'))
                    params.search_text = $('sitegroupvideo_videos_search_input_text').value;

                if ($('sitegroupvideo_videos_search_input_checkbox'))
                    params.my_video = $('sitegroupvideo_videos_search_input_checkbox').checked;

                if ($('sitegroupvideo_videos_search_input_selectbox'))
                    params.browse = $('sitegroupvideo_videos_search_input_selectbox').value;
            } else if (params.type == 'store-profile') {
                if ($('sitestorevideo_videos_search_input_text'))
                    params.search_text = $('sitestorevideo_videos_search_input_text').value;

                if ($('sitestorevideo_videos_search_input_checkbox'))
                    params.my_video = $('sitestorevideo_videos_search_input_checkbox').checked;

                if ($('sitestorevideo_videos_search_input_selectbox'))
                    params.browse = $('sitestorevideo_videos_search_input_selectbox').value;
            }

            en4.core.request.send(new Request.HTML({
                method: 'get',
                'url': href,
                'data': $merge(params, {
                    format: 'html',
                    'lightbox_type': 'sitevideolightboxview'
                }),
                evalScripts: true,
                onSuccess: function (responseTree, responseElements, responseHTML, responseJavaScript) {

                    en4.sitevideolightboxview.active_request = 0;
                    if (!$('white_content_default_sea_lightbox_video'))
                        return;
                    $('white_content_default_sea_lightbox_video').innerHTML = responseHTML;
                    if ($('ads_hidden_siteviewvideo'))
                        en4.sitevideolightboxview.ads = $('ads_hidden_siteviewvideo').innerHTML;
                    if ($('ads')) {
                        $('ads').innerHTML = en4.sitevideolightboxview.ads;
                        if ($('ads').getCoordinates().height < 30) {

                            $('ads').empty();
                        }
                        (function () {
                            if (!$('ads'))
                                return;
                            $('ads').style.bottom = "0px";
                            if ($('photo_lightbox_right_content').getCoordinates().height < ($('photo_right_content').getCoordinates().height + $('ads').getCoordinates().height + 10)) {
                                $('ads').empty();
                                $('main_right_content_area').style.height = $('photo_lightbox_right_content').getCoordinates().height - 2 + "px";
                                $('main_right_content').style.height = $('photo_lightbox_right_content').getCoordinates().height - 2 + "px";
                            } else {
                                $('main_right_content_area').style.height = $('photo_lightbox_right_content').getCoordinates().height - ($('ads').getCoordinates().height + 10) + "px";
                                $('main_right_content').style.height = $('photo_lightbox_right_content').getCoordinates().height - ($('ads').getCoordinates().height + 10) + "px";
                            }
                        }).delay(50);
                    }
                    (function () {
                        if (!$('main_right_content_area'))
                            return;
                        rightSidePhotoContent = new SEAOMooVerticalScroll('main_right_content_area', 'main_right_content', {});
                    }).delay(50);
                    Smoothbox.bind($('white_content_default_sea_lightbox_video'));
                }
            }), {
                "force": true
            });
        },
        saveContent: function (subject_guid, column) {
            var str = document.getElementById('editor_svvideo_' + column).value.replace('/\n/g', '<br />');
            var str_temp = document.getElementById('editor_svvideo_' + column).value;
            if (column == 'title' && str_temp == '') {
                en4.core.showError('Video Title is a required field and cannot be left blank.' + '<br /><br /><button onclick="Smoothbox.close()">Close</button>');
                return;
            }
            if ($('svvideo_loading_' + column))
                $('svvideo_loading_' + column).style.display = "";

            $("edit_svvideo_" + column).style.display = 'none';
            en4.core.request.send(new Request.HTML({
                url: en4.core.baseUrl + 'sitevideo/lightbox/save-content',
                data: {
                    format: 'json',
                    text_string: str_temp,
                    column: column,
                    subject_guid: subject_guid
                },
                onSuccess: function (responseJSON) {
                    if (str == '')
                        str_temp = document.getElementById('editor_svvideo_' + column).title;

                    document.getElementById('svvideo_' + column).innerHTML = str_temp.replace(/\n/gi, "<br /> \n");
                    en4.sitevideolightboxview.switchEditMode(column, 'display');
                    var descEls = $$('.lightbox_photo_description');
                    if (descEls.length > 0) {
                        descEls[0].enableLinks();
                    }
                }
            }), {
                "force": true
            });

        },
        switchEditMode: function (column, mode) {
            if ($('svvideo_loading_' + column))
                $('svvideo_loading_' + column).style.display = "none";
            if (mode == 'edit') {
                $("edit_svvideo_" + column).style.display = 'block';
                $("link_svvideo_" + column).style.display = 'none';
            } else {
                $("edit_svvideo_" + column).style.display = 'none';
                $("link_svvideo_" + column).style.display = 'block';
            }
        },
        setFeatured: function (element, video_guid) {

            var request = new Request.JSON({
                url: en4.core.baseUrl + 'sitevideo/lightbox/set-featured',
                data: {
                    format: 'json',
                    subject_guid: video_guid
                },
                onSuccess: function (responseJSON) {
                    if ($type(responseJSON) && responseJSON.status == true) {
                        if (responseJSON.featured == 1) {
                            element.innerHTML = en4.core.language.translate('Make Un-Featured');
                        } else {
                            element.innerHTML = en4.core.language.translate('Make Featured');
                        }
                    }
                }
            });

            en4.core.request.send(request, {
                'force': true
            });

        },
        setHighlighted: function (element, video_guid) {
            var request = new Request.JSON({
                url: en4.core.baseUrl + 'sitevideo/lightbox/set-highlighted',
                data: {
                    format: 'json',
                    subject_guid: video_guid
                },
                onSuccess: function (responseJSON) {
                    if ($type(responseJSON) && responseJSON.status == true) {

                        if (responseJSON.highlighted == 1) {
                            element.innerHTML = en4.core.language.translate('Make Un-highlighted');
                        } else {
                            element.innerHTML = en4.core.language.translate('Make highlighted');
                        }
                    }
                }
            });
            en4.sitevideolightboxview.active_request = 1;
            en4.core.request.send(request, {
                'force': true
            });

        }

    },
    en4.core.runonce.add(function () {

        for (classname in en4.sitevideolightboxview.addclasstypes) {
            $$("." + classname).each(function (el) {
                el.getParent('.generic_layout_container').addClass(en4.sitevideolightboxview.addclasstypes[classname]);
            });
        }
        ;
        en4.sitevideolightboxview.attachClickEvent(en4.sitevideolightboxview.attach_event_classes);
    });

})(); // END NAMESPACE