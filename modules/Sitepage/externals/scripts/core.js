/* $Id: core.js 2011-05-05 9:40:21Z SocialEngineAddOns Copyright 2010-2011 BigStep Technologies Pvt. Ltd. $ */
var tab_content_id_sitepage = 0;
var prev_tab_id = 0;
var ads_display = '';
var changeurl = 0;
var content_integration_tab_id = '';
var manageinfo = function (manage_id, owner_id, url, page_id)
{
    var total_div = $('count_div').value;
    if (total_div >= 1) {
        total_div = total_div - 1;
    }
    var parentnode = $(manage_id + '_page_main').parentNode;
    var childnode = $(manage_id + '_page_main');

    en4.core.request.send(new Request.JSON({
        url: url,
        data: {
            format: 'json',
            managedelete_id: manage_id,
            owner_id: owner_id,
            page_id: page_id
        },
        onSuccess: function (responseJSON) {
            parentnode.removeChild(childnode);
            if (owner_id == viewer_id)
            {
                window.location = url;
            }
            if (total_div == 0) {
                //$(manage_id+ '_page').innerHTML = '';
                //parentnode.removeChild(childnode);

            } else {
                // $(manage_id+ '_page').innerHTML = '';
                $('count_div').value = total_div;
            }
        }
    }))
};
//var execute_Request = 1;
var ShowContent = function (tabid, execute_Request, temptabid, type, modulename, widgetname,
        page_showtitle, page_url, ads_display, page_communityad_integration, adwithoutpackage, itemCount,
        itemCount_photo, resourcetype, albumorder, changeurl, title_truncation, show_posted_date) {

    //EXECUTE THIS BELOW LINE IF YOU HAVE TO HIDE PAGE LEFT DIV.
    if (typeof page_url == 'undefined' || page_url == null) {
        var page_url = '';
    }

    if ((page_communityads == 1 && ads_display != 0 && page_communityad_integration == 1 && adwithoutpackage == 1) || execute_Request == false)
        hideLeftContainer(ads_display, page_communityad_integration, adwithoutpackage);

    $('id_' + tabid).innerHTML = '<div class="seaocore_content_loader"></div>';
    var url = en4.core.baseUrl + 'widget/index/mod/' + modulename + '/name/' + widgetname;


    if (typeof changeurl != 'undefined' && changeurl == 1) {
        var integratedUrl = en4.core.baseUrl + page_url + '/resource_type/' + resourcetype + '/tab/' + tabid;
        if (history.pushState)
            history.pushState({}, document.title, integratedUrl);
        else {
            window.location.hash = integratedUrl;
        }
    }
    scrollToTopForPage($('id_' + tabid));
    var callBackReturn = function () {
        if (page_showtitle == 1) {
            if ($('layout_' + type)) {
                $('layout_' + type).style.display = 'block';
            }
        }
        en4.core.runonce.trigger();
        if (typeof type != 'undefined' && type == 'document' && window.addCarousal) {
            addCarousal();
        }

        if (window.InitiateAction) {
            InitiateAction();
        }
    };
    if (modulename == 'sitepagemember') {
        en4.sitepagemember.profileTabRequest({
            content_id: tabid,
            callBackFunction: callBackReturn
        });
    } else if (modulename == 'siteevent') {
        en4.siteeventcontenttype.profileTabRequest({
            content_id: tabid,
            callBackFunction: callBackReturn
        });
    } else {
        temp2 = new Request.HTML({
            method: 'get',
            'url': url,
            'data': {
                'page_url': page_url,
                'format': 'html',
                'subject': en4.core.subject.guid,
                'isajax': '1',
                'identity_temp': temptabid,
                'itemCount': itemCount,
                'itemCount_photo': itemCount_photo,
                'resource_type': resourcetype,
                'albumsorder': albumorder,
                'title_truncation': title_truncation,
                'show_posted_date': show_posted_date
            },
            onSuccess: function (responseTree, responseElements, responseHTML, responseJavaScript) {
                $('id_' + tabid).innerHTML = responseHTML;


                if (modulename == 'sitepagevideo') {
                    showsearchvideocontent();
                    if (en4.sitevideoview)
                        en4.sitevideoview.attachClickEvent(Array('item_photo_sitepagevideo_video', 'sitepagevideo_profile_title'));
                }

                else if (modulename == 'sitepagedocument') {
                    showsearchdocumentcontent();
                }
                else if (modulename == 'sitecrowdfunding') {
                    showsearchcrowdfundingcontent();
                }
                else if (modulename == 'sitemusic') {
                    showsearchsitemusiccontent();
                }
                else if (modulename == 'sitepagenote') {
                    showsearchnotecontent();
                }

                else if (modulename == 'sitepagepoll') {
                    showsearchpollcontent();
                }

                else if (modulename == 'sitepagemember') {
                    showsearchmembercontent(tabid);
                }

                else if (modulename == 'sitepageevent') {
                    showsearcheventcontent();
                }
                else if (modulename == 'sitepageintegration') {
                    showsearchnotecontent();
                }
                else if (modulename == 'sitepagemusic') {
                    if (en4.sitepagemusic)
                        en4.sitepagemusic.player.enablePlayers();
                    if (window.showlink) {
                        showlink();
                    }
                }

                callBackReturn();
                //        if(page_showtitle == 1) {
                //          if($('layout_' + type)) {
                //            $('layout_'+ type).style.display = 'block'; 
                //          }
                //        }
                //        if(typeof type != 'undefined' && type == 'document' && window.addCarousal) {
                //          addCarousal();      	
                //        }
                //
                //        if (window.InitiateAction) {
                //          InitiateAction ();
                //        }     
            }
        });
        temp2.send();
    }
}

