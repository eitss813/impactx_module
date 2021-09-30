<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: export-excel.tpl 2017-03-27 9:40:21Z SocialEngineAddOns $
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
    header("Content-Disposition: attachment; filename=Funding-report.xls"); 
    print "\n"; // Add a line, unless excel error..
  ?>
<?php 
$values = $this->values;
$select_project = $values['select_project'];
?>

<?php if($select_project == 'specific_project'): ?>
    <?php $backers = $this->rawdata ?>
    <?php $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $values['project_id']);
   ?>
    <div class="clr mtop10"> 
      <b class="bold"><?php echo $this->translate('Project Name') ?>:</b>
      <?php echo $this->htmlLink($project->getHref(), $project->getTitle(), array('target' => '_blank')); ?>

      <b class="bold"><?php echo $this->translate('Start Date') ?>:</b>
      <?php echo date("M d, Y",strtotime($project->start_date)); ?>
  </br/>
      <b class="bold"><?php echo $this->translate('Total Backers') ?>:</b>
      <?php echo $project->backer_count; ?>

      <b class="bold"><?php echo $this->translate('Total Backed Amount') ?>:</b>
      <?php echo Engine_Api::_()->sitecrowdfunding()->getPriceWithCurrencyAdmin($this->backedAmount); ?>

      <b class="bold"><?php echo $this->translate('Total Commission') ?>:</b>
      <?php echo Engine_Api::_()->sitecrowdfunding()->getPriceWithCurrencyAdmin($this->totalCommission); ?>
      
    </div>


  <?php if($this->reportType == 'summarised'): ?>

    <div id='stat_table' class="mtop10 clr">
      <?php if(!count($backers) && $this->post == 1) :?>
        <div class="tip">
          <span>
            <?php echo $this->translate("There are no backers found in the selected project.") ?>
          </span>
        </div> 
      <?php else: ?>
          <table class="admin_table seaocore_admin_table" style="width:100%;">
            <thead>
              <tr>   
                <th class='admin_table_short'><?php echo $this->translate('Backer Id') ?></th>
                <th class='admin_table_short'><?php echo $this->translate('Backer’s Name') ?></th>
                <th class='admin_table_short'><?php echo $this->translate('Backing Date') ?></th>
                <th class='admin_table_short'><?php echo $this->translate('Backing Amount') ?></th>
                <th class='admin_table_short'><?php echo $this->translate('Commission') ?></th>
              </tr> 
            </thead>
            <tbody> 
              <?php foreach($backers as $backer) : ?> 
                  <tr>
                    <?php
                      $user = Engine_Api::_()->getItem('user', $backer->user_id);
                    ?>
                    <td><?php echo $backer->backer_id ?></td>   
                    <td>                  
                      <?php echo $this->htmlLink($user->getHref(), $user->getTitle(), array('target' => '_blank')); ?>
                    </td>
                    <td><?php echo date("d-m-Y" ,strtotime($backer->creation_date)); ?></td>       
                    <td><?php echo Engine_Api::_()->sitecrowdfunding()->getPriceWithCurrencyAdmin($backer->amount); ?></td>                        
                    <td><?php echo Engine_Api::_()->sitecrowdfunding()->getPriceWithCurrencyAdmin($backer->commission_value); ?></td>           
                  </tr> 
                <?php endforeach; ?>
            </tbody>  
          </table>
          <b>
            Total Amount: <?php echo Engine_Api::_()->sitecrowdfunding()->getPriceWithCurrencyAdmin($this->backedAmount); ?>
            Total Commission: <?php echo Engine_Api::_()->sitecrowdfunding()->getPriceWithCurrencyAdmin($this->totalCommission); ?>
          </b>
          <div class="clear"></div> 
      <?php endif; ?>
    </div> 

  <?php else: ?>

      <div id='stat_table' class="mtop10 clr">
        <?php if(!count($backers) && $this->post == 1) :?>
          <div class="tip">
            <span>
              <?php echo $this->translate("There are no backers found in the selected project.") ?>
            </span>
          </div> 
        <?php else: ?>
            <table class="admin_table seaocore_admin_table" style="width:100%;"> 
              <tbody>
              <?php $month_year = ''; ?> 
                <?php foreach($backers as $backer) : ?> 
                   <?php $presentMonthYear = date("F Y", strtotime($backer->creation_date)); ?>
                  <?php if($month_year != $presentMonthYear): ?>
                      <?php $month_year = date("F Y", strtotime($backer->creation_date)); ?>
                      <thead>
                      <tr><th colspan= "5" ><?php echo date("F Y", strtotime($backer->creation_date));?></th> 
                      </tr>
                        <tr>   
                          <th class='admin_table_short'><?php echo $this->translate('Backer Id') ?></th>
                          <th class='admin_table_short'><?php echo $this->translate("Backer's Name") ?></th>
                          <th class='admin_table_short'><?php echo $this->translate('Backing Date') ?></th>
                          <th class='admin_table_short'><?php echo $this->translate('Backing Amount') ?></th>
                          <th class='admin_table_short'><?php echo $this->translate('Commission') ?></th>
                        </tr> 
                      </thead>
                  <?php endif; ?> 
                    <tr>
                      <?php
                        $user = Engine_Api::_()->getItem('user', $backer->user_id);
                      ?>
                      <td><?php echo $backer->backer_id ?></td>   
                      <td>                  
                        <?php echo $this->htmlLink($user->getHref(), $user->getTitle(), array('target' => '_blank')); ?>
                      </td>
                      <td><?php echo date("d-m-Y" ,strtotime($backer->creation_date)); ?></td>       
                      <td><?php echo Engine_Api::_()->sitecrowdfunding()->getPriceWithCurrencyAdmin($backer->amount); ?></td>                        
                      <td><?php echo Engine_Api::_()->sitecrowdfunding()->getPriceWithCurrencyAdmin($backer->commission_value); ?></td>           
                    </tr> 
                  <?php endforeach; ?>
              </tbody>  
            </table>
            <b>
              Total Amount: <?php echo Engine_Api::_()->sitecrowdfunding()->getPriceWithCurrencyAdmin($this->backedAmount); ?>
              Total Commission: <?php echo Engine_Api::_()->sitecrowdfunding()->getPriceWithCurrencyAdmin($this->totalCommission); ?>
            </b>
            <div class="clear"></div> 
        <?php endif; ?>
      </div> 

    <?php endif; ?> 

