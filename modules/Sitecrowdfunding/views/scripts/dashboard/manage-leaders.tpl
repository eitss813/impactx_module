<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: manage-leaders.tpl 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php include_once APPLICATION_PATH . '/application/modules/Sitecrowdfunding/views/scripts/_DashboardNavigation.tpl'; ?>
<div class="sitecrowdfunding_dashboard_content">

    <?php if (empty($this->is_ajax)) : ?>
        <div class="layout_middle">
            <?php echo $this->partial('application/modules/Sitecrowdfunding/views/scripts/dashboard/header.tpl', array('project' => $this->project, 'sectionTitle' => 'Manage Admin', 'sectionDescription' => "Below you can see all the admins who can manage your project, like you can do. You can add new guests as leader of this project and remove any existing ones. Note that admin selected by you for this page will get complete authority like you to manage this project, including deleting it. Thus you should be specific in selecting them.")); ?>
            <div class="sitecrowdfunding_dashboard_content">
                <div id="show_tab_content">
                <?php endif; ?> 
                <?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitecrowdfunding/externals/scripts/core.js'); ?>
                <div class="global_form">
                    <div>
                        <div>
                            <div class="sitecrowdfunding_leaders">
                                <!--<h3> <?php echo $this->translate('Manage Admin'); ?> </h3>
                                <p class="form-description"><?php echo $this->translate("Below you can see all the admins who can manage your project, like you can do. You can add new guests as leader of this project and remove any existing ones. Note that admin selected by you for this page will get complete authority like you to manage this project, including deleting it. Thus you should be specific in selecting them.") ?></p>
                                <br />-->
                                <?php foreach ($this->members as $member): ?>

                                    <div id='<?php echo $member->user_id ?>_page_main'  class='sitecrowdfunding_leaders_list'>
                                        <div class='sitecrowdfunding_leaders_thumb' id='<?php echo $member->user_id ?>_pagethumb'>
                                        <a href="<?php echo $member->getHref();?>">
                                            <?php echo $this->itemBackgroundPhoto($member, null, null, array('tag' => 'i')); ?>
                                        </a> 
                                            <?php //echo $this->htmlLink($member->getHref(), $this->itemPhoto($member->getOwner(), 'thumb.normal')) ?>
                                        </div> 
                                        <div id='<?php echo $member->user_id ?>_page' class="sitecrowdfunding_leaders_detail">
                                            <?php if ($this->project->owner_id != $member->user_id): ?>
                                                <div class="sitecrowdfunding_leaders_cancel">

                                                    <?php if ($this->owner_id != $member->user_id) : ?>
                                                        <span class="sitecrowdfunding_link_wrap mright5">
                                                            <i class="seaocore_txt_red seaocore_icon_remove_square"></i>
                                                            <?php
                                                            echo $this->htmlLink(array('route' => 'sitecrowdfunding_extended', 'controller' => 'dashboard', 'action' => 'demote', 'project_id' => $this->project->getIdentity(), 'user_id' => $member->getIdentity()), $this->translate('Remove as Admin'), array(
                                                                //'class' => 'buttonlink smoothbox icon_sitecrowdfunding_demote'
                                                                'class' => ' smoothbox'
                                                            ))
                                                            ?>
                                                        </span>
                                                    <?php endif; ?>
                                                </div>
                                            <?php endif; ?>
                                            <span><?php echo $this->htmlLink($member->getHref(), $member->getTitle()) ?></span>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                            <input type="hidden" id='count_div' value='<?php echo count($this->members) ?>' />
                            <form method='post' class="mtop10" action='<?php echo $this->url(array('controller' => 'dashboard', 'action' => 'manage-leaders', 'project_id' => $this->project->project_id), 'sitecrowdfunding_extended') ?>'>
                                <div class="fleft sitecrowdfunding_leaders_guest">
                                    <div>
                                        <?php if (!empty($this->message)): ?>
                                            <div class="tip">
                                                <span>
                                                    <?php echo $this->message; ?>
                                                </span>
                                            </div>
                                        <?php endif; ?>
                                        <div class="sitecrowdfunding_leaders_input">
                                            <span><?php echo $this->translate("Start typing the name of the guest...") ?></span>
                                            <span><input type="text" id="searchtext" name="searchtext" value="" style="width: 320px;" />
                                                <input type="hidden" id="user_id" name="user_id" /></span>
                                            <span><button id="promoteButton" type="submit" disabled="disabled"  name="submit"><?php echo $this->translate("Make Project Admin") ?></button></span>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <br />	
                <div id="show_tab_content_child">
                </div>
                <?php if (empty($this->is_ajax)) : ?>
                </div>
            </div>
        </div>
    <?php endif; ?> 	
</div>
</div>
</div>  
<?php
$this->headScript()
        ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Observer.js')
        ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.js')
        ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Local.js')
        ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Request.js');
?>

<script type="text/javascript">
    en4.core.runonce.add(function ()
    {
        var contentAutocomplete = new Autocompleter.Request.JSON('searchtext', '<?php echo $this->url(array('controller' => 'dashboard', 'action' => 'manage-auto-suggest', 'project_id' => $this->project->project_id), 'sitecrowdfunding_extended', true) ?>', {
            'postVar': 'text',
            'minLength': 1,
            'maxChoices': 40,
            'selectMode': 'pick',
            'autocompleteType': 'tag',
            'className': 'tag-autosuggest seaocore-autosuggest',
            'customChoices': true,
            'filterSubset': true,
            'multiple': false,
            'injectChoice': function (token) {
                var choice = new Element('li', {'class': 'autocompleter-choices1', 'html': token.photo, 'id': token.label});
                new Element('div', {'html': this.markQueryValue(token.label), 'class': 'autocompleter-choice1'}).inject(choice);
                this.addChoiceEvents(choice).inject(this.choices);
                choice.store('autocompleteChoice', token);
            }
        });

        contentAutocomplete.addEvent('onSelection', function (element, selected, value, input) {
            document.getElementById('promoteButton').removeAttribute("disabled");
            document.getElementById('user_id').value = selected.retrieve('autocompleteChoice').id;
        });
    });
</script>
<style type="text/css">
    .global_form > div > div{background:none;border:none;padding:0px;}
</style>
