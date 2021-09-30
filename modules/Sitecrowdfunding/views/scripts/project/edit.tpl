<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: edit.tpl 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
$cateDependencyArray = Engine_Api::_()->getDbTable('categories', 'sitecrowdfunding')->getCatDependancyArray();
$subCateDependencyArray = Engine_Api::_()->getDbTable('categories', 'sitecrowdfunding')->getSubCatDependancyArray();
$this->headScript()
        ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Observer.js')
        ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.js')
        ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Local.js')
        ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Request.js')
        ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitecrowdfunding/externals/scripts/_commonFunctions.js')
        ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitecrowdfunding/externals/scripts/jquery-3.4.1.min.js');
?>
<?php
/* Include the common user-end field switching javascript */
echo $this->partial('_jsSwitch.tpl', 'fields', array(
));
?>

<?php include_once APPLICATION_PATH . '/application/modules/Sitecrowdfunding/views/scripts/_DashboardNavigation.tpl'; ?>
<div class="sitecrowdfunding_dashboard_content">

    <?php echo $this->partial('application/modules/Sitecrowdfunding/views/scripts/dashboard/header.tpl', array('project' => $this->project, 'sectionTitle' => 'Project Profile' )); ?> <br>

    <div class="sitecrowdfunding_dashboard_form">
      <?php if($this->display_type === 'tag'): ?>
        <div style="margin-left: 13px;" id="add-newtag">
            <div id="keywordlabel" class="tag-label"><label for="tags" class="optional">Tags (Keyword)</label></div>
            <div style="display: flex">
                <input  type="text" placeholder="Add a tag .." name="tags" id="keyword" value="" autocomplete="off">
                <p id="add-new-tag" onclick="addNewTag()"> + </p>
            </div>
            <div id="tag-details" > </div> <br>
            <!-- <button onclick="saveChanges()"> Save Changes </button> -->
        </div>
        <?php
         $projectTags = $project->tags()->getTagMaps();
        $tagString = array();
        foreach ($projectTags as $tagmap) {
        $tagString[] = $tagmap->getTag()->getTitle();
        }
         ?>
        <ul style="margin-left: 16px;display: flex;flex-wrap: wrap;">
                <?php   $i=0;
                    foreach ($tagString as $name) { ?>
                          <li style="margin-bottom: 10px;display: flex;padding: 6px;border: 1px solid;border-radius: 2px;margin-right: 7px;font-size: 14px;" id="tag-<?php echo $i; ?>"> <p > <?php print_r($name); ?>  </p> <p style="margin-left: 16px;cursor: pointer"  onclick="removeTag('<?php echo $i;?>')"> <i class="fa fa-remove"></i> </p></li>
                    <?php $i++;  }
                 ?>
        </ul>

      <!--  <?php if(count($tagString) == 0) {  ?>
        <button style="margin-left: 16px; margin-top: 26px;" onclick="showAddEditForm()"> Add  </button>
        <?php } ?>
        <?php if(count($tagString) > 0) {  ?>
        <button style="margin-left: 16px; margin-top: 26px;" onclick="showAddEditForm()">  Edit </button>
        <?php } ?> -->
        <?php $addtagString = array(); ?>
        <?php else: ?>
             <?php echo $this->form->render(); ?>
        <?php endif;?>
        <div id="demo"> <?php echo $this->form->render(); ?> </div>
    </div>	
