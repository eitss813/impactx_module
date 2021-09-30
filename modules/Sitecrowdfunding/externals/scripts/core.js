/* $Id: core.js 2011-08-26 9:40:21Z SocialEngineAddOns Copyright 2010-2011 BigStep Technologies Pvt. Ltd. $ */
en4.sitecrowdfunding = {
};
var tab_content_id_sitecrowdfunding = 0;
en4.sitecrowdfunding.ajaxTab = {
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
                if (en4.sitecrowdfunding.ajaxTab.click_elment_id == widget_id)
                    return;
                en4.sitecrowdfunding.ajaxTab.click_elment_id = widget_id;
                en4.sitecrowdfunding.ajaxTab.sendReq(params);
            });
            element.store('addClickEvent', true);
            var attachOnLoadEvent = false;
            if (tab_content_id_sitecrowdfunding == widget_id) {
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
                                if (addActiveTab || tab_content_id_sitecrowdfunding == widget_id) {
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
            en4.sitecrowdfunding.ajaxTab.click_elment_id = widget_id;
            en4.sitecrowdfunding.ajaxTab.sendReq(params);
        });


    },
    sendReq: function (params) {
        params.responseContainer.each(function (element) {
            if ((typeof params.loading) == 'undefined' || params.loading == true) {
                element.empty();
                new Element('div', {
                    'class': 'sitecrowdfunding_profile_loading_image'
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
function fundingProgressiveBarAnimation() {
    var el = $$('.sitecrowdfunding_funding_bar .funding_animation');
    el.each(function (item) {
        if (!item.hasClass('widthfull')) {
            item.addClass('widthfull');
        }
    })
}

var NavigationSitecrowdfunding = function () {
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
en4.sitecrowdfunding.ajaxTab = {
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
                if (en4.sitecrowdfunding.ajaxTab.click_elment_id == widget_id)
                    return;
                en4.sitecrowdfunding.ajaxTab.click_elment_id = widget_id;
                en4.sitecrowdfunding.ajaxTab.sendReq(params);
            });
            element.store('addClickEvent', true);
            var attachOnLoadEvent = false;
            if (tab_content_id_sitecrowdfunding == widget_id) {
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
                                if (addActiveTab || tab_content_id_sitecrowdfunding == widget_id) {
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
            en4.sitecrowdfunding.ajaxTab.click_elment_id = widget_id;
            en4.sitecrowdfunding.ajaxTab.sendReq(params);
        });


    },
    sendReq: function (params) {
        params.responseContainer.each(function (element) {
            if ((typeof params.loading) == 'undefined' || params.loading == true) {
                element.empty();
                new Element('div', {
                    'class': 'sitecrowdfunding_profile_loading_image'
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
                    if (typeof fundingProgressiveBarAnimation == 'function') {
                        fundingProgressiveBarAnimation();
                    }
                    en4.core.runonce.trigger();
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


var seao_getstarttime = function (date) { 
  starttime = date.split("/");
  date = starttime[0] + '/' + starttime[1] + '/' + starttime[2];
  return date; 
}

en4.core.runonce.add(function() {
    fundingProgressiveBarAnimation();
});

function showSmoothBox(url) {
    Smoothbox.open(url);
}