<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: export-backers.tpl 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>


<?php
header("Expires: 0");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Content-type: application/vnd.ms-excel;charset:UTF-8");
header("Content-Disposition: attachment; filename=" . $this->yndform->getTitle() . '- Submissions.xls');
print "\n"; // Add a line, unless excel error..
?>

<?php $headerTitles = array();?>
<?php $headerTitles[] = 'ID';?>
<?php $headerTitles[] = 'Submitted By';?>
<?php $headerTitles[] = 'Submitted Project';?>
<?php $headerTitles[] = 'Submission Time';?>

<?php $body = array();?>

<?php foreach ($this->form_submitted_paginator as $entry_details): ?>

    <?php $bodyContent = array();?>

    <?php $entry = Engine_Api::_() -> getItem('yndynamicform_entry', $entry_details->getIdentity()); ;?>
    <?php $fieldStructure = Engine_Api::_() -> fields() -> getFieldsStructurePartial($entry);?>

    <?php $bodyContent[] = $entry_details->getIdentity();?>

    <?php
        if ($entry && $entry->owner_id) {
            $title = $entry->getOwner()->getTitle();
        } else if ($entry->user_email) {
            $title = $entry->user_email;
        } else {
            $title = 'Anonymous';
        }
        
        $title .= ( !empty($entry->submission_status) && ($entry->submission_status == 'preview') )? ' (test)': '';
            
        $bodyContent[] = $title;
    ?>

    <?php
        if ($entry && $entry->project_id) {
            $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $entry->project_id);
            if($project){
                $bodyContent[] = $project->getTitle();
            }
            else {
                     $bodyContent[] = '-';
             }
        }else{
            $bodyContent[] = '-';
        }
    ?>
    <?php
        $options = array();$options['format'] = 'H:m a, F';
        $bodyContent[] = $this->locale()->toDateTime($entry->creation_date, $options);
    ?>

    <?php $uniqueTitles = array();?>
    <?php foreach ($fieldStructure as $map):?>

        <?php
            // Get field meta object
            $field = $map->getChild();
            $value = $field->getValue($entry);
            $label = $field->label;
            $label = str_replace("#540","'",$label);
        ?>
        <?php if($value->value):?>
            <?php $uniqueTitles[] = $label;?>
            <?php $bodyContent[] = $value->value ;?>
        <?php endif;?>
    <?php endforeach; ?>

    <?php $headerTitles = array_unique (array_merge ($headerTitles, $uniqueTitles)) ;?>

    <?php $body[] = $bodyContent;?>
<?php endforeach; ?>

<table>
    <tr class="sitecrowdfunding_detail_table_head">
        <?php foreach ($headerTitles as $header):?>
        <!-- Set the background -->
        <th style='background: #f7f7f7;'><?php echo $header; ?></th>
        <?php endforeach; ?>
    </tr>
    <?php foreach ($body as $b):?>
        <tr>
            <?php foreach ($b as $val):?>
                <?php
                    $val = (strstr($val, '+') && strstr($val, '-'))? '[' . $val . ']': $val;
                ?>
                <th><?php echo $val; ?></th>
            <?php endforeach; ?>
        </tr>
    <?php endforeach; ?>
</table>