</div>
</div>
</div>
<script type="text/javascript">
    var tagCounter=0;
    var tagNewData=[];

   function removeTag(index) {
       var tag_array =<?php echo json_encode($tagString );?>;
       tag_array.splice(index, 1);

       var myobj = document.getElementById('tag-'+ index);
       myobj.remove();
       console.log('arw',tag_array.toString());
       $("tags").value = tag_array.length==0 ? null : tag_array.toString();
       $("sitecrowdfundings_create_form").submit();

   }
   function showAddEditForm(){
       document.getElementById('demo').style.display='block';
   }
   function addNewTag() {
       var tag_arraytemp =<?php echo json_encode($addtagString );?>;
       var tag_array =<?php echo json_encode($tagString );?>;
       var tagKeyword = document.getElementById("keyword").value;
       var res = tag_array.concat(tagKeyword);

       $("tags").value = res;
       $("sitecrowdfundings_create_form").submit();


   }
    function closeCommand(id) {
      console.log('closeCommand',id);
        var element = document.getElementById(id);
        element.parentNode.removeChild(element);
        tagNewData.splice(id, 1);

    }
   function showTag() {
       document.getElementById('add-newtag').style.display="block";
   }
   function saveChanges() {
       console.log('res tagNewData',tagNewData);
       var tag_array =<?php echo json_encode($tagString );?>;
       var res = tag_array.concat(tagNewData);
       $("tags").value = res;
       $("sitecrowdfundings_create_form").submit();
   }
    en4.core.runonce.add(function ()
    {
        new Autocompleter.Request.JSON('tags', '<?php echo $this->url(array('module' => 'seaocore', 'controller' => 'index', 'action' => 'tag-suggest', 'resourceType' => 'sitecrowdfunding_project'), 'default', true) ?>', {
            'postVar': 'text',
            'minLength': 1,
            'selectMode': 'pick',
            'autocompleteType': 'tag',
            'className': 'tag-autosuggest',
            'filterSubset': true,
            'multiple': true,
            'injectChoice': function (token) {
                var choice = new Element('li', {'class': 'autocompleter-choices', 'value': token.label, 'id': token.id});
                new Element('div', {'html': this.markQueryValue(token.label), 'class': 'autocompleter-choice'}).inject(choice);
                choice.inputValue = token;
                this.addChoiceProjects(choice).inject(this.choices);
                choice.store('autocompleteChoice', token);
            }
        });
    });
    var prefieldForm = function () {
<?php
$defaultProfileId = "0_0_" . $this->defaultProfileId;
foreach ($this->form->getSubForms() as $subForm) {
    foreach ($subForm->getElements() as $element) {

        $elementGetName = $element->getName();
        $elementGetValue = $element->getValue();
        $elementGetType = $element->getType();

        if ($elementGetName != $defaultProfileId && $elementGetName != '' && $elementGetName != null && $elementGetValue != '' && $elementGetValue != null) {

            if (!is_array($elementGetValue) && $elementGetType == 'Engine_Form_Element_Radio') {
                ?>
                        $('<?php echo $elementGetName . "-" . $elementGetValue ?>').checked = 1;
            <?php } elseif (!is_array($elementGetValue) && $elementGetType == 'Engine_Form_Element_Checkbox') { ?>
                        $('<?php echo $elementGetName ?>').checked = <?php echo $elementGetValue ?>;
                <?php
            } elseif (is_array($elementGetValue) && ($elementGetType == 'Engine_Form_Element_MultiCheckbox' || $elementGetType == 'Fields_Form_Element_Ethnicity' || $elementGetType == 'Fields_Form_Element_LookingFor' || $elementGetType == Fields_Form_Element_PartnerGender)) {
                foreach ($elementGetValue as $key => $value) {
                    ?>
                            $('<?php echo $elementGetName . "-" . $value ?>').checked = 1;
                    <?php
                }
            } elseif (is_array($elementGetValue) && $elementGetType == 'Engine_Form_Element_Multiselect') {
                foreach ($elementGetValue as $key => $value) {
                    $key_temp = array_search($value, array_keys($element->options));
                    if ($key !== FALSE) {
                        ?>
                                $('<?php echo $elementGetName ?>').options['<?php echo $key_temp ?>'].selected = 1;
                        <?php
                    }
                }
            } elseif (!is_array($elementGetValue) && ($elementGetType == 'Engine_Form_Element_Text' || $elementGetType == 'Engine_Form_Element_Textarea' || $elementGetType == 'Fields_Form_Element_AboutMe' || $elementGetType == 'Fields_Form_Element_Aim' || $elementGetType == 'Fields_Form_Element_City' || $elementGetType == 'Fields_Form_Element_Facebook' || $elementGetType == 'Fields_Form_Element_FirstName' || $elementGetType == 'Fields_Form_Element_Interests' || $elementGetType == 'Fields_Form_Element_LastName' || $elementGetType == 'Fields_Form_Element_Location' || $elementGetType == 'Fields_Form_Element_Twitter' || $elementGetType == 'Fields_Form_Element_Website' || $elementGetType == 'Fields_Form_Element_ZipCode')) {
                ?>
                        $('<?php echo $elementGetName ?>').value = "<?php echo $this->string()->escapeJavascript($elementGetValue, false) ?>";
            <?php } elseif (!is_array($elementGetValue) && $elementGetType != 'Engine_Form_Element_Date' && $elementGetType != 'Fields_Form_Element_Birthdate' && $elementGetType != 'Engine_Form_Element_Heading') { ?>
                        $('<?php echo $elementGetName ?>').value = "<?php echo $this->string()->escapeJavascript($elementGetValue, false) ?>";
                <?php
            }
        }
    }
}
?>
    }
    var subcatid = '<?php echo $this->subcategory_id; ?>';

    var cateDependencyArray = '<?php echo json_encode($cateDependencyArray); ?>';

    var submitformajax = 0;
    var show_subcat = 1;
    var cateDependencyArray = new Array();
    var subCateDependencyArray = new Array();
