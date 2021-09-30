<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: backer-details.tpl 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php if (!empty($this->backer_id)) : ?>

    <h2 class="payment_transaction_detail_headline">
        Backing Details
    </h2>

    <dl class="payment_transaction_details">
        <dd>
            Project Title
        </dd> 
        <dt>
        <a href="<?php echo $this->url(array('project_id' => $this->project->project_id, 'slug' => $this->project->getSlug()), "sitecrowdfunding_entry_view"); ?>"  target='_blank' title="<?php echo ucfirst($this->project->title); ?>">
            <?php echo $this->project->title; ?></a>
        </dt>

        <dd>
            backer ID
        </dd>
        <dt>
        <?php echo $this->locale()->toNumber($this->backer_id) ?>
        </dt>

        <dd>
            Project Owner
        </dd>
        <dt>
        <?php if ($this->owner && $this->owner->getIdentity()): ?>
            <?php echo $this->htmlLink($this->owner->getHref(), $this->owner->getTitle(), array('target' => '_parent')) ?>  
        <?php else: ?>
            <i>Deleted Project Owner</i>
        <?php endif; ?>
        </dt> 

        <dd>
            Project End Date
        </dd>
        <dt>
        <?php echo $this->locale()->toDateTime($this->project->funding_end_date) ?>
        </dt>

        <dd>
            Selected Reward
        </dd> 
        <dt>
        <?php echo $this->reward->title; ?>
        </dt> 

        <dd>
            Project Status
        </dd> 
        <dt>
        <?php echo $this->project->status; ?>
        </dt> 
        <dd>
            Reward Sent
        </dd> 
        <dt>
        <?php echo $this->reward_status; ?>
        </dt> 
        <button onclick='javascript:parent.Smoothbox.close()' style="float:right;"><?php echo 'Close'; ?></button>

    </dl>
    <?php if (@$this->closeSmoothbox): ?>
        <script type="text/javascript">
            TB_close();
        </script>
    <?php endif; ?> 

<?php else : ?>
    <?php echo $this->message; ?>
<?php endif; ?>

