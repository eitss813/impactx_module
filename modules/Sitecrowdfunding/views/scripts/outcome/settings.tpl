<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: editphotos.tpl 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php include_once APPLICATION_PATH . '/application/modules/Sitecrowdfunding/views/scripts/_DashboardNavigation.tpl'; ?>

<div class="sitecrowdfunding_dashboard_content">
    <?php echo $this->partial('application/modules/Sitecrowdfunding/views/scripts/dashboard/header.tpl', array('project' => $this->project, 'sectionTitle'=>'Project Impact', 'sectionDescription'=> '')); ?>
    <div class="sitecrowdfunding_dashboard_form">
        <?php echo $this->form->render($this); ?>
    </div>
</div>
<?php
   $parentOrganization = Engine_Api::_()->getDbtable('pages', 'sitecrowdfunding')->getParentPages( $this->project->getIdentity());
   $org_id = $parentOrganization['page_id'];
?>
<?php if($org_id != 7): ?>
<style type="text/css">
    div#no_of_jobs-wrapper {
        display: none;
    }
    div#help_desc-wrapper {
        display: none;
    }
</style>
<?php endif; ?>
<style type="text/css">
    .sitecrowdfunding_dashboard_form{
        padding: 10px;
    }
</style>