<?php foreach ($cateDependencyArray as $cat) : ?>
        cateDependencyArray.push(<?php echo $cat ?>);
<?php endforeach; ?>
<?php foreach ($subCateDependencyArray as $cat) : ?>
        subCateDependencyArray.push(<?php echo $cat ?>);
<?php endforeach; ?>

<?php if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.categoryedit', 1) && !empty($this->project->category_id)) : ?>
        show_subcat = 0;
<?php endif; ?>

    window.addEvent('domready', function () {
<?php if ($this->profileType): ?>
            $('<?php echo '0_0_' . $this->defaultProfileId ?>').value = <?php echo $this->profileType ?>;
            changeFields($('<?php echo '0_0_' . $this->defaultProfileId ?>'));
<?php endif; ?>
        if ($('lifetime-0')) {
            if ($('lifetime-0').checked) {
                initializeCalendar(0);
            } else {
                initializeCalendar(1);
            }
        } else {
            initializeCalendar(0);
        }
    });

    var getProfileType = function (category_id) {

        var mapping = <?php echo Zend_Json_Encoder::encode(Engine_Api::_()->getDbTable('categories', 'sitecrowdfunding')->getMapping('profile_type')); ?>;
        for (i = 0; i < mapping.length; i++) {
            if (mapping[i].category_id == category_id)
                return mapping[i].profile_type;
        }
        return 0;
    }

    var defaultProfileId = '<?php echo '0_0_' . $this->defaultProfileId ?>' + '-wrapper';
    if ($type($(defaultProfileId)) && typeof $(defaultProfileId) != 'undefined') {
        $(defaultProfileId).setStyle('display', 'none');
    }

    var subcategories = function (category_id, subcatid, subcatname, subsubcatid)
    {
        if (subcatid > 0) {
            changesubcategory(subcatid, subsubcatid);
        }
        if (!in_array(cateDependencyArray, category_id)) {
            if ($('subcategory_id-wrapper'))
                $('subcategory_id-wrapper').style.display = 'none';
            if ($('subcategory_id-label'))
                $('subcategory_id-label').style.display = 'none';
            if ($('buttons-wrapper')) {
                $('buttons-wrapper').style.display = 'block';
            }
            return;
        }
        if ($('subsubcategory_backgroundimage'))
            $('subcategory_backgroundimage').style.display = 'block';
        if ($('subcategory_id'))
            $('subcategory_id').style.display = 'none';
        if ($('subsubcategory_id'))
            $('subsubcategory_id').style.display = 'none';
        if ($('subcategory_id-label'))
            $('subcategory_id-label').style.display = 'none';
        if ($('subcategory_backgroundimage'))
            $('subcategory_backgroundimage').innerHTML = '<div class="form-label"></div><div  class="form-element"><img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitecrowdfunding/externals/images/loading.gif" /></center></div>';


        if ($('buttons-wrapper')) {
            $('buttons-wrapper').style.display = 'none';
        }
        if ($('subsubcategory_id-wrapper'))
            $('subsubcategory_id-wrapper').style.display = 'none';
        if ($('subsubcategory_id-label'))
            $('subsubcategory_id-label').style.display = 'none';
        var url = '<?php echo $this->url(array('action' => 'sub-category'), 'sitecrowdfunding_general', true); ?>';
        en4.core.request.send(new Request.JSON({
            url: url,
            data: {
                format: 'json',
                category_id_temp: category_id
            },
            onSuccess: function (responseJSON) {
                if ($('buttons-wrapper')) {
                    $('buttons-wrapper').style.display = 'block';
                }
                if ($('subcategory_backgroundimage'))
                    $('subcategory_backgroundimage').style.display = 'none';

                clear('subcategory_id');
                var subcatss = responseJSON.subcats;
                addOption($('subcategory_id'), " ", '0');
                for (i = 0; i < subcatss.length; i++) {
                    addOption($('subcategory_id'), subcatss[i]['category_name'], subcatss[i]['category_id']);
                    if (show_subcat == 0) {
                        if ($('subcategory_id'))
                            $('subcategory_id').disabled = 'disabled';
                        if ($('subsubcategory_id'))
                            $('subsubcategory_id').disabled = 'disabled';
                    }
                    if ($('subcategory_id')) {
                        $('subcategory_id').value = '<?php echo $this->project->subcategory_id; ?>';
                    }
                }

                if (category_id == 0) {
                    clear('subcategory_id');
                    if ($('subcategory_id'))
                        $('subcategory_id').style.display = 'none';
                    if ($('subcategory_id-label'))
                        $('subcategory_id-label').style.display = 'none';
                }
            }
        }), {
            "force": true
        });
    };
    function in_array(ArrayofCategories, value) {
        for (var i = 0; i < ArrayofCategories.length; i++) {
            if (ArrayofCategories[i] == value) {
                return true;
            }
        }
        return false;
    }

    var changesubcategory = function (subcatid, subsubcatid)
    {
        if ($('buttons-wrapper')) {
            $('buttons-wrapper').style.display = 'none';
        }

        if (!in_array(subCateDependencyArray, subcatid)) {
            if ($('subsubcategory_id-wrapper'))
                $('subsubcategory_id-wrapper').style.display = 'none';
            if ($('subsubcategory_id-label'))
                $('subsubcategory_id-label').style.display = 'none';
            if ($('buttons-wrapper')) {
                $('buttons-wrapper').style.display = 'block';
            }
            return;
        }
        if ($('subsubcategory_backgroundimage'))
            $('subsubcategory_backgroundimage').style.display = 'block';
        if ($('subsubcategory_id'))
            $('subsubcategory_id').style.display = 'none';
        if ($('subsubcategory_id-label'))
            $('subsubcategory_id-label').style.display = 'none';
        if ($('subsubcategory_backgroundimage'))
            $('subsubcategory_backgroundimage').innerHTML = '<div class="form-label"></div><div  class="form-element"><img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitecrowdfunding/externals/images/loading.gif" /></center></div>';


        if ($('buttons-wrapper')) {
            $('buttons-wrapper').style.display = 'none';
        }
        var url = '<?php echo $this->url(array('action' => 'subsub-category'), 'sitecrowdfunding_general', true); ?>';
        var request = new Request.JSON({
            url: url,
            data: {
                format: 'json',
                subcategory_id_temp: subcatid
            },
            onSuccess: function (responseJSON) {
                if ($('buttons-wrapper')) {
                    $('buttons-wrapper').style.display = 'block';
                }
                if ($('subsubcategory_backgroundimage'))
                    $('subsubcategory_backgroundimage').style.display = 'none';

                clear('subsubcategory_id');
                var subsubcatss = responseJSON.subsubcats;
                if ($('subsubcategory_id')) {
                    addSubOption($('subsubcategory_id'), " ", '0');
                    for (i = 0; i < subsubcatss.length; i++) {
                        addSubOption($('subsubcategory_id'), subsubcatss[i]['category_name'], subsubcatss[i]['category_id']);
                        if ($('subsubcategory_id')) {
                            $('subsubcategory_id').value = '<?php echo $this->project->subsubcategory_id; ?>';
                        }
                    }
                }
            }
        });
        request.send();
    };

    function clear(ddName)
    {
        if (document.getElementById(ddName)) {
            for (var i = (document.getElementById(ddName).options.length - 1); i >= 0; i--)
            {
                document.getElementById(ddName).options[ i ] = null;
            }
        }
    }
    function addOption(selectbox, text, value)
    {
        if ($('subcategory_id')) {
            var optn = document.createElement("OPTION");
            optn.text = text;
            optn.value = value;

            if (optn.text != '' && optn.value != '') {
                if ($('subcategory_id'))
                    $('subcategory_id').style.display = 'block';
                if ($('subcategory_id-wrapper'))
                    $('subcategory_id-wrapper').style.display = 'block';
                if ($('subcategory_id-label'))
                    $('subcategory_id-label').style.display = 'block';
                selectbox.options.add(optn);
            } else {
                if ($('subcategory_id'))
                    $('subcategory_id').style.display = 'none';
                if ($('subcategory_id-wrapper'))
                    $('subcategory_id-wrapper').style.display = 'none';
                if ($('subcategory_id-label'))
                    $('subcategory_id-label').style.display = 'none';
                selectbox.options.add(optn);
            }
        }
    }

    var cat = '<?php echo $this->category_id ?>';
    if (cat != '') {
        subcatid = '<?php echo $this->subcategory_id; ?>';
        subsubcatid = '<?php echo $this->subsubcategory_id; ?>';
        var subcatname = '<?php echo $this->subcategory_name; ?>';
        subcategories(cat, subcatid, subcatname, subsubcatid);
    }
    function addSubOption(selectbox, text, value)
    {
        if ($('subsubcategory_id')) {
            var optn = document.createElement("OPTION");
            optn.text = text;
            optn.value = value;
            if (optn.text != '' && optn.value != '') {
                if ($('subsubcategory_id'))
                    $('subsubcategory_id').style.display = 'block';
                if ($('subsubcategory_id-wrapper'))
                    $('subsubcategory_id-wrapper').style.display = 'block';
                if ($('subsubcategory_id-label'))
                    $('subsubcategory_id-label').style.display = 'block';
                selectbox.options.add(optn);
            } else {
                if ($('subsubcategory_id'))
                    $('subsubcategory_id').style.display = 'none';
                if ($('subsubcategory_id-wrapper'))
                    $('subsubcategory_id-wrapper').style.display = 'none';
                if ($('subsubcategory_id-label'))
                    $('subsubcategory_id-label').style.display = 'none';
                selectbox.options.add(optn);
            }
        }
    }