//EXECUTING CODE WHEN DOM IS READY IF NECESSARY

var hideLeftContainer = function (ads_display, page_communityad_integration, adwithoutpackage) {
    if (page_communityads == 1 && ads_display != 0 && page_communityad_integration != 0 && adwithoutpackage == 1) {

        if (typeof page_hide_left_container != 'undefined' && page_hide_left_container == 0)
            return;

        //EXECUTE THIS BELOW LINE IF YOU HAVE TO HIDE PAGE LEFT DIV.
        var globalContentElement = en4.seaocore.getDomElements('content');
        var leftcontainer = $(globalContentElement).getElement('.layout_left');
        var rightcontainer = $(globalContentElement).getElement('.layout_right');
        if (leftcontainer) {
            if (leftcontainer.style.display != 'none') {
                leftcontainer.setStyle('display', 'none');
                if ($('thumb_icon')) {
                    $('thumb_icon').style.display = 'block';
                }
            }
            else {
                return;
            }
        }
        if (rightcontainer) {
            if (rightcontainer.style.display != 'none') {
                rightcontainer.setStyle('display', 'none');
                if ($('thumb_icon')) {
                    $('thumb_icon').style.display = 'block';
                }

            }
            else {
                return;
            }
        }
        if ($(globalContentElement).getElement('.layout_sitepage_page_cover_information_sitepage')) {
            $(globalContentElement).getElement('.layout_sitepage_page_cover_information_sitepage').style.display = 'none';
        }

        if ($(globalContentElement).getElement('.layout_sitecontentcoverphoto_content_cover_photo')) {
            $(globalContentElement).getElement('.layout_sitecontentcoverphoto_content_cover_photo').style.display = 'block';
        }
        if ($(globalContentElement).getElement('.layout_sitepagemember_pagecover_photo_sitepagemembers')) {
            $(globalContentElement).getElement('.layout_sitepagemember_pagecover_photo_sitepagemembers').style.display = 'none';
        }
    }
}

var ShowUrlColumn = function (page_id) {
    var e4 = $('page_url_msg-wrapper');
    if ($('page_url_msg-wrapper')) {
        $('page_url_msg-wrapper').setStyle('display', 'none');
    }
    if ($('page_url-element')) {
        var pageurlcontainer = $('page_url-element');
        var language = en4.core.language.translate('Check Availability');
        var newdiv = document.createElement('div');
        newdiv.id = 'url_varify';
        newdiv.innerHTML = '<a href="javascript:void(0);"  name="check_availability" id="check_availability" onclick="PageUrlBlur(\'' + page_id + '\');return false;" class="check_availability_button">' + language + '</a> <br />';

        pageurlcontainer.insertBefore(newdiv, pageurlcontainer.childNodes[2]);
        checkDraft();
    }
}


function checkDraft() {
    if ($('draft')) {
        if ($('draft').value == 0) {
            $("search-wrapper").style.display = "none";
            $("search").checked = false;
        } else {
            $("search-wrapper").style.display = "block";
            $("search").checked = true;
        }
    }
}


