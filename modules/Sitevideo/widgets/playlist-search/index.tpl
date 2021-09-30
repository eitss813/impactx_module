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
$apiKey = Engine_Api::_()->seaocore()->getGoogleMapApiKey();
$this->headScript()->appendFile("https://maps.googleapis.com/maps/api/js?libraries=places&key=$apiKey");
$this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/core.js');
?>
<?php $this->headLink()->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/styles/styles.css'); ?>

<script type="text/javascript">
    var pageAction = function (page) {
        $('page').value = page;
        $('filter_form').submit();
    };

    var searchSitevideos = function () {

        var formElements = $('filter_form').getElements('li');
        formElements.each(function (el) {
            var field_style = el.style.display;
            if (field_style == 'none') {
                el.destroy();
            }
        });

        if (Browser.Engine.trident) {
            document.getElementById('filter_form').submit();
        } else {
            $('filter_form').submit();
        }
    };

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
</script>

<?php if ($this->viewType == 'horizontal'): ?>
    <div class="seaocore_searchform_criteria <?php
    if ($this->whatWhereWithinmile): echo "seaocore_searchform_criteria_advanced";
    endif;
    if ($this->viewType == 'horizontal'): echo " seaocore_search_horizontal";
    endif;
    ?>">
             <?php echo $this->form->render($this); ?>
    </div>
<?php else: ?>
    <div class="seaocore_searchform_criteria">
        <?php echo $this->form->render($this); ?>
    </div>
<?php endif; ?>

<script type="text/javascript">

    en4.core.runonce.add(function ()
    {
        var item_count = 0;
        var contentAutocomplete = new Autocompleter.Request.JSON('video_title', '<?php echo $this->url(array('action' => 'get-search-videos'), "sitevideo_video_general", true) ?>', {
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
                        var search = $('video_title').value;
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
        var url = '<?php echo $this->url(array('action' => 'browse'), "sitevideo_video_general", true); ?>';
        window.location.href = url + "?search=" + encodeURIComponent($('video_title').value);
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