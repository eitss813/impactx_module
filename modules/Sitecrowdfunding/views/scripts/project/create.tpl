<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: create.tpl 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
$hasPackageEnable = Engine_Api::_()->sitecrowdfunding()->hasPackageEnable();
if ($hasPackageEnable):
    ?>
    <?php $this->PackageCount = Engine_Api::_()->getDbTable('packages', 'sitecrowdfunding')->getPackageCount(); ?>
<?php endif; ?>
<?php
$this->headTranslate(array('edit', 'Date & Time', 'on the following days', 'Specific dates and times are set for this project.', 'Start time should be greater than the current time.', 'End time should be greater than the Start time.', 'Daily Project:', 'until', 'Days:', 'weeks', 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'In Every', 'months', 'of every month', 'of the month', 'from', 'first', 'second', 'third', 'fourth', 'fifth', 'sixth', 'sprojecth', 'to', 'every', 'Every', 'Day'));
?>

<?php
$this->headLink()->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitecrowdfunding/externals/styles/style_sitecrowdfunding_dashboard.css');
$this->headLink()->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitecrowdfunding/externals/styles/style_sitecrowdfunding.css');
?>
<?php
$this->headScript()
        ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Observer.js')
        ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.js')
        ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Local.js')
        ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Request.js')
        ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitecrowdfunding/externals/scripts/core.js')
        ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitecrowdfunding/externals/scripts/_commonFunctions.js')
        ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitecrowdfunding/externals/scripts/jquery-3.4.1.min.js');
$this->tinyMCESEAO()->addJS();
?> 


<!--WE ARE NOT USING STATIC BASE URL BECAUSE SOCIAL ENGINE ALSO NOT USE FOR THIS JS-->
<!--CHECK HERE Engine_View_Helper_TinyMce => protected function _renderScript()-->
<?php $coreSettings = Engine_Api::_()->getApi('settings', 'core'); ?>
<script type="text/javascript">
     en4.core.runonce.add(function () {
         if(document.getElementById('location') && (('<?php echo !Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.locationspecific', 0);?>') || ('<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.locationspecific', 0);?>' && '<?php echo !Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.locationspecificcontent', 0); ?>'))) {
             var autocompleteSECreateLocation = new google.maps.places.Autocomplete(document.getElementById('location'));
             <?php include APPLICATION_PATH . '/application/modules/Seaocore/views/scripts/location.tpl'; ?>
         }
    });
