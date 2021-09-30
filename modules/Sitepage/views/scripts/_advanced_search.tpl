<?php
	$baseUrl = $this->layout()->staticBaseUrl;
	$this->headLink()->appendStylesheet($baseUrl . 'application/modules/Yndynamicform/externals/styles/ui-redmond/jquery-ui-1.8.18.custom.css');
	$this->headScript()
		->appendFile($baseUrl .'application/modules/Yndynamicform/externals/scripts/jquery-1.10.2.min.js')
		->appendFile($baseUrl .'application/modules/Yndynamicform/externals/scripts/jquery-ui-1.11.4.min.js')
		->appendFile($baseUrl .'application/modules/Yndynamicform/externals/scripts/conditional_logic.js');

	// get option id by passing directly to the tpl, or from the request's param
	$form_id = (isset($this->option_id)) ? $this->option_id : Zend_Controller_Front::getInstance()->getRequest()->getParam('form_id', 0);
	if ($form_id) {
		$form = Engine_Api::_()->getItem('yndynamicform_form', $form_id);
		$option_id = $form->option_id;
	} else {
		$option_id = (isset($this->option_id)) ? $this->option_id : Zend_Controller_Front::getInstance()->getRequest()->getParam('option_id', 0);
	}
	$fieldData = array();
?>

<?php if($option_id): ?>
	<?php
	// Get fields for conditional logic
	$fieldMaps = Engine_Api::_()->fields()->getFieldsMaps('yndynamicform_entry')->getRowsMatching('option_id', $option_id);
	$fieldInfo = Engine_Api::_()->yndynamicform()->getFieldInfo('fields');

	foreach( $fieldMaps as $map ) {
		$field = $map->getChild();

		if( $field->type == 'profile_type' || !$fieldInfo[$field->type]['conditional_compare'] )
			continue;

		$fieldItem = $field->toArray();
		unset($fieldItem['description']);
		$fieldItem['options'] = array();

		if ($field->canHaveDependents()) {
			$childOptions = $field->getOptions();
			// skip if predefined values is empty
			if (!count($childOptions))
				continue;
			$fieldItem['conditional_predefined'] = 1;
			foreach ($childOptions as $childOption)
			{
				$fieldItem['conditional_options'][$childOption->option_id] = $childOption->label;
			}
		} else {
			$fieldItem['conditional_predefined'] = 0;
		}
		if (isset($fieldInfo[$field->type]['multiOptions'])) {
			$fieldItem['conditional_predefined'] = 1;
			foreach ($fieldInfo[$field->type]['multiOptions'] as $key => $value)
			{
				$fieldItem['conditional_options'][$key] = $value;
			}
		}
		if (isset($fieldInfo[$field->type]['conditional_multioptions'])) {
			$fieldItem['conditional_predefined'] = 1;
			$options = Engine_Api::_()->yndynamicform()->getConditionalMultiOptions($field->type);
			foreach ($options as $key => $value)
			{
				$fieldItem['conditional_options'][$key] = $value;
			}
		}
		$fieldItem['conditional_compare'] = $fieldInfo[$field->type]['conditional_compare'];
		$fieldData[$field->field_id] = $fieldItem;
	}
	?>
<?php endif ?>

	<div id="yndform_conditional_container">
		<div id="yndform_conditional_list">
		</div>
	</div>

<script>
	var fieldData,
		conditionalLogicData = [],
		conditionalContainer = $('yndform_conditional_container'),
		conditionalList = $('yndform_conditional_list'),
		fieldList = new Element('select', {
			'class' : 'yndform_conditional_logic_field',
			'name': 'conditional_logic[field_id][]'
		});
	
	window.addEvent('domready', function(){
		fieldData = JSON.parse('<?php echo addslashes(json_encode($fieldData)) ?>');
		// toggle search panel
		// get field data
		<?php foreach ($fieldData as $key => $field): ?>
			new Element('option', {
				'value': <?php echo $key ?>,
				'text': '<?php echo $field['label'] ?>'
			}).inject(fieldList);
		<?php endforeach; ?>
		var conditionalLogicEle = $('advsearch_text');
		if (conditionalLogicEle && conditionalLogicEle.get('value') && conditionalLogicEle.get('value') != 'null') {
			conditionalLogicData = JSON.parse(conditionalLogicEle.get('value'));
			var length = conditionalLogicData.field_id.length;
			for (var i=0; i<length; i++) {
				if (fieldData.hasOwnProperty(conditionalLogicData.field_id[i])) {
					yndformSetItem(yndformCreateNewConditionalItem(), {
						field_id: conditionalLogicData.field_id[i],
						compare: conditionalLogicData.compare[i],
						value: conditionalLogicData.value[i]
					});
				}
			}
		}

		// remove the minus button on last item
		var childrenList = conditionalList.getChildren('.yndform_conditional_logic_item');
		if (childrenList.length == 1) {
			childrenList[0].getChildren('.yndform_conditional_logic_buttons').addClass('yndform_nominus');
		}

		yndformToggleConditionalLogic($('advsearch'));
	});
</script>