function PageUrlBlur(page_id) {
    if ($('page_url_alert') == null) {
        var pageurlcontainer = $('page_url-element');
        var newdiv = document.createElement('span');
        newdiv.id = 'page_url_alert';
        newdiv.innerHTML = '<img src="' + en4.core.staticBaseUrl + 'application/modules/Sitepage/externals/images/loading.gif" />';
        pageurlcontainer.insertBefore(newdiv, pageurlcontainer.childNodes[3]);
    }
    else {
        $('page_url_alert').innerHTML = '<img src="' + en4.core.staticBaseUrl + 'application/modules/Sitepage/externals/images/loading.gif" />';
    }

    //var url = '<?php echo $this->url(array('action' => 'pageurlvalidation' ), 'sitepage_general', true);?>';
    en4.core.request.send(new Request.JSON({
        url: en4.core.baseUrl + 'sitepage/index/pageurlvalidation',
        method: 'get',
        data: {
            page_url: $('page_url').value,
            check_url: 1,
            page_id: page_id,
            format: 'html'
        },
        onSuccess: function (responseJSON) {
            //$('page_url_msg-wrapper').setStyle('display', 'block');
            if (responseJSON.success == 0) {
                $('page_url_alert').innerHTML = responseJSON.error_msg;
                if ($('page_url_alert')) {
                    $('page_url_alert').innerHTML = responseJSON.error_msg;
                }
            }
            else {
                $('page_url_alert').innerHTML = responseJSON.success_msg;
                if ($('page_url_alert')) {
                    $('page_url_alert').innerHTML = responseJSON.success_msg;
                }
            }
        }
    }));
}

var ShowDashboardPageContent = function (PageUrl, PageId, show_url, edit_url, page_id) {

    $('show_tab_content').innerHTML = '<center><img src="' + en4.core.staticBaseUrl + 'application/modules/Sitepage/externals/images/spinner_temp.gif" /></center>';
    var request = new Request.HTML({
        'url': PageUrl,
        'method': 'get',
        'data': {
            'format': 'html',
            'is_ajax': 1

        },
        onSuccess: function (responseTree, responseElements, responseHTML, responseJavaScript) {
            if (Show_Tab_Selected) {
                $('id_' + Show_Tab_Selected).set('class', '');
                Show_Tab_Selected = PageId;
            }
            $('id_' + PageId).set('class', 'selected');

            $('show_tab_content').innerHTML = responseHTML;
            if (window.InitiateAction) {
                InitiateAction();
            }

            if (($type(show_url) && show_url == 1) && ($type(edit_url) && edit_url == 1)) {
                ShowUrlColumn(page_id);
            }
            if (window.activ_autosuggest) {
                activ_autosuggest();
            }

            var e4 = $('page_url_msg-wrapper');
            if ($('page_url_msg-wrapper'))
                $('page_url_msg-wrapper').setStyle('display', 'none');

            if (typeof cat != 'undefined' && typeof subcatid != 'undefined' && typeof subcatname != 'undefined' && typeof subsubcatid != 'undefined') {
                subcategory(cat, subcatid, subcatname, subsubcatid);
            }

            if (document.getElementById("category_name")) {
                $('category_name').focus();
            }
            en4.core.runonce.trigger();
        }

    });

    request.send();
}


var requestActive = false;
window.addEvent('load', function () {
    var globalWrapperElement = en4.seaocore.getDomElements('contentWrapper');
    var globalContentElement = en4.seaocore.getDomElements('content');

    if ($(globalWrapperElement) && $(globalWrapperElement).getElementById(globalContentElement)) {
        formElement = $(globalWrapperElement).getElementById(globalContentElement).getElement('.global_form');
        if (formElement && typeof formElement != 'undefined') {
            formElement.addEvent('submit', function (event) {
                if (typeof submitformajax != 'undefined' && submitformajax == 1) {
                    submitformajax = 0;
                    event.stop();
                    Savevalues();
                }
            })
        }
    }
});

var InitiateAction = function () {
    var globalWrapperElement = en4.seaocore.getDomElements('contentWrapper');
    var globalContentElement = en4.seaocore.getDomElements('content');

    if ($(globalWrapperElement) && $(globalWrapperElement).getElementById(globalContentElement)) {
        formElement = $(globalWrapperElement).getElementById(globalContentElement).getElement('.global_form');
        if (formElement && typeof formElement != 'undefined') {
            formElement.addEvent('submit', function (event) {
                if (typeof submitformajax != 'undefined' && submitformajax == 1) {
                    submitformajax = 0;
                    event.stop();
                    Savevalues();
                }
            })
        }
    }
}

