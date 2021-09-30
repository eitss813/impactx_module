function yndformToggleConditionalLogic(el) {

	//switch conditional list
	el.checked ? conditionalContainer.show() : conditionalContainer.hide();

	// add the first element
	if (Object.getLength(fieldData) && !conditionalList.getChildren('.yndform_conditional_logic_item').length)
	{
		var newItem = yndformCreateNewConditionalItem();
		newItem.getChildren('.yndform_conditional_logic_buttons').addClass('yndform_nominus');
	}
}

function yndformCreateNewConditionalItem() {
	// the conditional row
	var conditionalItem = new Element('div', {
		'class' : 'yndform_conditional_logic_item'
	});

	var newFieldList = fieldList.clone();
	newFieldList.addEvent('change', yndformOnChangeField);

	newFieldList.inject(conditionalItem);

	new Element('select', {
		'class' : 'yndform_conditional_logic_compare'
	}).inject(conditionalItem);

	new Element('input', {
		'class' : 'yndform_conditional_logic_options'
	}).inject(conditionalItem);

	var buttons = new Element('div', {
		'class' : 'yndform_conditional_logic_buttons'
	});

	new Element('span', {
		'class' : 'yndform_conditional_logic_add ynicon yn-plus',
	}).addEvent('click', yndformAddItem.bind(this)).inject(buttons);

	new Element('span', {
		'class' : 'yndform_conditional_logic_remove ynicon yn-minus',
	}).addEvent('click', yndformRemoveItem.bind(this)).inject(buttons);

	buttons.inject(conditionalItem);
	yndformUpdateItem(conditionalItem);
	conditionalItem.inject(conditionalList);
	return conditionalItem;
}

function yndformOnChangeField() {
	var conditionalItem = this.getParent('.yndform_conditional_logic_item');
	yndformUpdateItem(conditionalItem);
}

function yndformUpdateItem(conditionalItem) {
	var itemField = conditionalItem.getChildren('.yndform_conditional_logic_field')[0];
	var field_id = itemField.get('value'),
		itemCompare = conditionalItem.getChildren('.yndform_conditional_logic_compare')[0],
		itemOption = conditionalItem.getChildren('.yndform_conditional_logic_options')[0],
		itemType = conditionalItem.getChildren('.yndform_conditional_logic_type')[0];

	// inject compare method
	var compare = ynformGetCompare(field_id),
		length = compare.length,
		newItemCompare = new Element('select', {
			'class' : 'yndform_conditional_logic_compare',
			'name': 'conditional_logic[compare][]'
		});
	// inject choices
	for (var i=0;i<length;i++) {
		new Element('option', {
			'value': compare[i],
			'text': compare[i].replace('_', ' ')
		}).inject(newItemCompare);
	}

	if (itemCompare)
	{
		newItemCompare.replaces(itemCompare);
	} else {
		newItemCompare.inject(conditionalItem);
	}

	// conditional logic is predefined?
	var randomID = 'yndform_rand_' + Math.floor(Math.random() * 1e4);
	if (yndformIsPredefined(field_id)) {
		var newItemOptions = new Element('select', {
			'class': 'yndform_conditional_logic_options',
			'name': 'conditional_logic[value][]'
		});
		var options = ynformGetOptions(field_id);
		for (var option in options) {
			if (options.hasOwnProperty(option)) {
				new Element('option', {
					'value': option,
					'text': options[option]
				}).inject(newItemOptions);
			}
		}
	} else if (fieldData[field_id]['type'] == 'birthdate' || fieldData[field_id]['type'] == 'date') {
		var newItemOptions = new Element('div', {
			'class' : 'form-element yndform_conditional_logic_options',
		});
		new Element('input', {
			'id': randomID,
			'name': 'conditional_logic[value][]',
			'type' : 'text'
		}).inject(newItemOptions);
	} else {
		var newItemOptions = new Element('input', {
			'class' : 'yndform_conditional_logic_options',
			'name': 'conditional_logic[value][]',
			'type' : 'text'
		});
	}
	conditionalItem.getChildren('.ui-datepicker-trigger').destroy();

	if (itemOption) {
		newItemOptions.replaces(itemOption);
	} else {
		newItemOptions.inject(conditionalItem);
	}
	// additional type info
	var newItemType = new Element('input', {
		'class': 'yndform_conditional_logic_type',
		'name': 'conditional_logic[type][]',
		'type': 'hidden',
		'value': fieldData[field_id]['type']
	});
	if (itemType) {
		newItemType.replaces(itemType);
	} else {
		newItemType.inject(conditionalItem);
	}

	setTimeout(function(){
		if (jQuery('#' + randomID).length) {
			jQuery('#' + randomID).datepicker({
				firstDay: 1,
				dateFormat: 'yy-mm-dd',
				showOn: "button",
				buttonImage:'<?php echo $this->baseUrl() ?>/application/modules/Yndynamicform/externals/images/calendar.png',
				buttonImageOnly: true,
				buttonText: '<?php echo $this -> translate("Select date")?>'
			});
		}
	}, 100);
}

function yndformSetItem(conditionalItem, preset) {

	var itemFieldList = conditionalItem.getElement('.yndform_conditional_logic_field');

	if (preset.hasOwnProperty('field_id')) {
		itemFieldList.set('value', preset['field_id']);
		itemFieldList.fireEvent('change');
	}

	var itemCompareList = conditionalItem.getElement('.yndform_conditional_logic_compare');
	var itemOptionList = conditionalItem.getElement('.yndform_conditional_logic_options');

	if (preset.hasOwnProperty('compare')) {
		itemCompareList.set('value', preset['compare']);
	}

	if (preset.hasOwnProperty('value')) {
		if (itemOptionList.getElement('input')) {
			itemOptionList.getElement('input').set('value', preset['value']);
		} else {
			itemOptionList.set('value', preset['value']);
		}
	}
}

function ynformGetCompare(field_id) {
	var field = fieldData[field_id];
	if (field)
		return field.conditional_compare;
	else
		return [];
}

function ynformGetOptions(field_id) {
	var field = fieldData[field_id];

	if (field) {
		return field.conditional_options;
	} else {
		return [];
	}
}

function yndformIsPredefined(field_id) {
	var field = fieldData[field_id];
	if (field)
		return field.conditional_predefined ? 1: 0;
	else
		return 0;
}

function yndformAddItem(evt) {
	var conditionalItem = $(evt.target).getParent('.yndform_conditional_logic_item');
	conditionalItem.getChildren('.yndform_conditional_logic_buttons').removeClass('yndform_nominus');
	var newItem = yndformCreateNewConditionalItem();
	newItem.inject(conditionalItem, 'after');
}

function yndformRemoveItem(evt) {
	var conditionalItem = $(evt.target).getParent('.yndform_conditional_logic_item');

	// hide minus is this is the last element
	conditionalItem.destroy();

	var childrenList = conditionalList.getChildren('.yndform_conditional_logic_item');
	if (childrenList.length == 1) {
		childrenList[0].getChildren('.yndform_conditional_logic_buttons').addClass('yndform_nominus');
	}
}