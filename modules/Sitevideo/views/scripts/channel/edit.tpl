<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: edit.tpl 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
$cateDependencyArray = Engine_Api::_()->getDbTable('channelCategories', 'sitevideo')->getCatDependancyArray();
$subCateDependencyArray = Engine_Api::_()->getDbTable('channelCategories', 'sitevideo')->getSubCatDependancyArray();
?>
<?php
$this->headScript()
        ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Observer.js')
        ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.js')
        ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Local.js')
        ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Request.js');
?>

<!--WE ARE NOT USING STATIC BASE URL BECAUSE SOCIAL ENGINE ALSO NOT USE FOR THIS JS-->
<!--CHECK HERE Engine_View_Helper_TinyMce => protected function _renderScript()-->
<?php $this->tinyMCESEAO()->addJS(); ?>
<?php if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.channel.edit.url', 0)): ?>
    <script type="text/javascript">

        window.addEvent('domready', function () {
            var e4 = $('channel_url_msg-wrapper');
            $('channel_url_msg-wrapper').setStyle('display', 'none');
            var channelurlcontainer = $('channel_url-element');
            var language = '<?php echo $this->string()->escapeJavascript($this->translate('Check Availability')) ?>';
            var newdiv = document.createElement('div');
            newdiv.id = 'url_varify';
            newdiv.innerHTML = "<a href='javascript:void(0);'  name='check_availability' id='check_availability' onclick='ChannelUrlBlur();return false;' class='check_availability_button'>" + language + "</a> <br />";
            channelurlcontainer.insertBefore(newdiv, channelurlcontainer.childNodes[2]);
        });

        function ChannelUrlBlur(channel_id) {
            if ($('channel_url_alert') == null) {
                var channelurlcontainer = $('channel_url-element');
                var newdiv = document.createElement('span');
                newdiv.id = 'channel_url_alert';
                newdiv.innerHTML = '<img src="' + en4.core.staticBaseUrl + 'application/modules/Sitevideo/externals/images/loading.gif" />';
                channelurlcontainer.insertBefore(newdiv, channelurlcontainer.childNodes[3]);
            }
            else {
                $('channel_url_alert').innerHTML = '<img src="' + en4.core.staticBaseUrl + 'application/modules/Sitevideo/externals/images/loading.gif" />';
            }
            var channel_url = $('channel_url').value;
            en4.core.request.send(new Request.JSON({
                url: en4.core.baseUrl + 'sitevideo/index/channelurlvalidation',
                method: 'get',
                data: {
                    channel_url: channel_url, check_url: 1,
                    channel_id: channel_id,
                    format: 'html'
                },
                onSuccess: function (responseJSON) {
                    if (responseJSON.success == 0) {
                        $('channel_url_alert').innerHTML = responseJSON.error_msg;
                        if ($('channel_url_alert')) {
                            $('channel_url_alert').innerHTML = responseJSON.error_msg;
                        }
                    }
                    else {
                        $('channel_url_alert').innerHTML = responseJSON.success_msg;
                        if ($('channel_url_alert')) {
                            $('channel_url_alert').innerHTML = responseJSON.success_msg;
                        }
                    }
                }
            }));
        }

    </script>
<?php endif; ?>
<?php
/* Include the common user-end field switching javascript */
echo $this->partial('_jsSwitch.tpl', 'fields', array(
        //'topLevelId' => (int) @$this->topLevelId,
        //'topLevelValue' => (int) @$this->topLevelValue
))
?>

<?php include_once APPLICATION_PATH . '/application/modules/Sitevideo/views/scripts/_DashboardNavigation.tpl'; ?>
<div class="sitevideo_dashboard_content">

    <?php echo $this->partial('application/modules/Sitevideo/views/scripts/dashboard/header.tpl', array('channel' => $this->channel)); ?>
    <div class="sitevideo_video_form">
        <?php echo $this->form->render(); ?>
    </div>	
</div>

</div>

<script>
    var subcatid = '<?php echo $this->channel->subcategory_id; ?>';

    var cateDependencyArray = '<?php echo json_encode($cateDependencyArray); ?>';

    var submitformajax = 0;
    var show_subcat = 1;
    var cateDependencyArray = new Array();
    var subCateDependencyArray = new Array();
<?php foreach ($cateDependencyArray as $cat) : ?>
        cateDependencyArray.push(<?php echo $cat ?>);
