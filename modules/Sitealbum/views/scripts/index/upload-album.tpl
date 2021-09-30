<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitealbum
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: upload.tpl 2011-08-026 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
$apiKey = Engine_Api::_()->seaocore()->getGoogleMapApiKey();
$this->headScript()->appendFile("https://maps.googleapis.com/maps/api/js?libraries=places&key=$apiKey");
?>

<?php
/* Include the common user-end field switching javascript */
echo $this->partial('_jsSwitch.tpl', 'fields', array(
        //'topLevelId' => (int) @$this->topLevelId,
        //'topLevelValue' => (int) @$this->topLevelValue
));
?>

<div class="o_hidden seao_add_photo_lightbox_header">
    <div class="fleft">
        <h3><?php echo $this->translate('Create Photo Album') ?></h3>
    </div>
    <div class="fright txt_right">


        <div class="seaocore_button fleft">
            <a class="buttonlink" href="javascript:void(0);" id="demo1-browse"><i class="seaocore_icon_add"></i><?php echo $this->translate('Add More Photos') ?></a>
        </div>
        <?php if (Engine_Api::_()->authorization()->isAllowed('album', $this->viewer(), 'create')) : ?>
            <div class="seaocore_button fleft">
                <a class="buttonlink" href="<?php echo $this->url(array('action' => 'manage'), 'sitealbum_general', true); ?>"><?php echo $this->translate('My Albums') ?></a>
            </div>
        <?php endif; ?>
        <div class="seaocore_button fleft">
            <a class="buttonlink" href="javascript:void(0);" id="demo1-clear" style='display: none;'><?php echo $this->translate('Clear List') ?></a>
        </div>
        <?php if (Engine_Api::_()->sitealbum()->openAddNewPhotosInLightbox()) : ?>
            <a class="bold" name="cancel" id="cancel" type="button" href="javascript:void(0)" onclick="openCancelSmoothbox();"></a>
        <?php endif; ?>
    </div>
</div>

<script type="text/javascript">
    isFlash = true;
    function openCancelSmoothbox() {

        var fileids = document.getElementById('file');

        if (fileids.value == '') {
            SmoothboxSEAO.close();
        } else {
            Smoothbox.open('<div class="settings global_form_popup" style="width:96%;box-sizing:border-box;padding:2%;"><h3><?php echo $this->string()->escapeJavascript($this->translate("Cancel and Delete Photos?")) ?></h3><br /><div><?php echo $this->string()->escapeJavascript($this->translate("You have already added some photos to this album. Do you want to cancel and delete these photos?")) ?></div><br/><button onclick="deletePhotos();"><?php echo $this->string()->escapeJavascript($this->translate("Yes, Delete Photos")) ?></button> <?php echo $this->string()->escapeJavascript($this->translate("or")) ?> <a href="javascript:void(0);" onclick="albumSubmitForm();return false;"><?php echo $this->string()->escapeJavascript($this->translate("No, Finish Album")) ?></a></div>');
        }
    }

    function deletePhotos() {
        var fileids = document.getElementById('file');
        if (fileids.value.trim()) {
            request = new Request.JSON({
                'format': 'json',
                'url': en4.core.baseUrl + 'sitealbum/index/cancel-photos',
                'data': {
                    'photo_ids': fileids.value,
                    'isAjax': 1
                },
                'onSuccess': function (responseJSON) {
                    SmoothboxSEAO.close();
                    window.location.reload();
                }
            });
            request.send();
        }
    }

    function albumSubmitForm() {
        document.getElementById('form-upload').submit();
        return false;
    }
<?php if (!Engine_Api::_()->seaocore()->isMobile()): ?>
        if (!Autocompleter) {
            SmoothboxSEAO.addScriptFiles.push("<?php echo $this->layout()->staticBaseUrl ?>externals/autocompleter/Observer.js");
            SmoothboxSEAO.addScriptFiles.push("<?php echo $this->layout()->staticBaseUrl ?>externals/autocompleter/Autocompleter.js");
            SmoothboxSEAO.addScriptFiles.push("<?php echo $this->layout()->staticBaseUrl ?>externals/autocompleter/Autocompleter.Local.js");
            SmoothboxSEAO.addScriptFiles.push("<?php echo $this->layout()->staticBaseUrl ?>externals/autocompleter/Autocompleter.Request.js");
        }
