<?php

function get_browser_name($user_agent)
{
    if (preg_match('/(Opera|OPR)\/([^\s]+)/', $user_agent, $browser)){
        $browser[1] = 'Opera';
        return $browser;
    }
    elseif (preg_match('/(Edge)\/([^\s]+)/', $user_agent, $browser))
        return $browser;
    elseif (preg_match('/(Chrome)\/([^\s]+)/', $user_agent, $browser))
        return $browser;
    elseif (preg_match('/(Safari)\/([^\s]+)/', $user_agent, $browser))
        return $browser;
    elseif (preg_match('/(Firefox)\/([^\s]+)/', $user_agent, $browser))
        return $browser;
    elseif (preg_match('/(MSIE|Trident\/7)\/([^\s]+)/', $user_agent, $browser)) {
        $browser[1] = 'Internet Explorer';
        return $browser;
    }
    return array('Other', 'Other', 0);
}
?>
<script type="text/javascript">
    var prefix = '<?php echo $this->prefix ?>';
    var arrayIsValid = {};
    var listFieldsValues = <?php echo json_encode($this -> fieldsValues); ?>;
    var listFieldsIds = <?php echo json_encode($this -> fieldIds); ?>;
    var totalPageBreak = <?php echo $this->totalPageBreak; ?>;
    var arrErrorMessage = <?php echo json_encode($this->arrErrorMessage); ?>;
    var pageBreakId = 0;

    // Confirmation and notification
    var confConditionalLogic = <?php echo json_encode($this->confConditionalLogic) ?>;
    var confOrder = <?php echo json_encode($this->confOrder) ?>;
    var notiConditionalLogic = <?php echo json_encode($this->notiConditionalLogic) ?>;
    var notiOrder = <?php echo json_encode($this->notiOrder) ?>;

    window.addEvent('domready', function() {
        if (totalPageBreak) {
            pageBreakId = totalPageBreak + 2;
            if ($('progress_1')) $('progress_1').addClass('active');
            $('page_1-label').dispose();
            for (var i = 2; i < pageBreakId; i++) {
                if ($('page_'+i+'-wrapper')) {
                    $('page_'+i+'-wrapper').setStyle('display', 'none');
                    $('page_'+i+'-label').dispose();
                }
            }
        }

        <?php $browser = get_browser_name($_SERVER['HTTP_USER_AGENT']); ?>
        if ($('uaBrowser')) {
            $('uaBrowser').value = '<?php echo $browser[1] ?>';
        }
        if ($('uaBrowserVersion')) {
            $('uaBrowserVersion').value = '<?php echo $browser[2] ?>';
        }
        if ($('uaIpAddress')) {
            $('uaIpAddress').value = '<?php echo $_SERVER['REMOTE_ADDR'] ?>';
        }

        // BEGINE: Check conditional logic
        addEventForAllElements(listFieldsValues, listFieldsIds);
        initFormWithConditionalLogic(listFieldsValues, listFieldsIds);
    })

    function validateRequiredFieldInPage(id) {
        //TODO: CHECK TYPE AND GET VALUE OF ELEMENT. IF VALUE IS NULL. RETURN ERROR
        var allRequiredElement = $$('#page_'+id+'-element .required');
        var check = true;
        if (!$$('.form-errors').length) {
            var form_errors = new Element('ul.form-errors');
            var div_element = $$('#form_detail .form-elements').getParent()[0];
            form_errors.inject(div_element, 'top');
        } else {
            var form_errors = $$('.form-errors')[0];
        }
        form_errors.innerHTML = '';
        allRequiredElement.each(function (ele) {
            var field_id = ele.get('for');
            if (arrayIsValid[field_id] == false) {
                return false;
            }
            var value = getElementValueByID(field_id);
            if (!value || value == '') {
                if (arrErrorMessage.hasOwnProperty(field_id)) {
                    form_errors.insertAdjacentHTML('beforeEnd', '<li>' + ele.innerHTML + '<ul class="errors"><li>' + arrErrorMessage[field_id] +  '</li></ul></li>');
                } else {
                    form_errors.insertAdjacentHTML('beforeEnd', '<li>' + ele.innerHTML + '<ul class="errors"><li>Value is required and can\'t be empty</li></ul></li>');
                }
                check = false;
            }
        })
        return check && validateAgreement(id);
    }

    function pageNext(id) {
        if (validateRequiredFieldInPage(id-1)) {
            $('page_'+id+'-wrapper').setStyle('display', 'flex');
            $('page_'+(id-1)+'-wrapper').setStyle('display', 'none');
            // Progress
            if ($('progress_'+id)) {
                $('progress_'+id).addClass('active');
                $('progress_'+(id-1)).addClass('actived');
            }
        } else {
            return;
        }
    }

    function pagePrev(id) {
        if (validateRequiredFieldInPage(id-1)) {
            $('page_' + id + '-wrapper').setStyle('display', 'flex');
            $('page_' + (id + 1) + '-wrapper').setStyle('display', 'none');
            // Progress
            if ($('progress_'+id)) {
                $('progress_' + id).addClass('active');
                $('progress_' + id).removeClass('actived');
                $('progress_' + (id + 1)).removeClass('active');
            }
        } else {
            return;
        }
    }

    // BEGIN: CHECK CONDITIONAL LOGIC SECTION
    function doActionShowHide(elementID, check, isShow) {
        var elementWrraper = $(elementID + '-wrapper') || $(elementID);
        if (check == true)
        {
            //console.log(value + check);
            if (isShow == 0 || isShow == '0') {
                elementWrraper.setStyle('display', 'none');
            } else {
                if (elementWrraper.hasClass('yndform_section_break')) {
                    elementWrraper.setStyle('display', 'block');
                } else {
                    elementWrraper.setStyle('display', 'flex');
                }
            }
        } else {
            //console.log(value + check);
            if (isShow == 0 || isShow == '0') {
                if (elementWrraper.hasClass('yndform_section_break')) {
                    elementWrraper.setStyle('display', 'block');
                } else {
                    elementWrraper.setStyle('display', 'flex');
                }
            } else {
                elementWrraper.setStyle('display', 'none');
            }
        }
    }

    function initFormWithConditionalLogic(listFieldsValues, listFieldsIds) {
        Object.each(listFieldsIds, function (value, key) {
            if (listFieldsValues.hasOwnProperty(value) && listFieldsValues[value].conditional_enabled && listFieldsValues[value].conditional_logic) {

                /*
                 * If this fields is section break. We will apply conditional logic of all next fields
                 * (until next section break) with conditional logic of this fields.
                 */
                if ($(value + '-wrapper') && $(value + '-wrapper').hasClass('yndform_section_break')) {
                    $(value + '-wrapper').addClass('section_content');
                    var sectionBreakSubElement = $(value + '-wrapper').getAllNext();
                    var isSectionBreak = true;
                    sectionBreakSubElement.each(function (ele) {
                        if (ele.hasClass('yndform_section_break')) {
                            isSectionBreak = false;
                        }
                        if (ele.type == 'button' || ele.type == 'submit') {
                            return false;
                        }
                        if (isSectionBreak) {
                            ele.addClass('yndform_section_break_element');
                            ele.inject($(value + '-wrapper', 'bottom'));
                        } else {
                            return isSectionBreak;
                        }

                    })
                }

                var field_value = listFieldsValues[value];
                var check = checkCondLogicOfField(value, field_value, listFieldsIds);
                arrayIsValid[value] = check;
                doActionShowHide(value, check, field_value.conditional_show)
            }
        })
    }

    function addEventForAllElements(listFieldsValues, listFieldsIds) {
        // BEGIN: Check conditional logic
        var countIds = Object.keys(listFieldsIds).length;
        Object.each(listFieldsIds, function (value, key) {
            var elementInput = $(value);
            var elementRadio = $$('input[type=radio][name=' + value +']');
            var elementMultipleCheckbox = $$('input[type=checkbox][name=' + value +'[]]');
            if (elementInput) {
                var element = elementInput;
            } else if(elementMultipleCheckbox.length) {
                var element = elementMultipleCheckbox;
            } else {
                var element = elementRadio;
            }
            if (element) {
                element.addEvent('change', function () {
                    for (var i = parseInt(key) + 1; i <= countIds; i++)
                    {
                        var field_id = listFieldsIds[i];
                        if (listFieldsValues.hasOwnProperty(field_id) && listFieldsValues[field_id].conditional_enabled && listFieldsValues[field_id].conditional_logic) {
                            var field_value = listFieldsValues[field_id];
                            var check = checkCondLogicOfField(field_id, field_value, listFieldsIds);
                            arrayIsValid[field_id] = check;

                            doActionShowHide(field_id, check, field_value.conditional_show)
                        }
                    }
                })
            }
        })
    }

    function getElementValueByID(field_id) {
        if ($(field_id)) {
            var element = $(field_id);
            var type = element.type;
            var eleValue = null;
            switch (type) {
                case 'text':
                case 'hidden':
                    eleValue = element.value;
                    break;
                case 'select-multiple':
                    eleValue = element.getSelected().get('value');
                    break;
                case 'checkbox':
                    eleValue = element.checked;
                    break;
                case 'file':
                    eleValue = element.files.length;
                    break;
                default:
                    eleValue = element.value;
                    break;
            }
            return eleValue;

        } else if ($$('input[type=radio][name=' + field_id + ']:checked').length) {
            // For element radio button
            return $$('input[type=radio][name=' + field_id + ']:checked')[0].value;

        } else if ($$('input[name=' + field_id + '[]][type=checkbox]:checked').length) {
            // For element multiple checkbox
            return $$('input[name=' + field_id + '[]][type=checkbox]:checked').get('value');

        } else if ($$('input[name=' + field_id + '][type=hidden]').length) {
            // For element hidden input
            return $$('input[name=' + field_id + '][type=hidden]')[0].value;

        } else {
            return null;
        }
    }

    function indexOfField(listFieldIds, search) {
        var index = -1;
        Object.each(listFieldIds, function (value, key) {
            if (value == search){
                index = key;
            }
        })
        return parseInt(index);
    }

    function checkCondLogicItem(field_id, operator, value) {
        var isValid = false;
        /* Check status this fields.
         * Null is not yet checked.
         * True: checked and valid.
         * False: checked and invalid.
         */
        if (arrayIsValid.hasOwnProperty(field_id) && arrayIsValid[field_id] == false) {
            return isValid;
        } else {
            var eleValue = getElementValueByID(field_id);
//            console.log('Check ' + field_id + ' ' + eleValue + ' ' + operator + ' ' + value);
            switch (operator) {
                case 'is':
                    isValid = eleValue == value ? true:false;
                    break
                case 'is_not':
                    isValid = eleValue != value ? true:false;
                    break;
                case 'contains':
                    isValid = eleValue.contains(value)? true:false;
                    break;
                case 'starts_with':
                    isValid = eleValue.startsWith(value) ? true:false;
                    break;
                case 'ends_with':
                    isValid = eleValue.endsWith(value) ? true:false;
                    break;
                case 'does_not_contain':
                    isValid = eleValue.contains(value) ? false:true;
                    break;
                case 'greater_than':
                    isValid = (isNaN(eleValue) || eleValue == '') ? false : (parseFloat(eleValue) > parseFloat(value)) ? true:false;
                    break;
                case 'less_than':
                    isValid = (isNaN(eleValue) || eleValue == '') ? false : (parseFloat(eleValue) < parseFloat(value)) ? true:false;
                    break;
                case 'selected':
                case 'unselected':
                    isValid = (eleValue == value) ? true:false;
                    break;
                case 'not_empty':
                    isValid = (eleValue > 0) ? true:false;
                    break;
                case 'empty':
                    isValid = (eleValue == 0) ? true:false;
                    break;
                case 'before':
                    var date_value = new Date(value);
                    eleValue = new Date(eleValue);
                    isValid = (eleValue < date_value) ? true:false;
                    break;
                case 'after':
                    var date_value = new Date(value);
                    eleValue = new Date(eleValue);
                    isValid = (eleValue > date_value) ? true:false;
                    break;
                default:
                    isValid = false;
            }
//            console.log(field_id + ' ' + eleValue + ' ' + operator + ' ' + value + ' result => ' + isValid);
            return isValid;
        }
    }

    // Check for all above this field.
    function checkCondLogicOfField(field_id, field_value, listFieldsIds) {
        var isValid = true, j = 0;
        if (!field_value.conditional_logic) {
            return isValid;
        }
        var numFields = field_value.conditional_logic.field_id ? field_value.conditional_logic.field_id.length:0;
        // Check for all fields is conditional logic of this field
        for(; j < numFields; j++)
        {
            var fId = prefix + field_value.conditional_logic.field_id[j];
            // Check if the position of conditional logic field is above position of this fields
            if (indexOfField(listFieldsIds, fId) < indexOfField(listFieldsIds, field_id)) {
                var op = field_value.conditional_logic.compare[j];
                var val = field_value.conditional_logic.value[j];
                if (checkCondLogicItem(fId, op, val)) {
                    isValid = true;
                    if (field_value.conditional_scope == 'any') {
                        break;
                    }
                } else {
                    if (field_value.conditional_scope == 'all') {
                        isValid = false; break;
                    } else {
                        isValid = false;
                    }
                }
            } else {
                continue;
            }
        }
        return isValid;
    }
    // END: CHECK CONDITIONAL LOGIC SECTION

    // CHECK AFTER PRESS SUBMIT
    function validateAgreement(page) {
        if (page) {
            var agreementElements = $$('#page_' + page + '-wrapper .yndform_agreement_checkbox-element');
        } else {
            var agreementElements = $$('.yndform_agreement_checkbox-element');
        }
        var isValid = true;
        if (agreementElements.length != 0) {
            agreementElements.forEach(function (ele) {
                if (!isValid) {
                    return false;
                }
                var field_id = ele.get('id');
                if (arrayIsValid[field_id] !== undefined &&  arrayIsValid[field_id] == false) {
                    return false;
                }

                if (!ele.checked) {
                    isValid = false;
                    if (!$$('.form-errors').length) {
                        var form_errors = new Element('ul.form-errors');
                        var div_element = $$('#form_detail .form-elements').getParent()[0];
                        form_errors.inject(div_element, 'top');
                    } else {
                        var form_errors = $$('.form-errors')[0];
                    }
                    form_errors.insertAdjacentHTML('beforeEnd', '<li>' + ele.innerHTML + '<ul class="errors"><li>Please agree to all the terms of service</li></ul></li>');
                }
            })
        }
        return isValid;
    }

    function getConfirmationNotification(type) {
        if (type == 'confirmation') {
            var order = confOrder;
            var conditionalLogic = confConditionalLogic;
        } else if (type == 'notification') {
            var order = notiOrder;
            var conditionalLogic = notiConditionalLogic;
        } else {
            return;
        }
        var hasSelected = false;
        Object.each(order, function (value, key) {
            if (!hasSelected) {
                var noti = conditionalLogic[value];
                if (noti.conditional_enabled) {
                    var isValid = true, numFields = noti.conditional_logic.field_id.length, j = 0;
                    for (j = 0; j < numFields; j++) {
                        var fId = prefix + noti.conditional_logic.field_id[j];
                        var op = noti.conditional_logic.compare[j];
                        var val = noti.conditional_logic.value[j];
                        if (checkCondLogicItem(fId, op, val)) {
                            isValid = true;
                            if (noti.conditional_scope == 'any') {
                                break;
                            }
                        } else {
                            if (noti.conditional_scope == 'all') {
                                isValid = false;
                                break;
                            } else {
                                isValid = false;
                            }
                        }
                    }
                } else {
                    var isValid = true;
                }

                if (isValid) hasSelected = value;
            }
        })

        if (hasSelected) {
            var element = new Element('input', {
                type: 'hidden',
                name: 'selected_' + type,
                value: hasSelected,
                id: 'selected_' + type,
            });
            element.inject($('form_detail'), 'bottom');
        }
    }
</script>