var Savevalues = function () {
    if (requestActive)
        return;

    if (!($('category_name'))) {
        if (typeof manage_admin_formsubmit != 'undefined' && manage_admin_formsubmit == 1) {
            if ($('user_id').value == '')
            {
                submitformajax = 1;
                return;
            }
            else
            {
                manage_admin_formsubmit = 1;
            }
        }
    } else {
        if ($('category_name').value == '')
        {
            submitformajax = 1;
            return;
        }
        else
        {
            manage_admin_formsubmit = 1;
        }

    }
    requestActive = true;
    var globalContentElement = en4.seaocore.getDomElements('content');
    var pageurl = $(globalContentElement).getElement('.global_form').action;
    if ($('subject') && pages_id) {
        $('subject').value = 'sitepage_page_' + pages_id;

    }
    currentValues = formElement.toQueryString();
    $('show_tab_content_child').innerHTML = '<center><img src="' + en4.core.staticBaseUrl + 'application/modules/Sitepage/externals/images/spinner_temp.gif" /></center>';
    if (typeof page_url != 'undefined') {
        var param = (currentValues ? currentValues + '&' : '') + 'is_ajax=1&format=html&page_url=' + page_url;
    }
    else {
        var param = (currentValues ? currentValues + '&' : '') + 'is_ajax=1&format=html';
    }

    var request = new Request.HTML({
        url: pageurl,
        onSuccess: function (responseTree, responseElements, responseHTML, responseJavaScript) {
            if ($('show_tab_content')) {
                $('show_tab_content').innerHTML = responseHTML;
            }

            else if ($('show_tab_content_child')) {
                $('show_tab_content_child').innerHTML = responseHTML;
            }
            InitiateAction();
            requestActive = false;
            if (window.activ_autosuggest) {
                activ_autosuggest();
            }
            if (document.getElementById("category_name")) {
                $('category_name').focus();
            } 
        }
    });
    request.send(param);
}


en4.sitepage = {
    rotate: function (photo_id, angle) {
        request = new Request.JSON({
            url: en4.core.baseUrl + 'sitepage/photo/rotate',
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
                $('media_image').src = response.href;
                // Ok, let's refresh the page I guess
                if ($('canReload'))
                    $('canReload').value = 1;
                else
                    window.location.reload(true);
                $('media_image').style.marginTop = "0px";
            }
        });
        request.send();
        return request;
    },
    flip: function (photo_id, direction) {
        request = new Request.JSON({
            url: en4.core.baseUrl + 'sitepage/photo/flip',
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
                $('media_image').src = response.href;
                // Ok, let's refresh the page I guess
                if ($('canReload'))
                    $('canReload').value = 1;
                else
                    window.location.reload(true);
                $('media_image').style.marginTop = "0px";
            }
        });
        request.send();
        return request;
    },
    pageStatistics: function (page_id) {
        new Request.JSON({
            url: en4.core.baseUrl + 'sitepage/insights/page-statistics',
            data: {
                format: 'json',
                page_id: page_id
            }
        }).send();
    }

};

en4.sitepage.ajaxTab = {
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
                if (en4.sitepage.ajaxTab.click_elment_id == widget_id)
                    return;
                en4.sitepage.ajaxTab.click_elment_id = widget_id;
                en4.sitepage.ajaxTab.sendReq(params);
            });
            element.store('addClickEvent', true);
            var attachOnLoadEvent = false;
            if (tab_content_id_sitepage == widget_id) {
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
                                if (addActiveTab) {
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
            en4.sitepage.ajaxTab.click_elment_id = widget_id;
            en4.sitepage.ajaxTab.sendReq(params);
        });


    },
    sendReq: function (params) {
        params.responseContainer.each(function (element) {
            if ((typeof params.loading) == 'undefined' || params.loading == true) {
                element.empty();
                new Element('div', {
                    'class': 'sitepage_profile_loading_image'
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
                });

            }
        });
        request.send();
    }
};