</script>

<script type="text/javascript">
var viewerIsAdmin = '<?php echo $this->viewerIsAdmin; ?>';
    var initializeCalendar = function (isLifetime) {

        if ($('starttime-minute')) {
            $('starttime-minute').style.display = 'none';
        }
        if ($('starttime-ampm')) {
            $('starttime-ampm').style.display = 'none';
        }
        if ($('starttime-hour')) {
            $('starttime-hour').style.display = 'none';
        }
        if ($('endtime-minute')) {
            $('endtime-minute').style.display = 'none';
        }
        if ($('endtime-ampm')) {
            $('endtime-ampm').style.display = 'none';
        }
        if ($('endtime-hour')) {
            $('endtime-hour').style.display = 'none';
        }

        var startDate = '<?php echo $this->project->funding_start_date !== null ? date('Y-m-d', strtotime($this->project->funding_start_date)) : 0 ; ?>';
        var expiryDate = '<?php echo $this->project->funding_end_date !== null ?  date('Y-m-d', strtotime($this->project->funding_end_date)) : 0; ?>';
        if(startDate !== 0 || expiryDate !== 0){
            return
        }
        days = 89;
        isLifetime = parseInt(isLifetime);
        if (isLifetime) {
            days = 1824;//5 Years -1 day
        }

        var cal_bound_start = new Date(startDate);
        var start_date = new Date(startDate); 
        var cal_bound_end = new Date(start_date.setDate(start_date.getDate() + days)); 
        if(viewerIsAdmin != 1) {
            cal_starttime.calendars[0].start = cal_bound_start; 
        } else {
            cal_starttime.calendars[0].val = cal_bound_start;  
        } 
        cal_endtime.calendars[0].start = cal_bound_start;
        cal_endtime.calendars[0].end = cal_bound_end;

        // redraw calendar
        cal_starttime.navigate(cal_starttime.calendars[0], 'm', 1);
        cal_starttime.navigate(cal_starttime.calendars[0], 'm', -1);

        cal_endtime.navigate(cal_endtime.calendars[0], 'm', 1);
        cal_endtime.navigate(cal_endtime.calendars[0], 'm', -1); 

        $('calendar_output_span_starttime-date').innerHTML = startDate;
        $('calendar_output_span_endtime-date').innerHTML = expiryDate;

        cal_starttime.changed(cal_starttime.calendars[0]);
        cal_endtime.changed(cal_endtime.calendars[0]);
        if(viewerIsAdmin != 1) {
            cal_starttime.calendars[0].val = cal_starttime.calendars[0].start;
            cal_starttime.calendars[0].month = cal_starttime.calendars[0].start.getMonth();
            cal_starttime.calendars[0].year = cal_starttime.calendars[0].start.getFullYear(); 
        } 
    }
    function checkDraft(value) {
        if (value == 'draft') {
            $("search-wrapper").style.display = "none";
            $("search").checked = false;
        } else {
            $("search-wrapper").style.display = "block";
            $("search").checked = true;
        }
    } 
    projectCurrentState = '<?php echo $this->project->state; ?>'; 
    backerCount = '<?php echo $this->project->backer_count; ?>';
    if ($('state'))
        checkDraft($('state').value); 
    window.addEvent('domready', function () {
        //backerCount != 0 &&
        if ( projectCurrentState != 'draft' && viewerIsAdmin != 1) {
            $('starttime-date').getParent().getChildren("button")[0].set('disabled', true);
            $('endtime-date').getParent().getChildren("button")[0].set('disabled', true);
        }
    });
