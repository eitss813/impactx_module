<?php
$baseUrl = $this->layout()->staticBaseUrl;
$this->headLink()
	->appendStylesheet($baseUrl . 'application/modules/Yndynamicform/externals/styles/ui-redmond/jquery-ui-1.8.18.custom.css')
	->appendStylesheet($baseUrl . 'application/modules/Sitepage/externals/styles/style_sitepage.css');
$this->headScript()
	->appendFile($baseUrl .'application/modules/Yndynamicform/externals/scripts/jquery-1.10.2.min.js')
	->appendFile($baseUrl .'application/modules/Yndynamicform/externals/scripts/jquery-ui-1.11.4.min.js')
	->appendFile($baseUrl .'application/modules/Yndynamicform/externals/scripts/conditional_logic.js');
?>

<?php
// get option id by passing directly to the tpl, or from the request's param
$form_id = (isset($this->option_id)) ? $this->option_id : Zend_Controller_Front::getInstance()->getRequest()->getParam('form_id', 0);
$page_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('page_id', 0);

if ($form_id) {
	$form = Engine_Api::_()->getItem('yndynamicform_form', $form_id);
	$option_id = $form->option_id;
} else {
	$option_id = (isset($this->option_id)) ? $this->option_id : Zend_Controller_Front::getInstance()->getRequest()->getParam('option_id', 0);
}
$field_id = (isset($this->field_id)) ? $this->field_id : Zend_Controller_Front::getInstance()->getRequest()->getParam('field_id', 0);

$field_screen_type = Zend_Controller_Front::getInstance()->getRequest()->getActionName();

if($field_id){
	$fieldMeta = Engine_Api::_()->fields()->getField($field_id, 'yndynamicform_entry');
}
?>

