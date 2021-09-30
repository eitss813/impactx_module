<?php
$baseUrl = $this->layout()->staticBaseUrl;
$this->headLink()->appendStylesheet($baseUrl . 'application/modules/Yndynamicform/externals/styles/ui-redmond/jquery-ui-1.8.18.custom.css');
$this->headScript()
->appendFile($baseUrl . 'application/modules/Sitecrowdfunding/externals/scripts/jquery-3.4.1.min.js')
->appendFile($baseUrl .'application/modules/Yndynamicform/externals/scripts/jquery-ui-1.11.4.min.js');

if($this->project_id && !$this->user_id){
    $entriesres = Engine_Api::_()->getDbTable('entries', 'yndynamicform')->getEntriesByFormIdProjectId($this -> form -> getIdentity(), $this->project_id);
}

if(!$this->project_id && $this->user_id){
    $entriesres = Engine_Api::_()->getDbTable('entries', 'yndynamicform')->getEntriesByFormIdProjectId($this -> form -> getIdentity(), $this->user_id);
}

?>
<script>
    function validateFormFields() {
        var result = 0;
        var elements = document.getElementById("form_detail").elements;
        var obj ={};
        for(var i = 0 ; i < elements.length ; i++){
            var item = elements.item(i);
            obj[item.name] = item.value;
        }


        var url = en4.core.baseUrl + 'organizations/manageforms/validate-form-fields';
        var request = new Request.HTML({
            url: url,
            method: 'POST',
            data: {
                format: 'html',
                'post_val': JSON.stringify(obj),
                'ajaxform_option_id': '<?php echo $this->ajaxform_option_id ?>',
                'ajaxform_field_id': '<?php echo $this->ajaxform_field_id ?>'
            },
            onSuccess: function (responseTree, responseElements, responseHTML, responseJavaScript) {
                result = responseHTML.length;
                console.log(responseHTML.length);
            }
        });
        request.send();
        
        return result;
    }    
</script>
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
    @media(max-width:991px){
        input#phn_num{
            width:55% !important;
        }
        span.phn_span_element{
            width:45% !important;
        }
        span.phn_span_element select{
            width:100% !important;
        }
    }
</style>
<?php $noerrmsg = true; ?>
<?php if(count($entriesres[0])): ?>
 <?php $noerrmsg = false; ?>
 <div class="tip" ><span style="background-color: white;">Your form has been submitted already successfully.</span></div>
<?php endif; ?>
<?php if(!$this->flag && $noerrmsg == true): ?>
<div class="layout_page_yndynamicform_form_detail" id="edit-form">

    <div class="">
        <div class="">
            <div class="">
                <div class="title" style="display: flex;justify-content: center">
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
                    }
                    //elseif (!$this->form->isGetMaximumEntries()) {
                   // echo '<div class="tip"><span>'.$this -> form -> entries_max_message.'</span></div>';
                    //}
                    else {
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
                    
                        alert("HELLO");
                        return false;

                        validateFormFields();
                        return false;
                    
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
                                                // alert("No results found");
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
        display: flex;
        margin-bottom: 12px;
        margin-left: 0px;
        justify-content: center;
        align-items: center;
    }
</style>

<script type="text/javascript">
    en4.core.runonce.add(function() {
        if(document.getElementById('location') ) {
            var autocompleteSECreateLocation = new google.maps.places.Autocomplete(document.getElementById('location'));
        <?php include APPLICATION_PATH . '/application/modules/Seaocore/views/scripts/location.tpl'; ?>
        }
    });
</script>

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
        })

    });
</script>
<?php endif; ?>




