<?php

/**
 * socialnetworking.solutions
 *
 * @category   Application_Modules
 * @package    Sesblog
 * @copyright  Copyright 2014-2020 Ahead WebSoft Technologies Pvt. Ltd.
 * @license    https://socialnetworking.solutions/license/
 * @version    $Id: _customFields.tpl 2016-07-23 00:00:00 socialnetworking.solutions $
 * @author     socialnetworking.solutions
 */

?>
<script type="text/javascript">

en4.core.runonce.add(function() {

  var topLevelId = '<?php echo sprintf('%d', (int) @$this->topLevelId) ?>';
  var topLevelValue = '<?php echo sprintf('%d', (int) @$this->topLevelValue) ?>';
  var elementCache = {};

  function getFieldsElements(selector) {
    if( selector in elementCache || $type(elementCache[selector]) ) {
      return elementCache[selector];
    } else {
      return elementCache[selector] = $$(selector);
    }
  }

  function updateFieldValue(element, value) {
    if( element.get('tag') == 'option' ) {
      element = element.getParent('select');
    } else if( element.get('type') == 'checkbox' || element.get('type') == 'radio' ) {
      element.set('checked', Boolean(value));
      return;
    }
    if (element.get('tag') == 'select') {
      if (element.get('multiple')) {
        element.getElements('option').each(function(subEl){
          subEl.set('selected', false);
        });
      }
    }
    if( element ) {
      element.set('value', value);
    }
  }
	var valueNotChanged;
  var changeFields = window.changeFields = function(element, force, isLoad,valueNotChanged) {
    element = $(element);
    // We can call this without an argument to start with the top level fields
    if( !$type(element) ) {
      getFieldsElements('.parent_' + topLevelId).each(function(element) {
        changeFields(element, force, isLoad);
      });
      return;
    }
    // If this cannot have dependents, skip
    if( !$type(element) || !$type(element.onchange) ) {
      return;
    }
    // Get the input and params
    var field_id = element.get('class').match(/field_([\d]+)/i)[1];
    var parent_field_id = element.get('class').match(/parent_([\d]+)/i)[1];
    var parent_option_id = element.get('class').match(/option_([\d]+)/i)[1];
    if( !field_id || !parent_option_id || !parent_field_id ) {
      return;
    }
    force = ( $type(force) ? force : false );
    // Now look and see
    // Check for multi values
    var option_id = [];
    if( element.name.indexOf('[]') > 0 ) {
      if( element.type == 'checkbox' ) { // MultiCheckbox
        getFieldsElements('.field_' + field_id).each(function(multiEl) {
          if( multiEl.checked ) {
            option_id.push(multiEl.value);
          }
        });
      } else if( element.get('tag') == 'select' && element.multiple ) { // Multiselect
        element.getChildren().each(function(multiEl) {
          if( multiEl.selected ) {
            option_id.push(multiEl.value);
          }
        });
      }
    } else if( element.type == 'radio' ) {
      if( element.checked ) {
        option_id = [element.value];
      }
    } else {
      option_id = [element.value];
    }
    // Iterate over children
    getFieldsElements('.parent_' + field_id).each(function(childElement) {
      var childContainer;
      if( childElement.getParent('form').get('class') == 'field_search_criteria' ) {
        childContainer = $try(function(){ return childElement.getParent('li').getParent('li'); });
      }
      if( !childContainer ) {
         childContainer = childElement.getParent('div.form-wrapper');
      }
      if( !childContainer ) {
        childContainer = childElement.getParent('div.form-wrapper-heading');
      }
      if( !childContainer ) {
        childContainer = childElement.getParent('li');
      }
      var childOptions = childElement.get('class').match(/option_([\d]+)/gi);
      for(var i = 0; i < childOptions.length; i++) {
        for(var j = 0; j < option_id.length; j++) {
          if(childOptions[i] == "option_" + option_id[j]) {
            var childOptionId = option_id[j];
            break;
          }
        }
      }
      var childIsVisible = ( 'none' != childContainer.getStyle('display') );
      var skipPropagation = false;
      // Forcing hide
			if(isLoad != 'yes'){
				if(typeof valueNotChanged == 'string' || typeof valueNotChanged == 'number'){
				if(typeof valueNotChanged == 'number')
					var valueId = [valueNotChanged];
				else
					var valueId = valueNotChanged.split(',');
					for(var i =0 ; i<valueId.length;i++){
						if(sesJqueryObject(childElement).hasClass('option_'+valueId[i])){
								sesJqueryObject(childElement).closest('div').parent().css('display','block');
								return;
						}
					}
				}else if(sesJqueryObject(childElement).hasClass('option_'+valueNotChanged)){
					sesJqueryObject(childElement).closest('div').parent().css('display','block');
					return;
				}
			}
				sesJqueryObject(childElement).closest('div').parent().css('display','none');
				updateFieldValue(childElement, null,valueNotChanged);
				return;
    });
    window.fireEvent('onChangeFields');
  }
});
</script>
