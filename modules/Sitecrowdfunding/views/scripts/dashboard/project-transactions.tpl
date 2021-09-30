<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: project-transactions.tpl 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>  
<?php
$this->headScript()->appendFile($this->layout()->staticBaseUrl . 'externals/calendar/calendar.compat.js');
$this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'externals/calendar/styles.css');
?>
<script type="text/javascript">
    Asset.css('<?php echo $this->layout()->staticBaseUrl
 . 'application/modules/Sitecrowdfunding/externals/styles/style_sitecrowdfunding.css'
?>');
</script>  
<?php if (!$this->only_list_content): ?>
  <?php include_once APPLICATION_PATH . '/application/modules/Sitecrowdfunding/views/scripts/_DashboardNavigation.tpl'; ?>
  <div class="sitecrowdfunding_dashboard_content">
    <?php echo $this->partial('application/modules/Sitecrowdfunding/views/scripts/dashboard/header.tpl', array('project' => $this->project, 'isFundingSection'=> true, 'sectionTitle' => 'Transactions', 'sectionDescription'=> 'Here, you can view list of backers, respective backed amount, commission charged from the backers, payment gateway used, payment status and date of transaction. Entering criteria into the filter fields will help you find specific detail.')); ?>
    <div class="sitecrowdfunding_project_form">
      <div id="sitecrowdfunding_manage_backer_content"> 
      <?php endif; ?>
      <?php $paginationCount = count($this->paginator); ?>
      <?php if (empty($this->call_same_action)) : ?>
        <div class="sitecrowdfunding_manage_project">
          <h3 class="mbot10">
            <?php //echo $this->translate('Transactions') ?>
          </h3>

          <?php if (empty($this->commissionFreePackage)) : ?>
            <div class='tabs mbot10'>
              <ul class="navigation">
                <li <?php if (empty($this->tab)) : ?> class="active" <?php endif; ?>>
                  <a href="javascript:void(0)" onclick="manage_project_dashboard(55, 'project-transactions/tab/0', 'dashboard')">
                    <?php echo $this->translate("Backer's Transactions") ?>
                  </a>
                </li>       
                <li <?php if (!empty($this->tab)) : ?> class="active" <?php endif; ?>>
                  <a href="javascript:void(0)" onclick="manage_project_dashboard(55, 'project-transactions/tab/1', 'dashboard')">
                    <?php echo $this->translate("Commissions Paid Transactions") ?>
                  </a>
                </li>    
              </ul>
            </div>
          <?php endif; ?>
        <?php endif; ?>
        <?php if (empty($this->tab)) : ?>
          <?php include APPLICATION_PATH . '/application/modules/Sitecrowdfunding/views/scripts/_backerRelatedTransaction.tpl'; ?>
        <?php else: ?>
          <?php include APPLICATION_PATH . '/application/modules/Sitecrowdfunding/views/scripts/_siteAdminRelatedTransaction.tpl'; ?>
        <?php endif; ?>
        <?php if (empty($this->call_same_action)) : ?>
        </div>
      <?php endif; ?>
      <?php if (!$this->only_list_content): ?>
      </div>
    </div>  
  </div>    
<?php endif; ?>
</div>

 