var hideWidgetsForModule = function (widgetname) {

// 	if(widgetname == 'sitepagemember') {
// 		if(sitepage_layout_setting == 0) {
// 			if($(globalContentElement).getElement('.layout_sitepage_page_cover_information_sitepage')) {
// 				$(globalContentElement).getElement('.layout_sitepage_page_cover_information_sitepage').style.display = 'block';
// 			}
// 
// 			if($(globalContentElement).getElement('.layout_sitepagemember_pagecover_photo_sitepagemembers')) {
// 				$(globalContentElement).getElement('.layout_sitepagemember_pagecover_photo_sitepagemembers').style.display = 'block';
// 			}
// 		} else {
// 			if($(globalContentElement).getElement('.layout_sitepage_page_cover_information_sitepage')) {
// 				$(globalContentElement).getElement('.layout_sitepage_page_cover_information_sitepage').style.display = 'block';
// 			}
// 
// 			if($(globalContentElement).getElement('.layout_sitepagemember_pagecover_photo_sitepagemembers')) {
// 				$(globalContentElement).getElement('.layout_sitepagemember_pagecover_photo_sitepagemembers').style.display = 'block';
// 			}
// 		}
// 	}
// 	else {
// 		if(sitepage_layout_setting == 0) {
// 			if($(globalContentElement).getElement('.layout_sitepage_page_cover_information_sitepage')) {
// 				$(globalContentElement).getElement('.layout_sitepage_page_cover_information_sitepage').style.display = 'block';
// 			}
// 
// 			if($(globalContentElement).getElement('.layout_sitepagemember_pagecover_photo_sitepagemembers')) {
// 				$(globalContentElement).getElement('.layout_sitepagemember_pagecover_photo_sitepagemembers').style.display = 'block';
// 			}
// 		} else {
// 			if($(globalContentElement).getElement('.layout_sitepage_page_cover_information_sitepage')) {
// 				$(globalContentElement).getElement('.layout_sitepage_page_cover_information_sitepage').style.display = 'block';
// 			}
// 
// 			if($(globalContentElement).getElement('.layout_sitepagemember_pagecover_photo_sitepagemembers')) {
// 				$(globalContentElement).getElement('.layout_sitepagemember_pagecover_photo_sitepagemembers').style.display = 'block';
// 			}
// 		}
// 	}

    if (sitepage_layout_setting == 1) {
        return;
    }
    var globalContentElement = en4.seaocore.getDomElements('content');

    if (widgetname == 'sitepageactivityfeed') {
        if ($(globalContentElement).getElement('.layout_activity_feed')) {
            $(globalContentElement).getElement('.layout_activity_feed').style.display = 'block';
        }
    }
    else {
        if ($(globalContentElement).getElement('.layout_activity_feed')) {
            $(globalContentElement).getElement('.layout_activity_feed').style.display = 'none';
        }
    }
    if (widgetname == 'sitepageadvancedactivityactivityfeed') {
        if ($(globalContentElement).getElement('.layout_advancedactivity_home_feeds')) {
            $(globalContentElement).getElement('.layout_advancedactivity_home_feeds').style.display = 'block';
        }
    }
    else {
        if ($(globalContentElement).getElement('.layout_advancedactivity_home_feeds')) {
            $(globalContentElement).getElement('.layout_advancedactivity_home_feeds').style.display = 'none';
        }
    }

    if (widgetname == 'sitepageseaocoreactivityfeed') {
        if ($(globalContentElement).getElement('.layout_seaocore_feed')) {
            $(globalContentElement).getElement('.layout_seaocore_feed').style.display = 'block';
        }
    } else {
        if ($(globalContentElement).getElement('.layout_seaocore_feed')) {
            $(globalContentElement).getElement('.layout_seaocore_feed').style.display = 'none';
        }
    }

    if (widgetname == 'sitepageinfo') {
        if ($(globalContentElement).getElement('.layout_sitepage_info_sitepage')) {
            $(globalContentElement).getElement('.layout_sitepage_info_sitepage').style.display = 'block';
        }
    }
    else {
        if ($(globalContentElement).getElement('.layout_sitepage_info_sitepage')) {
            $(globalContentElement).getElement('.layout_sitepage_info_sitepage').style.display = 'none';
        }
    }
    if (widgetname == 'sitepageoverview') {
        if ($(globalContentElement).getElement('.layout_sitepage_overview_sitepage')) {
            $(globalContentElement).getElement('.layout_sitepage_overview_sitepage').style.display = 'block';
        }
    }
    else {
        if ($(globalContentElement).getElement('.layout_sitepage_overview_sitepage')) {
            $(globalContentElement).getElement('.layout_sitepage_overview_sitepage').style.display = 'none';
        }
    }
    if (widgetname == 'sitepagelocation') {
        if ($(globalContentElement).getElement('.layout_sitepage_location_sitepage')) {
            $(globalContentElement).getElement('.layout_sitepage_location_sitepage').style.display = 'block';
        }
    }
    else {
        if ($(globalContentElement).getElement('.layout_sitepage_location_sitepage')) {
            $(globalContentElement).getElement('.layout_sitepage_location_sitepage').style.display = 'none';
        }
    }
    if (widgetname == 'sitepagelink') {
        if ($(globalContentElement).getElement('.layout_core_profile_links')) {
            $(globalContentElement).getElement('.layout_core_profile_links').style.display = 'block';
        }
    }
    else {
        if ($(globalContentElement).getElement('.layout_core_profile_links')) {
            $(globalContentElement).getElement('.layout_core_profile_links').style.display = 'none';
        }
    }

    if (widgetname == 'sitepageintegration') {
        if ($(globalContentElement).getElement('.layout_sitepageintegration_profile_items')) {
            $(globalContentElement).getElement('.layout_sitepageintegration_profile_items').style.display = 'block';
        }
    }
    else {
        if ($(globalContentElement).getElement('.layout_sitepageintegration_profile_items')) {
            $(globalContentElement).getElement('.layout_sitepageintegration_profile_items').style.display = 'none';
        }
    }
//   if(widgetname == 'sitepagetwitter') {
//     if($(globalContentElement).getElement('.layout_sitepagetwitter_feeds_sitepagetwitter')) {
//       $(globalContentElement).getElement('.layout_sitepagetwitter_feeds_sitepagetwitter').style.display = 'block';
//     }
//   }
//   else {
//     if($(globalContentElement).getElement('.layout_sitepagetwitter_feeds_sitepagetwitter')) {
//       $(globalContentElement).getElement('.layout_sitepagetwitter_feeds_sitepagetwitter').style.display = 'none';
//     }
//   }
}