</script>
<?php
/* Include the common user-end field switching javascript */
echo $this->partial('_jsSwitch.tpl', 'fields', array(
        //'topLevelId' => (int) @$this->topLevelId,
        //'topLevelValue' => (int) @$this->topLevelValue
))
?>
<div class='sitecrowdfunding_dashboard_form'>
    <?php if ($this->current_count >= $this->quota && !empty($this->quota)): ?>
        <div class="tip"> 
            <span>
                <?php $msg = 'You have already started the maximum number of projects allowed i.e. '; ?>
                <?php echo $this->translate(array("$msg%s project.", "$msg%s projects.", $this->quota), $this->quota); ?> 
            </span>
        </div>
        <br/>
    <?php elseif ($this->category_count > 0): ?>
        <?php if ($this->sitecrowdfunding_render == 'sitecrowdfunding_form'): ?>
            <?php if ($hasPackageEnable && $this->PackageCount > 0): ?>
                <h3><?php echo $this->translate("Create a Project") ?></h3>
            <!--	<p><?php echo $this->translate("Create a Project using these quick, easy steps and get going."); ?></p>	-->
                <h4 class="sitecrowdfunding_create_step"><?php echo $this->translate("Start a project based on the package you have chosen."); ?></h4>
                <div class='sitecrowdfundingpage_layout_right'>      
                    <div class="sitecrowdfunding_package_page p5">          
                        <ul class="sitecrowdfunding_package_list">
                            <li class="p5">
                                <div class="sitecrowdfunding_package_list_title">
                                    <h3><?php echo $this->translate('Package Details'); ?>: <?php echo $this->translate(ucfirst($this->package->title)); ?></h3>
                                </div>           
                                <div class="sitecrowdfunding_package_stat"> 
                                    <?php if (in_array('price', $this->packageInfoArray)): ?>
                                        <span>
                                            <b><?php echo $this->translate("Price") . ": "; ?> </b>
                                            <?php if (isset($this->package->price)): ?>
                                                <?php
                                                if ($this->package->price > 0):echo Engine_Api::_()->sitecrowdfunding()->getPriceWithCurrency($this->package->price);
                                                else: echo $this->translate('FREE');
                                                endif;
                                                ?>
                                            <?php endif; ?>
                                        </span>
                                    <?php endif; ?>
                                    <?php if (in_array('billing_cycle', $this->packageInfoArray)): ?>
                                        <span>
                                            <b><?php echo $this->translate("Billing Cycle") . ": "; ?> </b>
                                            <?php echo $this->package->getBillingCycle() ?>
                                        </span>
                                    <?php endif; ?>
                                    <?php if (in_array('duration', $this->packageInfoArray)): ?>
                                        <span style="width: auto;">
                                            <b><?php echo ($this->package->price > 0 && $this->package->recurrence > 0 && $this->package->recurrence_type != 'forever' ) ? $this->translate("Billing Duration") . ": " : $this->translate("Duration") . ": "; ?> </b>
                                            <?php echo $this->package->getPackageQuantity(); ?>
                                        </span>
                                    <?php endif; ?>
                                    <?php if (in_array('featured', $this->packageInfoArray)): ?>
                                        <span>
                                            <b><?php echo $this->translate("Featured") . ": "; ?> </b>
                                            <?php
                                            if ($this->package->featured == 1)
                                                echo $this->translate("Yes");
                                            else
                                                echo $this->translate("No");
                                            ?>
                                        </span>
                                    <?php endif; ?>
                                    <?php if (in_array('Sponsored', $this->packageInfoArray)): ?>
                                        <span>
                                            <b><?php echo $this->translate("Sponsored") . ": "; ?> </b>
                                            <?php
                                            if ($this->package->sponsored == 1)
                                                echo $this->translate("Yes");
                                            else
                                                echo $this->translate("No");
                                            ?>
                                        </span>
                                    <?php endif; ?>
                                    <?php if (in_array('rich_overview', $this->packageInfoArray) && ($this->overview && Engine_Api::_()->authorization()->getPermission($this->viewer->level_id, 'sitecrowdfunding_project', "overview"))): ?>
                                        <span>
                                            <b><?php echo $this->translate("Rich Overview") . ": "; ?> </b>
                                            <?php
                                            if ($this->package->overview == 1)
                                                echo $this->translate("Yes");
                                            else
                                                echo $this->translate("No");
                                            ?>
                                        </span>
                                    <?php endif; ?>
                                    <?php if (in_array('videos', $this->packageInfoArray) && Engine_Api::_()->authorization()->getPermission($this->viewer->level_id, 'sitecrowdfunding_project', "video")): ?>
                                        <span>
                                            <b><?php echo $this->translate("Videos") . ": "; ?> </b>
                                            <?php
                                            if ($this->package->video == 1)
                                                if ($this->package->video_count)
                                                    echo $this->package->video_count;
                                                else
                                                    echo $this->translate("Unlimited");
                                            else
                                                echo $this->translate("No");
                                            ?>
                                        </span>
                                    <?php endif; ?>
                                    <?php if (in_array('photos', $this->packageInfoArray) && Engine_Api::_()->authorization()->getPermission($this->viewer->level_id, 'sitecrowdfunding_project', "photo")): ?>
                                        <span>
                                            <b><?php echo $this->translate("Photos") . ": "; ?> </b>
                                            <?php
                                            if ($this->package->photo == 1)
                                                if ($this->package->photo_count)
                                                    echo $this->package->photo_count;
                                                else
                                                    echo $this->translate("Unlimited");
                                            else
                                                echo $this->translate("No");
                                            ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                                <?php if (in_array('description', $this->packageInfoArray)): ?>
                                    <div class="sitecrowdfunding_list_details">
                                        <?php echo $this->translate($this->package->description); ?>
                                    </div>
                                <?php endif; ?>
                                <?php if ($this->PackageCount > 1): ?>
                                    <div class="common_btn mtop10">
                                        <a href="<?php echo $this->url(array('action' => 'index'), "sitecrowdfunding_package", true) ?>">&laquo; <?php echo $this->translate("Choose a different package"); ?></a>
                                    </div>
                                <?php endif; ?>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="sitecrowdfundingpage_layout_left">
                <?php endif; ?>

                <?php echo $this->form->setAttrib('class', 'global_form sitecrowdfunding_create_list_form')->render($this); ?>
                <?php if ($hasPackageEnable && $this->PackageCount > 0): ?>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <?php echo $this->translate($this->sitecrowdfunding_formrender); ?>
        <?php endif; ?>
    <?php endif; ?>
</div>

<script type="text/javascript">
    if ($('subcategory_id'))
        $('subcategory_id').style.display = 'none';
    en4.core.runonce.add(function ()
    {
        new Autocompleter.Request.JSON('tags', '<?php echo $this->url(array('module' => 'seaocore', 'controller' => 'index', 'action' => 'tag-suggest', 'resourceType' => 'sitecrowdfunding_project'), 'default', true) ?>', {
            'postVar': 'text',
            'minLength': 1,
            'selectMode': 'pick',
            'autocompleteType': 'tag',
            'className': 'tag-autosuggest',
            'customChoices': true,
            'filterSubset': true, 'multiple': true,
            'injectChoice': function (token) {
                var choice = new Element('li', {'class': 'autocompleter-choices', 'value': token.label, 'id': token.id});
                new Element('div', {'html': this.markQueryValue(token.label), 'class': 'autocompleter-choice'}).inject(choice);
                choice.inputValue = token;
                this.addChoiceProjects(choice).inject(this.choices);
                choice.store('autocompleteChoice', token);
            }
        });
    });
    var getProfileType = function (category_id) {
        var mapping = <?php echo Zend_Json_Encoder::encode(Engine_Api::_()->getDbTable('categories', 'sitecrowdfunding')->getMapping('profile_type')); ?>;
        for (i = 0; i < mapping.length; i++) {
            if (mapping[i].category_id == category_id)
                return mapping[i].profile_type;
        }
        return 0;
    }
    en4.core.runonce.add(function () {
        var defaultProfileId = '<?php echo '0_0_' . $this->defaultProfileId ?>' + '-wrapper';
        if ($type($(defaultProfileId)) && typeof $(defaultProfileId) != 'undefined') {
            $(defaultProfileId).setStyle('display', 'none');
        }
    });
</script>
<script type="text/javascript">

    window.addEvent('domready', function () {
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

    }); 
    function checkDraft(value) {

        if (value == 'draft') {
            $("search-wrapper").style.display = "none";
            $("search").checked = false;
        } else {
            $("search-wrapper").style.display = "block";
            $("search").checked = true;
        }
    }
    //checkDraft($('state').value);
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