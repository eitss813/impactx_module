<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: backers.tpl 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
$this->headScript()
        ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Observer.js')
        ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.js')
        ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Local.js')
        ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Request.js');
?>

<?php $hasPackageEnable = Engine_Api::_()->sitecrowdfunding()->hasPackageEnable(); ?>
<?php $currency = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD'); ?>
<script type="text/javascript">
    var currentOrder = '<?php echo $this->order ?>';
    var currentOrderDirection = '<?php echo $this->order_direction ?>';
    var changeOrder = function (order, default_direction) {

        if (order == currentOrder) {
            $('order_direction').value = (currentOrderDirection == 'ASC' ? 'DESC' : 'ASC');
        } else {
            $('order').value = order;
            $('order_direction').value = default_direction;
        }
        $('filter_form').submit();
    }
</script>

<h2>
    Crowdfunding / Fundraising / Donations Plugin
</h2>
<?php if (count($this->navigation)): ?>
    <div class='seaocore_admin_tabs'>
        <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render() ?>
    </div>
<?php endif; ?>

<h2>Manage Backers</h2>
<?php if (!empty($this->project)): ?>
    <h4>
        <?php echo 'This page lists all the backers of project<b> ' . $this->project->getTitle() . '</b>'; ?>
    </h4><br/>
<?php else: ?>
    <?php echo 'Below, you can view the list of backers who have funded various Projects on your site. Entering criteria into the filter fields will help you find specific backers. You can also payout / refund the backed amount for a specific backer in case this process has been failed for the backer when the process was being carried out from the "Manage Projects" for all the backers of a project.'; ?> 
<?php endif; ?>



<div class='admin_search mtop10'>
    <?php echo $this->formFilter->render($this) ?>
</div>
<div class='admin_members_results mtop10'>
    <?php $counter = $this->paginator->getTotalItemCount(); ?>
    <?php if (!empty($counter)): ?>
        <div class="">
            <?php echo $this->translate(array('%s backer found.', '%s backers found.', $counter), $this->locale()->toNumber($counter)) ?>
        </div>
    <?php else: ?>
        <div class="tip mtop10">
            <span>
                No results were found.
            </span>
        </div>
    <?php endif; ?>
    <?php echo $this->paginationControl($this->paginator); ?>
</div>
<br />