var deleteMemberCategory = function (manage_id, url, page_id)
{
    var total_div = $('count_div').value;
    if (total_div >= 1) {
        total_div = total_div - 1;
    }
    var parentnode = $(manage_id + '_page').parentNode;
    var childnode = $(manage_id + '_page');

    en4.core.request.send(new Request.JSON({
        url: url,
        data: {
            format: 'json',
            category_id: manage_id,
            page_id: page_id
        },
        onSuccess: function (responseJSON) {
            parentnode.removeChild(childnode);
            if (total_div == 0) {
                //$(manage_id+ '_page').innerHTML = '';
                //parentnode.removeChild(childnode);
            } else {
                // $(manage_id+ '_page').innerHTML = '';
                $('count_div').value = total_div;
            }
            if (document.getElementById("category_name")) {
                $('category_name').focus();
            }
        }
    }))
};
en4.sitepagemember = {
    profileTabParams: Array(),
    profileTabRequest: function (options) {

        var params = en4.sitepagemember.profileTabParams[options.content_id];
        var url = en4.core.baseUrl + 'widget';
        if (params.requestUrl)
            url = params.requestUrl;

        params.requestParams.content_id = options.content_id;
        if (params.searchFormElement && $(params.searchFormElement)) {
            url = url + '?' + $(params.searchFormElement).toQueryString();
        }
        if (params.loadingElement && $(params.loadingElement)) {
            $(params.loadingElement).innerHTML = '<div class="seaocore_content_loader"></div>';
        }
        if (options.requestParams) {
            params.requestParams = $merge(params.requestParams, options.requestParams);
        }
        var request = new Request.HTML({
            url: url,
            data: $merge(params.requestParams, {
                format: 'html',
                subject: en4.core.subject.guid,
                is_ajax_load: true
            }),
            evalScripts: true,
            onSuccess: function (responseTree, responseElements, responseHTML, responseJavaScript) {
                var container = $('id_' + options.content_id);
                //  params.responseContainer.each(function(container){
                container.empty();
                Elements.from(responseHTML).inject(container);
                en4.core.runonce.trigger();
                Smoothbox.bind(container);
                //  });
                if (options.callBackFunction) {
                    options.callBackFunction();
                }
            }
        });
        request.send();
    }
}


