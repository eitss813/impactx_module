<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: profiltype.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php
	$this->headScript()
->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Observer.js')
->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.js')
->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Local.js')
->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Request.js');
?>
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl .'application/modules/Sitecrowdfunding/externals/scripts/jquery-3.4.1.min.js'); ?>


<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl .'application/modules/Sitecrowdfunding/externals/scripts/jquery-3.4.1.min.js'); ?>
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl.'application/modules/Sitecrowdfunding/externals/scripts/cropper/cropper.js'); ?>
<?php $this->headLink()->prependStylesheet($this->layout()->staticBaseUrl.'application/modules/Sitecrowdfunding/externals/scripts/cropper/cropper.css'); ?>

<?php include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/sitepage_dashboard_main_header.tpl'; ?>

<div class="generic_layout_container layout_middle">
    <div class="generic_layout_container layout_core_content">

        <div class="layout_middle">
            <?php include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/edit_tabs.tpl'; ?>
            <?php echo $this->partial('application/modules/Sitepage/views/scripts/sitepage_dashboard_section_header.tpl', array( 'sitepage_id'=>$this->sitepage->page_id,'sectionTitle'=> 'Initiatives', 'sectionDescription' => '')); ?>
            <div class="sitepage_edit_content">
                <div id="show_tab_content">
                    <?php echo $this->form->render(); ?>
                </div>
            </div>

        </div>
    </div>
</div>