<!-- edit -->
<?php if($this->flag && $noerrmsg == true): ?>
    <div class="layout_page_yndynamicform_form_detail">
          <div class="generic_layout_container layout_main">
                <div class="generic_layout_container layout_middle">
                    <div class="generic_layout_container layout_core_content">

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
                                <h1 class="h1" style="display: flex;justify-content: center">
                                    <?php
                                        echo $this->yndform->getTitle();
                                    ?>
                                </h1>

                            </div>
                            <div>
                                <!-- <span class="yndform_text">
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
                             <i class="yn_dots">.</i>
                             <?php if ($this->entry->owner_id): ?>
                             <?php echo '<span class="yndform_text_submit">'.$this->translate('Submitted by').'</span>'.' '.$this->htmlLink($this->entry->getOwner()->getHref(), $this->entry->getOwner()->getTitle(), array()); ?>
                             <?php endif; ?>
                             -->
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
                           <!-- <div id="yndform_buttons_group-element" class="form-element">
                                <button onclick="submit_form()" name="submit_button" id="submit_button" type="submit"><?php echo $this->translate('Save')?></button>

                            </div>-->
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
                            


                        var has_error = validateFormFields();
                        alert(has_error);
                        return false;
                            
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
                                                        // alert("No results found");
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

                    </div>
                </div>
            </div>
          </div>
    </div>
    <style>
        #submit_button{
            float: right;
        }
    </style>
<?php endif; ?>
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
        // add click event for reset button
        $$('#reset_button').addEvent('click', function() {
            if(document.getElementById("reset_form").value == false){

                document.getElementById("reset_form").value = true;
                document.getElementById("form_detail").submit();

            }
        });

    });
</script>
<script type="text/javascript">


    var ynDynamicFormCalendar= {
        currentText: '<?php echo $this->string()->escapeJavascript($this->translate('Today')) ?>',
        monthNames: ['<?php echo $this->string()->escapeJavascript($this->translate('January')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('February')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('March')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('April')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('May')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('June')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('July')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('August')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('September')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('October')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('November')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('December')) ?>',
        ],
        monthNamesShort: ['<?php echo $this->string()->escapeJavascript($this->translate('Jan')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('Feb')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('Mar')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('Apr')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('May')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('Jun')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('Jul')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('Aug')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('Sep')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('Oct')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('Nov')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('Dec')) ?>',
        ],
        dayNames: ['<?php echo $this->string()->escapeJavascript($this->translate('Sunday')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('Monday')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('Tuesday')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('Wednesday')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('Thursday')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('Friday')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('Saturday')) ?>',
        ],
        dayNamesShort: ['<?php echo $this->translate('Su') ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('Mo')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('Tu')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('We')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('Th')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('Fr')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('Sa')) ?>',
        ],
        dayNamesMin: ['<?php echo $this->string()->escapeJavascript($this->translate('Su')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('Mo')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('Tu')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('We')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('Th')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('Fr')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('Sa')) ?>',
        ],
        firstDay: 0,
        isRTL: <?php echo $this->layout()->orientation == 'right-to-left'? 'true':'false' ?>,
    showMonthAfterYear: false,
        yearSuffix: ''
    };

    jQuery(document).ready(function(){
        jQuery.datepicker.setDefaults(ynDynamicFormCalendar);
        jQuery('#1_151_203').datepicker({
            firstDay: 1,
            dateFormat: 'yy-mm-dd',
            showOn: "button",
            buttonImage:'<?php echo $this->baseUrl() ?>/application/modules/Yndynamicform/externals/images/calendar.png',
            buttonImageOnly: true,
            buttonText: '<?php echo $this -> translate("Select date")?>'
        });

        jQuery('#valid_to_date').datepicker({
            firstDay: 1,
            dateFormat: 'yy-mm-dd',
            showOn: "button",
            buttonImage:'<?php echo $this->baseUrl() ?>/application/modules/Yndynamicform/externals/images/calendar.png',
            buttonImageOnly: true,
            buttonText: '<?php echo $this -> translate("Select date")?>'
        });
    });
