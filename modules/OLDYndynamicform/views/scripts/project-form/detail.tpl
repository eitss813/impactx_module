<div class="layout_page_yndynamicform_form_detail">
    <div class="generic_layout_container layout_top"></div>
    <div class="generic_layout_container layout_main">
        <div class="generic_layout_container layout_middle">
            <div class="generic_layout_container layout_core_content">

                <div class="title">
                    <?php echo $this->form->getTitle(); ?>
                </div>

                <!-- MAKE SURE WE WILL CHECK CONDITIONAL LOGIC-->
                <?php if ($this->doCheckConditionalLogic && !$this->error): ?>

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

                <!-- STYLE FOR SUBMIT BUTTON FLLOW FORM SETTINGS -->
                <?php if ($this->new_entry_form): ?>
                <div class="global_form">
                    <!--  BEGIN: Config for form with form settings  -->
                    <?php
                    $btn = $this->new_entry_form->getElement('submit_button');
                    if ($this->form->input_type == 'txt' && $btn instanceof Engine_Form_Element_Button) {
                        $btn->setLabel($this->form->btn_text);
                    } else if ($btn instanceof Engine_Form_Element_Button) {
                        $btn->setLabel('');
                    }
                    ?>

                    <style>
                        <?php if ($this -> form -> input_type == 'txt'):?>
                           .yndform_button_submit {
                               color: <?php echo $this -> form -> txt_color ?> !important;
                               background-color: <?php echo $this -> form -> btn_color ?> !important;
                           }

                            .yndform_button_submit:hover {
                                color: <?php echo $this -> form -> txt_hover_color ?> !important;
                                background-color: <?php echo $this -> form -> btn_hover_color ?> !important;
                            }

                        <?php else: ?>
                             .yndform_button_submit {
                                 width: 70px;
                                 height: 30px;
                                 -moz-background-size: contain;
                                 -webkit-background-size: contain;
                                 border: none;
                                 background-color: transparent;
                                 background-size: contain;
                                 background-image: url('<?php echo $this -> form -> btn_image?>');
                             }

                            .yndform_button_submit:hover {
                                background-color: transparent;
                                background-image: url('<?php echo $this -> form -> btn_hover_image?>');
                            }

                        <?php endif; ?>
                    </style>
                    <!--  END: Config for form with form settings  -->

                    <!--  Render form detail  -->
                    <?php
                    if (!$this -> form -> isSubmittable()) {
                        echo '<div class="tip"><span>'.$this -> form -> require_login_message.'</span></div>';
                    } elseif (!$this->form->isGetMaximumEntries()) {
                        echo '<div class="tip"><span>'.$this -> form -> entries_max_message.'</span></div>';
                    } else {
                        if ($this->totalPageBreak)
                            echo $this->partial('_progress-indicator.tpl', 'yndynamicform', array('pageBreakConfigs' => $this->pageBreakConfigs, 'totalPageBreak' => ($this->totalPageBreak + 2)));

                        $this->new_entry_form->setAttribs(array('id' => 'form_detail', 'onsubmit' => 'return validate_form()', 'enctype' => 'multipart/form-data', 'style' => $this->form->style));
                    if (count($this->new_entry_form) > 1) {
                        echo $this->new_entry_form->render($this);
                    }

                        // RENDER TO CONDITIONAL LOGIC
                        echo $this->partial('_check-conditional-logic.tpl', 'yndynamicform', array(
                            'prefix' => $this->prefix,
                            'fieldsValues' => $this->fieldsValues,
                            'fieldIds' => $this->fieldIds,
                            'totalPageBreak' => $this->totalPageBreak,
                            'confConditionalLogic' => $this->confConditionalLogic,
                            'confOrder' => $this->confOrder,
                            'notiConditionalLogic' => $this->notiConditionalLogic,
                            'notiOrder' => $this->notiOrder,
                            'arrErrorMessage' => $this->arrErrorMessage,
                        ));
                    }
                    ?>

                </div>
                <?php endif; ?>

                <script type="text/javascript">
                    document.addEvent('domready', function() {
                        <?php if(!$this -> viewer -> getIdentity() && !$this -> form -> require_login): ?>
                            guestSubmit();
                        <?php endif; ?>
                    });
                    var showPopUp = <?php echo $this->form->show_email_popup ?>;
                    var clickSubmit = false;

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
                        getConfirmationNotification('notification');

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
                        <?php if(!$this -> viewer -> getIdentity() && !$this -> form -> require_login): ?>
                            if ($('email_guest'))
                                return true;
                            else {
                                clickSubmit = true;
                                return guestSubmit();
                            }
                        <?php else: ?>
                            return true;
                        <?php endif; ?>
                        } else {
                            return false;
                        }
                    }

                    function guestSubmit() {
                        if ($('email_guest')) {
                            return true;
                        }
                        else if (showPopUp == 2) {
                            Smoothbox.open('<?php echo $this->url(array('module' => 'yndynamicform', 'action' => 'show-pop-up-email', 'require_email' => false), 'yndynamicform_form_general', true); ?>');
                        } else if (showPopUp == 1) {
                            Smoothbox.open('<?php echo $this->url(array('module' => 'yndynamicform', 'action' => 'show-pop-up-email', 'require_email' => true), 'yndynamicform_form_general', true); ?>');
                        } else
                            return true;
                        return false;
                    }

                    function doGuestSubmit(email) {
                        var emailGuest = new Element('input#email_guest', {
                            name: 'email_guest',
                            type: 'hidden',
                            value: email,
                        });
                        emailGuest.inject($('form_detail'), 'bottom');
                        if (clickSubmit)
                            $('form_detail').submit();
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
                <!--
                <script
                        src="https://maps.googleapis.com/maps/api/js?key=<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('yndynamicform.google.api.key', 'AIzaSyB3LowZcG12R1nclRd9NrwRgIxZNxLMjgc') ?>&callback=initMap"
                        async defer>
                </script>
                -->
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

                <?php else: ?>
                <div class="tip"><span><?php echo $this->message ?></span></div>
                <?php endif; ?>

            </div>
        </div>
    </div>
</div>

<style>
    .title{
        -moz-border-radius: 6px;
        -webkit-border-radius: 6px;
        border-radius: 6px;
        font-size: 24px !important;
        height: 47px;
        -moz-box-shadow: 0 1px 8px 0 rgba(0, 0, 0, .05);
        display: flex;
        margin-bottom: 12px;
        margin-left: 0px;
        justify-content: center;
        align-items: center;
        -webkit-box-shadow: 0 1px 8px 0 rgba(0, 0, 0, .05);
        box-shadow: 0 1px 8px 0 rgba(0, 0, 0, .05);
    }
</style>

<script>
    window.addEvent('load', function () {
        // add click event for save button
        $$('#save_button').addEvent('click', function() {
            if(document.getElementById("save_form").value == false){
                if (validate_form()) {
                    document.getElementById("save_form").value = true;
                    document.getElementById("form_detail").submit();
                } else {
                    return;
                }
            }
        });
    });
</script>