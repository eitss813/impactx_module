/* $Id: core.js 2011-08-26 9:40:21Z SocialEngineAddOns Copyright 2010-2011 BigStep Technologies Pvt. Ltd. $ */
en4.sitevideo = {
};
/*
 * 
 * Watch later
 */

en4.sitevideo.watchlaters = {
    add: function (video_id)
    {
        (new Request.JSON({
            'format': 'json',
            'url': en4.core.baseUrl + 'sitevideo/watchlater/add-to-watchlater',
            'data': {
                'format': 'json',
                'video_id': video_id,
            },
            'onSuccess': function (responseJSON, responseText)
            {
                $$('a.removewatchlater_' + video_id).each(function (el) {
                    el.style.display = 'inline-block';
                });
                $$('a.addwatchlater_' + video_id).each(function (el) {
                    el.style.display = 'none';
                });
            }
        })).send();
    },
    remove: function (video_id)
    {
        (new Request.JSON({
            'format': 'json',
            'url': en4.core.baseUrl + 'sitevideo/watchlater/remove-from-watchlater-json',
            'data': {
                'format': 'json',
                'video_id': video_id,
            },
            'onSuccess': function (responseJSON, responseText)
            {
                $$('a.removewatchlater_' + video_id).each(function (el) {
                    el.style.display = 'none';
                });
                $$('a.addwatchlater_' + video_id).each(function (el) {
                    el.style.display = 'inline-block';
                });
            }
        })).send();
    }
}
/*
 * Subscribe channels
 */
en4.sitevideo.subscriptions = {
    subscribe: function (channel_id)
    {
        (new Request.JSON({
            'format': 'json',
            'url': en4.core.baseUrl + 'sitevideo/subscription/subscribe-channel',
            'data': {
                'format': 'json',
                'channel_id': channel_id,
            },
            'onSuccess': function (responseJSON, responseText)
            {
                $$('a.unsubscription_' + channel_id).each(function (el) {
                    el.style.display = 'inline-block';
                });
                $$('a.subscription_' + channel_id).each(function (el) {
                    el.style.display = 'none';
                });
            }
        })).send();
    },
    unsubscribe: function (channel_id)
    {
        (new Request.JSON({
            'format': 'json',
            'url': en4.core.baseUrl + 'sitevideo/subscription/unsubscribe-channel',
            'data': {
                'format': 'json',
                'channel_id': channel_id,
            },
            'onSuccess': function (responseJSON, responseText)
            {
                $$('a.unsubscription_' + channel_id).each(function (el) {
                    el.style.display = 'none';
                });
                $$('a.subscription_' + channel_id).each(function (el) {
                    el.style.display = 'inline-block';
                });
            }
        })).send();
    }

}
/*
 * 
 * ratings
 */
en4.sitevideo.ratings = {
    setRating: function (subject_pre_rate, resource_id)
    {
        var subject_rating = subject_pre_rate;
        for (var x = 1; x <= parseInt(subject_rating); x++) {
            $('rate_' + resource_id + '_' + x).set('class', 'seao_rating_star_generic rating_star_y');
        }

        for (var x = parseInt(subject_rating) + 1; x <= 5; x++) {
            $('rate_' + resource_id + '_' + x).set('class', 'seao_rating_star_generic seao_rating_star_disabled');
        }

        var remainder = Math.round(subject_rating) - subject_rating;
        if (remainder <= 0.5 && remainder != 0) {
            var last = parseInt(subject_rating) + 1;
            $('rate_' + resource_id + '_' + last).set('class', 'seao_rating_star_generic rating_star_half_y');
        }
    }
}

/**
 * likes
 */