<?php if ($this->paginator->getTotalItemCount() > 0): ?>
    <form id='multidelete_form' method="post" >

        <table class='admin_table seaocore_admin_table' width="100%">
            <thead>
                <tr>
                    <?php $class = ( $this->order == 'backer_id' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
                    <th class="<?php echo $class ?>" ><a href="javascript:void(0);" onclick="javascript:changeOrder('backer_id', 'DESC');">ID</a></th>
                    <?php $class = ( $this->order == 'title' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
                    <th class="<?php echo $class ?>"  align="left" ><a href="javascript:void(0);" onclick="javascript:changeOrder('title', 'ASC');">Project Title</a></th>

                    <?php $class = ( $this->order == 'username' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
                    <th class="<?php echo $class ?>"  align="left" ><a href="javascript:void(0);" onclick="javascript:changeOrder('username', 'ASC');">Backer’s Name</a></th>  
                    <?php $class = ( $this->order == 'amount' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
                    <th class="<?php echo $class ?>"  align="left" ><a href="javascript:void(0);" onclick="javascript:changeOrder('amount', 'ASC');">Backed Amount</a></th> 

                    <?php $class = ( $this->order == 'creation_date' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
                    <th class="<?php echo $class ?>"><a href="javascript:void(0);" onclick="javascript:changeOrder('creation_date', 'DESC');">Date</a></th>

                    <th class=""><a href="javascript:void(0);" >Payment Gateway</a></th>
                    <th class=""><a href="javascript:void(0);" >Payment Status</a></th>
                    <th class=""><a href="javascript:void(0);" >Payout</a></th>
                    <th class=""><a href="javascript:void(0);" >Refund</a></th>
                    <?php if($this->isGatewayEnabled): ?>
                    <th class=""><a href="javascript:void(0);" >Options</a></th>  
                    <?php endif ?>
                </tr>
            </thead>

            <tbody>
                <?php if (count($this->paginator) > 0): ?>
                    <?php foreach ($this->paginator as $item): ?> 
                        <?php $gateway = Engine_Api::_()->getItem('payment_gateway', $item->gateway_id); ?>
                        <?php $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $item->project_id); ?>
                        <tr>

                            <td><?php echo $item->backer_id ?></td>
                            <td class='admin_table_bold' title="<?php echo $project->title ?>"> <?php echo $this->htmlLink($project->getHref(), Engine_Api::_()->seaocore()->seaocoreTruncateText($project->getTitle(), 15), array('target' => '_blank')) ?>
                            </td>
                            <?php $owner = Engine_Api::_()->getItem('user', $item->user_id); ?>
                            <td class='admin_table_bold' title="<?php echo $item->username ?>"> <?php echo $this->htmlLink($owner->getHref(), Engine_Api::_()->seaocore()->seaocoreTruncateText($owner->getTitle(), 15), array('target' => '_blank')) ?>
                            </td> 
                            <td><?php echo Engine_Api::_()->sitecrowdfunding()->getPriceWithCurrencyAdmin($item->amount); ?></td>
                            <td><?php echo gmdate('M d,Y, g:i A', strtotime($item->creation_date)) ?></td>
                            <td><?php echo $gateway->getTitle() ?></td>
                            <td align="center" class="admin_table_centered"><?php echo ucfirst($item->paymentStatus()) ?></td>
                            <td><?php echo ($item->payout_status == '') ? '--' : ucfirst($item->payout_status) ?></td>
                            <td><?php echo ($item->refund_status == '') ? '--' : ucfirst($item->refund_status) ?></td>
                            <?php if($this->isGatewayEnabled): ?>
                            <td class='admin_table_options'>
                                <?php $status = $item->payoutStatus(); ?>
                                <?php if ($status == 'payout'): ?>
                                    <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitecrowdfunding', 'controller' => 'general', 'action' => 'backer-payout', 'project_id' => $item->project_id, 'backer_id' => $item->backer_id, 'gateway_id' => $item->gateway_id), 'Payout', array('class' => 'smoothbox')) ?>
                                <?php elseif ($status == 'refund'): ?>
                                    <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitecrowdfunding', 'controller' => 'general', 'action' => 'backer-refund', 'project_id' => $item->project_id, 'backer_id' => $item->backer_id, 'gateway_id' => $item->gateway_id), 'Refund', array('class' => 'smoothbox')) ?>
                                <?php else: ?>
                                    <?php echo '--'; ?>
                                <?php endif; ?>

                            </td> 
                            <?php endif ?>
                        </tr>

                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </form>
<?php endif; ?>

<div id="thankYou" style="display:none;">
    <div>
        <div id="showMessage_featured" class="sitecrowdfunding_manage_msg" style="display:none;">This project has already been marked as Featured. If you mark it as New, then its Featured marker will be automatically removed. Click on 'OK' button to mark it as New.</div>
        <div id="showMessage_new" class="sitecrowdfunding_manage_msg" style="display:none;">This project has already been marked as New. If you mark it as Featured, then its New marker will be automatically removed. Click on 'OK' button to mark it as Featured.</div>
        <div id="hidden_url" style="display:none;" ></div>
        <br />
        <button onclick="continueSetLabel();">Ok</button> or
        <a onclick="closeThankYou();" href="javascript:void(0);"> cancel</a></div>
</div>			
</div>


<script type="text/javascript">
    en4.core.runonce.add(function () {
        var contentAutocomplete = new Autocompleter.Request.JSON('title', '<?php echo $this->url(array('module' => 'sitecrowdfunding', 'controller' => 'admin-manage', 'action' => 'get-backed-projects'), 'default', true) ?>', {
            'postVar': 'text',
            'minLength': 1,
            'selectMode': 'pick',
            'autocompleteType': 'tag',
            'className': 'tag-autosuggest seaocore-autosuggest',
            'customChoices': true,
            'filterSubset': true,
            'multiple': false,
            'injectChoice': function (token) {
                var choice = new Element('li', {
                    'class': 'autocompleter-choices',
                    'html': token.photo,
                    'id': token.label
                });
                new Element('div', {
                    'html': this.markQueryValue(token.label),
                    'class': 'autocompleter-choice'
                }).inject(choice);
                this.addChoiceEvents(choice).inject(this.choices);
                choice.store('autocompleteChoice', token);
            },
        });

        contentAutocomplete.addEvent('onSelection', function (element, selected, value, input) {
            document.getElementById('project_id').value = selected.retrieve('autocompleteChoice').id;
        });
    });

</script>