en4.siteeventcontenttype = {
    profileTabParams: Array(),
    profileTabRequest: function (options) {

        var params = en4.siteeventcontenttype.profileTabParams[options.content_id];
        var url = en4.core.baseUrl + 'widget';
        if (params.requestUrl)
            url = params.requestUrl;

        params.requestParams.content_id = options.content_id;
        if (params.searchFormElement && $(params.searchFormElement)) {
            url = url + '?' + $(params.searchFormElement).toQueryString();
        }
        if (params.loadingElement && $(params.loadingElement)) {
            $(params.loadingElement).innerHTML = '<div class="seaocore_content_loader"></div>';
        }
        if (options.requestParams) {
            params.requestParams = $merge(params.requestParams, options.requestParams);
        }
        var request = new Request.HTML({
            url: url,
            data: $merge(params.requestParams, {
                format: 'html',
                subject: en4.core.subject.guid,
                is_ajax_load: true
            }),
            evalScripts: true,
            onSuccess: function (responseTree, responseElements, responseHTML, responseJavaScript) {
                var container = $('id_' + options.content_id);
                //  params.responseContainer.each(function(container){
                container.empty();
                Elements.from(responseHTML).inject(container);
                en4.core.runonce.trigger();
                Smoothbox.bind(container);
                //  });
                if (options.callBackFunction) {
                    options.callBackFunction();
                }
            }
        });
        request.send();
    }
}

var SitepageCoverPhoto = new Class({
    Implements: [Options],
    options: {
        element: null,
        buttons: 'seao_cover_options',
        photoUrl: '',
        position_url: '',
        position: {
            top: 0,
            left: 0
        }
    },
    block: null,
    buttons: null,
    element: null,
    changeButton: null,
    saveButton: null,
    initialize: function (options) {
        if (options.block == null) {
            return;
        }
        this.block = options.block;
        this.setOptions(options);
        //    this.block = this.element.getParent();
        this.getCoverPhoto();
    },
    attach: function () {
        var self = this;
        if (!$(this.options.buttons)) {
            return;
        }
        this.element = self.block.getElement('.cover_photo');
        this.buttons = $(this.options.buttons);
        this.saveButton = this.buttons.getElement('.save-button');
        this.editButton = this.buttons.getElement('.edit-button');
        if (this.saveButton) {
            this.saveButton.getElement('.positions-save').addEvent('click', function () {
                self.reposition.save();
            });
            this.saveButton.getElement('.positions-cancel').addEvent('click', function () {
                self.reposition.stop(1);
            });
        }
    },
    get: function (type) {
        if (type == 'block') {
            return this.block;
        }

        return this.element;
    },
    getButton: function (type) {
        if (type == 'save') {
            return this.saveButton;
        }

        return this.editButton;
    },
    getCoverPhoto: function (reposition_enable) {
        var self = this;

        new Request.HTML({
            'method': 'get',
            'url': self.options.photoUrl,
            'data': {
                'format': 'html'
            },
            'onComplete': function (responseTree, responseElements, responseHTML, responseJavaScript) {
                //        var block = self.get('block');
                //        var cover_block = block.getElementById('tl-cover');
                //
                if (responseHTML.length > 0) {
                    self.block.set('html', responseHTML);
                    Smoothbox.bind(self.block);
                    self.attach();
                    if (reposition_enable) {
                        Smoothbox.close();
                        self.options.position = {
                            top: 0,
                            left: 0
                        };
                        setTimeout(function () {
                            self.reposition.start()
                        }, '2000');
                    }
                }
            }
        }).send();
    },
    reposition: {
        drag: null,
        active: false,
        start: function () {
            if (this.active) {
                return;
            }

            var self = document.seaoCoverPhoto;
            var cover = self.get();
            this.active = true;
            //self.getButton().fireEvent('click');

            self.getButton().addClass('dnone');
            self.buttons.addClass('sitepage_cover_options_btm');
            self.getButton('save').removeClass('dnone');
            if (self.options.columnHeight && self.block.getElement('.cover_photo_wap')) {
                self.block.setStyle('height', self.options.columnHeight + 'px');
            }
            self.block.getElement('.cover_tip_wrap').removeClass('dnone');
            cover.addClass('draggable');
            var cont = cover.getParent();

            var verticalLimit = cover.offsetHeight.toInt() - cont.offsetHeight.toInt();
            var horizontalLimit = cover.offsetWidth.toInt() - cont.offsetWidth.toInt();
            var limit = {
                x: [0, 0],
                y: [0, 0]
            };

            if (verticalLimit > 0) {
                limit.y = [-verticalLimit, 0]
            }

            if (horizontalLimit > 0) {
                limit.x = [-horizontalLimit, 0]
            }

            this.drag = new Drag(cover, {
                limit: limit,
                onComplete: function (el) {
                    self.options.position.top = el.getStyle('top').toInt();
                    self.options.position.left = el.getStyle('left').toInt();
                }
            }).detach();

            this.drag.attach();
        },
        stop: function (reload) {
            var self = document.seaoCoverPhoto;
            self.reposition.drag.detach();
            self.getButton('save').addClass('dnone');
            self.block.getElement('.cover_tip_wrap').addClass('dnone');
            self.buttons.removeClass('sitepage_cover_options_btm');
            self.getButton().removeClass('dnone');

            self.get().removeClass('draggable');
            self.reposition.drag = null;
            self.reposition.active = false;
            if (reload)
                self.getCoverPhoto();
        },
        save: function () {
            if (!this.active) {
                return;
            }
            var self = document.seaoCoverPhoto;
            var current = this;
            new Request.JSON({
                method: 'get',
                url: self.options.positionUrl,
                data: {
                    'format': 'json',
                    'position': self.options.position
                },
                onSuccess: function (response) {
                    current.stop();
                }
            }).send();
        }
    }
});

