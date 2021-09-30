
window.addEvent('domready', function() {
    $$('.yncategories_sub_item').set('styles', {
        display : 'none'
    });

    $$('.yncategories_have_child').addEvent('click', function(event) {

        var row = this.getParent('li');
        var id = row.getAttribute('value');

        if (this.hasClass('yncategories_collapsed')) {

            var rowSubCategories = row.getAllNext('li.child_'+id);

            this.removeClass('yncategories_collapsed');
            this.addClass('yncategories_no_collapsed');

            for(var i = 0; i < rowSubCategories.length; i++) {

                if (!rowSubCategories[i].hasClass('yncategories_sub_item')) {
                    break;
                } else {
                    rowSubCategories[i].set('styles', {
                        display : 'block'
                    });
                }
            }

        } else {

            var rowSubCategories = row.getAllNext('li.child_'+id);

            this.removeClass('yncategories_no_collapsed');
            this.addClass('yncategories_collapsed');

            for(var i = 0; i < rowSubCategories.length; i++) {

                if (!rowSubCategories[i].hasClass('yncategories_sub_item')) {
                    break;
                } else {
                    collapsedChild(rowSubCategories);
                }
            }
        }
    });

    function collapsedChild(rowSubCategories) {
        for(var i = 0; i < rowSubCategories.length; i++) {

            if (!rowSubCategories[i].hasClass('yncategories_sub_item')) {
                break;
            } else {
                var collapsedDivs = rowSubCategories[i].getElements('.yncategories_have_child');

                if (collapsedDivs.length > 0) {
                    collapsedDivs[0].removeClass('yncategories_no_collapsed');
                    collapsedDivs[0].addClass('yncategories_collapsed');
                }

                rowSubCategories[i].set('styles', {
                    display : 'none'
                });

                var idSub = rowSubCategories[i].get('value');
                var rowSubSubCategories = rowSubCategories[i].getAllNext('li.child_'+idSub);
                if (rowSubSubCategories) collapsedChild(rowSubSubCategories);
            }
        }
    }
});