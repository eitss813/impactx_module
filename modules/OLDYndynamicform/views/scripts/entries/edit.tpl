<!-- MAKE SURE WE WILL CHECK CONDITIONAL LOGIC-->
<?php if ($this->doCheckConditionalLogic): ?>

    <!--STYLE FOR PAGEBREACK BUTTON IF PAGE BREAK ENABLE -->
    <?php if ($this->totalPageBreak): ?>
        <style>
            .yndform_page_prev_image, .yndform_page_next_image {
                width: 70px;
                height: 30px;
                -moz-background-size: contain;
                -webkit-background-size: contain;
                border: none;
                background-size: contain;
                background-color: transparent;
            }

            /* For Next Button*/
            <?php if($this->pageBreakConfigs['next_button'] == 'image'): ?>
            .yndform_page_next_image {
                background-image: url('<?php echo $this->pageBreakConfigs['next_button_image']?>');
            }

            .yndform_page_next_image:hover {
                background-color: transparent;
                background-image: url('<?php echo $this->pageBreakConfigs['next_button_image_hover']?>');
            }

            <?php elseif($this->pageBreakConfigs['next_button'] == 'text'): ?>
            .yndform_page_next_text {
                color: <?php echo $this->pageBreakConfigs['next_button_text_color'] ?> !important;
                background-color: <?php echo $this->pageBreakConfigs['next_button_text_bg_color'] ?>;
            }

            <?php endif; ?>

            /* For Pre Button*/
            <?php if($this->pageBreakConfigs['pre_button'] == 'image'): ?>
            .yndform_page_prev_image {
                background-image: url('<?php echo $this->pageBreakConfigs['pre_button_image']?>');
            }

            .yndform_page_prev_image:hover {
                background-color: transparent;
                background-image: url('<?php echo $this->pageBreakConfigs['pre_button_image_hover']?>');
            }

            <?php elseif($this->pageBreakConfigs['pre_button'] == 'text'): ?>
            .yndform_page_prev_text {
                color: <?php echo $this->pageBreakConfigs['pre_button_text_color'] ?>;
                background-color: <?php echo $this->pageBreakConfigs['pre_button_text_bg_color'] ?>;
            }

            <?php endif; ?>
        </style>
    <?php endif; ?>
    <div class="entry_breadcrum clearfix">
        <div class="yndform_title_parent">
            <h1 class="h1">
                <?php
                echo $this->yndform->getTitle();
                ?>
            </h1>
            <!--   <?php echo $this->htmlLink(array(
                 'route' => 'yndynamicform_form_detail',
                 'form_id' => $this->yndform->getIdentity()), '<span class="ynicon yn-arr-left"></span>'.$this->translate('Back to form'),array(
             'class' => 'yndform_backform'
             ))
             ?> -->
         </div>
         <div>
             <!--     <span class="yndform_text">
                   <?php echo $this -> htmlLink(array(
                       'module'=>'yndynamicform',
                       'action'=>'list',
                       'form_id'=> $this -> yndform -> getIdentity(),
                       'route'=>'yndynamicform_entry_general',
                   ),$this -> translate("View entries"), array()); ?>
               </span>
               <span class="yndform_slash">&#47;</span><span class="yndform_backslash">&#92;</span>
            <span class="yndform_text">
                   <?php echo '#'.$this->entry->getIdentity()?>
               </span>

             <i class="yn_dots">.</i>  -->
            <?php if ($this->entry->owner_id): ?>
                <?php echo '<span class="yndform_text_submit">'.$this->translate('Submitted by').'</span>'.' '.$this->htmlLink($this->entry->getOwner()->getHref(), $this->entry->getOwner()->getTitle(), array()); ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- STYLE FOR SUBMIT BUTTON FLLOW FORM SETTINGS -->
    <?php if ($this->edit_entry_form): ?>
        <div class="global_form">
            <!--  Render form detail  -->
            <?php
            if (!$this -> form -> isSubmittable()) {
                echo $this -> form -> require_login_message;
            } else {
                if ($this->totalPageBreak)
                    echo $this->partial('_progress-indicator.tpl', 'yndynamicform', array('pageBreakConfigs' => $this->pageBreakConfigs, 'totalPageBreak' => ($this->totalPageBreak + 2)));

                $this->edit_entry_form->setAttribs(array('id' => 'form_detail', 'onsubmit' => 'return validate_form()', 'enctype' => 'multipart/form-data', 'style' => $this->form->style));
                if (count($this->edit_entry_form) > 0) {
                    echo $this->edit_entry_form->render($this);

                    ?>
           <div id="yndform_buttons_group-element" class="form-element">
                        <button onclick="submit_form()" name="submit_button" id="submit_button" type="submit"><?php echo $this->translate('Update')?></button>
               <!--     <?php echo $this -> htmlLink(array(
               'module'=>'yndynamicform',
               'action' => 'print',
               'entry_id'=> $this -> entry -> getIdentity(),
               'route'=>'yndynamicform_entry_specific',
           ), '<button><span class="ynicon yn-print"></span>'.$this->translate('Print').'</button>', array('target' => '_blank', 'id' => 'print_button', 'class' => 'yndform_buttons')); ?>
           <?php if($this->layout()->orientation != 'right-to-left') echo $this -> htmlLink(array(
               'module'=>'yndynamicform',
               'action' => 'save-pdf',
               'entry_id'=> $this -> entry -> getIdentity(),
               'route'=>'yndynamicform_entry_specific',
           ), '<button><span class="ynicon yn-downloads"></span>'.$this->translate('Save as PDF').'</button>', array('target' => '_blank', 'id' => 'save_button', 'class' => 'yndform_buttons')); ?>
           <?php echo $this->translate('or'); ?>
           <a name="cancel" id="cancel" type="button" href="javascript:void(0);" onclick="history.go(-1); return false;"><?php echo $this->translate('Cancel') ?></a>-->
          </div>
                    <?php
                }

                // RENDER TO CONDITIONAL LOGIC
                echo $this->partial('_check-conditional-logic.tpl', 'yndynamicform', array(
                    'prefix' => $this->prefix,
                    'fieldsValues' => $this->fieldsValues,
                    'fieldIds' => $this->fieldIds,
                    'totalPageBreak' => $this->totalPageBreak,
                    'confConditionalLogic' => $this->confConditionalLogic,
                    'confOrder' => $this->confOrder,
                    'arrErrorMessage' => $this->arrErrorMessage,
                ));
            }
            ?>

        </div>
    <?php endif; ?>

    <script type="text/javascript">
        function submit_form() {
            if (validate_form()) {
                $('form_detail').submit();
            } else {
                return;
            }
        }
        function validate_form() {
            if (totalPageBreak) {
                if (!validateRequiredFieldInPage(totalPageBreak + 1))
                    return false;
            }

            if ($('g-recaptcha') && grecaptcha.getResponse().length == 0) {
                if (!$$('.form-errors').length) {
                    var form_errors = new Element('ul.form-errors');
                    var div_element = $$('#form_detail .form-elements').getParent()[0];
                    form_errors.inject(div_element, 'top');
                } else {
                    var form_errors = $$('.form-errors')[0];
                }
                form_errors.insertAdjacentHTML('beforeEnd', '<li>Recaptcha<ul class="errors"><li>Please verify that you are not a robot.</li></ul></li>');
                return false;
            }

            // Get Confirmation and Notification
            getConfirmationNotification('confirmation');

            if ($('arrayIsValid')) {
                $('arrayIsValid').destroy();
            }

            var element_valid = new Element('input', {
                type: 'hidden',
                id: 'arrayIsValid',
                name: 'arrayIsValid',
                value: JSON.stringify(arrayIsValid),
            });

            element_valid.inject($('form_detail'), 'bottom');

            // 0 mean one page
            if (validateAgreement(0)) {
                return true;
            } else {
                return false;
            }
        }
    </script>
    <script type="text/javascript">

        var topLevelId = '0';

        function changeFields(element, force) {
            // We can call this without an argument to start with the top level fields
            if (!$type(element)) {
                $$('.parent_' + topLevelId).each(function (element) {
                    changeFields(element);
                });
                return;
            }

            // Detect if this is an input or the container
            if (element.hasClass('field_container')) {
                element = element.getElement('.field_input');
            }

            // If this cannot have dependents, skip
            if (!$type(element) || !$type(element.onchange)) {
                return;
            }

            // Get the input and params
            var params = element.id.split(/[-_]/);
            if (params.length > 3) {
                params.shift();
            }
            force = ( $type(force) ? force : false );

            // Now look and see
            var option_id = element.value;

            // Iterate over children
            $$('.parent_' + params[2]).each(function (childElement) {
                // Forcing hide
                var nextForce;
                if (force == 'hide' || force == 'show') {
                    childElement.style.display = ( force == 'hide' ? 'none' : '' );
                    nextForce = force;
                }

                // Hide fields not tied to the current option (but propogate hiding)
                else if (!childElement.hasClass('option_' + option_id)) {
                    childElement.style.display = 'none';
                    nextForce = 'hide';
                }

                // Otherwise show field and propogate (nothing, show?)
                else {
                    childElement.style.display = '';
                    nextForce = undefined;
                }

                changeFields(childElement, nextForce);
            });
        }

        window.addEvent('load', function () {
            changeFields();
        });

    </script>
    <!--
    -- The following code to check if the form has User analytics element.
    -- Check if any the enabled element in array: State, City, Country, Long, Lat
    -- and set value for enabled element in this form.
    -->
    <script
        src="https://maps.googleapis.com/maps/api/js?key=<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('yndynamicform.google.api.key', 'AIzaSyB3LowZcG12R1nclRd9NrwRgIxZNxLMjgc') ?>&callback=initMap"
        async defer></script>
    <script>
        function initMap() {
            // Try HTML5 geolocation.
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function (position) {
                    var pos = {
                        lat: position.coords.latitude,
                        lng: position.coords.longitude
                    };
                    if (pos) {
                        var current_posstion = new Request.JSON({
                            'format': 'json',
                            'url': '<?php echo $this->url(array('action' => 'get-my-location'), 'yndynamicform_general') ?>',
                            'data': {
                                latitude: pos.lat,
                                longitude: pos.lng,
                            },
                            'onSuccess': function (json, text) {
                                results = json.results;
                                if (json.status == 'OK') {
                                    var indice = 0;
                                    for (var j = 0; j < results.length; j++) {
                                        if (results[j].types[0] == 'locality') {
                                            indice = j;
                                            break;
                                        }
                                    }
                                    for (var i = 0; i < results[indice].address_components.length; i++) {
                                        if (results[indice].address_components[i].types[0] == "locality") {
                                            //this is the object you are looking for
                                            city = results[indice].address_components[i];
                                        }
                                        if (results[indice].address_components[i].types[0] == "administrative_area_level_1") {
                                            //this is the object you are looking for
                                            state = results[indice].address_components[i];
                                        }
                                        if (results[indice].address_components[i].types[0] == "country") {
                                            //this is the object you are looking for
                                            country = results[indice].address_components[i];
                                        }
                                        if (results[indice].address_components[i].types[0] == "postal_code") {

                                        }
                                    }
                                } else {
                                    alert("No results found");
                                }

                                // Pass value
                                if (json.status == 'OK') {
                                    if ($('uaCountry')) {
                                        $('uaCountry').value = country.long_name;
                                    }
                                    if ($('uaState')) {
                                        $('uaState').value = state.long_name;
                                    }
                                    if ($('uaCity')) {
                                        $('uaCity').value = city.long_name;
                                    }
                                    if ($('uaLongitude')) {
                                        $('uaLongitude').value = pos.lng;
                                    }
                                    if ($('uaLatitude')) {
                                        $('uaLatitude').value = pos.lat;
                                    }
                                }
                                else {
                                    handleNoGeolocation(true, location);
                                }
                            }
                        });
                        current_posstion.send();
                    }

                }, function () {
                    handleLocationError(true);
                });
            } else {
                // Browser doesn't support Geolocation
                handleLocationError(false);
            }
        }

        function handleLocationError(browserHasGeolocation) {
            //console.log('OK');
        }
    </script>

<?php endif; ?>

<?php if ($this -> selected_confirmation) echo $this->selected_confirmation->confirmation_text; ?>
<style>
    #phn_err{
        color: red;
        margin-top: 3px;
        padding: 2px;
        text-align: end;
    }

    .phn_element {
        display: flex !important;
        flex-direction: row-reverse;
    }
    .phn_span_element{
        margin-right: 5px;
    }
    .description{
        display: none;
    }
    .phn_span_element{
        font-weight: 200;

    }
    .form-label {
        display: flex !important;
        flex-direction: column;
    }
    .yndform_title_parent {
        padding: 6px;
    }
    .generic_layout_container.layout_yndynamicform_browse_menu {
        display: none;
    }
    .form-label {
        margin-bottom: 0px !important;
    }
    div.form-element {
        display: flex !important;
        flex-direction: column-reverse;
        margin-bottom: 5px !important;
    }
    p.description {
        margin-bottom: 10px !important;
        margin-top: 0px !important;
    }
    .text_editor p {
        max-width: 100% !important;
    }
</style>