<?php if($option_id): ?>
	<?php
	// Get fields for conditional logic
	$fieldMaps = Engine_Api::_()->fields()->getFieldsMaps('yndynamicform_entry')->getRowsMatching('option_id', $option_id);
	$fieldInfo = Engine_Api::_()->yndynamicform()->getFieldInfo('fields');
	$fieldData = array();

	foreach( $fieldMaps as $map ) {
		$field = $map->getChild();

		if( $field->type == 'profile_type' || !$fieldInfo[$field->type]['conditional_compare'] )
			continue;

		// exclude current field
		if ($field_id && ($field_id == $field->field_id))
			continue;

		$fieldItem = $field->toArray();
		unset($fieldItem['description']);
		$fieldItem['options'] = array();
		$fieldItem['conditional_predefined'] = 0;

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
	<div id="yndform_conditional_container">

		<!-- append for create -->
                <!-- In case of edit, also need to run same Field Create form -->
		<?php if($field_screen_type == 'field-create' || $field_screen_type == 'field-edit'):?>

			<!-- Metrics Type -->
			<div id="metric_fetch_type-wrapper" class="form-wrapper">
				<div id="metric_fetch_type-label" class="form-label">
					<label for="label" class="required"><?php echo $this->translate('Select Metrics') ?></label>
				</div>
                            
                                <?php $metric_fetch_type = Zend_Controller_Front::getInstance()->getRequest()->getParam('metric_fetch_type', ''); ?>
                                <?php 
                                    if($field_screen_type == 'field-edit')
                                        $metric_fetch_type = 'existing_metric';
                                ?>
				<div id="metric_fetch_type-element" class="form-element">
					<select name="metric_fetch_type" id="metric_fetch_type" onchange="javascript:onselect_metric_fetch_type('call')">
						<option value="null"><?php echo $this->translate('Select') ?></option>
                                                <option value="existing_metric" <?php echo (!empty($metric_fetch_type) && ($metric_fetch_type == 'existing_metric'))? 'selected': ''; ?>><?php echo $this->translate('Existing Metrics') ?></option>
						<option value="new_metric" <?php echo (!empty($metric_fetch_type) && ($metric_fetch_type == 'new_metric'))? 'selected': ''; ?>><?php echo $this->translate('New Metrics') ?></option>
					</select>
				</div>
			</div>

                        <?php if( $field_screen_type == 'field-create' ): ?>
        			<input class="metric_input" type="hidden" name="selected_metric_id" id="selected_metric_id" value="">
                        <?php endif; ?>

			<!-- display metric create form -->
			<div id="metric_create_form_container" style="display: none">
				<h4>Create a New Metric</h4>
				<span id="metric_create_success" style="display: none;color: green">Created Successfully</span>
				<div id="metric_create_form">
					<input type="text" placeholder="Name" name="metric_name" id="metric_name" value="<?php echo Zend_Controller_Front::getInstance()->getRequest()->getParam('metric_name', ''); ?>">
					<input type="text" placeholder="Description" name="metric_description" id="metric_description" value="<?php echo Zend_Controller_Front::getInstance()->getRequest()->getParam('metric_description', ''); ?>">
					<input type="text" placeholder="Unit" name="metric_unit" id="metric_unit" value="<?php echo Zend_Controller_Front::getInstance()->getRequest()->getParam('metric_unit', ''); ?>">
					<button id="metric_create_button" type="button" onclick="saveMetrics()">Create</button> <span id="create_metric_spinner"></span>
				</div>
				<br>
			</div>

			<!-- display metric autocomplete -->
                        <?php
                            $metricsSuggestion = Engine_Api::_()->impactx()->metricsSuggestion($page_id, '');
                        ?>
                        <div id="metric_autocomplete_container" class="metric_multiradio" style="display: none">
                            <?php if( !empty($metricsSuggestion) ): ?>
                                <?php $tempMetricsArray = array(); ?>
                                <?php $metricAutocompletePostVal = Zend_Controller_Front::getInstance()->getRequest()->getParam('metric_autocomplete', ''); ?>
                                <?php foreach($metricsSuggestion as $metricSuggestion): ?>
                                <?php $metricsID = $metricSuggestion['id']; ?>
                                <?php $metricsLabel = $metricSuggestion['label']; ?>
                                <?php $metricsMetricName = str_replace("\\'", "'", $metricSuggestion['metric_name']); ?>
                                <?php $metricsMetricDescription = str_replace("\\'", "'", $metricSuggestion['metric_description']); ?>
                                <?php $metricsMetricUnit = $metricSuggestion['metric_unit']; ?>
                                <?php $metricsMetricId = $metricSuggestion['metric_id']; ?>
                                <?php $tempMetricsArray[$metricsID] = $metricsLabel; ?>
                                
                                    <div class='metric_multiradio_single_div'>
                                        <input type="radio" <?php echo (!empty($metricAutocompletePostVal) && ($metricAutocompletePostVal == $metricSuggestion['id']))? 'checked': ''; ?> value="<?php echo $metricSuggestion['id'] ?>" class="metric_input" name="metric_autocomplete" id="metric_autocomplete" onclick="select_metric('<?php echo $metricsID ?>', '<?php echo $metricsLabel ?>', '<?php echo $metricsMetricName ?>', '<?php echo $metricsMetricDescription ?>', '<?php echo $metricsMetricUnit ?>', '<?php echo $metricsMetricId ?>'); insert_metric('onload');">
                                        <label><?php echo $metricsMetricName ?></label>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                                <!-- <input class="metric_input" type="text" placeholder="Type Metric Name...." name="metric_autocomplete" id="metric_autocomplete" value=""> -->
			</div>
                        
                        <div class="form-wrapper" id="metric_multiradio_value-wrapper" style="display: none">
                            <h4>Selected Metric<br />Please <a href="javascript:void(0)" onclick="reselect_metric();">click here</a> to change the metrics</h4>
                            <input type="text" name="metric_multiradio_value" class="metric_multiradio_value" id="metric_multiradio_value" readonly="readonly" value="<?php echo Zend_Controller_Front::getInstance()->getRequest()->getParam('metric_multiradio_value', ''); ?>" />
                        </div>
                        
                        <div id="label-wrapper" class="form-wrapper" style="display: block;"></div>
                        <div id="description-wrapper" class="form-wrapper" style="display: block;"></div>
                        <input type="hidden" name="temp_label" id="hidden_label" value="" />
                        <input type="hidden" name="temp_description" id="hidden_description" value="" />

			<!-- Metrics Aggregate fields-->
                        <?php if($field_screen_type == 'field-create'): ?>
                            <div id="metric_aggregate_type-wrapper" class="form-wrapper">
                                    <div id="metric_aggregate_type-label" class="form-label">
                                            <label class="optional"><?php echo $this->translate('Select Metrics Input ') ?>&nbsp;</label>
                                    </div>
                                    <div id="metric_aggregate_type-element" class="form-element">
                                            <?php $metric_aggregate_type = Zend_Controller_Front::getInstance()->getRequest()->getParam('metric_aggregate_type', ''); ?>
                                            <select name="metric_aggregate_type" id="metric_aggregate_type" onchange="javascript:onselect_metric_aggregate_type()">
                                                    <option value="null"><?php echo $this->translate('Standard Input') ?></option>
                                                    <option value="metric_sum" <?php echo (!empty($metric_aggregate_type) && ($metric_aggregate_type == 'metric_sum'))? 'selected': ''; ?>><?php echo $this->translate('Sum of other fields') ?></option>
                                                    <option value="own_formula" <?php echo (!empty($metric_aggregate_type) && ($metric_aggregate_type == 'own_formula'))? 'selected': ''; ?>><?php echo $this->translate('Make your own formula') ?></option>
                                            </select>

                                            <div id="own_formula_input" class="own_formula_input">
                                                <div id="own_formula_input-wrapper">
                                                    <h4>
                                                        <?php echo $this->translate('Make your own formula use the exact field name to make the formula, please see examples following: <br />i.e [field-1]-[field-2] OR [field-1]*[field-2] OR ([field-1]*[field-2])/2 AND and many more<br /><b>NOTE: allowed params are [field_name], (), %, /, -, +, * and 0 to 9</b>');  ?>
                                                    </h4>

                                                    <input type="text" name="own_formula_input" placeholder="Enter your formula" value="<?php echo Zend_Controller_Front::getInstance()->getRequest()->getParam('own_formula_input', ''); ?>" />
                                                </div>
                                            </div>
                                    </div>
                            </div>
                        
                            <div id="yndform_own_formula_metric_list">
                                    <div id="yndform_own_formula_metric_list-wrapper">
                                            <?php $tempAvailableFields = array(); ?>
                                            <h4><?php echo $this->translate('Find available fields following:') ?></h4>
                                            <?php foreach ($fieldData as $key => $field): ?>
                                                    <?php if($field['type'] == 'integer' || $field['type'] == 'float' ):?>
                                                            <?php $tempAvailableFields[] = array('field_id' => $field['field_id'], 'label' => $field['label']); ?>
                                                            <?php echo '[' . $field['label'] . ']';?> <br>
                                                    <?php endif;?>
                                            <?php endforeach; ?>
                                            <input type='hidden' name="own_formula_metric_all_list" value='<?php echo json_encode($tempAvailableFields); ?>'>
                                    </div>
                            </div>

                            <div id="yndform_metric_conditional_list">
                                    <div id="metric_sum_fields-wrapper">
                                            <h4><?php echo $this->translate('Select Fields For Aggregate') ?></h4>
                                            <?php foreach ($fieldData as $key => $field): ?>
                                                    <?php if($field['type'] == 'integer' || $field['type'] == 'float' ):?>
                                                            <input type="checkbox" name="metric_aggregate_fields[]" id="<?php echo $field['field_id'];?>" value="<?php echo $field['field_id'];?>"> <?php echo $field['label'];?> <br>
                                                    <?php endif;?>
                                            <?php endforeach; ?>
                                    </div>
                            </div>
                        
                        <?php endif; ?>

		<?php endif;?>

		<!-- append for edit -->
		<?php if($field_screen_type == 'field-edit'):?>

			<?php
			$metric_aggregate_type = $fieldMeta->config['metric_aggregate_type'];
			$metric_aggregate_fields = $fieldMeta->config['metric_aggregate_fields'];
			$selected_metric_id = $fieldMeta->config['selected_metric_id'];
			$own_formula_input = $fieldMeta->config['own_formula_input'];
                        
                        // Start work to make the new formula according to the ids, may number fields name has been updated!
                        $own_formula_by_id = $fieldMeta->config['own_formula_by_id'];
                        foreach ($fieldData as $key => $field):
                            if($field['type'] == 'integer' || $field['type'] == 'float' ):
                                $own_formula_by_id = @str_replace('field_id_' . $field['field_id'], '[' . $field['label'] . ']', $own_formula_by_id);
                            endif;
                        endforeach;
                        
                        $own_actual_formula = !empty($own_formula_by_id)? $own_formula_by_id: $fieldMeta->config['own_actual_formula'];
			?>
                        
                        <?php foreach ($fieldData as $key => $field): ?>
                                <?php if($field['type'] == 'integer' || $field['type'] == 'float' ):?>
                                        <?php $tempAvailableFields[] = array('field_id' => $field['field_id'], 'label' => $field['label']); ?>
                                        <?php // echo '[' . $field['label'] . ']<br>';?>
                                <?php endif;?>
                        <?php endforeach; ?>
                        
			<?php if($metric_aggregate_type == "metric_sum" && count($metric_aggregate_fields)):?>
                        
				<!-- Metrics Aggregate fields-->
				<div id="metric_aggregate_type-wrapper" class="form-wrapper">
					<div id="metric_aggregate_type-label" class="form-label">
						<label class="optional"><?php echo $this->translate('Select Metrics Input ') ?> &nbsp; &nbsp;</label>
					</div>
					<div id="metric_aggregate_type-element" class="form-element">
                                                <select name="metric_aggregate_type" id="metric_aggregate_type" onchange="javascript:onselect_metric_aggregate_type()">
                                                        <option value="null"><?php echo $this->translate('Standard Input') ?></option>
                                                        <option value="metric_sum" <?php echo (!empty($metric_aggregate_type) && ($metric_aggregate_type == 'metric_sum'))? 'selected': ''; ?>><?php echo $this->translate('Sum of other fields') ?></option>
                                                        <option value="own_formula" <?php echo (!empty($metric_aggregate_type) && ($metric_aggregate_type == 'own_formula'))? 'selected': ''; ?>><?php echo $this->translate('Make your own formula') ?></option>
                                                </select>
                                            
                                                <div id="own_formula_input" class="own_formula_input">
                                                    <div id="own_formula_input-wrapper">
                                                        <h4>
                                                            <?php echo $this->translate('Make your own formula use the exact field name to make the formula, please see examples following: <br />i.e [field-1]-[field-2] OR [field-1]*[field-2] OR ([field-1]*[field-2])/2 AND and many more<br /><b>NOTE: allowed params are [field_name], (), %, /, -, +, * and 0 to 9</b>');  ?>
                                                        </h4>
                                                        <input type="text" name="own_formula_input" placeholder="Enter your formula" value="<?php echo $own_actual_formula; ?>" />
                                                    </div>
                                                </div>
					</div>
				</div>
                                
                                <div id="yndform_own_formula_metric_list">
                                        <div id="yndform_own_formula_metric_list-wrapper">
                                                <?php $tempAvailableFields = array(); ?>
                                                <h4><?php echo $this->translate('Find available fields following:') ?></h4>
                                                <?php foreach ($fieldData as $key => $field): ?>
                                                        <?php if($field['type'] == 'integer' || $field['type'] == 'float' ):?>
                                                                <?php $tempAvailableFields[] = array('field_id' => $field['field_id'], 'label' => $field['label']); ?>
                                                                <?php echo '[' . $field['label'] . ']';?> <br>
                                                        <?php endif;?>
                                                <?php endforeach; ?>
                                                <input type='hidden' name="own_formula_metric_all_list" value='<?php echo json_encode($tempAvailableFields); ?>'>
                                        </div>
                                </div>

				<div id="yndform_metric_conditional_list">
					<div id="metric_sum_fields-wrapper">
						<h4><?php echo $this->translate('Select Fields For Aggregate') ?></h4>
						<?php foreach ($fieldData as $key => $field): ?>
							<?php if($field['type'] == 'integer' || $field['type'] == 'float' ):?>
								<input type="checkbox" <?= in_array($field['field_id'], $metric_aggregate_fields) ? ' checked="checked"' : '';?> name="metric_aggregate_fields[]" id="<?php echo $field['field_id'];?>" value="<?php echo $field['field_id'];?>"> <?php echo $field['label'];?> <br>
							<?php endif;?>
						<?php endforeach; ?>
					</div>
				</div>

			<?php else: ?>

				<!-- Metrics Aggregate fields-->
				<div id="metric_aggregate_type-wrapper" class="form-wrapper">
					<div id="metric_aggregate_type-label" class="form-label">
						<label class="optional"><?php echo $this->translate('Select Metrics Input') ?>&nbsp;</label>
					</div>
					<div id="metric_aggregate_type-element" class="form-element">
                                                <select name="metric_aggregate_type" id="metric_aggregate_type" onchange="javascript:onselect_metric_aggregate_type()">
                                                        <option value="null"><?php echo $this->translate('Standard Input') ?></option>
                                                        <option value="metric_sum" <?php echo (!empty($metric_aggregate_type) && ($metric_aggregate_type == 'metric_sum'))? 'selected': ''; ?>><?php echo $this->translate('Sum of other fields') ?></option>
                                                        <option value="own_formula" <?php echo (!empty($metric_aggregate_type) && ($metric_aggregate_type == 'own_formula'))? 'selected': ''; ?>><?php echo $this->translate('Make your own formula') ?></option>
                                                </select>
                                            
                                                <div id="own_formula_input" class="own_formula_input">
                                                    <div id="own_formula_input-wrapper">
                                                        <h4>
                                                            <?php echo $this->translate('Make your own formula use the exact field name to make the formula, please see examples following: <br />i.e [field-1]-[field-2] OR [field-1]*[field-2] OR ([field-1]*[field-2])/2 AND and many more<br /><b>NOTE: allowed params are [field_name], (), %, /, -, +, * and 0 to 9</b>');  ?>
                                                        </h4>
                                                        <input type="text" name="own_formula_input" placeholder="Enter your formula" value="<?php echo $own_actual_formula; ?>" />
                                                    </div>
                                                </div>
					</div>
				</div>
                                
                                <div id="yndform_own_formula_metric_list">
                                        <div id="yndform_own_formula_metric_list-wrapper">
                                                <?php $tempAvailableFields = array(); ?>
                                                <h4><?php echo $this->translate('Find available fields following:') ?></h4>
                                                <?php foreach ($fieldData as $key => $field): ?>
                                                        <?php if($field['type'] == 'integer' || $field['type'] == 'float' ):?>
                                                                <?php $tempAvailableFields[] = array('field_id' => $field['field_id'], 'label' => $field['label']); ?>
                                                                <?php echo '[' . $field['label'] . ']';?> <br>
                                                        <?php endif;?>
                                                <?php endforeach; ?>
                                                <input type='hidden' name="own_formula_metric_all_list" value='<?php echo json_encode($tempAvailableFields); ?>'>
                                        </div>
                                </div>

				<div id="yndform_metric_conditional_list">
					<div id="metric_sum_fields-wrapper">
						<h4><?php echo $this->translate('Select Fields For Aggregate') ?></h4>
						<?php foreach ($fieldData as $key => $field): ?>
							<?php if($field['type'] == 'integer' || $field['type'] == 'float' ):?>
								<input type="checkbox" name="metric_aggregate_fields[]" id="<?php echo $field['field_id'];?>" value="<?php echo $field['field_id'];?>"> <?php echo $field['label'];?> <br>
							<?php endif;?>
						<?php endforeach; ?>
					</div>
				</div>

			<?php endif;?>

			<input class="metric_input" type="hidden" name="selected_metric_id" id="selected_metric_id" value="<?php echo $selected_metric_id;?>">
		<?php endif;?>
	</div>

	<script type='text/javascript'>

		// build conditional logic list and data
		window.addEvent('domready', function(){

                        if( document.getElementById("temp_description-wrapper") && document.getElementById("description-wrapper") ) {
                            var temp_description_html = document.getElementById("temp_description-wrapper").innerHTML;
                            temp_description_html = temp_description_html.replaceAll("temp_description", "description");
                            document.getElementById("description-wrapper").innerHTML = temp_description_html;
                            document.getElementById("temp_description-wrapper").remove();
                        }

                        if( document.getElementById("temp_label-wrapper") && document.getElementById("label-wrapper") ) {
                            var temp_label_html = document.getElementById("temp_label-wrapper").innerHTML;
                            temp_label_html = temp_label_html.replaceAll("temp_label", "label");
                            document.getElementById("label-wrapper").innerHTML = temp_label_html;
                            document.getElementById("temp_label-wrapper").remove();
                        }
                        
                        <?php $isDefaultLabelAvailable = Zend_Controller_Front::getInstance()->getRequest()->getParam('label', ''); ?>
                        <?php if( !empty($isDefaultLabelAvailable) || (!empty($field_screen_type) && ($field_screen_type == 'field-edit')) ): ?>
                                if(document.getElementById("label-wrapper"))
                                    document.getElementById("label-wrapper").style.display = 'block';
                                    
                                if(document.getElementById("description-wrapper"))
                                    document.getElementById("description-wrapper").style.display = 'block';
                                
                                onselect_metric_fetch_type('onload');
                                onselect_metric_aggregate_type();
                                insert_metric('onload');
                        <?php endif; ?>
                        
                        <?php if(empty($isDefaultLabelAvailable) && $field_screen_type == 'field-create'): ?>
				// disable the metric aggregate
				document.getElementById('yndform_metric_conditional_list').style.display = "none";

				// disable aggrgate input, as we need to select metric
				document.getElementById('metric_aggregate_type-wrapper').style.display = "none";
                                
                                // disable own formula field!
                                document.getElementById('own_formula_input').style.display = "none";
                                document.getElementById('yndform_own_formula_metric_list').style.display = "none";
                                document.getElementById("metric_multiradio_value").style.display = none;
                                document.getElementById("metric_multiradio_value-wrapper").style.display = none;
                        <?php endif; ?>
                        
                        
			<?php if(false && $field_screen_type == 'field-create'):?>

				// disable the metric aggregate
				document.getElementById('yndform_metric_conditional_list').style.display = "none";

				// disable aggrgate input, as we need to select metric
				document.getElementById('metric_aggregate_type-wrapper').style.display = "none";

				// add metric autocomplete to field
				// append the metric autocomplete
				var contentAutocomplete = new Autocompleter.Request.JSON('metric_autocomplete', '<?php echo $this->url(array( 'module' => 'sitepage' ,'controller' => 'dashboard', 'action' => 'metrics-auto-suggest', 'page_id' => $page_id), 'default', true) ?>', {
					'postVar' : 'text',
					'selectMode': 'pick',
					'autocompleteType': 'tag',
					'className': 'searchbox_autosuggest',
					'customChoices' : true,
					'filterSubset' : true,
					'multiple' : false,
					'injectChoice': function(token){
						var choice = new Element('li', {'class': 'autocompleter-choices1', 'html': token.photo, 'id':token.label});
						new Element('div', {'html': this.markQueryValue(token.label),'class': 'autocompleter-choice1'}).inject(choice);
						this.addChoiceEvents(choice).inject(this.choices);
						choice.store('autocompleteChoice', token);
					}
				});
				contentAutocomplete.addEvent('onSelection', function(element, selected, value, input) {
					document.getElementById('selected_metric_id').value = selected.retrieve('autocompleteChoice').id;
					document.getElementById('label').value = selected.retrieve('autocompleteChoice').metric_name;
					document.getElementById('description').value = selected.retrieve('autocompleteChoice').metric_description;
					document.getElementById('hidden_label').value = selected.retrieve('autocompleteChoice').metric_name;
					document.getElementById('hidden_description').value = selected.retrieve('autocompleteChoice').metric_description;

                                        document.getElementById("label-wrapper").style.display = 'block';
                                        document.getElementById("description-wrapper").style.display = 'block';
                                        
                                        showLimit1();
				});

			<?php endif;?>

			<?php if($field_screen_type == 'field-edit'):?>

				// disable the metric aggregate
				<?php if($metric_aggregate_type == '' || $metric_aggregate_type == null || $metric_aggregate_type == "null" ):?>

					document.getElementById('yndform_metric_conditional_list').style.display = "none";

				<?php endif;?>

			<?php endif;?>

		});
                
                function get_existing_metrics(calling_type) {
                    // var spinner_name = 'metric_list';
                    // var page_nos = parseInt(page_no) - 1;
                    document.getElementById('metric_autocomplete_container').innerHTML = '<img src="' + en4.core.staticBaseUrl + 'application/modules/Sitecrowdfunding/externals/images/loading.gif" />';
                    var url = en4.core.baseUrl + 'sitepage/dashboard/get-available-metrics';
                    // $(spinner_name).innerHTML = '<img src="' + en4.core.staticBaseUrl + 'application/modules/Sitecrowdfunding/externals/images/loading.gif" />';
                    var request = new Request.JSON({
                        url: url,
                        data:{
                            format: 'json',
                            page_id: <?php echo $page_id; ?>,
                            selected_metric_id: (document.getElementById("selected_metric_id"))? document.getElementById("selected_metric_id").value: 0
                            // subject: en4.core.subject.guid,
                            // is_ajax: 0
                        },
                        // evalScripts: true,
                        onSuccess: function (responseJSON) {
                           document.getElementById('metric_autocomplete_container').innerHTML = responseJSON.output;
                           
                           if( responseJSON.is_selected && (responseJSON.is_selected == '1') ) {
                                setTimeout(function() {
                                    document.getElementById('selected_metric_id').value = responseJSON.matrics_id;
                                    
                                    if( calling_type == 'call' ) {
                                        document.getElementById('label').value = responseJSON.matrics_label;
                                        document.getElementById('description').value = responseJSON.metrics_description;
                                        document.getElementById('hidden_label').value = responseJSON.matrics_label;
                                        document.getElementById('hidden_description').value = responseJSON.metrics_description;
                                        showLimit1();

                                        if( document.getElementById('metric_unit') )
                                            document.getElementById('metric_unit').value = responseJSON.metrics_unit;
                                    }

                                    document.getElementById("label-wrapper").style.display = 'block';
                                    document.getElementById("description-wrapper").style.display = 'block';
                                    
                                    insert_metric(calling_type);
                                }, 100);
                           }
                            
                            
                            // $('hidden_ajax_metrics_data').innerHTML = responseHTML;
                            // $('initiative_metric').innerHTML = $('hidden_ajax_metrics_data').getElement('#initiative_metric').innerHTML;
                            // $('hidden_ajax_metrics_data').innerHTML = '';
                            // fundingProgressiveBarAnimation();
                            // Smoothbox.bind($(spinner_name));
                            // en4.core.runonce.trigger();
                        }
                    });
                    request.send();
                }

		function onselect_metric_fetch_type(calling_type){
                        <?php if($field_screen_type == 'field-edit'):?>
                            // var id = 'existing_metric';
                        <?php else: ?>
                            // var id = document.getElementById('metric_fetch_type').value;
                        <?php endif; ?>
                        
                        var id = document.getElementById('metric_fetch_type').value;
                        
			if(id=='new_metric'){
                                if( document.getElementById('metric_create_form_container') )
                                    document.getElementById('metric_create_form_container').style.display = "block";
                                
                                if( document.getElementById('metric_autocomplete_container') )
                                    document.getElementById('metric_autocomplete_container').style.display = "none";
                                
                                if( document.getElementById('metric_multiradio_value-wrapper') )
                                    document.getElementById('metric_multiradio_value-wrapper').style.display = "none";
                                
                                if( document.getElementById('metric_aggregate_type-wrapper') )
                                    document.getElementById('metric_aggregate_type-wrapper').style.display = "block";
			}
			else if(id=="existing_metric"){
                                get_existing_metrics(calling_type);
                                
                                if( calling_type != 'call' ) {
                                    if( document.getElementById('metric_fetch_type-wrapper') )
                                        document.getElementById('metric_fetch_type-wrapper').style.display = "none";
                                }
                                
                                if( document.getElementById('metric_create_form_container') )
                                    document.getElementById('metric_create_form_container').style.display = "none";
                                
                                if( document.getElementById('metric_autocomplete_container') )
                                    document.getElementById('metric_autocomplete_container').style.display = "block";
                                
                                if( document.getElementById('metric_multiradio_value-wrapper') )
                                    document.getElementById('metric_multiradio_value-wrapper').style.display = "block";
                                
                                <?php if($field_screen_type != 'field-edit'):?>
                                    if( document.getElementById('metric_aggregate_type-wrapper') )
                                        document.getElementById('metric_aggregate_type-wrapper').style.display = "block";
                                <?php endif; ?>
			}
			else{
                                if( document.getElementById('metric_create_form_container') )
                                    document.getElementById('metric_create_form_container').style.display = "none";
                                
                                if( document.getElementById('metric_autocomplete_container') )
                                    document.getElementById('metric_autocomplete_container').style.display = "none";
                                
                                if( document.getElementById('metric_multiradio_value-wrapper') )
                                    document.getElementById('metric_multiradio_value-wrapper').style.display = "none";
                                
                                if( document.getElementById('metric_aggregate_type-wrapper') )
                                    document.getElementById('metric_aggregate_type-wrapper').style.display = "none";
			}

                        <?php if( empty($isDefaultLabelAvailable) ): ?>
                            <?php if($field_screen_type != 'field-edit'):?>
                                if( document.getElementById('metric_aggregate_type') )
                                    document.getElementById('metric_aggregate_type').value = null;
                                    
                                if( document.getElementById('selected_metric_id') )
                                    document.getElementById('selected_metric_id').value = null;
                            <?php endif; ?>
                            
                            if( document.getElementById('yndform_metric_conditional_list') )
                                document.getElementById('yndform_metric_conditional_list').style.display = "none";
                            
                            if( document.getElementById('metric_aggregate_fields') )
                                document.getElementById('metric_aggregate_fields').value = null;
                        <?php endif; ?>
		}

		function onselect_metric_aggregate_type(){
                        var id = '';
                        
                        <?php if($field_screen_type == 'field-edit'):?>
                            id = "<?php echo $metric_aggregate_type; ?>";
                        <?php endif; ?>
                        
                        if( document.getElementById('metric_aggregate_type') )
                            id = document.getElementById('metric_aggregate_type').value;
                        
			if(id=='metric_sum' || id=='own_formula'){
				document.getElementById('yndform_metric_conditional_list').style.display = "block";
			}else{
				document.getElementById('yndform_metric_conditional_list').style.display = "none";
			}
                        
                        if(id=='own_formula'){
                            document.getElementById('own_formula_input').style.display = "block";
                            document.getElementById('yndform_own_formula_metric_list').style.display = "block";
                            document.getElementById('yndform_metric_conditional_list').style.display = "none";
                        }else {
                            document.getElementById('own_formula_input').style.display = "none";
                            document.getElementById('yndform_own_formula_metric_list').style.display = "none";
                        }
                        
		}

		function saveMetrics(){
			var metric_name = document.getElementById('metric_name').value;
			var metric_description = document.getElementById('metric_description').value;
			var metric_unit = document.getElementById('metric_unit').value;

			if(metric_name && metric_unit && metric_description){
				document.getElementById("create_metric_spinner").innerHTML = '<img src="' + en4.core.staticBaseUrl + 'application/modules/Sitecrowdfunding/externals/images/loading.gif" />';
				document.getElementById('metric_create_success').style.display = 'none';
				var request1 = new Request.JSON({
					url: en4.core.baseUrl + 'sitepage/dashboard/create-metrics',
					method: 'POST',
					data: {
						format: 'json',
						metric_name: metric_name,
						metric_description: metric_description,
						metric_unit: metric_unit,
						page_id : "<?php echo $page_id;?>"
					},
					onRequest: function () {
						console.log('debugging onRequest')
					},
					onSuccess: function (responseJSON) {
                                        
                                                metrics_array[responseJSON.metric_id] = responseJSON.metric_name;
                                        
                                                console.log('debugging onSuccess');
                                                document.getElementById("create_metric_spinner").innerHTML = '';
                                                document.getElementById('metric_create_success').style.display = 'block';
                                                document.getElementById('selected_metric_id').value = responseJSON.metric_id;
                                                document.getElementById('label').value = responseJSON.metric_name;
                                                document.getElementById('description').value = responseJSON.metric_description;
                                                document.getElementById('hidden_label').value = responseJSON.metric_name;
                                                document.getElementById('hidden_description').value = responseJSON.metric_description;

                                                document.getElementById("label-wrapper").style.display = 'block';
                                                document.getElementById("description-wrapper").style.display = 'block';
                                                
                                                document.getElementById("metric_fetch_type").value = 'existing_metric';
                                                onselect_metric_fetch_type('onload');
                                                
                                                showLimit1();
					}
				});
				request1.send();
			}
		}



	</script>
<?php endif ?>

<style>
	div#metric_aggregate_type-wrapper {
		display: flex;
		flex-wrap: wrap;
		flex-direction: column;
	}
	ul.searchbox_autosuggest {
		position: unset !important;
	}
	#yndform_conditional_container {
		background-color: unset !important;
		 border: unset !important;
		 border-top: unset !important;
		 padding: unset !important;
	}
	#yndform_conditional_container #metric_fetch_type-wrapper{
		 margin-right: unset !important;
		 // display: block !important;
	}
	#yndform_conditional_container .form-wrapper .form-label {
		text-align: unset !important;
		line-height: unset !important;
		padding-left: unset !important;
	}
	.global_form_popup .form-elements .form-wrapper {
		margin: 15px auto !important;
	}
	div#buttons-wrapper, div#label-wrapper {
		 margin-left: unset !important;
	}
	#yndform_conditional_container .form-wrapper select {
		width: 100% !important;
	}
	#metric_create_form input {
		margin: 10px 0px;
	}
	#metric_create_button{
		float: right;
	}
	#metric_create_form_container{
		padding: 20px;
		border: 1px solid #ccc;
	}
	#metric_create_form_container h4{
		text-decoration: underline;
	}
	ul.searchbox_autosuggest {
		max-height: 84px;
		overflow-y: auto !important;
	}
	#metric_sum_fields-wrapper input[type="checkbox"]{
		margin-top: 4px;
	}
	#global_page_sitepage-manageforms-field-create .form-elements,
	#global_page_sitepage-manageforms-field-edit .form-elements{
		margin-bottom: 85px;
	}