<?php else: ?>
    <?php if($this->reportType == 'summarised'): ?>
        <div id='stat_table' class="mtop10 clr">
          <?php if(!count($this->rawdata) && $this->post == 1) :?>
            <div class="tip">
              <span>
                <?php echo $this->translate("There are no projects found in the selected criteria.") ?>
              </span>
            </div>
          <?php else: ?>
            <table class="admin_table seaocore_admin_table" style="width:100%;">
              <thead>
                <tr>   
                  <th class='admin_table_short'><?php echo $this->translate('Project Id') ?></th>
                  <th class='admin_table_short'><?php echo $this->translate('Project Name') ?></th>
                  <th class='admin_table_short'><?php echo $this->translate('Owner') ?></th>
                  <th class='admin_table_short'><?php echo $this->translate('Start Date') ?></th>
                  <th class='admin_table_short'><?php echo $this->translate('Backer Count') ?></th>
                  <th class='admin_table_short'><?php echo $this->translate('Goal Amount') ?></th>
                  <th class='admin_table_short'><?php echo $this->translate('Backed Amount') ?></th> 
                  <th class='admin_table_short'><?php echo $this->translate('Total Commission') ?></th>
                </tr> 
              </thead>
              <tbody> 
                <?php foreach($this->rawdata as $project) : ?>
                  <tr>
                    <?php 
                      $user = Engine_Api::_()->getItem('user', $project->owner_id);
                    ?>
                    <td><?php echo $project->project_id ?></td>   
                    <td>
                      <?php echo $project->getTitle(); ?>
                    </td>   
                    <td>                  
                      <?php echo $user->getTitle(); ?>
                    </td>
                    <td><?php echo date("d-m-Y",strtotime($project->start_date)); ?></td>                        
                    <td><?php echo $project->backer_count ?></td>  
                    <td><?php echo Engine_Api::_()->sitecrowdfunding()->getPriceWithCurrencyAdmin($project->goal_amount); ?></td>                      
                    <td><?php echo Engine_Api::_()->sitecrowdfunding()->getPriceWithCurrencyAdmin($project->total_backed_amount); ?></td> 
                    <td><?php echo Engine_Api::_()->sitecrowdfunding()->getPriceWithCurrencyAdmin($project->total_commission); ?></td>              
                  </tr> 
                  <?php endforeach; ?>
                </tbody>  
              </table>
          <?php endif; ?>
        </div>
      <?php else: ?>

      <?php if(!count($this->rawdata) && $this->post == 1) :?>
            <div class="tip">
              <span>
                <?php echo $this->translate("There are no projects found in the selected criteria.") ?>
              </span>
            </div>
      <?php else: ?> 
            <table class="admin_table seaocore_admin_table" style="width:100%;"> 
              <tbody> 
                <?php $month_year = '';?>
                <?php foreach($this->rawdata as $project) : ?>
                  <?php $presentMonthYear = $project->month_no.$project->year; ?>
                    <?php if($month_year != $presentMonthYear): ?>
                        <?php $month_year = $project->month_no.$project->year; ?>
                        <tr><th colspan= "8" ><?php echo date("F Y", strtotime($project->creation_date));?></th> 
                        </tr>
                        <thead>
                        <tr>   
                          <th class='admin_table_short'><?php echo $this->translate('Project Id') ?></th>
                          <th class='admin_table_short'><?php echo $this->translate("Project's Name") ?></th>
                          <th class='admin_table_short'><?php echo $this->translate('Owner') ?></th>
                          <th class='admin_table_short'><?php echo $this->translate('Creation Date') ?></th>
                          <th class='admin_table_short'><?php echo $this->translate('Backer Count') ?></th>
                          <th class='admin_table_short'><?php echo $this->translate('Goal Amount') ?></th>
                          <th class='admin_table_short'><?php echo $this->translate('Backed Amount') ?></th> 
                          <th class='admin_table_short'><?php echo $this->translate('Total Commission') ?></th>
                        </tr> 
                      </thead> 
                    <?php endif; ?> 
                    <tr>
                      <?php 
                        $user = Engine_Api::_()->getItem('user', $project->owner_id);
                      ?>
                      <td><?php echo $project->project_id ?></td>   
                      <td>
                        <?php echo $project->getTitle(); ?>
                      </td>   
                      <td>                  
                        <?php echo $user->getTitle(); ?>
                      </td>
                      <td><?php echo date("d-m-Y",strtotime($project->start_date)); ?></td>                        
                      <td><?php echo $project->backer_count ?></td>  
                      <td><?php echo Engine_Api::_()->sitecrowdfunding()->getPriceWithCurrencyAdmin($project->goal_amount); ?></td>                      
                      <td><?php echo Engine_Api::_()->sitecrowdfunding()->getPriceWithCurrencyAdmin($project->total_backed_amount); ?></td> 
                      <td><?php echo Engine_Api::_()->sitecrowdfunding()->getPriceWithCurrencyAdmin($project->total_commission); ?></td>              
                    </tr>
                    
                <?php endforeach; ?>
                </tbody>  
              </table>

        <?php endif; ?> 
      <?php endif; ?>

<?php endif; ?>