en4.sitevideo.likes = {
    like: function (type, id, comment_id) {
        en4.core.request.send(new Request.JSON({
            url: en4.core.baseUrl + 'core/comment/like',
            data: {
                format: 'json',
                type: type,
                id: id,
                comment_id: 0
            },
            onSuccess: function (responseJSON) {
                if ($type(responseJSON) == 'object' && $type(responseJSON.status)) {
                    if ($(type + 'like_link'))
                        $(type + 'like_link').style.display = "none";
                    if ($(type + 'unlike_link'))
                        $(type + 'unlike_link').style.display = "inline-block";
                }
            }
        }), {
            'element': $('comments')
        }, true);
    },
    unlike: function (type, id, comment_id) {
        en4.core.request.send(new Request.JSON({
            url: en4.core.baseUrl + 'core/comment/unlike',
            data: {
                format: 'json',
                type: type,
                id: id,
                comment_id: comment_id
            },
            onSuccess: function (responseJSON) {
                if ($type(responseJSON) == 'object' && $type(responseJSON.status)) {
                    if ($(type + 'unlike_link'))
                        $(type + 'unlike_link').style.display = "none";
                    if ($(type + 'like_link'))
                        $(type + 'like_link').style.display = "inline-block";
                }
            }
        }), {
            'element': $('comments')
        }, true);
    }


};

/**
 * @description dropdown Navigation
 * @param {String} id id of ul element with navigation lists
 * @param {Object} settings object with settings
 */


var NavigationSitevideo = function () {
    var main = {
        obj_nav: $(arguments[0]) || $("nav"),
        settings: {
            show_delay: 0,
            hide_delay: 0,
            _ie6: /MSIE 6.+Win/.test(navigator.userAgent),
            _ie7: /MSIE 7.+Win/.test(navigator.userAgent)
        },
        init: function (obj, level) {
            obj.lists = obj.getChildren();
            obj.lists.each(function (el, ind) {
                main.handlNavElement(el);
                if ((main.settings._ie6 || main.settings._ie7) && level) {
                    main.ieFixZIndex(el, ind, obj.lists.size());
                }
            });
            if (main.settings._ie6 && !level) {
                document.execCommand("BackgroundImageCache", false, true);
            }
        },
        handlNavElement: function (list) {
            if (list !== undefined) {
                list.onmouseover = function () {
                    main.fireNavEvent(this, true);
                };
                list.onmouseout = function () {
                    main.fireNavEvent(this, false);
                };
                if (list.getElement("ul")) {
                    main.init(list.getElement("ul"), true);
                }
            }
        },
        ieFixZIndex: function (el, i, l) {
            if (el.tagName.toString().toLowerCase().indexOf("iframe") == -1) {
                el.style.zIndex = l - i;
            } else {
                el.onmouseover = "null";
                el.onmouseout = "null";
            }
        },
        fireNavEvent: function (elm, ev) {

            if (ev) {
                elm.addClass("over");
                elm.getElement("a").addClass("over");
                if (elm.getChildren()[1]) {
                    main.show(elm.getChildren()[1]);
                }
            } else {
                elm.removeClass("over");
                elm.getElement("a").removeClass("over");
                if (elm.getChildren()[1]) {
                    main.hide(elm.getChildren()[1]);
                }
            }
        },
        show: function (sub_elm) {
            if (sub_elm.hide_time_id) {
                clearTimeout(sub_elm.hide_time_id);
            }
            sub_elm.show_time_id = setTimeout(function () {
                if (!sub_elm.hasClass("shown-sublist")) {
                    sub_elm.addClass("shown-sublist");
                }
            }, main.settings.show_delay);
        },
        hide: function (sub_elm) {
            if (sub_elm.show_time_id) {
                clearTimeout(sub_elm.show_time_id);
            }
            sub_elm.hide_time_id = setTimeout(function () {
                if (sub_elm.hasClass("shown-sublist")) {
                    sub_elm.removeClass("shown-sublist");
                }
            }, main.settings.hide_delay);
        }
    };
    if (arguments[1]) {
        main.settings = Object.extend(main.settings, arguments[1]);
    }
    if (main.obj_nav) {
        main.init(main.obj_nav, false);
    }
};

