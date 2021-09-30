/* $Id: reorder.js $ */

    var reorder = function (e,item,url) {
        var menuitems = e.parentNode.childNodes;
        var ordering = {};
        var params = {};
        var i = 1;
        for (var menuitem in menuitems)
        {
            var child_id = menuitems[menuitem].id;

            if ((child_id != undefined))
            {
                ordering[child_id] = i;
                i++;
            }
        }
        params['order'] = ordering;
        params['format'] = 'json';
        params['item'] = item;

        // Send request
        var request = new Request.JSON({
            'url': url,
            'method': 'POST',
            'data': params,
            onSuccess: function (responseJSON) {
            }
        });

        request.send();
    }