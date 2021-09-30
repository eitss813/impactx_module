/* $Id: core.js 2011-08-26 9:40:21Z SocialEngineAddOns Copyright 2010-2011 BigStep Technologies Pvt. Ltd. $ */
en4.sitealbum = {
    rotate: function (photo_id, angle) {
        request = new Request.JSON({
            url: en4.core.baseUrl + 'sitealbum/photo/rotate',
            data: {
                format: 'json',
                photo_id: photo_id,
                angle: angle
            },
            onComplete: function (response) {
                // Check status
                if ($type(response) == 'object' &&
                        $type(response.status) &&
                        response.status == false) {
                    en4.core.showError('An error has occurred processing the request. The target may no longer exist.' + '<br /><br /><button onclick="Smoothbox.close()">Close</button>');
                    return;
                } else if ($type(response) != 'object' ||
                        !$type(response.status)) {
                    en4.core.showError('An error has occurred processing the request. The target may no longer exist.' + '<br /><br /><button onclick="Smoothbox.close()">Close</button>');
                    return;
                }
                // Ok, let's refresh the page I guess
//        $('canReloadSitealbum').value = 1;
                if($('media_photo')) {
                    $('media_photo').src = response.href;
                    $('media_photo').style.marginTop = "0px";
                } else {
                    $('media_photo_'+photo_id).src = response.href;
                    $('media_photo_'+photo_id).style.marginTop = "0px";
                }
            }
        });
        request.send();
        return request;
    },
    flip: function (photo_id, direction, url) {
        request = new Request.JSON({
            url: en4.core.baseUrl + 'sitealbum/photo/flip',
            data: {
                format: 'json',
                photo_id: photo_id,
                direction: direction
            },
            onComplete: function (response) {
                // Check status
                if ($type(response) == 'object' &&
                        $type(response.status) &&
                        response.status == false) {
                    en4.core.showError('An error has occurred processing the request. The target may no longer exist.' + '<br /><br /><button onclick="Smoothbox.close()">Close</button>');
                    return;
                } else if ($type(response) != 'object' ||
                        !$type(response.status)) {
                    en4.core.showError('An error has occurred processing the request. The target may no longer exist.' + '<br /><br /><button onclick="Smoothbox.close()">Close</button>');
                    return;
                }

                // Ok, let's refresh the page I guess     
                //window.location.reload(true);
//        $('canReloadSitealbum').value = 1;
                $('media_photo').src = response.href;
                $('media_photo').style.marginTop = "0px";
            }
        });
        request.send();
        return request;
    }
};
/**
 * likes
 */
en4.sitealbum.likes = {
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

//var getSitealbumPhoto= function(url,isajax){
//  var photoUrl= url.replace("/sitealbums/", "/albums/");
//  photoUrl= photoUrl.replace("/light-box-view/album_id/", "/view/album_id/");
//  if (history.replaceState) {
//    history.replaceState( {}, document.title, photoUrl );
//  } else {
//    window.location.hash = photoUrl;
//  }
//  $$(".lightbox_btm_bl").each(function(el){ 
//    if(isajax)
//      el.innerHTML="<center><img src='"+en4.core.staticBaseUrl+"application/modules/Seaocore/externals/images/icons/loader-large.gif' style='height:30px;' /> </center>";
//  });
//    
//  var remove_extra = 0;
//  contentPhotoSize['height'] = $("photo_lightbox_left").getCoordinates().height - remove_extra;
//  if(isajax == 0 )
//    remove_extra = remove_extra + 289;
//  contentPhotoSize['width'] = $("photo_lightbox_left").getCoordinates().width - remove_extra;
//
//  addAgainscrollFalg = true;
//  en4.core.request.send(new Request.HTML({
//    url : url,
//    data : {
//      format : 'html',
//      isajax : isajax
//    },
//    onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) { 
//      if($('white_content_default_album')){
//        $('white_content_default_album').innerHTML = responseHTML;        
//        switchFullModePhoto(fullmode_photo); 
//      }      
//    }
//  }), true);
//}

/**
 * @description dropdown Navigation
 * @param {String} id id of ul element with navigation lists
 * @param {Object} settings object with settings
 */


var NavigationSitealbum = function () {
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

var tab_content_id_sitestore = 0;
en4.sitealbum.ajaxTab = {
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
                if (en4.sitealbum.ajaxTab.click_elment_id == widget_id)
                    return;
                en4.sitealbum.ajaxTab.click_elment_id = widget_id;
                en4.sitealbum.ajaxTab.sendReq(params);
            });
            element.store('addClickEvent', true);
            var attachOnLoadEvent = false;
            if (tab_content_id_sitestore == widget_id) {
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
                                if (addActiveTab || tab_content_id_sitestore == widget_id) {
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
            en4.sitealbum.ajaxTab.click_elment_id = widget_id;
            en4.sitealbum.ajaxTab.sendReq(params);
        });


    },
    sendReq: function (params) {
        params.responseContainer.each(function (element) {
            if ((typeof params.loading) == 'undefined' || params.loading == true) {
                element.empty();
                new Element('div', {
                    'class': 'sitealbum_profile_loading_image'
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
                    Smoothbox.bind(container);
                    
                     if(SmoothboxSEAO)
                     SmoothboxSEAO.bind( container);
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



function showJustifiedView(id,rowHeight,maxRowHeight,margin,lastRow)
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


function openAlbumViewPage(href) {
    window.location.href= href;
}