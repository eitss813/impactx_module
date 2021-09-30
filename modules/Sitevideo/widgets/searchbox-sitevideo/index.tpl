<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
$baseUrl = $this->layout()->staticBaseUrl;
$this->headLink()->prependStylesheet($baseUrl . 'application/modules/Sitevideo/externals/styles/style_sitevideo.css');
?>
<div class="sitevideo_form_quick_search">
    <?php echo $this->form->setAttrib('class', 'sitevideo-search-box')->render($this) ?>
</div>	
<?php
$this->headScript()
        ->appendFile($baseUrl . 'externals/autocompleter/Observer.js')
        ->appendFile($baseUrl . 'externals/autocompleter/Autocompleter.js')
        ->appendFile($baseUrl . 'externals/autocompleter/Autocompleter.Local.js')
        ->appendFile($baseUrl . 'externals/autocompleter/Autocompleter.Request.js');
?>
<?php
$apiKey = Engine_Api::_()->seaocore()->getGoogleMapApiKey();
$this->headScript()->appendFile("https://maps.googleapis.com/maps/api/js?libraries=places&key=$apiKey");
?>
<script type="text/javascript">

    var doSearching = function (searchboxcategory_id) {

        var categoryElementExist = <?php echo $this->categoryElementExist; ?>;
        var searchboxcategory_id = 0;
        if (categoryElementExist == 1) {
            searchboxcategory_id = $('ajaxcategory_id').value;
        }

        if (searchboxcategory_id != 0) {

            var categoriesArray = <?php echo Zend_Json_Encoder::encode(Engine_Api::_()->getDbtable('channelCategories', 'sitevideo')->getCategoriesDetails($this->categoriesLevel)); ?>;
            $('searchBox').getElementById('category_id').value = categoriesArray[searchboxcategory_id].category_id;
            $('searchBox').getElementById('subcategory_id').value = categoriesArray[searchboxcategory_id].subcategory_id;
        }
        $('searchBox').submit();
    }

    en4.core.runonce.add(function ()
    {
        var item_count = 0;
        var contentAutocomplete = new Autocompleter.Request.JSON('search', '<?php echo $this->url(array('action' => 'get-search-channels'), "sitevideo_general", true) ?>', {
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
                    if (token.sitevideo_url != 'seeMoreLink') {
                        var choice = new Element('li', {'class': 'autocompleter-choices', 'html': token.video, 'id': token.label, 'sitevideo_url': token.sitevideo_url, onclick: 'javascript:getPageResults("' + token.sitevideo_url + '")'});
                        new Element('div', {'html': this.markQueryValue(token.label), 'class': 'autocompleter-choice'}).inject(choice);
                        this.addChoiceEvents(choice).inject(this.choices);
                        choice.store('autocompleteChoice', token);
                    }
                    if (token.sitevideo_url == 'seeMoreLink') {
                        var search = $('search').value;
                        var choice = new Element('li', {'class': 'autocompleter-choices1', 'html': '', 'id': 'stopevent', 'sitevideo_url': ''});
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
                        var url = selected.retrieve('autocompleteChoice').sitevideo_url;
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
        var url = '<?php echo $this->url(array('action' => 'browse'), "sitevideo_general", true); ?>';
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
</script>