<?php endif; ?>
    function openSmoothbox(photo_id) {
        Smoothbox.open('<div class="settings global_form_popup" style="width:96%;box-sizing:border-box;padding:2%;"><h3><?php echo $this->string()->escapeJavascript($this->translate("Make Album Main Photo")) ?></h3><br /><div><?php echo $this->string()->escapeJavascript($this->translate("Do you want to make this photo main photo of album?")) ?></div><br/><button onclick=setMainPhoto(' + photo_id + ');Smoothbox.close();><?php echo $this->string()->escapeJavascript($this->translate("Save")) ?></button> <?php echo $this->string()->escapeJavascript($this->translate("or")) ?> <a href="javascript:void(0);" onclick=closeSmoothbox(' + photo_id + ');><?php echo $this->string()->escapeJavascript($this->translate("Cancel")) ?></a></div>');
    }

    function closeSmoothbox(photo_id) {
        if ($$('.media_main_photo')) {
            $$('.media_main_photo').destroy();
        }
        Smoothbox.close();
    }

    var updateTextFields = function ()
    {
        var fieldToggleGroup = ['#title-wrapper', '#category_id-wrapper', '#description-wrapper', '#search-wrapper', '#sitealbum_location-wrapper',
            '#auth_view-wrapper', '#auth_comment-wrapper', '#auth_tag-wrapper', '#tags-wrapper'];
        fieldToggleGroup = $$(fieldToggleGroup.join(','))
        if ($('album').get('value') == 0) {
            fieldToggleGroup.show();
        } else {
            fieldToggleGroup.hide();
        }
    }
    en4.core.runonce.add(updateTextFields);

    setTimeout(function () {


        window.addEvent('domready', function () {
            if ($('sitealbum_location') && (('<?php echo!Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.locationspecific', 0); ?>') || ('<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.locationspecific', 0); ?>' && '<?php echo!Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.locationspecificcontent', 0); ?>'))) {
                var autocomplete = new google.maps.places.Autocomplete(document.getElementById('sitealbum_location'));
                google.maps.event.addListener(autocomplete, 'place_changed', function () {
                    var place = autocomplete.getPlace();
                    if (!place.geometry) {
                        return;
                    }

                    var address = '', country = '', state = '', zip_code = '', city = '';
                    var data = {};
                    if (place.address_components) {
                        var len_add = place.address_components.length;

                        for (var i = 0; i < len_add; i++) {
                            var types_location = place.address_components[i]['types'][0];
                            if (types_location === 'country') {
                                country = place.address_components[i]['long_name'];
                            } else if (types_location === 'administrative_area_level_1') {
                                state = place.address_components[i]['long_name'];
                            } else if (types_location === 'administrative_area_level_2') {
                                city = place.address_components[i]['long_name'];
                            } else if (types_location === 'zip_code') {
                                zip_code = place.address_components[i]['long_name'];
                            } else if (types_location === 'street_address') {
                                if (address === '')
                                    address = place.address_components[i]['long_name'];
                                else
                                    address = address + ',' + place.address_components[i]['long_name'];
                            } else if (types_location === 'locality') {
                                if (address === '')
                                    address = place.address_components[i]['long_name'];
                                else
                                    address = address + ',' + place.address_components[i]['long_name'];
                            } else if (types_location === 'route') {
                                if (address === '')
                                    address = place.address_components[i]['long_name'];
                                else
                                    address = address + ',' + place.address_components[i]['long_name'];
                            } else if (types_location === 'sublocality') {
                                if (address === '')
                                    address = place.address_components[i]['long_name'];
                                else
                                    address = address + ',' + place.address_components[i]['long_name'];
                            }
                        }
                    }
                    var locationParams = '{"location" :"' + document.getElementById('sitealbum_location').value + '","latitude" :"' + place.geometry.location.lat() + '","longitude":"' + place.geometry.location.lng() + '","formatted_address":"' + place.formatted_address + '","address":"' + address + '","country":"' + country + '","state":"' + state + '","zip_code":"' + zip_code + '","city":"' + city + '"}';
                    data.name = place.name;
                    data.google_id = place.id;
                    data.latitude = place.geometry.location.lat();
                    data.longitude = place.geometry.location.lng();
                    data.vicinity = (place.vicinity) ? place.vicinity : place.formatted_address;
                    data.icon = place.icon;
                    data.types = place.types.join(',');
                    data.prefixadd = data.types.indexOf('establishment') > -1 ? en4.core.language.translate('at') : en4.core.language.translate('in');
                    data.resource_guid = 0;
                    data.type = 'place';
                    data.reference = place.reference;
                    var dataHash = new Hash(data);
                    dataHashStr = dataHash.toQueryString();
                    document.getElementById('dataParams').value = dataHashStr;
                    document.getElementById('locationParams').value = locationParams;
                    var fileids = document.getElementById('file');
                    var vInputString = fileids.value;
                    var vArray = vInputString.split(" ");

                    if (vArray.length > 1) {
                        for (i = 0; i < (vArray.length - 1); i++) {
                            savePhotoLocationLink(vArray[i]);
                        }
                    }
                });
            }
        });
    }, 100);