<?php endforeach; ?>
<?php foreach ($subCateDependencyArray as $cat) : ?>
        subCateDependencyArray.push(<?php echo $cat ?>);
<?php endforeach; ?>


    window.addEvent('domready', function () {
<?php if ($this->profileType): ?>
            $('<?php echo '0_0_' . $this->defaultProfileId ?>').value = <?php echo $this->profileType ?>;
            changeFields($('<?php echo '0_0_' . $this->defaultProfileId ?>'));
<?php endif; ?>
    });

    var prefieldForm = function () {
<?php
$defaultProfileId = "0_0_" . $this->defaultProfileId;
foreach ($this->form->getSubForms() as $subForm) {
    foreach ($subForm->getElements() as $element) {

        $elementGetName = $element->getName();
        $elementGetValue = $element->getValue();
        $elementGetType = $element->getType();

        if ($elementGetName != $defaultProfileId && $elementGetName != '' && $elementGetName != null && $elementGetValue != '' && $elementGetValue != null) {

            if (!is_array($elementGetValue) && $elementGetType == 'Engine_Form_Element_Radio') {
                ?>
                        $('<?php echo $elementGetName . "-" . $elementGetValue ?>').checked = 1;
            <?php } elseif (!is_array($elementGetValue) && $elementGetType == 'Engine_Form_Element_Checkbox') { ?>
                        $('<?php echo $elementGetName ?>').checked = <?php echo $elementGetValue ?>;
                <?php
            } elseif (is_array($elementGetValue) && ($elementGetType == 'Engine_Form_Element_MultiCheckbox' || $elementGetType == 'Fields_Form_Element_Ethnicity' || $elementGetType == 'Fields_Form_Element_LookingFor' || $elementGetType == 'Fields_Form_Element_PartnerGender')) {
                foreach ($elementGetValue as $key => $value) {
                    ?>
                            $('<?php echo $elementGetName . "-" . $value ?>').checked = 1;
                    <?php
                }
            } elseif (is_array($elementGetValue) && $elementGetType == 'Engine_Form_Element_Multiselect') {
                foreach ($elementGetValue as $key => $value) {
                    $key_temp = array_search($value, array_keys($element->options));
                    if ($key !== FALSE) {
                        ?>
                                $('<?php echo $elementGetName ?>').options['<?php echo $key_temp ?>'].selected = 1;
                        <?php
                    }
                }
            } elseif (!is_array($elementGetValue) && ($elementGetType == 'Engine_Form_Element_Text' || $elementGetType == 'Engine_Form_Element_Textarea' || $elementGetType == 'Fields_Form_Element_AboutMe' || $elementGetType == 'Fields_Form_Element_Aim' || $elementGetType == 'Fields_Form_Element_City' || $elementGetType == 'Fields_Form_Element_Facebook' || $elementGetType == 'Fields_Form_Element_FirstName' || $elementGetType == 'Fields_Form_Element_Interests' || $elementGetType == 'Fields_Form_Element_LastName' || $elementGetType == 'Fields_Form_Element_Location' || $elementGetType == 'Fields_Form_Element_Twitter' || $elementGetType == 'Fields_Form_Element_Website' || $elementGetType == 'Fields_Form_Element_ZipCode')) {
                ?>
                        $('<?php echo $elementGetName ?>').value = "<?php echo $this->string()->escapeJavascript($elementGetValue, false) ?>";
            <?php } elseif (!is_array($elementGetValue) && $elementGetType != 'Engine_Form_Element_Date' && $elementGetType != 'Fields_Form_Element_Birthdate' && $elementGetType != 'Engine_Form_Element_Heading') { ?>
                        $('<?php echo $elementGetName ?>').value = "<?php echo $this->string()->escapeJavascript($elementGetValue, false) ?>";
                <?php
            }
        }
    }
}
?>
    };
<?php if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.tags.enabled', 1)): ?>
        en4.core.runonce.add(function ()
        {
            new Autocompleter.Request.JSON('tags', '<?php echo $this->url(array('module' => 'seaocore', 'controller' => 'index', 'action' => 'tag-suggest', 'resourceType' => 'sitevideo_channel'), 'default', true) ?>', {
                'postVar': 'text',
                'minLength': 1,
                'selectMode': 'pick',
                'autocompleteType': 'tag',
                'className': 'tag-autosuggest',
                'customChoices': true,
                'filterSubset': true, 'multiple': true,
                'injectChoice': function (token) {
                    var choice = new Element('li', {'class': 'autocompleter-choices', 'value': token.label, 'id': token.id});
                    new Element('div', {'html': this.markQueryValue(token.label), 'class': 'autocompleter-choice'}).inject(choice);
                    choice.inputValue = token;
                    this.addChoiceEvents(choice).inject(this.choices);
                    choice.store('autocompleteChoice', token);
                }
            });
        });
