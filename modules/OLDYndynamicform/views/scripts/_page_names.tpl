<div id="yndform_page_names" class="yndform_page_names form-wrapper">
    <div id="first_page_name" class="form-wrapper">
        <div class="form-label">
            <label></label>
        </div>
        <div id="title-element" class="form-element">
            <input type="text" name="page_names[]" id="page_names[]" value="">
        </div>
    </div>
</div>

<script>
    var totalPages,
        pageNamesVal = [],
        pageNames = [],
        cloneEle,
        pagesContainer,
        firstPageName;

    window.addEvent('domready', function(){
        firstPageName = $('first_page_name');
        totalPages = $('total_pages').get('value');
        pagesContainer = $$('.yndform_page_names')[0];
        pageNamesVal = $('page_names_hidden').get('value');
        if (pageNamesVal) {
            pageNames = JSON.parse(pageNamesVal);
        }
        yndformSetPageName(firstPageName, 1);
        for (var i=2; i<=totalPages; i++) {
            cloneEle = firstPageName.clone();
            yndformSetPageName(cloneEle, i);
            cloneEle.inject(pagesContainer, 'bottom');
        }
    });

    function yndformSetPageName(el, index) {
        el.getElements('label')[0].set('html', 'Page ' + index);
        el.getElements('input')[0].set('value', pageNames[index - 1] ? pageNames[index - 1] : '');
    }
</script>