</style>

<script> 
    var metrics_array = JSON.parse('<?php echo json_encode($tempMetricsArray); ?>');
    function select_metric(id, label, metricsLabel, metricsDescription, metricsUnit, metricsId) {
        document.getElementById('selected_metric_id').value = metricsId;
        document.getElementById('label').value = metricsLabel;
        document.getElementById('description').value = metricsDescription;
        document.getElementById('hidden_label').value = metricsLabel;
        document.getElementById('hidden_description').value = metricsDescription;
        
        if( document.getElementById('metric_unit') )
            document.getElementById('metric_unit').value = metricsUnit;

        document.getElementById("label-wrapper").style.display = 'block';
        document.getElementById("description-wrapper").style.display = 'block';
        document.getElementById("metric_fetch_type-wrapper").style.display = 'none';
        
        showLimit1();
    }
    
    function insert_metric(calling_type) {
        var selected_metric = document.querySelector('input[name="metric_autocomplete"]:checked').value;
        
        if( selected_metric && metrics_array[selected_metric] ) {
            document.getElementById("metric_multiradio_value").value = metrics_array[selected_metric];
            document.getElementById("metric_multiradio_value-wrapper").style.display = 'block';
            document.getElementById("metric_multiradio_value").style.display = 'block';
            
            if( calling_type == 'call' )
                document.getElementById("metric_autocomplete_container").style.display = 'block';
            else
                document.getElementById("metric_autocomplete_container").style.display = 'none';
        }
    }
    
    function reselect_metric() {
        document.getElementById("metric_multiradio_value-wrapper").style.display = 'none';
        document.getElementById("metric_multiradio_value").style.display = 'none';
        document.getElementById("metric_autocomplete_container").style.display = 'block';
        document.getElementById("metric_fetch_type-wrapper").style.display = 'block';
    }
    
    <?php if( $field_screen_type == 'field-edit' ):?>
        onselect_metric_aggregate_type();
    <?php endif; ?>
    
</script>
                                        
<style>
    .metric_multiradio {
        background: #fff;
        padding: 10px;
        border-radius: 5px;
        max-height: 100px;
        overflow-y: scroll;
    }
    
    .metric_multiradio_single_div {
        padding-top: 5px;
        color: #696969;
        font-size: 13px;
    }
    
    .own_formula_input {
        padding-top: 10px;
    }
</style>