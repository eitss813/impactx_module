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
<?php $projectTitle = $this->project->getTitle(); ?>
<?php
header("Expires: 0");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Content-type: application/vnd.ms-excel;charset:UTF-8");
header("Content-Disposition: attachment; filename=" . $this->translate("Backers Report - ") . "$projectTitle.xls");
print "\n"; // Add a line, unless excel error..
?>
<?php if (empty($this->total_item)): ?>
    <div class="tip">
        <span><?php echo $this->translate('There are no backers in this project yet.') ?></span>
    </div>
<?php else: ?>
    <table>
        <tr class="sitecrowdfunding_detail_table_head">
            <th class="txt_center"><?php echo $this->translate('Backer Id') ?></th>
            <th><?php echo $this->translate('Backer') ?></th>
            <th><?php echo $this->translate('Amount') ?></th>
            <th><?php echo $this->translate('Date') ?></th>
            <th><?php echo $this->translate('Reward Sent') ?></th>
            <th><?php echo $this->translate('Shipping Address') ?></th>
            <th><?php echo $this->translate('Email') ?></th>
        </tr>
        <?php foreach ($this->paginator as $item): ?>
            <?php $user = Engine_Api::_()->getItem('user', $item->user_id);?>  
            <tr>
                <td><?= $item->backer_id; ?></td>
                <td><?= $user->getTitle(); ?></td>
                <td><?= Engine_Api::_()->sitecrowdfunding()->getPriceWithCurrency($item->amount); ?></td>
                <td><?= $this->localeDate($item->creation_date); ?></td>
                <td>
                    <?php if ($item->reward_id && $item->reward_status == 1) : ?>
                        <?php echo $this->translate('Yes'); ?>
                    <?php elseif ($item->reward_id && $item->reward_status == 0): ?>
                        <?php echo $this->translate('No'); ?>
                    <?php else: ?>
                        <?php echo $this->translate('No Reward Selected'); ?>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if (!empty($item->shipping_address1) || !empty($item->shipping_address2) || !empty($item->shipping_city) || !empty($item->shipping_zip)): ?>
                        <?php if($item->shipping_address1) : ?>
                            <?php echo $this->translate($item->shipping_address1); ?>
                        <?php endif; ?> <br>
                        <?php if($item->shipping_address2) : ?>
                            <?php echo $this->translate($item->shipping_address2); ?>
                        <?php endif; ?><br>
                        <?php if($item->shipping_city) : ?>
                            <?php echo $this->translate($item->shipping_city); ?>
                        <?php endif; ?>
                        <?php if($item->shipping_country) : ?>
                            <?php echo $this->translate($item->shipping_country); ?>
                        <?php endif; ?>
                        <?php if($item->shipping_zip) : ?>
                            <?php echo $this->translate($item->shipping_zip); ?>
                        <?php endif; ?> 
                    <?php else: ?>
                        <?php echo '-'; ?>
                    <?php endif; ?>
                </td>
                <td><?= $user->email; ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php endif; ?>


