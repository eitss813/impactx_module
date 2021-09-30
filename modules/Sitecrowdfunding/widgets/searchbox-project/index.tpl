<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.php 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitecrowdfunding/externals/scripts/core.js'); ?>
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/core.js'); ?>


<?php
  $baseUrl = $this->layout()->staticBaseUrl;
  $this->headLink()->appendStylesheet($baseUrl . 'application/modules/Sitecrowdfunding/externals/styles/style_sitecrowdfunding.css');
?>

<?php
$this->headScript()
        ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Observer.js')
        ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.js')
        ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Local.js')
        ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Request.js');        
?>
<?php
$apiKey = Engine_Api::_()->seaocore()->getGoogleMapApiKey();
$this->headScript()->appendFile("https://maps.googleapis.com/maps/api/js?libraries=places&key=$apiKey");
?>
<?php if ($this->loaded_by_ajax): ?>
    <script type="text/javascript">
        var params = {
            requestParams:<?php echo json_encode($this->params) ?>,
            responseContainer: $$('.layout_sitecrowdfunding_searchbox_project')
        }
        en4.sitecrowdfunding.ajaxTab.attachEvent('<?php echo $this->identity ?>', params);
    </script>
<?php endif; ?>

<?php $this->headLink()->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitecrowdfunding/externals/styles/style_sitecrowdfunding.css'); ?>

<?php if ($this->showContent): ?>
    <div class="sitecrowdfunding_form_quick_search">
        <?php echo $this->form->setAttrib('class', 'sitecrowdfunding-project-search-box')->render($this); ?>
    </div>	
<?php endif; ?><?php ?>
<script type="text/javascript">

    var doSearching = function (searchboxcategory_id) {

        var categoryElementExist = <?php echo $this->categoryElementExist; ?>;
        var searchboxcategory_id = 0;
        if (categoryElementExist == 1) {
            searchboxcategory_id = $('ajaxcategory_id').value;
        }

        if (searchboxcategory_id != 0) {

            var categoriesArray = <?php echo Zend_Json_Encoder::encode(Engine_Api::_()->getDbTable('categories', 'sitecrowdfunding')->getCategoriesDetails($this->categoriesLevel, 1)); ?>;
            $('searchBoxProject').getElementById('category_id').value = categoriesArray[searchboxcategory_id].category_id;
            $('searchBoxProject').getElementById('subcategory_id').value = categoriesArray[searchboxcategory_id].subcategory_id;
            $('searchBoxProject').getElementById('subsubcategory_id').value = categoriesArray[searchboxcategory_id].subsubcategory_id;
            $('searchBoxProject').getElementById('categoryname').value = encodeURIComponent(categoriesArray[searchboxcategory_id].categoryname);
            $('searchBoxProject').getElementById('subcategoryname').value = encodeURIComponent(categoriesArray[searchboxcategory_id].subcategoryname);
            $('searchBoxProject').getElementById('subsubcategoryname').value = encodeURIComponent(categoriesArray[searchboxcategory_id].subsubcategoryname);
        }

        $('searchBoxProject').submit();
    }
    var temp;
    en4.core.runonce.add(function ()
    {
        var item_count = 0;
        if ($('titleAjax')) {
            var contentAutocomplete = new Autocompleter.Request.JSON('titleAjax', '<?php echo $this->url(array('action' => 'get-search-projects'), "sitecrowdfunding_project_general", true) ?>', {
                'postVar': 'text',
                'minLength': 1,
                'selectMode': 'pick',
                'autocompleteType': 'tag',
                'className': 'tag-autosuggest seaocore-autosuggest',
                'customChoices': true,
                'filterSubset': true,
                'multiple': false,
                'injectChoice': function (token) {
                    temp = token;
                    if (typeof token.label != 'undefined') {
                        if (token.sitecrowdfunding_url != 'seeMoreLink') {
                            var choice = new Element('li', {'class': 'autocompleter-choices', 'html': token.photo, 'id': token.label, 'sitecrowdfunding_url': token.sitecrowdfunding_url, onclick: 'javascript:getPageResults("' + token.sitecrowdfunding_url + '")'});
                            new Element('div', {'html': this.markQueryValue(token.label), 'class': 'autocompleter-choice'}).inject(choice);
                            this.addChoiceEvents(choice).inject(this.choices);
                            choice.store('autocompleteChoice', token);
                        }
                        if (token.sitecrowdfunding_url == 'seeMoreLink') {
                            var titleAjax = $('titleAjax').value;
                            var choice = new Element('li', {'class': 'autocompleter-choices1', 'html': '', 'id': 'stopevent', 'sitecrowdfunding_url': ''});
                            new Element('div', {'html': 'See More Results for ' + titleAjax, 'class': 'autocompleter-choicess', onclick: 'javascript:Seemore()'}).inject(choice);
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
                            temp = selected.retrieve('autocompleteChoice');
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
        }
        if ($('locationSearch')) {
            var locationSearchField = <?php echo isset($_GET['locationSearch']) ? 1 : 0; ?>;
            locationAutoSuggest('<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.countrycities'); ?>', 'locationSearch', '');

            if (!locationSearchField) {

                var params = {
                    'detactLocation': <?php echo $this->locationDetection; ?>,
                    'fieldName': 'locationSearch',
                    'noSendReq': 1,
                    'locationmilesFieldName': 'locationmilesSearch',
                    'locationmiles': <?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.locationdefaultmiles', 1000); ?>,
                    'reloadPage': 1,
                };

                en4.seaocore.locationBased.startReq(params);
            }
        }
    });

    function Seemore() {
        $('stopevent').removeEvents('click');
        var url = '<?php echo $this->url(array('action' => 'browse'), "sitecrowdfunding_project_general", true); ?>';
        window.location.href = url + "?titleAjax=" + encodeURIComponent($('titleAjax').value);
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
</script>