<script type="text/javascript">
    var $j = jQuery.noConflict();

    // load image in bottom
    function loadImage(){
        var htmlStr = `
             <div id="custom-logo-wrapper" class="form-wrapper">
                <div id="custom-logo-label" class="form-label">
                    <label for="title">Initiative Image</label>
                </div>
                <div id="custom-logo-element" class="form-element">
                    <img id="display_photo_custom_id" style="height: 250px;width: 300px;object-fit: contain;" src="<?php echo $this->initiative->getLogoUrl('thumb.cover'); ?>" />
                <div>
             <div>
        `;

        var newNode = document.createElement('div');
        newNode.innerHTML = htmlStr;

        // Get the reference node
        var referenceNode = document.querySelector('#title-wrapper');

        // Insert the new node before the reference node
        referenceNode.parentNode.insertBefore(newNode, referenceNode.nextSibling);

        $j('#logo_edit_options-wrapper').append('<br/>');

    }

    // load metrics
    function loadMetrics(){

        // Create Metrics Form
        let metricsCount = <?php echo count($this->initiativeMetric);?>

        if(metricsCount == 0 ){
            metricsCount = 1;
        }

        var metricsX = 1; //Initial field counter is 1

        var htmlStr = `
             <div id="custom-metrics-wrapper" class="form-wrapper">
                <div id="custom-metrics-label" class="form-label">
                    <label for="title">Initiative Metrics</label>
                </div>
                <input type="hidden" name="metrics_counter" id="metrics_counter" value=${metricsCount}>


                <?php if(count($this->initiativeMetric) == 0 ):?>
                    <div id="custom-metrics-element" class="form-element custom-metrics-element">
                        <div class="custom-metrics-elements-fields">
                            <input class="metrics_input" type="hidden" placeholder="Metric Id" name="metric_id[]" id="metric_id_1" value="">
                            <input class="metrics_input" type="text" placeholder="Metric Name" name="metric_name[]" id="metric_name_1" value="">
                            <input class="metrics_input" type="text" placeholder="Metric Unit" name="metric_unit[]" id="metric_unit_1" value="">
                            <input class="metrics_input" type="text" placeholder="Metric Value" name="metric_value[]" id="metric_value_1" value="">
                            <input class="metrics_input" type="hidden" name="selected_metric_id[]" id="selected_metric_id_1" value="">
                        </div>
                    </div>
                <?php else: ?>

                    <div id="custom-metrics-element" class="form-element custom-metrics-element">
                        <?php $counter = 1;?>
                        <?php foreach($this->initiativeMetric as $key=>$value):?>
                             <div class="custom-metrics-elements-fields">
                                <input class="metrics_input" type="hidden" placeholder="Metric Id" name="metric_id[]" id="metric_id_<?php echo $counter;?>" value="<?php echo $value['metric_id'];?>">
                                <input class="metrics_input" type="text" placeholder="Metric Name" name="metric_name[]" id="metric_name_<?php echo $counter;?>" value="<?php echo $value['initiativemetric_name'];?>">
                                <input class="metrics_input" type="text" placeholder="Metric Unit" name="metric_unit[]" id="metric_unit_<?php echo $counter;?>" value="<?php echo $value['initiativemetric_unit'];?>">
                                <input class="metrics_input" type="text" placeholder="Metric Value" name="metric_value[]" id="metric_value_<?php echo $counter;?>" value="<?php echo $value['initiativemetric_value'];?>">
                                <input class="metrics_input" type="hidden" name="selected_metric_id[]" id="selected_metric_id_<?php echo $counter;?>" value="">
                                <?php if($key != 0):?>
                                    <button style="float:left;" type="button" id="remove_button">Remove</button>
                                <?php endif; ?>
                            </div>
                            <?php $counter++;?>
                        <?php endforeach;?>
                    </div>

                <?php endif; ?>
             </div>
        `;

        var newNode = document.createElement('div');
        newNode.innerHTML = htmlStr;
        var referenceNode = document.querySelector('#sections-wrapper');
        referenceNode.parentNode.insertBefore(newNode, referenceNode.nextSibling);

        var totalMetrics=<?php echo metricsCount; ?>;

        for (var i=1; i<=totalMetrics; i++) {

            var metrics_input_str = 'metric_name_'+i;
            var selected_metrics_id_input_str = 'selected_metric_id_'+i;
            var metrics_value_input_str = 'metric_value_'+i;

            // append the metrics autocomplete
            var contentAutocomplete = new Autocompleter.Request.JSON(metrics_input_str, '<?php echo $this->url(array( 'module' => 'sitepage' ,'controller' => 'dashboard', 'action' => 'metrics-auto-suggest', 'page_id' => $this->page_id), 'default', true) ?>', {
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
            })
            contentAutocomplete.addEvent('onSelection', function(element, selected, value, input) {
                var id= element.id;
                var res = id.split("metric_name_");
                var no = res[res.length-1];
                var selected_metrics_id_input_str = 'selected_metric_id_'+no;
                var metrics_unit_input_str = 'metric_unit_'+no;
                document.getElementById(selected_metrics_id_input_str).value = selected.retrieve('autocompleteChoice').id;
                document.getElementById(metrics_unit_input_str).value = selected.retrieve('autocompleteChoice').metric_unit;
                document.getElementById(metrics_unit_input_str).readOnly = true;
            });
        }


        // Dynamic forms
        var addButton = $j('#add_metrics_button'); //Add button selector
        var wrapper = $j('#custom-metrics-element'); //Input field wrapper
        var x = metricsCount;

        //Once add button is clicked
        $j(addButton).click(function () {
            x++; //Increment field counter

            var metrics_id_input_str = 'metric_id_'+x;
            var metrics_name_input_str = 'metric_name_'+x;
            var metrics_value_input_str = 'metric_value_'+x;
            var metrics_unit_input_str = 'metric_unit_'+x;
            var selected_metrics_id_input_str = 'selected_metric_id_'+x;

            var fieldHTML = `
                <div class="custom-metrics-elements-fields">
                    <input class="metrics_input" type="hidden" placeholder="Metric Id" name="metric_id[]" id="${metrics_id_input_str}" value="">
                    <input class="metrics_input" type="text" placeholder="Metric Name" name="metric_name[]" id=${metrics_name_input_str} value="">
                    <input class="metrics_input" type="text" placeholder="Metric Unit" name="metric_unit[]" id=${metrics_unit_input_str} value="">
                    <input class="metrics_input" type="text" placeholder="Metric Value" name="metric_value[]" id=${metrics_value_input_str} value="">
                    <input class="metrics_input" type="hidden" name="selected_metric_id[]" id=${selected_metrics_id_input_str} value="">
                    <button style="float:left;" type="button" id="remove_button">Remove</button>
                </div>
            `;

            $j(wrapper).append(fieldHTML); //Add field html
            $j('#metrics_counter').val(x);

            // append the metrics autocomplete
            var contentAutocomplete = new Autocompleter.Request.JSON(metrics_name_input_str, '<?php echo $this->url(array( 'module' => 'sitepage' ,'controller' => 'dashboard', 'action' => 'metrics-auto-suggest', 'page_id' => $this->page_id), 'default', true) ?>', {
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
            })
            contentAutocomplete.addEvent('onSelection', function(element, selected, value, input) {
                var id= element.id;
                var res = id.split("metric_name_");
                var no = res[res.length-1];
                var selected_metrics_id_input_str = 'selected_metric_id_'+no;
                var metrics_unit_input_str = 'metric_unit_'+no;
                document.getElementById(selected_metrics_id_input_str).value = selected.retrieve('autocompleteChoice').id;
                document.getElementById(metrics_unit_input_str).value = selected.retrieve('autocompleteChoice').metric_unit;
                document.getElementById(metrics_unit_input_str).readOnly = true;
            });
        });

        //Once remove button is clicked
        $j(wrapper).on('click', '#remove_button', function (e) {
            e.preventDefault();
            $j(this).parent('div').remove(); //Remove field html
            x--; //Decrement field counter
            $j('#metrics_counter').val(x);
        });

    }

    function loadQuestions(){

        /************ Questions Start ***********/

        // Create Metrics Form
        let questionsX = <?php echo count($this->initiativeQuestions);?>

        if(questionsX == 0 ){
            questionsX = 1;
        }

        // Create question Form
        var questionHtmlStr = `
             <div id="place_holder-wrapper" class="form-wrapper">
                <div id="place_holder-label" class="form-label">
                    <label class="place_holder_label" for="place_holder">Initiative Questions:</label>
                </div>
                <div id="place_holder-element" class="form-element">
                    &nbsp;
                </div>
             </div>
             <input type="hidden" name="questions_counter" id="questions_counter" value="${questionsX}">
             <div id="custom-question">
                <?php if(count($this->initiativeQuestions) > 0 ):?>
                    <?php foreach($this->initiativeQuestions as $key=>$value):?>
                        <?php $index = $key + 1; ?>
                         <div id="custom-question-wrapper" class="form-wrapper custom-question-wrapper">
                            <div id="custom-question-label" class="form-label">
                                <label class="question_label" for="title">Question <?php echo $index;?></label>
                            </div>
                            <div style="cursor: not-allowed;" id="custom-question-element" class="form-element custom-metrics-element">
                                 <div class="custom-question-elements-fields">
                                    <input type="hidden" name="question_id[]" value="<?php echo $value['initiativequestion_id'];?>" />
                                    <input style="pointer-events: none;" type="text" class="question_input" placeholder="Enter question title" name="question_title[]" id="question_title" value="<?php echo $value['initiativequestion_title'];?>" />
                                    <input style="pointer-events: none;" type="text" class="question_input" placeholder="Enter question hint" name="question_hint[]" id="question_hint" value="<?php echo $value['initiativequestion_hint'];?>" />
                                    <select style="pointer-events: none;" class="question_select_input" name="question_fieldtype[]" id="question_fieldtype">
                                        <option value="">Select question field type</option>
                                        <option value="smaller_text_box" <?php if ($value['initiativequestion_fieldtype'] === 'smaller_text_box') echo ' selected="selected"'; ?>>Small Textbox</option>
                                        <option value="larger_text_box" <?php if ($value['initiativequestion_fieldtype'] === 'larger_text_box') echo ' selected="selected"'; ?>>Large Textbox</option>
                                        <option value="number_box" <?php if ($value['initiativequestion_fieldtype'] === 'number_box') echo ' selected="selected"'; ?>>Number</option>
                                    </select>
                                </div>
                            </div>
                            <button style="float:right;margin-bottom:20px" type="button" id="remove_questions_button">Remove</button>
                        </div>
                    <?php endforeach;?>
                <?php else: ?>
                     <div id="custom-question-wrapper" class="form-wrapper custom-question-wrapper">
                        <div id="custom-question-label" class="form-label">
                            <label class="question_label" for="title">Question 1</label>
                        </div>
                        <div id="custom-question-element" class="form-element custom-metrics-element">
                             <div class="custom-question-elements-fields">
                                <input type="hidden" name="question_id[]" value="">
                                <input type="text" class="question_input" placeholder="Enter question title" name="question_title[]" id="question_title" value="" />
                                <input type="text" class="question_input" placeholder="Enter question hint" name="question_hint[]" id="question_hint" value="" />
                                <select class="question_select_input" name="question_fieldtype[]" id="question_fieldtype">
                                    <option value="" selected="selected">Select question field type</option>
                                    <option value="smaller_text_box">Small Textbox</option>
                                    <option value="larger_text_box">Large Textbox</option>
                                    <option value="number_box">Number</option>
                                </select>
                            </div>
                        </div>
                     </div>
                <?php endif; ?>
             </div>
        `;

        var newQuestionNode = document.createElement('div');
        newQuestionNode.innerHTML = questionHtmlStr;
        var referenceQuestionsNode = document.querySelector('#add_metrics_button');
        referenceQuestionsNode.parentNode.insertBefore(newQuestionNode, referenceQuestionsNode.nextSibling);

        // Dynamic Questions forms
        var addQuestionsButton = $j('#add_questions_button'); //Add button selector
        var questionsWrapper = $j('#custom-question'); //Input field wrapper

        function getQuestionFieldHtml(no) {
            var fieldQuestionsHTML = `
            <div id="custom-question-wrapper" class="form-wrapper custom-question-wrapper">
                <div id="custom-question-label" class="form-label">
                    <label class="question_label" for="title">Question ${no}</label>
                </div>
                <div id="custom-question-element" class="form-element custom-metrics-element">
                     <div class="custom-question-elements-fields">
                        <input type="hidden" name="question_id[]" value="">
                        <input type="text" class="question_input" placeholder="Enter question title" name="question_title[]" id="question_title" value=""/>
                        <input type="text" class="question_input" placeholder="Enter question hint" name="question_hint[]" id="question_hint" value=""/>
                        <select class="question_select_input" name="question_fieldtype[]"  id="question_fieldtype">
                            <option value="" selected="selected">Select question field type</option>
                            <option value="smaller_text_box">Small Textbox</option>
                            <option value="larger_text_box">Large Textbox</option>
                            <option value="number_box">Number</option>
                        </select>
                    </div>
                </div>
                <button style="float:right;margin-bottom:20px" type="button" id="remove_questions_button">Remove</button>
            </div>
            `;
            return fieldQuestionsHTML;
        }

        // When Add Clicked in Metrics
        $j(addQuestionsButton).click(function () {
            questionsX++; //Increment field counter
            $j(questionsWrapper).append(getQuestionFieldHtml(questionsX)); //Add field html
            $j('#questions_counter').val(questionsX);
        });

        // When Remove Clicked in Metrics
        $j(questionsWrapper).on('click', '#remove_questions_button', function (e) {
            e.preventDefault();
            $j(this).parent('div').remove(); //Remove field html
            questionsX--; //Decrement field counter
            $j('#questions_counter').val(questionsX);
            $j('.custom-question-wrapper').each(function(i, obj){
                var no = i;
                no++;
                $j(".question_label")[i].innerHTML = 'Question - '+no;
            });
        });

        /************ Questions End ***********/
    }

    window.addEventListener('DOMContentLoaded', function () {

        <?php if(!empty($this->initiative['logo'])):?>

            // Load image
            loadImage();

        <?php endif; ?>

        loadMetrics();
        loadQuestions();

        let paymentIsTaxDeductibleValue = "<?php echo $this->form->getValues()['payment_is_tax_deductible']; ?>";
        if (!paymentIsTaxDeductibleValue || paymentIsTaxDeductibleValue == '0') {
            $("payment_tax_deductible_label-wrapper").style.display = "none";
        }else{
            $("payment_tax_deductible_label-wrapper").style.display = "block";
        }

    });

    function openChangeModal(){
        Smoothbox.open('<?php echo $this->url(array('action' => 'upload-cover-photo', 'page_id' => $this->page_id, 'initiative_id' => $this->initiative_id), 'sitepage_initiatives', true) ?>');
    }

    function openRepositionModal(){
        Smoothbox.open('<?php echo $this->url(array('action' => 'reposition-cover-photo', 'page_id' => $this->page_id, 'initiative_id' => $this->initiative_id), 'sitepage_initiatives', true) ?>');
    }

    function openRemoveModal(){
        Smoothbox.open('<?php echo $this->url(array('action' => 'remove-cover-photo', 'page_id' => $this->page_id, 'initiative_id' => $this->initiative_id), 'sitepage_initiatives', true) ?>');
    }

    function onChangeIsTaxDeductible(value){
        if (!value || value == '0') {
            $("payment_tax_deductible_label-wrapper").style.display = "none";
        }else{
            $("payment_tax_deductible_label-wrapper").style.display = "block";
        }
    }
</script>
<style>
    div#payment_action_label-label {
        width: 22% !important;
    }
    #logo_edit_options-wrapper{
        text-align: center;
        margin-right: 10%;
    }
    .global_form input[type=text] + input[type=text]{
        margin-top:0px !important;
    }
    .metrics_input{
        float:left;
        width: 28% !important;
        margin-right: 5px;
    }
    #add_metrics_button,#add_questions_button{
        float: right;
        margin-bottom: 20px;
        margin-right: 5px;
    }
    @media (max-width: 767px) {
        button#change_logo , button#reposition_logo ,button#remove_logo {
            margin-bottom: 10px;
            width: 160px;
        }
    }
    .question_select_input{
        width: 100%;
        max-width: 100% !important;
    }
    #place_holder-wrapper{
        margin-top: 35px;
        margin-bottom: 20px;
    }
    .place_holder_label{
        border-bottom: 2px solid #44AEC1;
        font-size: 15px;
    }
    .question_input,.question_select_input{
        margin-bottom: 10px;
    }
</style>