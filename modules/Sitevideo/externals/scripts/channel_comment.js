/* $Id: channel_comment.js 2011-08-26 9:40:21Z SocialEngineAddOns Copyright 2010-2011 BigStep Technologies Pvt. Ltd. $ */
en4.sitevideo.comments = {
    loadComments: function (type, id, page) {
        en4.core.request.send(new Request.HTML({
            url: en4.core.baseUrl + 'sitevideo/comment/list',
            data: {
                format: 'html',
                type: type,
                id: id,
                page: page
            }
        }), {
            'element': $('comments' + '_' + type + '_' + id)
        });
    },
    attachCreateComment: function (formElement, type, id) {
        var bind = this;
        formElement.addEvent('submit', function (event) {
            event.stop();
            var form_values = formElement.toQueryString();
            form_values += '&format=json';
            form_values += '&id=' + formElement.identity.value;
            en4.core.request.send(new Request.JSON({
                url: en4.core.baseUrl + 'sitevideo/comment/create',
                data: form_values,
                type: type,
                id: id
            }), {
                'element': $('comments' + '_' + type + '_' + id)
            });
        })
    },
    comment: function (type, id, body) {
        en4.core.request.send(new Request.JSON({
            url: en4.core.baseUrl + 'sitevideo/comment/create',
            data: {
                format: 'json',
                type: type,
                id: id,
                body: body
            }
        }), {
            'element': $('comments' + '_' + type + '_' + id)
        });
    },
    like: function (type, id, comment_id) {
        en4.core.request.send(new Request.JSON({
            url: en4.core.baseUrl + 'sitevideo/comment/like',
            data: {
                format: 'json',
                type: type,
                id: id,
                comment_id: comment_id
            }
        }), {
            'element': $('comments' + '_' + type + '_' + id)
        });
    },
    unlike: function (type, id, comment_id) {
        en4.core.request.send(new Request.JSON({
            url: en4.core.baseUrl + 'sitevideo/comment/unlike',
            data: {
                format: 'json',
                type: type,
                id: id,
                comment_id: comment_id
            }
        }), {
            'element': $('comments' + '_' + type + '_' + id)
        });
    },
    showLikes: function (type, id) {
        en4.core.request.send(new Request.HTML({
            url: en4.core.baseUrl + 'sitevideo/comment/list',
            data: {
                format: 'html',
                type: type,
                id: id,
                viewAllLikes: true
            }
        }), {
            'element': $('comments' + '_' + type + '_' + id)
        });
    },
    deleteComment: function (type, id, comment_id) {
        if (!confirm(en4.core.language.translate('Are you sure you want to delete this?'))) {
            return;
        }
        (new Request.JSON({
            url: en4.core.baseUrl + 'sitevideo/comment/delete',
            data: {
                format: 'json',
                type: type,
                id: id,
                comment_id: comment_id
            },
            onComplete: function () {
                if ($('comment-' + comment_id)) {
                    $('comment-' + comment_id).destroy();
                }
                try {
                    var commentCount = $$('.comments_options span')[0];
                    var m = commentCount.get('html').match(/\d+/);
                    var newCount = (parseInt(m[0]) != 'NaN' && parseInt(m[0]) > 1 ? parseInt(m[0]) - 1 : 0);
                    commentCount.set('html', commentCount.get('html').replace(m[0], newCount));
                } catch (e) {
                }
            }
        })).send();
    }
};
