<!-- Used in initiative landing page -->
<?php     
$apiKey = Engine_Api::_()->seaocore()->getGoogleMapApiKey();
$this->headScript()->appendFile("https://maps.googleapis.com/maps/api/js?libraries=places&sensor=true&key=$apiKey");
$this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/core.js');
?>
<?php $this->headLink()->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/styles/styles.css'); ?>

<?php
/* Include the common user-end field switching javascript */
echo $this->partial('_jsSwitch.tpl', 'fields', array());
?>

<div class="seaocore_searchform_criteria">
    <?php echo $this->searchForm->render($this); ?>
</div>

<script type="text/javascript">


    <?php  if (!empty($this->categoryInSearchForm) && !empty($this->categoryInSearchForm->display)): ?>
        var search_category_id, search_subcategory_id, search_subsubcategory_id;
        en4.core.runonce.add(function () {
            search_category_id = '<?php echo $this->category_id ?>';
            if (search_category_id != 0) {
                addOptions(search_category_id, 'cat_dependency', 'subcategory_id', 1);
                search_subcategory_id = '<?php  echo $this->subcategory_id ?>';
                if (search_subcategory_id != 0) {
                    search_subsubcategory_id = '<?php echo $this->subsubcategory_id ?>';
                    addOptions(search_subcategory_id, 'subcat_dependency', 'subsubcategory_id', 1);
                }
            }
        });
    <?php endif; ?>


    function show_subcat(cat_id){
        if (document.getElementById('subcat_' + cat_id)) {
            if (document.getElementById('subcat_' + cat_id).style.display == 'block') {
                document.getElementById('subcat_' + cat_id).style.display = 'none';
                document.getElementById('img_' + cat_id).src = '<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitecrowdfunding/externals/images/bullet-right.png';
            }
            else if (document.getElementById('subcat_' + cat_id).style.display == '') {
                document.getElementById('subcat_' + cat_id).style.display = 'none';
                document.getElementById('img_' + cat_id).src = '<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitecrowdfunding/externals/images/bullet-right.png';
            }
            else {
                document.getElementById('subcat_' + cat_id).style.display = 'block';
                document.getElementById('img_' + cat_id).src = '<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitecrowdfunding/externals/images/bullet-bottom.png';
            }
        }
    }

    en4.core.runonce.add(function () {
        $$('#filter_form input[type=text]').each(function (f) {
            if (f.value == '' && f.id.match(/\min$/)) {
                new OverText(f, {'textOverride': 'min', 'element': 'span'});
                //f.set('class', 'integer_field_unselected');
            }
            if (f.value == '' && f.id.match(/\max$/)) {
                new OverText(f, {'textOverride': 'max', 'element': 'span'});
                //f.set('class', 'integer_field_unselected');
            }
        });
    });

    window.addEvent('onChangeFields', function () {
        var firstSep = $$('li.browse-separator-wrapper')[0];
        var lastSep;
        var nextEl = firstSep;
        var allHidden = true;
        do {
            if (!nextEl)
                return false;
            nextEl = nextEl.getNext();
            if (nextEl.get('class') == 'browse-separator-wrapper') {
                lastSep = nextEl;
                nextEl = false;
            } else {
                allHidden = allHidden && (nextEl.getStyle('display') == 'none');
            }
        } while (nextEl);
        if (lastSep) {
            lastSep.setStyle('display', (allHidden ? 'none' : ''));
        }
    });

    function showFields(cat_value, cat_level) {
        if (cat_level == 1 || (previous_mapped_level >= cat_level && previous_mapped_level != 1) || (profile_type == null || profile_type == '' || profile_type == 0)) {
            profile_type = getProfileType(cat_value);
            if (profile_type == 0) {
                profile_type = '';
            } else {
                previous_mapped_level = cat_level;
            }
            $('profile_type').value = profile_type;
            changeFields($('profile_type'));
        }
    }

    <?php if (isset($_GET['search']) || isset($_GET['location'])): ?>
        var advancedSearch = 1;
    <?php else: ?>
        var advancedSearch = <?php echo $this->advancedSearch; ?>;
    <?php endif; ?>

    en4.core.runonce.add(function (){
        var item_count = 0;
        var contentAutocomplete = new Autocompleter.Request.JSON('search', '<?php echo $this->url(array('action' => 'get-search-projects'), "sitecrowdfunding_project_general", true) ?>', {
            'postVar': 'text',
            'minLength': 1,
            'selectMode': 'pick',
            'autocompleteType': 'tag',
            'className': 'tag-autosuggest seaocore-autosuggest',
            'customChoices': true,
            'filterSubset': true,
            'multiple': false,
            'injectChoice': function (token) {
                if (typeof token.label != 'undefined') {
                    if (token.sitecrowdfunding_url != 'seeMoreLink') {
                        var choice = new Element('li', {'class': 'autocompleter-choices', 'html': token.photo, 'id': token.label, 'sitecrowdfunding_url': token.sitecrowdfunding_url, onclick: 'javascript:getPageResults("' + token.sitecrowdfunding_url + '")'});
                        new Element('div', {'html': this.markQueryValue(token.label), 'class': 'autocompleter-choice'}).inject(choice);
                        this.addChoiceEvents(choice).inject(this.choices);
                        choice.store('autocompleteChoice', token);
                    }
                    if (token.sitecrowdfunding_url == 'seeMoreLink') {
                        var search = $('search').value;
                        var choice = new Element('li', {'class': 'autocompleter-choices1', 'html': '', 'id': 'stopevent', 'sitecrowdfunding_url': ''});
                        new Element('div', {'html': 'See More Results for ' + search, 'class': 'autocompleter-choicess', onclick: 'javascript:Seemore()'}).inject(choice);
                        this.addChoiceEvents(choice).inject(this.choices);
                        choice.store('autocompleteChoice', token);
                    }
                }
            }
        });
        contentAutocomplete.addEvent('onSelection', function (element, selected, value, input) {
        window.addEvent('keyup', function (e) {
            if (e.key == 'enter') {
                if (selected.retrieve('autocompleteChoice') != 'null') {
                    var url = selected.retrieve('autocompleteChoice').sitecrowdfunding_url;
                    if (url == 'seeMoreLink') {
                        Seemore();
                    }
                    else {
                        window.location.href = url;
                    }
                }
            }
        });
        });
    });


    function Seemore() {
        $('stopevent').removeEvents('click');
        var url = '<?php echo $this->url(array('action' => 'browse'), "sitecrowdfunding_project_general", true); ?>';
        window.location.href = url + "?search=" + encodeURIComponent($('search').value);
    }

    function getPageResults(url) {
        if (url != 'null') {
            if (url == 'seeMoreLink') {
                Seemore();
            }
            else {
                window.location.href = url;
            }
        }
    }

    en4.core.runonce.add(function () {
        if ($('location')) {
            var params = {
                'detactLocation': <?php echo $this->locationDetection; ?>,
                'fieldName': 'location',
                'noSendReq': 1,
                'locationmilesFieldName': 'locationmiles',
                'locationmiles': <?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.locationdefaultmiles', 1000); ?>,
                'reloadPage': 1
            };
            en4.seaocore.locationBased.startReq(params);
        }
        locationAutoSuggest('<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.countrycities'); ?>', 'location', 'project_city');
    });

    var profile_type = 0;
    var previous_mapped_level = 0;
    var sitecrowdfunding_categories_slug = <?php echo json_encode($this->categories_slug); ?>;

    function showCustomFields(cat_value, cat_level) {

    if (cat_level == 1 || (previous_mapped_level >= cat_level && previous_mapped_level != 1) || (profile_type == null || profile_type == '' || profile_type == 0)) {
        profile_type = getProfileType(cat_value);
        if (profile_type == 0) {
            profile_type = '';
        } else {
            previous_mapped_level = cat_level;
        }
        $('profile_type').value = profile_type;
        changeFields($('profile_type'));
    }
    }

    var getProfileType = function (category_id) {
    var mapping = <?php echo Zend_Json_Encoder::encode(Engine_Api::_()->getDbtable('categories', 'sitecrowdfunding')->getMapping()); ?>;
    for (i = 0; i < mapping.length; i++) {
        if (mapping[i].category_id == category_id)
            return mapping[i].profile_type;
    }
    return 0;
    }

    function clear(element) {
        for (var i = (element.options.length - 1); i >= 0; i--) {
            element.options[ i ] = null;
        }
    }

    function addOptions(element_value, element_type, element_updated, domready) {

    var element = $(element_updated);
    if (domready == 0) {
        switch (element_type) {
            case 'cat_dependency':
                $('subcategory_id' + '-wrapper').style.display = 'none';
                clear($('subcategory_id'));
                $('subcategory_id').value = 0;
                $('categoryname').value = sitecrowdfunding_categories_slug[element_value];

            case 'subcat_dependency':
                $('subsubcategory_id' + '-wrapper').style.display = 'none';
                clear($('subsubcategory_id'));
                $('subsubcategory_id').value = 0;
                $('subsubcategoryname').value = '';
                if (element_type == 'subcat_dependency')
                    $('subcategoryname').value = sitecrowdfunding_categories_slug[element_value];
                else
                    $('subcategoryname').value = '';
        }
    }

    if (element_value <= 0)
        return;

    var url = '<?php echo $this->url(array('module' => 'sitecrowdfunding', 'controller' => 'project', 'action' => 'get-projects-categories', 'showAllCategories' => $this->showAllCategories), "default", true); ?>';
    en4.core.request.send(new Request.JSON({
        url: url,
        data: {
            format: 'json',
            element_value: element_value,
            element_type: element_type
        },
        onRequest: function (responseJSON) {
            $(element_updated+'_loadingimage').show();
        },
        onSuccess: function (responseJSON) {
            $(element_updated+'_loadingimage').hide();
            var categories = responseJSON.categories;
            var option = document.createElement("OPTION");
            option.text = "";
            option.value = 0;
            element.options.add(option);
            for (i = 0; i < categories.length; i++) {
                var option = document.createElement("OPTION");
                option.text = categories[i]['category_name'];
                option.value = categories[i]['category_id'];
                element.options.add(option);
                sitecrowdfunding_categories_slug[categories[i]['category_id']] = categories[i]['category_slug'];
            }

            if (categories.length > 0)
                $(element_updated + '-wrapper').style.display = 'inline-block';
            else
                $(element_updated + '-wrapper').style.display = 'none';

            if (domready == 1) {
                var value = 0;
                if (element_updated == 'category_id') {
                    value = search_category_id;
                } else if (element_updated == 'subcategory_id') {
                    value = search_subcategory_id;
                } else {
                    value = search_subsubcategory_id;
                }
                $(element_updated).value = value;
            }
        }

    }), {'force': true});
    }

    function searchSiteprojects() {

        $('project_browse_loader').style.display = 'inline-block';
        $('project_browse_content').style.display = 'none';
        $('project_browse_tabs').style.textAlign = "center";
        //document.getElementById("projects_list").style.height = document.getElementById("project_search").offsetHeight+"px";

        var formElements = document.getElementById('filter_form');
        var formParams = formElements.toQueryString();
        var initiative_id = <?php echo $this->initiative_id?>;
        var page_id = <?php echo $this->page_id?>;
        var finalParams = formParams + '&tab_link=browse_projects&is_ajax=1&initiative_id='+initiative_id+'&page_id='+page_id;

        goToMenuContainer();
        en4.core.request.send(new Request.HTML({
            method: 'post',
            url: en4.core.baseUrl + 'sitepage/initiatives/landing-page',
            data: finalParams,
            onSuccess: function (responseTree, responseElements, responseHTML, responseJavaScript) {
                $('hidden_ajax_data').innerHTML = responseHTML;
                $('landing_page_projects').innerHTML = $('hidden_ajax_data').getElement('#landing_page_projects').innerHTML;
                $('hidden_ajax_data').innerHTML = '';
                fundingProgressiveBarAnimation();
                Smoothbox.bind($('landing_page_projects'));
                en4.core.runonce.trigger();

                // Populate the form
                loadProjectBrowseUI();

                // set height of projects_list as like project_search
                /*var searchFormHeight = document.getElementById("project_search").offsetHeight;
                var browseListHeight = document.getElementById("projects_list").offsetHeight;

                if(browseListHeight < searchFormHeight){
                    document.getElementById("projects_list").style.height = document.getElementById("project_search").offsetHeight+"px";
                }*/

                $('project_browse_loader').style.display = 'none';
                $('project_browse_content').style.display = 'inline-block';
                $('project_browse_tabs').style.textAlign = "unset";

            }
        }), {
            "force": true
        });

    };

    function initiativeOptions(id, type , defaultValue,showInitiativeWrapperFlag) {
        if(id){
            var url = '<?php echo $this->url(array('action' => 'get-initiatives'), "sitepage_initiatives") ?>';
            var elementWrapper = null;
            var element = null;
            var page_id = null;
            var initiative_id = null;

            if(type =='initiative_names'){
                page_id = id;
                initiative_id = null;
            }

            if(type =='initiative_project_galleries'){
                if($('page_id')){
                    page_id = $('page_id').value;
                }
                initiative_id = id;
            }

            if(page_id){
                en4.core.request.send(new Request.JSON({
                    url: url,
                    data: {
                        format: 'json',
                        page_id:page_id,
                        initiative_id:initiative_id
                    },
                    onSuccess: function (responseJSON) {
                        var initiatives = responseJSON.initiatives;

                        if(initiative_id == null){
                            elementWrapper = $('initiative-wrapper');
                            element = $('initiative');
                        }
                        if(initiative_id != null){
                            elementWrapper = $('initiative_galleries-wrapper');
                            element = $('initiative_galleries');
                            elementWrapper.style.display = 'inline-block';
                        }

                        var option = document.createElement("OPTION");
                        option.text = "";
                        option.value = 0;
                        clear(element);
                        element.options.add(option);

                        for (let i = 0; i < initiatives.length; i++) {
                            var option = document.createElement("OPTION");
                            if(
                                (initiatives[i]['text']!==null && initiatives[i]['value']!==null)
                                ||
                                (initiatives[i]['text']!=='' && initiatives[i]['value']!=='')
                            )
                            {
                                option.text = initiatives[i]['text'];
                                option.value = initiatives[i]['value'];
                                element.options.add(option);
                            }
                        }

                        // set default value
                        element.value = defaultValue;

                        if(type == 'initiative_project_galleries'){
                            if(showInitiativeWrapperFlag===false){
                                $("initiative-wrapper").style.display = 'none';
                            }
                        }

                    }
                }), {'force': true});
            }

        }else{
            clear($('initiative'));
            clear($('initiative_galleries'));
        }

    }

</script>