var tab_content_id_sitevideo = 0;
en4.sitevideo.ajaxTab = {
    click_elment_id: '',
    attachEvent: function (widget_id, params) {
        params.requestParams.content_id = widget_id;
        var element;

        $$('.tab_' + widget_id).each(function (el) {
            if (el.get('tag') == 'li') {
                element = el;
                return;
            }
        });
        var onloadAdd = true;
        if (element) {
            if (element.retrieve('addClickEvent', false))
                return;
            element.addEvent('click', function () {
                if (en4.sitevideo.ajaxTab.click_elment_id == widget_id)
                    return;
                en4.sitevideo.ajaxTab.click_elment_id = widget_id;
                en4.sitevideo.ajaxTab.sendReq(params);
            });
            element.store('addClickEvent', true);
            var attachOnLoadEvent = false;
            if (tab_content_id_sitevideo == widget_id) {
                attachOnLoadEvent = true;
            } else {
                $$('.tabs_parent').each(function (element) {
                    var addActiveTab = true;
                    element.getElements('ul > li').each(function (el) {
                        if (el.hasClass('active')) {
                            addActiveTab = false;
                            return;
                        }
                    });
                    element.getElementById('main_tabs').getElements('li:first-child').each(function (el) {
                        if (el.getParent('div') && el.getParent('div').hasClass('tab_pulldown_contents'))
                            return;
                        el.get('class').split(' ').each(function (className) {
                            className = className.trim();
                            if (className.match(/^tab_[0-9]+$/) && className == "tab_" + widget_id) {
                                attachOnLoadEvent = true;
                                if (addActiveTab || tab_content_id_sitevideo == widget_id) {
                                    element.getElementById('main_tabs').getElements('ul > li').removeClass('active');
                                    el.addClass('active');
                                    element.getParent().getChildren('div.' + className).setStyle('display', null);
                                }
                                return;
                            }
                        });
                    });
                });
            }
            if (!attachOnLoadEvent)
                return;
            onloadAdd = false;

        }

        en4.core.runonce.add(function () {
            if (onloadAdd)
                params.requestParams.onloadAdd = true;
            en4.sitevideo.ajaxTab.click_elment_id = widget_id;
            en4.sitevideo.ajaxTab.sendReq(params);
        });


    },
    sendReq: function (params) {
        params.responseContainer.each(function (element) {
            if ((typeof params.loading) == 'undefined' || params.loading == true) {
                element.empty();
                new Element('div', {
                    'class': 'sitevideo_profile_loading_image'
                }).inject(element);
            }
        });
        var url = en4.core.baseUrl + 'widget';

        if (params.requestUrl)
            url = params.requestUrl;

        var request = new Request.HTML({
            url: url,
            data: $merge(params.requestParams, {
                format: 'html',
                subject: en4.core.subject.guid,
                is_ajax_load: true
            }),
            evalScripts: true,
            onSuccess: function (responseTree, responseElements, responseHTML, responseJavaScript) {
                params.responseContainer.each(function (container) {
                    container.empty();
                    Elements.from(responseHTML).inject(container);
                    en4.core.runonce.trigger();
                    en4.sitevideolightboxview.attachClickEvent(Array('sitevideo_thumb_viewer'));
                    Smoothbox.bind(container);
                    if (params.requestParams.hasOwnProperty('justifiedViewId') && params.requestParams.showPhotosInJustifiedView == 1) {
                        showJustifiedView
                                (
                                        params.requestParams.justifiedViewId,
                                        params.requestParams.rowHeight,
                                        params.requestParams.maxRowHeight,
                                        params.requestParams.margin,
                                        params.requestParams.lastRow
                                        );
                    }
                });

            }
        });
        request.send();
    }
};

function showJustifiedView(id, rowHeight, maxRowHeight, margin, lastRow)
{
    if ('undefined' != typeof window.jQuery) {
        var justifiedObj = jQuery("#" + id);
        if (justifiedObj.length > 0)
            justifiedObj.justifiedGallery(
                    {
                        rowHeight: rowHeight,
                        maxRowHeight: maxRowHeight,
                        margins: margin,
                        lastRow: lastRow
                    }
            ).on('jg.complete', function (e) {
                //Write complete trigger
            });
    }
}