<?php endif; ?>

    var getProfileType = function (category_id) {
        var mapping = <?php echo Zend_Json_Encoder::encode(Engine_Api::_()->getDbtable('channelCategories', 'sitevideo')->getMapping(array('category_id', 'profile_type'))); ?>;
        for (i = 0; i < mapping.length; i++) {
            if (mapping[i].category_id == category_id)
                return mapping[i].profile_type;
        }
        return 0;
    };
    en4.core.runonce.add(function () {
        var defaultProfileId = '<?php echo '0_0_' . $this->defaultProfileId ?>' + '-wrapper';
        if ($type($(defaultProfileId)) && typeof $(defaultProfileId) != 'undefined') {
            $(defaultProfileId).setStyle('display', 'none');
        }
    });

    var subcategories = function (category_id, subcatid, subcatname, subsubcatid)
    {
        if (subcatid > 0) {
            changesubcategory(subcatid, subsubcatid);
        }
      
        
        if ($('subsubcategory_backgroundimage'))
            $('subcategory_backgroundimage').style.display = 'block';
        if ($('subcategory_id'))
            $('subcategory_id').style.display = 'none';
        if ($('subsubcategory_id'))
            $('subsubcategory_id').style.display = 'none';
        if ($('subcategory_id-label'))
            $('subcategory_id-label').style.display = 'none';
        if ($('subcategory_backgroundimage'))
            $('subcategory_backgroundimage').innerHTML = '<div class="form-label"></div><div  class="form-element"><center><img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitevideo/externals/images/loading.gif" /></center></div>';

        if ($('buttons-wrapper')) {
            $('buttons-wrapper').style.display = 'none';
        }
        if ($('subsubcategory_id-wrapper'))
            $('subsubcategory_id-wrapper').style.display = 'none';
        if ($('subsubcategory_id-label'))
            $('subsubcategory_id-label').style.display = 'none';
        var url = '<?php echo $this->url(array('action' => 'sub-category'), 'sitevideo_general', true); ?>';
        en4.core.request.send(new Request.JSON({
            url: url,
            data: {
                format: 'json',
                category_id_temp: category_id,
                showAllCategories: 0
            },
            onSuccess: function (responseJSON) {
                if ($('buttons-wrapper')) {
                    $('buttons-wrapper').style.display = 'block';
                }
                if ($('subcategory_backgroundimage'))
                    $('subcategory_backgroundimage').style.display = 'none';

                clear('subcategory_id');
                var subcatss = responseJSON.subcats;
                addOption($('subcategory_id'), " ", '0');
                for (i = 0; i < subcatss.length; i++) {
                    addOption($('subcategory_id'), subcatss[i]['category_name'], subcatss[i]['category_id']);
                    if (show_subcat == 0) {
                        if ($('subcategory_id'))
                            $('subcategory_id').disabled = 'disabled';
                        if ($('subsubcategory_id'))
                            $('subsubcategory_id').disabled = 'disabled';
                    }
                    if ($('subcategory_id')) {
                        $('subcategory_id').value = '<?php echo $this->channel->subcategory_id; ?>';
                    }
                }

                if (category_id == 0) {
                    clear('subcategory_id');
                    if ($('subcategory_id'))
                        $('subcategory_id').style.display = 'none';
                    if ($('subcategory_id-label'))
                        $('subcategory_id-label').style.display = 'none';
                }
            }
        }), {
            "force": true
        });
    };
    function in_array(ArrayofCategories, value) {
        for (var i = 0; i < ArrayofCategories.length; i++) {
            if (ArrayofCategories[i] == value) {
                return true;
            }
        }
        return false;
    }

    var changesubcategory = function (subcatid, subsubcatid)
    {
        if ($('buttons-wrapper')) {
            $('buttons-wrapper').style.display = 'none';
        }

        if (!in_array(subCateDependencyArray, subcatid)) {
            if ($('subsubcategory_id-wrapper'))
                $('subsubcategory_id-wrapper').style.display = 'none';
            if ($('subsubcategory_id-label'))
                $('subsubcategory_id-label').style.display = 'none';
            if ($('buttons-wrapper')) {
                $('buttons-wrapper').style.display = 'block';
            }
            return;
        }
        if ($('subsubcategory_backgroundimage'))
            $('subsubcategory_backgroundimage').style.display = 'block';
        if ($('subsubcategory_id'))
            $('subsubcategory_id').style.display = 'none';
        if ($('subsubcategory_id-label'))
            $('subsubcategory_id-label').style.display = 'none';
        if ($('subsubcategory_backgroundimage'))
            $('subsubcategory_backgroundimage').innerHTML = '<div class="form-label"></div><div  class="form-element"><center><img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitevideo/externals/images/loading.gif" /></center></div>';


        if ($('buttons-wrapper')) {
            $('buttons-wrapper').style.display = 'none';
        }
        var url = '<?php echo $this->url(array('action' => 'subsub-category'), 'sitevideo_general', true); ?>';
        var request = new Request.JSON({
            url: url,
            data: {
                format: 'json',
                subcategory_id_temp: subcatid,
                showAllCategories: 0
            },
            onSuccess: function (responseJSON) {
                if ($('buttons-wrapper')) {
                    $('buttons-wrapper').style.display = 'block';
                }
                if ($('subsubcategory_backgroundimage'))
                    $('subsubcategory_backgroundimage').style.display = 'none';

                clear('subsubcategory_id');
                var subsubcatss = responseJSON.subsubcats;
                if ($('subsubcategory_id')) {
                    addSubOption($('subsubcategory_id'), " ", '0');
                    for (i = 0; i < subsubcatss.length; i++) {
                        addSubOption($('subsubcategory_id'), subsubcatss[i]['category_name'], subsubcatss[i]['category_id']);
                        if ($('subsubcategory_id')) {
                            $('subsubcategory_id').value = '<?php echo $this->channel->subsubcategory_id; ?>';
                        }
                    }
                }
            }
        });
        request.send();
    };

    function clear(ddName)
    {
        if (document.getElementById(ddName)) {
            for (var i = (document.getElementById(ddName).options.length - 1); i >= 0; i--)
            {
                document.getElementById(ddName).options[ i ] = null;
            }
        }
    }
    function addOption(selectbox, text, value)
    {
        if ($('subcategory_id')) {
            var optn = document.createElement("OPTION");
            optn.text = text;
            optn.value = value;

            if (optn.text != '' && optn.value != '') {
                if ($('subcategory_id'))
                    $('subcategory_id').style.display = 'block';
                if ($('subcategory_id-wrapper'))
                    $('subcategory_id-wrapper').style.display = 'block';
                if ($('subcategory_id-label'))
                    $('subcategory_id-label').style.display = 'block';
                selectbox.options.add(optn);
            } else {
                if ($('subcategory_id'))
                    $('subcategory_id').style.display = 'none';
                if ($('subcategory_id-wrapper'))
                    $('subcategory_id-wrapper').style.display = 'none';
                if ($('subcategory_id-label'))
                    $('subcategory_id-label').style.display = 'none';
                selectbox.options.add(optn);
            }
        }
    }

    var cat = '<?php echo $this->channel->category_id ?>';
    if (cat != '') {
        subcatid = '<?php echo $this->channel->subcategory_id; ?>';
        subsubcatid = '<?php echo $this->channel->subsubcategory_id; ?>';
        var subcatname = '<?php echo $this->subcategory_name; ?>';
        subcategories(cat, subcatid, subcatname, subsubcatid);
    }
    function addSubOption(selectbox, text, value)
    {
        if ($('subsubcategory_id')) {
            var optn = document.createElement("OPTION");
            optn.text = text;
            optn.value = value;
            if (optn.text != '' && optn.value != '') {
                if ($('subsubcategory_id'))
                    $('subsubcategory_id').style.display = 'block';
                if ($('subsubcategory_id-wrapper'))
                    $('subsubcategory_id-wrapper').style.display = 'block';
                if ($('subsubcategory_id-label'))
                    $('subsubcategory_id-label').style.display = 'block';
                selectbox.options.add(optn);
            } else {
                if ($('subsubcategory_id'))
                    $('subsubcategory_id').style.display = 'none';
                if ($('subsubcategory_id-wrapper'))
                    $('subsubcategory_id-wrapper').style.display = 'none';
                if ($('subsubcategory_id-label'))
                    $('subsubcategory_id-label').style.display = 'none';
                selectbox.options.add(optn);
            }
        }
    }
</script>