</script>
<style>
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
    button#last_page_prev, .yndform_page_next_text {
        background-color: white !important;
    }
    #print_button{
        display: none !important;

    }
    .generic_layout_container.layout_yndynamicform_browse_menu {
        display: none !important;
    }
    #edit-form{
        background-color: white;
        box-sizing: border-box;
        border-radius: 5px;
    }
    .metric_disable{
        cursor: not-allowed;
    }
</style>

<script>

    // metrics aggregate
    var $j = jQuery.noConflict();
    $j(document).ready(function() {
        const metrics_field_id_array = [];
        var matrics_aggregation_element = null;
        var matrics_aggregation_element_id = null;
        var matrics_aggregation_field_ids = null;

        // check if any metrics input is added and it has aggregation
        var elements = document.getElementById("form_detail").elements;
        var metrics_field_id_array_flag = 0;
        for (var i = 0, element; element = elements[i++];) {
            var metrics_aggregate_type = element.getAttribute("metric_aggregate_type");
            if(metrics_aggregate_type=="metric_sum"){
                matrics_aggregation_element = element;
                matrics_aggregation_element_id = element.id;
                
                // Add multiple metrics in the array
                metrics_field_id_array[metrics_field_id_array_flag++] = matrics_aggregation_element_id;
                
                var metrics_label = document.getElementById(matrics_aggregation_element_id+'-label').innerHTML;
                document.getElementById(matrics_aggregation_element_id+'-label').innerHTML = metrics_label + ' (Calculated automatically)';
                document.getElementById(matrics_aggregation_element_id).classList.add("metric_disable");
                document.getElementById(matrics_aggregation_element_id).readonly = true;
                matrics_aggregation_field_ids = element.getAttribute("metric_aggregate_fields");
                matrics_aggregation_field_ids = matrics_aggregation_field_ids.split(" ");
            }
            
            // Run the logic of Own Formula
            if(metrics_aggregate_type=="own_formula"){
                // alert(element.getAttribute("own_formula_input"));
                matrics_aggregation_element = element;
                matrics_aggregation_element_id = element.id;
                var metrics_label = document.getElementById(matrics_aggregation_element_id+'-label').innerHTML;
                // var own_formula_input_value = document.getElementById(matrics_aggregation_element_id).getAttribute("own_formula_input");
                
                var own_formula_by_id = document.getElementById(matrics_aggregation_element_id).getAttribute("own_formula_by_id");
                var own_formula_input_value = getUpdatedFormula(own_formula_by_id);
                
                // Add multiple metrics in the array
                metrics_field_id_array[metrics_field_id_array_flag++] = matrics_aggregation_element_id;
                
                document.getElementById(matrics_aggregation_element_id+'-label').innerHTML = metrics_label + ' (Calculated automatically) <br />Formula: ' + own_formula_input_value;
                document.getElementById(matrics_aggregation_element_id).classList.add("metric_disable");
                document.getElementById(matrics_aggregation_element_id).readonly = true;
                matrics_aggregation_field_ids = element.getAttribute("metric_aggregate_fields");
                matrics_aggregation_field_ids = matrics_aggregation_field_ids.split(" ");
            }
        }

        /*
        var form = document.getElementById("form_detail");
        form.addEventListener("input", function (event) {
            var elements = document.getElementById("form_detail").elements;
            var total = 0;
            for (var i = 0, element; element = elements[i++];) {
                var id = element.id;
                var full_id = element.id;
                id = id.split('_');
                id = id[id.length - 1];
                var isExistYn = matrics_aggregation_field_ids.includes(id);
                if(isExistYn === true){
                    var data = document.getElementById(full_id).value;
                    if(data && data !== null && data.value !== "" && !data.isNaN){
                        total = total + parseFloat(data);
                        if(total && total !== null && total.value !== "" && !total.isNaN){
                            document.getElementById(matrics_aggregation_element_id).value = total;
                        }
                    }
                }
            }
        });
        */
        
        var form = document.getElementById("form_detail");
        form.addEventListener("input", function (event) {
            calculateTheMetricsValue();
        });
        
        // Get the number form fields label.
        function getUpdatedFormula(formula) {
            var elements = document.getElementById("form_detail").elements;
            
            try {
                for (update_formula_flag = 0; update_formula_flag < elements.length; update_formula_flag++ ) {
                    if( elements[update_formula_flag] )
                        var element = elements[update_formula_flag];

                    if( element && element.id ) {
                        var id = element.id;
                        var full_id = element.id;
                        id = id.split('_');
                        id = id[id.length - 1];

                        var temp_label = ''
                        if( document.getElementById(full_id + '-label').childNodes[0].innerHTML ) {
                            temp_label = document.getElementById(full_id + '-label').childNodes[0].innerHTML;
                        }

                        // Exclude ( Maximum) from the string
                        var temp_label_max_array = temp_label.split("( Maximum");
                        
                        if( temp_label_max_array[0] )
                            var temp_label_min_array = temp_label_max_array[0].split("( Minimum");
                            
                        if( temp_label_min_array[0] )
                            temp_label = temp_label_min_array[0];
                            
                        // Trim the label
                        temp_label = temp_label.trim();

                        if( temp_label && (formula.search("field_id_" + id) >= 0) )
                            formula = formula.replaceAll("field_id_" + id, temp_label);
                        
                    }
                }
                
                return formula;
            }
            catch(err) {
                return formula;
            }
        }
        
        window.onload = function() {
          calculateTheMetricsValue();
        };
        
        function calculateTheMetricsValue() {
            var elements = document.getElementById("form_detail").elements;
            
            /*
            for (var i = 0, element; element = elements[i++];) {
                var id = element.id;
                var full_id = element.id;
                id = id.split('_');
                id = id[id.length - 1];
                var isExistYn = matrics_aggregation_field_ids.includes(id);

                if(isExistYn === true){
                    var data = document.getElementById(full_id).value;
                    if(data && data !== null && data.value !== "" && !data.isNaN){
                        data = parseInt(data);
                        var min_string = parseInt(element.getAttribute("min_value"));
                        var max_string = parseInt(element.getAttribute("max_value"));
                        
                        if(data < min_string) {
                           alert("Inserted value can not be less then " + min_string);
                           return;
                        }

                        if(data > max_string) {
                            alert("Inserted value can not be greater then " + max_string);
                            return;
                        }
                    }
                }
            }
            */
            
            for( var flag=0; flag < metrics_field_id_array.length; flag++ ) {
                var current_metrics_id = metrics_field_id_array[flag];

                var total = 0;
                var own_formula = null;
                var temp_metric_aggregate_type = document.getElementById(current_metrics_id).getAttribute("metric_aggregate_type");
                if( temp_metric_aggregate_type == 'own_formula' )
                    own_formula = document.getElementById(current_metrics_id).getAttribute("own_formula_by_id");

                for (var i = 0, element; element = elements[i++];) {
                    var id = element.id;
                    var full_id = element.id;
                    id = id.split('_');
                    id = id[id.length - 1];
                    var isExistYn = matrics_aggregation_field_ids.includes(id);

                    if(isExistYn === true){
                        var data = document.getElementById(full_id).value;
                        if(data && data !== null && data.value !== "" && !data.isNaN){
                            total = total + parseFloat(data);
                            if(total && total !== null && total.value !== "" && !total.isNaN){
                                if( temp_metric_aggregate_type == 'own_formula' ) {
                                    if( own_formula.search("field_id_" + id) >= 0 )
                                        own_formula = own_formula.replaceAll("field_id_" + id, parseFloat(data));
                                }else {
                                    document.getElementById(current_metrics_id).value = total;
                                }
                            }
                        }
                    }
                }

                if( temp_metric_aggregate_type == 'own_formula' ) {
                    var formula_value = eval(own_formula);
                    formula_value = formula_value.toFixed(2);
                    document.getElementById(current_metrics_id).value = formula_value;
                }
            }
        }

    });
</script>