en4.sitevideo.youtubeChannel = {
    searchUrl: 'https://www.googleapis.com/youtube/v3/search',
    queryString: '',
    api_key: '',
    type: "video",
    maxResults: 24,
    pageToken: "",
    channelId: "",
    content: "",
    selectedVideos: [],
    videoId: "",
    keyword: "",
    checkChannelUrl: "",
    isUpdate: false,
    id: "",
    videos: [],
    currentPage: 0,
    pageAllVideoSeleced : [],
    responseContainer: function () {
        if (this.type == 'channel')
            return $("channel_list");
        else
            return $("channel_videos");

    },
    logError: function (message) {
        if ($('youtube_error')) {
            $('youtube_error').destroy();
        }
        var ul = new Element('ul', {
            'class': 'form-errors',
            'id': 'youtube_error'
        });
        var li = new Element('li', {
            'html': message
        });
        li.inject(ul, 'bottom');

        ul.inject($('form-channel-upload').getChildren()[0].getChildren()[0].getElement('h3'), 'after');
    },
    buildQueryString: function () {
        this.queryString = '';
        args = {};
        args.part = 'snippet';
        args.type = this.type;
        args.order = 'date';
        if (this.type == 'video') {
            args.channelId = this.channelId;
        } else {
            args.q = this.keyword;
        }
        args.maxResults = this.maxResults
        if (this.pageToken.length > 0) {
            args.pageToken = this.pageToken;
        }
        args.key = this.api_key;
        this.queryString = Object.toQueryString(args);
    },
    buildSearchUrl: function () {
        this.buildQueryString();
        //console.log(this.searchUrl + "?" + this.queryString);
        return this.searchUrl + "?" + this.queryString;
    },
    getChannelId: function ($channelUrl) {
        var myURI = new URI($channelUrl);
        this.channelId = myURI.get('file');
    },
    searchVideo: function () {
        if ($('youtube_error')) {
            $('youtube_error').destroy();
        }
        if (this.api_key == '') {
            this.logError('Please configure your Youtube API Key.');
            return;
        }
        url = $('youtube_channel_url').value;
        if (url.trim() == '') {
            this.logError('Please enter the url of Youtube Channel.');
            return;
        }
        this.type = "video";
        this.keyword = "";
        this.selectedVideos = [];
        this.setSelectedVideo();
        this.getChannelId(url);
        obj = this;
        new Request.JSON({
            url: this.checkChannelUrl,
            method: 'get',
            data: {'youtube_channel_id': this.channelId, 'id': this.id},
            evalScripts: true,
            onSuccess: function (responseTree, responseElements, responseHTML, responseJavaScript) {
                if (responseElements == 'true') {
                    obj.logError("You have already used this Youtube channel. One Youtube channel can be associated with only one of your channels.")
                    $('youtube_channel_url').value = '';
                    obj.channelId = "";
                    obj.setSelectedVideo();
                    obj.emptyResponseContainer();
                    nextButton = $('next_button');
                    prevButton = $('prev_button');
                    nextButton.hide();
                    prevButton.hide();
                } else {
                    obj.search();
                }
            }
        }
        ).send();
    },
    searchChannel: function () {
        if ($('youtube_error')) {
            $('youtube_error').destroy();
        }
        if (this.api_key == '') {
            this.logError('Please configure your Youtube API Key.');
            return;
        }
        this.keyword = $('youtube_channel_keyword').value;
        this.channelId = "";
        this.type = "channel";
        this.selectedVideos = [];
        this.setSelectedVideo();
        this.search();
    },
    search: function () {
        this.setSelectedVideo();
        obj = this;
        var request = new Request.JSON({
            url: this.buildSearchUrl(),
            method: 'get',
            data: {},
            evalScripts: true,
            'onRequest': function () {
                obj.emptyResponseContainer();
                $('chk-select-div').hide();
                $('loding_image').show();
            },
            onSuccess: function (responseTree, responseElements, responseHTML, responseJavaScript) {
                $('loding_image').hide();
                obj.content = JSON.decode(responseElements);
                if (obj.content.items.length == 0) {
                    obj.logError('No videos found in the selected Youtube Channel.');
                } else {
                    obj.emptyResponseContainer();
                    obj.buildView();
                    obj.buildNextPrevButtons();
                    if (obj.type == 'channel') {
                        Smoothbox.close();
                        content = $('channel_list_div').show().innerHTML;
                        $('channel_list_div').hide();
                        $('channel_list').innerHTML = '';
                        Smoothbox.open(content, {width: "580"});

                    } else {
                        //console.log(obj.content.items.length);
                        if (obj.content.items.length > 1) {
                            $('chk-select-div').show();
                        }
                        window.scrollTo(0, document.body.scrollHeight);
                    }
                }
            },
            onFailure: function (text) {
                $('loding_image').hide();
                object = JSON.decode(text.responseText);
                obj.content = "";
                obj.logError(object.error.message);
            }
        }
        );
        request.send();
    },
    buildView: function () {
        items = this.content;
        obj = this;
        Object.each(items.items, function (item, key) {
            obj.buildItem(item);
        });
    },
    buildNextPrevButtons: function () {
        items = this.content;
        // console.log(items);
        if (this.type == 'channel') {
            nextButton = $('channel_next_button');
            prevButton = $('channel_prev_button');
        } else {
            nextButton = $('next_button');
            prevButton = $('prev_button');
        }
        nextButton.hide();
        prevButton.hide();
        if ('nextPageToken' in items)
            nextButton.show();

        if ('prevPageToken' in items)
            prevButton.show();

    },
    buildItem: function (item) {

        if (this.type == 'channel') {
            sid = 'c_' + item.id.channelId;
            id = item.id.channelId;
            subjectLink = 'https://www.youtube.com/channel/' + id;
        } else {
            sid = 'v_' + item.id.videoId;
            id = item.id.videoId;
            subjectLink = 'https://youtu.be/' + id;
        }

        container = this.responseContainer();
        if ('high' in item.snippet.thumbnails) {
            thumbnailUrl = item.snippet.thumbnails.high.url;
        } else if ('medium' in item.snippet.thumbnails) {
            thumbnailUrl = item.snippet.thumbnails.medium.url;
        } else {
            thumbnailUrl = item.snippet.thumbnails.default.url;
        }
        title = (item.snippet.title).truncate(20);
        fullTitle = item.snippet.title;
        // description = item.snippet.description;
        var li = new Element('li', {
            'id': sid,
        });
        var img = new Element('img', {
            src: thumbnailUrl,
            title: fullTitle,
        });
        var title = new Element('a', {
            class: 'content_title',
            html: title,
            title: fullTitle,
            href: subjectLink,
            target: '_blank',
            style: 'text-decoration: none;'
        });
//        var desc = new Element('div', {
//            class: 'content_desc',
//            html: description,
//        });
        var imgDiv = new Element('div', {
            class: "imgsec"
        });
        img.inject(imgDiv, 'bottom')
        imgDiv.inject(li, 'bottom');
        if (this.type == 'video') {

            var pPlusButton = new Element('p', {
                class: 'seaocore_plus_icon',
                title: 'Add Video'
            });
            var pMinusButton = new Element('p', {
                class: 'seaocore_icon_minus',
                title: 'Remove Video'
            });
            if (this.selectedVideos.contains(id)) {

                var addButton = new Element('span', {
                    class: 'video_length add_video',
                    id: 'add_video_' + id,
                    style: 'display:none;',
                    "onclick": "en4.sitevideo.youtubeChannel.addVideo('" + id + "')",
                });

                var removeButton = new Element('span', {
                    class: 'video_length remove_video',
                    id: 'remove_video_' + id,
                    "onclick": "en4.sitevideo.youtubeChannel.removeVideo('" + id + "')",
                });
                pPlusButton.inject(addButton);
                pMinusButton.inject(removeButton);
                addButton.inject(li, 'bottom');
                removeButton.inject(li, 'bottom');
            } else {
                if (!this.videos.contains(id)) {
                    var addButton = new Element('span', {
                        class: 'video_length',
                        id: 'add_video_' + id,
                        "onclick": "en4.sitevideo.youtubeChannel.addVideo('" + id + "')",
                    });
                    var removeButton = new Element('span', {
                        class: 'video_length',
                        id: 'remove_video_' + id,
                        style: 'display:none;',
                        "onclick": "en4.sitevideo.youtubeChannel.removeVideo('" + id + "')",
                    });
                    pPlusButton.inject(addButton);
                    pMinusButton.inject(removeButton);
                    addButton.inject(li, 'bottom');
                    removeButton.inject(li, 'bottom');
                } else {
                    img.addClass("selected_image");
                }

            }
        } else {
            var selectButton = new Element('span', {
                //class: 'video_length',
                html: 'select',
                id: 'select_channel_' + id,
                "onclick": "en4.sitevideo.youtubeChannel.selectChannel('" + id + "')",
            });
            selectButton.inject(li, 'bottom');
        }
        ttlDiv = new Element('div', {
            class: "info_contant"
        });
        title.inject(ttlDiv, 'bottom');
        //desc.inject(ttlDiv, 'bottom');
        ttlDiv.inject(li, 'bottom');
        li.inject(container, 'bottom');
    },
    selectChannel: function (channelId) {
        url = "https://www.youtube.com/channel/" + channelId;
        $('youtube_channel_url').value = url;
        $('youtube_channel_keyword').value = '';
        this.selectedVideos = [];
        this.setSelectedVideo();
        Smoothbox.close();
        items = this.content.items;
        if ($('title')) {
            Object.each(items, function (item, key) {
                if (item.snippet.channelId == channelId) {
                    title = $('title');
                    description = $('description');
                    if (title) {
                        title.value = item.snippet.title;
                    }
                    if (description) {
                        description.value = item.snippet.description;
                    }
                }
            });
        }
        this.searchVideo();

    },
    addVideo: function (videoId) {
        if (!this.selectedVideos.contains(videoId)) {
            this.selectedVideos[this.selectedVideos.length] = videoId;
            $('add_video_' + videoId).hide();
            $('remove_video_' + videoId).show();
            this.setSelectedVideo();
        }
    },
    removeVideo: function (videoId) {
        if (this.selectedVideos.contains(videoId)) {
            this.selectedVideos.erase(videoId);
            $('add_video_' + videoId).show();
            $('remove_video_' + videoId).hide();
            this.setSelectedVideo();
        }
    },
    addAllVideo: function () {
        items = en4.sitevideo.youtubeChannel.content.items;
        Object.each(items, function (item, key) {
            id = item.id.videoId;
            en4.sitevideo.youtubeChannel.addVideo(id);
        });
        this.pageAllVideoSeleced[this.currentPage] = 1;
    },
    removeAllVideo: function () {
        items = en4.sitevideo.youtubeChannel.content.items;
        Object.each(items, function (item, key) {
            id = item.id.videoId;
            en4.sitevideo.youtubeChannel.removeVideo(id);
        });
        this.pageAllVideoSeleced[this.currentPage] = 0;
    },
    nextPage: function () {
        $('chk-select').checked = false;
        this.pageToken = this.content.nextPageToken;
        if (this.type == 'video') {
            this.currentPage++;
            if(typeof this.pageAllVideoSeleced[this.currentPage] === 'undefined'){
                this.pageAllVideoSeleced[this.currentPage] = 0
            }else{
                $('chk-select').checked = (this.pageAllVideoSeleced[this.currentPage]==1)?true:false;
            }
        }
        this.search();

    },
    previousPage: function () {
        $('chk-select').checked = false;
        this.pageToken = this.content.prevPageToken;
        if (this.type == 'video') {
            this.currentPage--;
            if(typeof this.pageAllVideoSeleced[this.currentPage] === 'undefined'){
                this.pageAllVideoSeleced[this.currentPage] = 0
            }else{
                $('chk-select').checked = (this.pageAllVideoSeleced[this.currentPage]==1)?true:false;
            }
        }
        this.search();

    },
    emptyResponseContainer: function () {
        this.responseContainer().empty();
    },
    setSelectedVideo: function () {
        $('youtube_channel_id').value = this.channelId;
        if (this.selectedVideos.length > 0)
            $('pending_video').value = this.selectedVideos.join();
        else
            $('pending_video').value = '';
    },
    selectAll: function (item) {
        if (item.checked) {
            this.addAllVideo();
        } else {
            this.removeAllVideo();
        }
    }
}