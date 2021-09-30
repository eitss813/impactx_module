<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: upload-kyc.tpl 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<script type="text/javascript" >
    //var submitformajax = 1;
</script>
<?php include_once APPLICATION_PATH . '/application/modules/Sitecrowdfunding/views/scripts/_DashboardNavigation.tpl'; ?>
<div class="sitecrowdfunding_dashboard_content">
    <?php echo $this->partial('application/modules/Sitecrowdfunding/views/scripts/dashboard/header.tpl', array('project' => $this->project)); ?>
    <?php echo $this->form->render(); ?>
    <div id="show_tab_content_child"></div>
    
    <?php
    if ($this->mangopayuser) :
        $kycDocuments = $this->adminGateway->getService()->getKycdocuments($this->mangoPayUserId);
        if (count($kycDocuments) > 0 && !empty($this->mangoPayUserId)):
            ?>
            <table class="sitecrowdfunding-table">
                <tr>
                    <td><?php echo $this->translate('Sn.') ?></td>
                    <td><?php echo $this->translate('ID') ?></td>
                    <td><?php echo $this->translate('Creation Date') ?></td>
                    <td><?php echo $this->translate('Type') ?></td>
                    <td><?php echo $this->translate('Status') ?></td>
                    <td><?php echo $this->translate('Tag') ?></td>
                    <td><?php echo $this->translate('Message') ?></td>
                </tr>
                <?php foreach ($kycDocuments as $k => $document): ?>
                    <?php $reason = empty($document->RefusedReasonMessage) ? '' : ($document->RefusedReasonMessage); ?>
                    <tr>
                        <td><?= $this->translate(($k + 1)) ?></td>
                        <td><?= $this->translate($document->Id) ?></td>
                        <td><?= $this->translate(date('d-m-Y H:i:s', $document->CreationDate)) ?></td>
                        <td><?= $this->translate($document->Type == 'IDENTITY_PROOF' ? 'Proof of identity' : ($document->Type == 'ADDRESS_PROOF' ? 'Proof of address' : 'Unknown')) ?></td>
                        <td><?= $this->translate($document->Status) ?></td>
                        <td><?= $this->translate($document->Tag) ?></td>
                        <td><?php echo $this->translate($reason); ?></td>
                    </tr>
                    <?php
                endforeach;
            endif;
        endif;
        ?>
    </table>
</div>
</div>
</div>

<style>
    .sitecrowdfunding-table{
        width:100%;
        margin-top: 20px;
        border: 1px solid #e3e3e3;
    }   
    .sitecrowdfunding-table thead tr{
        background: #e3e3e3; 
        color: #000;
        font-weight: bold;
    }
    .sitecrowdfunding-table tr td{
        padding: 8px;
    }
    .sitecrowdfunding-table tbody tr td{
        font-size: 11px;
    }
</style>