</script>
<script type="text/javascript">
    var $j = jQuery.noConflict();
    $j(document).ready(function() {
        $("search-wrapper").style.display = "none";
        $("auth_topic-wrapper").style.display = "none";
        $("auth_comment-wrapper").style.display = "none";
        $("auth_view-wrapper").style.display = "none";
        $("member_invite-wrapper").style.display = "none";
        $("member_approval-wrapper").style.display = "none";
    });

</script>
<?php if($this->display_type === 'backstory'): ?>
<style>

    #title-wrapper,#tags-wrapper,#category_id-wrapper{
        display: none;
    }
    p.description{
        max-width: 800px !important;
    }
</style>
<?php endif;?>
<?php if($this->display_type === 'tag'): ?>
<style>
    #title-wrapper,#description-wrapper,#category_id-wrapper{
        display: none;
    }
</style>
<?php endif;?>
<?php if($this->display_type === 'category'): ?>
<style>
    #title-wrapper,#tags-wrapper,#description-wrapper{
        display: none;
    }
</style>
<?php endif;?>
<style>
    #demo {
        display: none;
    }
    .mce-tinymce{
        width: auto !important;
    }

     #keywordlabel {
         color: #270606;
         font-weight: 500;
         font-size: 16px;
         position: relative;
         bottom: 4px;
     }
    #add-new-tag{
        display: flex;
        align-items: center;
        margin-left: 11px;
        border: 1px solid;
        border-radius: 4px;
        color: white;
        font-size: 28px;
        /* width: 13px; */
        padding: 7px;
        background-color: #44AEC1;
    }
    #tag-details{
        display: flex;
        margin-top: 10px;
    }
    .tag-detail{
        margin-right: 15px;
        border: 1px solid;
        border-radius: 3px;
        padding: 3px;
    }
    .sitecrowdfunding_dashboard_form{
        width: 55%;
    }
    #keyword{
      width: 22%;
    }
    @media (max-width: 767px)
    {
        .sitecrowdfunding_dashboard_form{
            width: 100% !important;
        }
        #keyword{
            width: 80% !important;
        }
    }
</style>