function scrollToTopForPage(id) {

    if (typeof sitepage_slding_effect !== 'undefined' && sitepage_slding_effect == 0)
        return false;

    if (document.getElement('body').get('id')) {
        var scroll = new Fx.Scroll(document.getElement('body').get('id'), {
            wait: false,
            duration: 1000,
            offset: {
                'x': -200,
                'y': -100
            },
            transition: Fx.Transitions.Quad.easeInOut
        });

        scroll.toElement(id);
    }
    return;
}

function setLeftLayoutForPage(showLayout) {

    if (typeof page_hide_left_container != 'undefined' && page_hide_left_container == 0)
        return;

    var globalContentElement = en4.seaocore.getDomElements('content');
    if (showLayout == 1) {
        if ($$('.layout_left')) {
            $$('.layout_left').setStyle('display', 'none');
            if ($('thumb_icon')) {
                $('thumb_icon').style.display = 'block';
            }
        }
        if ($$('.layout_right')) {
            $$('.layout_right').setStyle('display', 'none');
            if ($('thumb_icon')) {
                $('thumb_icon').style.display = 'block';
            }
        }

        if ($(globalContentElement).getElement('.layout_sitepage_page_cover_information_sitepage')) {
            $(globalContentElement).getElement('.layout_sitepage_page_cover_information_sitepage').style.display = 'none';
        }

        if ($(globalContentElement).getElement('.layout_sitecontentcoverphoto_content_cover_photo')) {
            $(globalContentElement).getElement('.layout_sitecontentcoverphoto_content_cover_photo').style.display = 'block';
        }

        if ($(globalContentElement).getElement('.layout_sitepagemember_pagecover_photo_sitepagemembers')) {
            $(globalContentElement).getElement('.layout_sitepagemember_pagecover_photo_sitepagemembers').style.display = 'none';
        }
    } else {
        if ($$('.layout_left')) {
            $$('.layout_left').setStyle('display', 'block');
            if ($('thumb_icon')) {
                $('thumb_icon').style.display = 'none';
            }
        }
        if ($$('.layout_right')) {
            $$('.layout_right').setStyle('display', 'block');
            if ($('thumb_icon')) {
                $('thumb_icon').style.display = 'none';
            }
        }

        if ($(globalContentElement).getElement('.layout_sitepage_page_cover_information_sitepage')) {
            $(globalContentElement).getElement('.layout_sitepage_page_cover_information_sitepage').style.display = 'block';
        }

        if ($(globalContentElement).getElement('.layout_sitecontentcoverphoto_content_cover_photo')) {
            $(globalContentElement).getElement('.layout_sitecontentcoverphoto_content_cover_photo').style.display = 'block';
        }

        if ($(globalContentElement).getElement('.layout_sitepagemember_pagecover_photo_sitepagemembers')) {
            $(globalContentElement).getElement('.layout_sitepagemember_pagecover_photo_sitepagemembers').style.display = 'block';
        }
    }
}