<?php if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitealbum.tags.enabled', 1)): ?>
        en4.core.runonce.add(function ()
        {
            new Autocompleter.Request.JSON('tags', '<?php echo $this->url(array('module' => 'seaocore', 'controller' => 'index', 'action' => 'tag-suggest', 'resourceType' => 'album'), 'default', true) ?>', {
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
        var mapping = <?php echo Zend_Json_Encoder::encode(Engine_Api::_()->getDbTable('categories', 'sitealbum')->getMapping(array('category_id', 'profile_type'))); ?>;
        for (i = 0; i < mapping.length; i++) {
            if (mapping[i].category_id == category_id)
                return mapping[i].profile_type;
        }
        return 0;
    }
    en4.core.runonce.add(function () {
        var defaultProfileId = '<?php echo '0_0_' . $this->defaultProfileId ?>' + '-wrapper';
        if ($type($(defaultProfileId)) && typeof $(defaultProfileId) != 'undefined') {
            $(defaultProfileId).setStyle('display', 'none');
        }
    });
</script>
<?php if (Engine_Api::_()->seaocore()->isMobile()): ?>
    <style type="text/css">
        #form-upload #submitForm-wrapper {
            display: block;
        }
    </style>
<?php endif; ?>

<?php
$this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitealbum/externals/styles/style_sitealbum.css')
?>

<?php if (!Engine_Api::_()->seaocore()->isMobile()): ?>
    <div class="layout_left">
        <?php $fileElement = $this->form->file; ?>
        <?php $this->form->removeElement('file'); ?>
        <?php echo $this->form->render($this) ?>
    </div>
    <div class="layout_middle">
        <?php echo $fileElement; ?>
    </div>


    <?php
    $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitealbum/externals/scripts/core.js');
    ?>

    <script type="text/javascript">
        var uploadCount = 0;
        var extraData = <?php echo $this->jsonInline($this->data); ?>;

        function showMediaOptions(photo_id) {
            $('media_photo_options_' + photo_id).toggle();
        }

        function rotatePhoto(obj, photo_id) {
            $(obj).set('class', 'buttonlink icon_loading');
            en4.sitealbum.rotate(photo_id, 90).addEvent('complete', function () {
                obj.set('class', 'buttonlink icon_photos_rotate_ccw')
            }.bind(obj));
        }

        function updateTextareaValue(photo_id) {

            if ($('media_photo_description_value_' + photo_id).value == '')
                return;

            if ($('media_photo_description_' + photo_id)) {
                $('media_photo_description_' + photo_id).destroy();
            }

            var formObj = $('form-upload').getElementById('submitForm-wrapper');
            var photoDescription = new Element('input', {
                'type': 'hidden',
                'id': 'media_photo_description_' + photo_id,
                'value': $('media_photo_description_value_' + photo_id).value,
                'name': 'media_photo_description_' + photo_id
            });
            photoDescription.inject(formObj, 'after');
        }

        function updateTitleValue(photo_id) {

            if ($('media_photo_title_value_' + photo_id) == null)
                return;

            if ($('media_photo_title_value_' + photo_id) && $('media_photo_title_value_' + photo_id).value == '')
                return;

            if ($('media_photo_title_' + photo_id)) {
                $('media_photo_title_' + photo_id).destroy();
            }

            var formObj = $('form-upload').getElementById('submitForm-wrapper');
            var photoTitle = new Element('input', {
                'type': 'hidden',
                'id': 'media_photo_title_' + photo_id,
                'value': $('media_photo_title_value_' + photo_id).value,
                'name': 'media_photo_title_' + photo_id
            });
            photoTitle.inject(formObj, 'after');
        }

        function setMainPhoto(photo_id) {

            if ($$('.media_main_photo')) {
                $$('.media_main_photo').destroy();
            }

            var formObj = $('form-upload').getElementById('submitForm-wrapper');
            var mainPhotoId = new Element('input', {
                'type': 'hidden',
                'value': photo_id,
                'name': 'media_main_photo',
                'class': 'media_main_photo'
            });
            mainPhotoId.inject(formObj, 'after');
        }

        function cancelLocationLink(photo_id) {

            if ($('media_option_location_' + photo_id)) {
                $('media_option_location_' + photo_id).style.display = 'none';
            }

            if ($('media_photo_location_' + photo_id)) {
                $('media_photo_location_' + photo_id).style.display = 'none';
            }

            if ($('media_photo_save_location_' + photo_id)) {
                $('media_photo_save_location_' + photo_id).style.display = 'none';
            }

            if ($('media_photo_cancel_location_' + photo_id)) {
                $('media_photo_cancel_location_' + photo_id).style.display = 'none';
            }

            document.getElementById('media_photo_location_temporary_data_params_' + photo_id).value = '';
            document.getElementById('media_photo_location_temporary_params_' + photo_id).value = '';

        }

        function savePhotoLocationLink(photo_id) {

            if ($('media_photo_location_' + photo_id)) {
                $('media_photo_location_' + photo_id).style.display = 'none';
            }

            if ($('media_photo_save_location_' + photo_id)) {
                $('media_photo_save_location_' + photo_id).style.display = 'none';
            }

            if ($('media_photo_cancel_location_' + photo_id)) {
                $('media_photo_cancel_location_' + photo_id).style.display = 'none';
            }

            if ($('media_photo_location_temporary_data_params_' + photo_id).value) {
                $('media_photo_location_data_params_' + photo_id).value = document.getElementById('dataParams').value;
            } else {
                $('media_photo_location_data_params_' + photo_id).value = document.getElementById('dataParams').value;
            }

            if ($('media_photo_location_temporary_params_' + photo_id).value) {
                $('media_photo_location_params_' + photo_id).value = document.getElementById('locationParams').value;
            } else {
                $('media_photo_location_params_' + photo_id).value = document.getElementById('locationParams').value;
            }

            $('media_photo_location_temporary_data_params_' + photo_id).value = '';
            $('media_photo_location_temporary_params_' + photo_id).value = '';

            if ($('media_location_information_' + photo_id) && $('media_photo_location_' + photo_id).value) {
                $('media_location_information_' + photo_id).style.display = 'block';
                $('media_location_information_' + photo_id).innerHTML = '<div>' + $('media_photo_location_' + photo_id).value + '</div>' + '<a href="javascript:void(0);" onclick="deleteLocationMedia(' + photo_id + ')">X</a>';
            } else if ($('sitealbum_location').value) {
                $('media_location_information_' + photo_id).style.display = 'block';
                $('media_location_information_' + photo_id).innerHTML = '<div>' + $('sitealbum_location').value + '</div>' + '<a href="javascript:void(0);" onclick="deleteLocationMedia(' + photo_id + ')">X</a>';
                $('media_photo_location_' + photo_id).value = $('sitealbum_location').value;
            }

            if ($('media_photo_location_value_' + photo_id)) {
                $('media_photo_location_value_' + photo_id).destroy();
            }
            var value = '';
            if ($('media_photo_location_' + photo_id) && $('media_photo_location_' + photo_id).value) {
                value = $('media_photo_location_' + photo_id).value;
            } else if ($('sitealbum_location').value) {
                value = $('sitealbum_location').value;
            }

            var formObj = $('form-upload').getElementById('submitForm-wrapper');
            var photoLocationValue = new Element('input', {
                'type': 'hidden',
                'id': 'media_photo_location_value_' + photo_id,
                'value': value,
                'name': 'media_photo_location_value_' + photo_id
            });
            photoLocationValue.inject(formObj, 'after');
        }

        function deleteLocationMedia(photo_id) {
            $('media_location_information_' + photo_id).style.display = 'none';
            $('media_location_information_' + photo_id).innerHTML = '';
            $('media_photo_location_' + photo_id).value = '';
        }

        function makeLocationAsInput(photo_id) {

            if ($('media_option_location_' + photo_id)) {
                $('media_option_location_' + photo_id).style.display = 'block';
            }

            if ($('media_photo_location_' + photo_id)) {
                $('media_photo_location_' + photo_id).style.display = 'block';
            }

            if ($('media_photo_save_location_' + photo_id)) {
                $('media_photo_save_location_' + photo_id).style.display = 'block';
            }

            if ($('media_photo_cancel_location_' + photo_id)) {
                $('media_photo_cancel_location_' + photo_id).style.display = 'block';
            }
            setTimeout(function () {
                window.addEvent('domready', function () {
                    if ($('media_photo_location_' + photo_id) && (('<?php echo!Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.locationspecific', 0); ?>') || ('<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.locationspecific', 0); ?>' && '<?php echo!Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.locationspecificcontent', 0); ?>'))) {
                        var autocomplete = new google.maps.places.Autocomplete(document.getElementById('media_photo_location_' + photo_id));
                        google.maps.event.addListener(autocomplete, 'place_changed', function () {
                            var place = autocomplete.getPlace();
                            if (!place.geometry) {
                                return;
                            }

                            var address = '', country = '', state = '', zip_code = '', city = '';
                            var data = {};
                            if (place.address_components) {
                                var len_add = place.address_components.length;

                                for (var i = 0; i < len_add; i++) {
                                    var types_location = place.address_components[i]['types'][0];
                                    if (types_location === 'country') {
                                        country = place.address_components[i]['long_name'];
                                    } else if (types_location === 'administrative_area_level_1') {
                                        state = place.address_components[i]['long_name'];
                                    } else if (types_location === 'administrative_area_level_2') {
                                        city = place.address_components[i]['long_name'];
                                    } else if (types_location === 'zip_code') {
                                        zip_code = place.address_components[i]['long_name'];
                                    } else if (types_location === 'street_address') {
                                        if (address === '')
                                            address = place.address_components[i]['long_name'];
                                        else
                                            address = address + ',' + place.address_components[i]['long_name'];
                                    } else if (types_location === 'locality') {
                                        if (address === '')
                                            address = place.address_components[i]['long_name'];
                                        else
                                            address = address + ',' + place.address_components[i]['long_name'];
                                    } else if (types_location === 'route') {
                                        if (address === '')
                                            address = place.address_components[i]['long_name'];
                                        else
                                            address = address + ',' + place.address_components[i]['long_name'];
                                    } else if (types_location === 'sublocality') {
                                        if (address === '')
                                            address = place.address_components[i]['long_name'];
                                        else
                                            address = address + ',' + place.address_components[i]['long_name'];
                                    }
                                }
                            }
                            var locationParams = '{"location" :"' + document.getElementById('media_photo_location_' + photo_id).value + '","latitude" :"' + place.geometry.location.lat() + '","longitude":"' + place.geometry.location.lng() + '","formatted_address":"' + place.formatted_address + '","address":"' + address + '","country":"' + country + '","state":"' + state + '","zip_code":"' + zip_code + '","city":"' + city + '"}';
                            data.name = place.name;
                            data.google_id = place.id;
                            data.latitude = place.geometry.location.lat();
                            data.longitude = place.geometry.location.lng();
                            data.vicinity = (place.vicinity) ? place.vicinity : place.formatted_address;
                            data.icon = place.icon;
                            data.types = place.types.join(',');
                            data.prefixadd = data.types.indexOf('establishment') > -1 ? en4.core.language.translate('at') : en4.core.language.translate('in');
                            data.resource_guid = 0;
                            data.type = 'place';
                            data.reference = place.reference;
                            var dataHash = new Hash(data);
                            dataHashStr = dataHash.toQueryString();
                            document.getElementById('media_photo_location_temporary_data_params_' + photo_id).value = dataHashStr;
                            document.getElementById('media_photo_location_temporary_params_' + photo_id).value = locationParams;
                        });
                    }
                });
            }, 100);
        }

        function SortablesInstance() {
            return false;
            var SortablesInstance;
            $$('.demo-list > li').addClass('sortable');
            SortablesInstance = new Sortables($$('.demo-list'), {
                clone: true,
                constrain: true,
                //handle: 'span',
                onComplete: function (e) {
                    var ids = [];
                    $$('.demo-list > li').each(function (el) {
                        //if(el.get('id'))
                        ids.push(el.get('id').match(/\d+/)[0]);
                    });
                    var vArray = ids;
                    var photo_ids = '';
                    for (i = 0; i < (vArray.length); i++) {
                        photo_ids = photo_ids + vArray[i] + " ";
                    }
                    fileids = document.getElementById('file');
                    fileids.value = photo_ids;
                }
            });
        }

        function setPhotosDate(obj) {
            var fileids = document.getElementById('file');
            var vInputString = fileids.value;
            if (vInputString == "")
                return false;
            var vArray = vInputString.split(" ");
            if (obj.value == 1) {
                $('photo-add-date').style.display = 'none';
                for (i = 0; i < (vArray.length - 1); i++) {
                    setUseDateFromPhotos(vArray[i]);
                }
            } else if (obj.value == 2) {
                $('photo-add-date').style.display = 'none';
                for (i = 0; i < (vArray.length - 1); i++) {
                    setPickupdateValue(vArray[i]);
                }
            }
        }

        function setPickupdateValue(photo_id) {

            var photoDate = $('day-album').value;
            var photoMonth = $('month-album').value;
            var photoYear = $('year-album').value;
            var photoAddDateBlock = $('photo-add-date-block-' + photo_id);
            var monthNames = ["January", "February", "March", "April", "May", "June",
                "July", "August", "September", "October", "November", "December"];
            if (photoAddDateBlock) {
                if (photoAddDateBlock.getElementById("year-" + photo_id)) {
                    photoAddDateBlock.getElementById("year-" + photo_id).value = parseInt(photoYear);
                    photoAddDateBlock.getElementById("year-" + photo_id).text = parseInt(photoYear);
                }

                if (photoAddDateBlock.getElementById("month-" + photo_id)) {
                    showMonth(parseInt(photoMonth), photo_id);
                    photoAddDateBlock.getElementById("month-" + photo_id).value = parseInt(photoMonth);
                    photoAddDateBlock.getElementById("month-" + photo_id).options[parseInt(photoMonth)].text = monthNames[parseInt(photoMonth)];
                }

                if (photoAddDateBlock.getElementById("day-" + photo_id)) {
                    showDay(parseInt(photoDate), photo_id);
                    photoAddDateBlock.getElementById("day-" + photo_id).value = parseInt(photoDate);
                }
            }
        }

        function setUseDateFromPhotos(photo_id) {

            var photoDateValue = $('media_photo_previous_date_' + photo_id).value;
            var photoDateArray = photoDateValue.split("-");
            var photoDate = photoDateArray[2];
            var photoMonth = photoDateArray[1];
            var photoYear = photoDateArray[0];
            if ($('photo-add-date-block-' + photo_id)) {
                var photoAddDateBlock = $('photo-add-date-block-' + photo_id);
                if (photoAddDateBlock.getElementById("year-" + photo_id)) {
                    photoAddDateBlock.getElementById("year-" + photo_id).value = parseInt(photoYear);
                    photoAddDateBlock.getElementById("year-" + photo_id).text = parseInt(photoYear);
                }
                var monthNames = ["January", "February", "March", "April", "May", "June",
                    "July", "August", "September", "October", "November", "December"];
                if (photoAddDateBlock.getElementById("month-" + photo_id)) {
                    showMonth(parseInt(photoMonth), photo_id);
                    photoAddDateBlock.getElementById("month-" + photo_id).value = parseInt(photoMonth);
                    photoAddDateBlock.getElementById("month-" + photo_id).options[parseInt(photoMonth)].text = monthNames[parseInt(photoMonth)];
                }

                if (photoAddDateBlock.getElementById("day-" + photo_id)) {
                    showDay(parseInt(photoDate), photo_id);
                    photoAddDateBlock.getElementById("day-" + photo_id).value = parseInt(photoDate);
                }
            }
        }
    </script>

    <div style="display:none;" id="photo-add-date" >
        <select id="year-album" name="year">
            <option label="Year" value="Year" disabled="disabled"><?php echo $this->translate('Year'); ?></option>
            <?php $curYear = date('Y'); ?>
            <?php for ($i = 0; $i <= 110; $i++) : ?>
                <option label="<?php echo $curYear; ?>" value="<?php echo $curYear; ?>" <?php if ($i == 0): ?> selected="selected" <?php endif; ?>><?php echo $curYear; ?></option>
                <?php $curYear--; ?>
            <?php endfor; ?>
        </select>
        <a style="display:none;" id="addday-album"  onclick="showDay(0, 'album');" href="javascript:void(0);"><?php echo $this->translate('+ Add Day'); ?></a>
        <select id="day-album" name="day" style="display:block;"></select>
        <a onclick="showMonth(0, 'album');" href="javascript:void(0);" id="addmonth-album" style="display:none;"><?php echo $this->translate('+ Add Month'); ?></a>
        <select id="month-album" name="month" onblur="showAddmonth(2, 'album')" onclick="showMonth(1, 'album')" onchange="showAddday(2, 'album')" style="display:block;">
            <option label="Month" value="0"><?php echo $this->translate('Month'); ?></option>
            <?php $curMonth = (int) date('m'); ?>
            <?php for ($k = 1; $k <= 12; $k++): ?>
                <?php $month = date('F', mktime(0, 0, 0, $k, 1)); ?>
                <option label="<?php echo $month; ?>" value="<?php echo $k; ?>" <?php if ($k == $curMonth): ?> selected="selected" <?php endif; ?>><?php echo $this->translate($month); ?></option>
            <?php endfor; ?>
        </select>
        <button id="photo-add-date-save" onclick="savePhotoDate('album');"><?php echo $this->translate('Save'); ?></button><button id="photo-add-date-cancel" onclick="cancelPhotoDate('album');"><?php echo $this->translate('Cancel'); ?></button>
    </div>

    <script type="text/javascript">

        function savePhotoDate(ele) {
            if ($('photo-add-date-block-' + ele).getElementById("month-" + ele).value == 0 || $('photo-add-date-block-' + ele).getElementById("day-" + ele).value == 0)
                return false;
            if (ele == 'album') {
                var fullDate = $('photo-add-date-' + ele).getElementById("year-" + ele).value + '-' + $('photo-add-date-' + ele).getElementById("month-" + ele).value + '-' + $('photo-add-date-' + ele).getElementById("day-" + ele).value;
                $('photo-add-date').style.display = 'none';
            } else {
                var fullDate = $('photo-add-date-block-' + ele).getElementById("year-" + ele).value + '-' + $('photo-add-date-block-' + ele).getElementById("month-" + ele).value + '-' + $('photo-add-date-block-' + ele).getElementById("day-" + ele).value;
                $('photo-add-date-block-' + ele).style.display = 'none';
            }

            if ($('media_photo_previous_date_' + ele)) {
                $('media_photo_previous_date_' + ele).destroy();
            }
            var photoCurrentDateValue = new Element('input', {
                'type': 'hidden',
                'id': 'media_photo_previous_date_' + ele,
                'value': fullDate,
                'name': 'media_photo_previous_date_' + ele
            });
            photoCurrentDateValue.inject($('form-upload').getElementById('submitForm-wrapper'), 'after');
        }

        function cancelPhotoDate(ele) {
            if (ele == 'album') {
                $('photo-add-date').style.display = 'none';
            } else {
                $('photo-add-date-block-' + ele).style.display = 'none';
            }
        }

        var addDay = 0;
        var addMonth = 0;
        function showMonth(month, ele) {
            addMonth = month;
            document.getElementById('month-' + ele).style.display = 'block';
            var sel = document.getElementById("month-" + ele);
            var year = document.getElementById("year-" + ele);
            var selectedTextYear = year.options[year.selectedIndex].text;
            var selectedValueYear = year.options[year.selectedIndex].value;
            var currentYear = '<?php echo (int) date("Y"); ?>';
            //get the selected option
            var selectedTextMonth = sel.options[sel.selectedIndex].text;
            var selectedValueMonth = sel.options[sel.selectedIndex].value;

            var selday = document.getElementById("day-" + ele);
            //get the selected option
            selday.options[selday.selectedIndex].text = 0;
            selday.options[selday.selectedIndex].value = 0;

            if (selectedTextMonth != 'Month') {
                if (parseInt(selectedValueMonth) > '<?php echo (int) date("m"); ?>' && (currentYear == parseInt(selectedTextYear))) {
                    sel.selectedIndex = "Month";
                    document.getElementById('addday-' + ele).style.display = 'none';
                    document.getElementById('day-' + ele).style.display = 'none';
                    document.getElementById('day-' + ele).value = 0;
                }
                else {
                    document.getElementById('addday-' + ele).style.display = 'block';
                    document.getElementById('day-' + ele).style.display = 'none';
                    document.getElementById('day-' + ele).value = 0;
                }
            } else {
                document.getElementById('addday-' + ele).style.display = 'none';
                document.getElementById('day-' + ele).style.display = 'none';
            }
        }

        function showAddmonth(month, ele) {
            if (addMonth == 0 || month == 2) {
                addMonth = 0;
                var sel = document.getElementById("month-" + ele);
                //get the selected option
                var selectedText = sel.options[sel.selectedIndex].text;
                if (selectedText == 'Month') {
                    document.getElementById('month-' + ele).style.display = 'none';
                    document.getElementById('addday-' + ele).style.display = 'none';
                    document.getElementById('day-' + ele).style.display = 'none';
                    document.getElementById('day-' + ele).value = 0;
                }
            }
        }

        function showDay(day, ele) {
            addDay = day;
            clear('day-' + ele);
            if (document.getElementById('addday-' + ele))
                document.getElementById('addday-' + ele).style.display = 'none';
            if (document.getElementById('day-' + ele))
                document.getElementById('day-' + ele).style.display = 'block';
            if (document.getElementById('day-' + ele))
                addOptionDate($('day-' + ele), '<?php echo $this->translate("Day"); ?>', 0);
            if (document.getElementById('month-' + ele))
                var month_day = document.getElementById('month-' + ele).value;
            if (document.getElementById('year-' + ele))
                var year_day = document.getElementById('year-' + ele).value;
            var num = new Date(year_day, month_day, 0).getDate();
    <?php $curMonth = (int) date('m'); ?>
            var currentDate = '<?php echo (int) date('d'); ?>';
            if (month_day == '<?php echo (int) date("m"); ?>') {
                for (j = 1; j <= currentDate; j++) {
                    if (document.getElementById('day-' + ele))
                        addOptionDate($('day-' + ele), j, j);
                }
            } else {
                for (j = 1; j <= num; j++) {
                    if (document.getElementById('day-' + ele))
                        addOptionDate($('day-' + ele), j, j);
                }
            }
        }

        if ($('day-album')) {
            $('day-album').removeEvents().addEvent('blur', function (event) {
                showAddday(2, 'album');
            });
            $('day-album').removeEvents().addEvent('click', function (event) {
                showDay(1, 'album');
            });
            $('day-album').removeEvents().addEvent('change', function (event) {
                showAddday(2, 'album');
            });
        }

        function addOptionDate(selectbox, text, value)
        {
            var optn = document.createElement("OPTION");
            optn.text = text;
            optn.value = value;
            selectbox.options.add(optn);
        }

        function clear(ddName)
        {
            for (var k = (document.getElementById(ddName).options.length - 1); k >= 0; k--)
            {
                document.getElementById(ddName).options[ k ] = null;
            }
        }

        function showAddday(day, ele) {
            var sel = document.getElementById("day-" + ele);
            if (addDay == 0 || day == 2) {
                addDay = 0;
                //get the selected option
                var selectedText = sel.options[sel.selectedIndex].text;
                var selectedValue = sel.options[sel.selectedIndex].value;
                var selYear = document.getElementById("year-" + ele);
                var currentYear = '<?php echo (int) date("Y"); ?>';
                var selectedTextYear = selYear.options[selYear.selectedIndex].text;
                var selectedYearValue = selYear.options[selYear.selectedIndex].value;
                var selMonth = document.getElementById("month-" + ele);
                var currentMonth = selMonth.options[selMonth.selectedIndex].text;
                var selectedMonthValue = selMonth.options[selMonth.selectedIndex].value;
                if (selectedText == 'Day') {
                    document.getElementById('addday-' + ele).style.display = 'block';
                    document.getElementById('day-' + ele).style.display = 'none';
                }
                else {
                    if (parseInt(selectedValue) > '<?php echo (int) date("d"); ?>' && (currentYear == parseInt(selectedTextYear)) && parseInt(selectedMonthValue) == '<?php echo (int) date("m"); ?>') {
                        sel.selectedIndex = "Day";
                        sel.value = 0;
                    }
                    else {
                        document.getElementById('addday-' + ele).style.display = 'none';
                        document.getElementById('day-' + ele).style.display = 'block';
                    }
                }
            }
        }

        setTimeout(function () {
            window.addEvent('domready', function () {
                showDay(0, 'album');
                var sel = document.getElementById("day-album");
                var currentDate = '<?php echo (int) date("d"); ?>';
                sel.value = currentDate;
            });

        }, 100);

        function changeDateOptions(photo_id) {
            $('photo-add-date-block-' + photo_id).style.display = 'block';
        }


    </script>
<?php else : ?>
    <div class="sitealbum_add_photo_content" id="sitealbum_add_photo_content">
        <?php echo $this->form->render($this); ?>
        <div  class="layout_middle" id="photo_upload_form_right">
            <div class="form-elements" id="photo_upload_form_right_content"></div>
        </div>
    </div>
    <script type="text/javascript">
        setTimeout(function () {
            $('sitealbum_add_photo_content').getElementById('form-upload').firstChild.id = "video_upload_form_left";
            $('photo_upload_form_right').inject($('sitealbum_add_photo_content').getElementById('form-upload'));
            injectInRightWrapper(rightWrapperItems);
        }, 100);
        var rightWrapperItems = ["file"];
        function injectInRightWrapper(items) {
            items.each(function (value) {
                //$('sitealbum_add_photo_content').getElementById('form-upload').getElementById(value + '-wrapper').inject($('photo_upload_form_right_content'));
                //   $('video_upload_form_left').addClass("layout_left");
                $('file-label').hide();
                $$('.swiff-uploader-box').hide();
                $$('.demo-status').hide();
            });
        }
    </script>
<?php endif; ?>
<script type="text/javascript">
    en4.sitealbum.onPhotoUpload = function (file, json) {
        if (json.status == '1') {
            //SortablesInstance();
            var formObj = $('form-upload').getElementById('submitForm-wrapper');
            var datePhoto = json.currentDate;
            if ($('form-upload').elements["sitealbum_photo_date_method"] && $('form-upload').elements["sitealbum_photo_date_method"].value == 1) {
                datePhoto = json.datePhoto;
            }
            var photo_id = json.photo_id;
            file.addClass('file-success');
            file.id = 'thumbs-photo-' + photo_id;
            file.getElement('.file-size').destroy();
            file.getElement('.file-name').destroy();
            var el = file.getElement('.file-remove');
            el.innerHTML = "";
            el.inject(file.getElement('.file-info'), 'before');
            var mediaPhotoDetails = "<a class='buttonlink icon_photos_rotate_ccw' href='javascript:void(0)' onclick='rotatePhoto(this, " + json.photo_id + ")' title='<?php echo $this->translate("Rotate This Photo"); ?>'>&nbsp;</a><img id='media_photo_" + json.photo_id + "' style=''src=" + json.src + " />";
            var photoLocation = '';

            <?php if (SEA_PHOTOLIGHTBOX_EDITLOCATION && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitealbum.location', 1)): ?>

            var editLocationLink = "makeLocationAsInput(" + json.photo_id + ");return false;";
            var saveLocationLink = "savePhotoLocationLink(" + json.photo_id + ");return false;";
            var cancelLocationLink = "cancelLocationLink(" + json.photo_id + ");return false;";
            photoLocation = "<div><a href='javascript:void(0);' onclick='" + editLocationLink + "'><?php echo $this->translate("Edit Location") ?></a><div class='media_option_location' id='media_option_location_" + json.photo_id + "' style='display:none;'><ul><input type='text' id='media_photo_location_" + json.photo_id + "'  /></ul><button  onclick='" + saveLocationLink + "' id='media_photo_save_location_" + json.photo_id + "'><?php echo $this->translate("Save") ?></button><button id='media_photo_cancel_location_" + json.photo_id + "' onclick='" + cancelLocationLink + "' ><?php echo $this->translate("Cancel") ?></button></div></div>";
            if ($('media_photo_location_' + photo_id)) {
                $('media_photo_location_' + photo_id).style.display = 'block';
            }

            if ($('media_photo_location_params_' + photo_id)) {
                $('media_photo_location_params_' + photo_id).destroy();
            }

            if ($('media_photo_location_data_params_' + photo_id)) {
                $('media_photo_location_data_params_' + photo_id).destroy();
            }


            var photoLocationParams = new Element('input', {
                'type': 'hidden',
                'id': 'media_photo_location_params_' + photo_id,
                'name': 'media_photo_location_params_' + photo_id
            });
            photoLocationParams.inject(formObj, 'after');

            var photoDataLocation = new Element('input', {
                'type': 'hidden',
                'id': 'media_photo_location_data_params_' + photo_id,
                'name': 'media_photo_location_data_params_' + photo_id
            });
            photoDataLocation.inject(formObj, 'after');

            if ($('media_photo_location_temporary_params_' + photo_id)) {
                $('media_photo_location_temporary_params_' + photo_id).destroy();
            }

            if ($('media_photo_location_temporary_data_params_' + photo_id)) {
                $('media_photo_location_temporary_data_params_' + photo_id).destroy();
            }

            var photoLocationTempParams = new Element('input', {
                'type': 'hidden',
                'id': 'media_photo_location_temporary_params_' + photo_id,
                'name': 'media_photo_location_temporary_params_' + photo_id
            });
            photoLocationTempParams.inject(formObj, 'after');

            var photoDataLocationTemp = new Element('input', {
                'type': 'hidden',
                'id': 'media_photo_location_temporary_data_params_' + photo_id,
                'name': 'media_photo_location_temporary_data_params_' + photo_id
            });
            photoDataLocationTemp.inject(formObj, 'after');

            <?php endif; ?>

            if ($('photo-add-date-block-' + json.photo_id)) {
                $('photo-add-date-block-' + json.photo_id).destroy();
            }

            var photoAddDateBlock = new Element('div', {
                'id': 'photo-add-date-block-' + photo_id,
                'class': 'media_option_add_date'
            });
            photoAddDateBlock.inject(file.getElement('.file-info'), 'after');
            photoAddDateBlock.innerHTML = $('photo-add-date').innerHTML;
            photoAddDateBlock.style.display = 'none';
            photoAddDateBlock.getElementById('year-album').id = 'year-' + photo_id;
            photoAddDateBlock.getElementById('addmonth-album').id = 'addmonth-' + photo_id;
            photoAddDateBlock.getElementById('addmonth-' + photo_id).setAttribute('onclick', "showMonth(0, " + photo_id + ")");
            photoAddDateBlock.getElementById('month-album').id = 'month-' + photo_id;
            photoAddDateBlock.getElementById('month-' + photo_id).setAttribute('onblur', "showAddmonth(2, " + photo_id + ")");
            photoAddDateBlock.getElementById('month-' + photo_id).setAttribute('onclick', "showMonth(1, " + photo_id + ")");
            photoAddDateBlock.getElementById('month-' + photo_id).setAttribute('onchange', "showAddday(2, " + photo_id + ")");
            photoAddDateBlock.getElementById('addday-album').id = 'addday-' + photo_id;
            photoAddDateBlock.getElementById('addday-' + photo_id).setAttribute('onclick', "showDay(0, " + photo_id + ")");
            photoAddDateBlock.getElementById('day-album').id = 'day-' + photo_id;

            photoAddDateBlock.getElementById('photo-add-date-save').id = 'photo-add-date-save-' + photo_id;
            photoAddDateBlock.getElementById('photo-add-date-save-' + photo_id).setAttribute('onclick', "savePhotoDate(" + photo_id + ")");

            photoAddDateBlock.getElementById('photo-add-date-cancel').id = 'photo-add-date-cancel-' + photo_id;
            photoAddDateBlock.getElementById('photo-add-date-cancel-' + photo_id).setAttribute('onclick', "cancelPhotoDate(" + photo_id + ")");

            if (photoAddDateBlock.getElementById('day-' + photo_id)) {

                photoAddDateBlock.getElementById('day-' + photo_id).removeEvents().addEvent('blur', function (event) {
                    showAddday(2, photo_id);
                });

                photoAddDateBlock.getElementById('day-' + photo_id).removeEvents().addEvent('click', function (event) {
                    showDay(1, photo_id);
                });

                photoAddDateBlock.getElementById('day-' + photo_id).removeEvents().addEvent('change', function (event) {
                    showAddday(2, photo_id);
                });
            }
            var photoDateArray = datePhoto.split("-");
            var photoDate = photoDateArray[2];
            var photoMonth = photoDateArray[1];
            var photoYear = photoDateArray[0];

            if (photoAddDateBlock.getElementById("year-" + photo_id)) {
                photoAddDateBlock.getElementById("year-" + photo_id).value = parseInt(photoYear);
                photoAddDateBlock.getElementById("year-" + photo_id).text = parseInt(photoYear);
            }
            var monthNames = ["January", "February", "March", "April", "May", "June",
            "July", "August", "September", "October", "November", "December"];
            if (photoAddDateBlock.getElementById("month-" + photo_id)) {
                showMonth(parseInt(photoMonth), photo_id);
                photoAddDateBlock.getElementById("month-" + photo_id).value = parseInt(photoMonth);
                photoAddDateBlock.getElementById("month-" + photo_id).options[parseInt(photoMonth)].text = monthNames[parseInt(photoMonth)];
            }

            if (photoAddDateBlock.getElementById("day-" + photo_id)) {
                showDay(parseInt(photoDate), photo_id);
                photoAddDateBlock.getElementById("day-" + photo_id).value = parseInt(photoDate);
            }

            var albumMainLink = "<div><a href='javascript:void(0);' onclick='openSmoothbox(" + photo_id + ");'><?php echo $this->translate("Make Album Main Photo") ?></a></div>";

            var changeDateLink = "<div><a href='javascript:void(0);' onclick='changeDateOptions(" + photo_id + ");'><?php echo $this->translate("Change Date") ?></a></div>";

            file.getElement('.file-info').set('html', mediaPhotoDetails);

            if ($('media_information_description_' + json.photo_id)) {
                $('media_information_description_' + json.photo_id).destroy();
            }
            var mediaDescInformation = new Element('div', {
                'id': 'media_information_description_' + json.photo_id
            });
            mediaDescInformation.inject(file.getElement('.file-info'), 'after');


            if ($('media_photo_options_' + json.photo_id)) {
                $('media_photo_options_' + json.photo_id).destroy();
            }

            var mediaPhotoOptions = new Element('div', {
                'id': 'media_photo_options_' + photo_id,
                'style': 'display:none',
                'class': 'media_photo_options',
                'html': photoLocation + albumMainLink + changeDateLink
            });
            mediaPhotoOptions.inject(file.getElement('.file-info'), 'after');
            if ($('media_settings_options_' + json.photo_id)) {
                $('media_settings_options_' + json.photo_id).destroy();
            }

            var mediaSettings = new Element('a', {
                'href': 'javascript:void(0);',
                'id': 'media_settings_options_' + photo_id,
                'class': 'media_settings_options',
            });

            mediaSettings.addEvents({
                click: function () {
                    showMediaOptions(photo_id);
                    return false;
                }
            });

            mediaSettings.inject(file.getElement('.file-info'), 'after');


            if (mediaDescInformation) {
                var mediaDescInformationInput = new Element('textarea', {
                    placeholder: '<?php echo $this->translate("Say something about this photo...") ?>',
                    id: 'media_photo_description_value_' + json.photo_id
                });

                mediaDescInformationInput.inject(mediaDescInformation);

                mediaDescInformationInput.addEvent('blur', function () {
                    updateTextareaValue(json.photo_id);
                });
            }

            if ($('sitealbum_location') && $('sitealbum_location').value) {
                if ($('media_location_information_' + json.photo_id)) {
                    $('media_location_information_' + json.photo_id).destroy();
                }
                var mediaLocationInformation = new Element('div', {
                    'id': 'media_location_information_' + json.photo_id
                });
                mediaLocationInformation.inject(mediaDescInformation, 'after');
                savePhotoLocationLink(photo_id);
            }

            if ($('media_photo_previous_date_' + photo_id)) {
                $('media_photo_previous_date_' + photo_id).destroy();
            }

            var photoPreviousDateValue = new Element('input', {
                'type': 'hidden',
                'id': 'media_photo_previous_date_' + photo_id,
                'value': json.datePhoto,
                'name': 'media_photo_previous_date_' + photo_id
            });
            photoPreviousDateValue.inject(formObj, 'after');

        } else {
            file.addClass('file-failed');
            file.getElement('.file-info').set('html', '<span><?php echo $this->string()->escapeJavascript($this->translate('An error occurred:')) ?></span> ' + (json.error ? (json.error) : response));
        }

        file.getElements('.demo-status-progress .demo-status-overall').each(function (item, inex)
        {
            item.style.display = 'none';
        });
    }
</script>