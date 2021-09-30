<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: commission.tpl 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<h2 class="fleft">
    Crowdfunding / Fundraising / Donations Plugin
</h2>

<?php if (count($this->navigation)): ?>
    <div class='seaocore_admin_tabs'>
        <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render() ?>
    </div>
<?php endif; ?>  

<h3 style="margin-bottom:6px;">Commissions</h3>
<p>
    Below, you can view commissions you have received from various Projects on your site. Entering criteria into the filter fields will help you find specific received commission.
</p>

<br style="clear:both;" />

<!-- SEARCH FORM -->
<div class="admin_search sitecrowdfunding_admin_search">
    <div class="search">
        <form name="manage_orders_search_form" id="manage_orders_search_form" method="post" class="global_form_box" action="">
            <input type="hidden" name="post_search" /> 

            <div>
                <label><?php echo "Project Title" ?></label>
                <?php if (empty($this->title)): ?>
                    <input type="text" name="title" /> 
                <?php else: ?>
                    <input type="text" name="title" value="<?php echo $this->title ?>"/>
                <?php endif; ?>
            </div> 

            <div>
                <label><?php echo "Project Owner" ?></label>
                <?php if (empty($this->projectOwner)): ?>
                    <input type="text" name="username" /> 
                <?php else: ?>
                    <input type="text" name="username" value="<?php echo $this->projectOwner ?>"/>
                <?php endif; ?>
            </div>

            <div>
                <label><?php echo "Backed Amount"; ?></label>
                <div>
                    <?php if ($this->backed_min_amount == ''): ?>
                        <input type="text" name="backed_min_amount" placeholder="min" class="input_field_small" /> 
                    <?php else: ?>
                        <input type="text" name="backed_min_amount" placeholder="min" value="<?php echo $this->backed_min_amount ?>" class="input_field_small" />
                    <?php endif; ?>

                    <?php if ($this->backed_max_amount == ''): ?>
                        <input type="text" name="backed_max_amount" placeholder="max" class="input_field_small" /> 
                    <?php else: ?>
                        <input type="text" name="backed_max_amount" placeholder="max" value="<?php echo $this->backed_max_amount ?>" class="input_field_small" />
                    <?php endif; ?>
                </div>   
            </div>

            <div>
                <label><?php echo "Commission Amount"; ?></label>
                <div>
                    <?php if ($this->commission_min_amount == ''): ?>
                        <input type="text" name="commission_min_amount" placeholder="min" class="input_field_small" /> 
                    <?php else: ?>
                        <input type="text" name="commission_min_amount" placeholder="min" value="<?php echo $this->commission_min_amount ?>" class="input_field_small" />
                    <?php endif; ?>

                    <?php if ($this->commission_max_amount == ''): ?>
                        <input type="text" name="commission_max_amount" placeholder="max" class="input_field_small" /> 
                    <?php else: ?>
                        <input type="text" name="commission_max_amount" placeholder="max" value="<?php echo $this->commission_max_amount ?>" class="input_field_small" />
                    <?php endif; ?>
                </div>
            </div> 

            <div style="margin-top:16px;">
                <button type="submit" name="search" ><?php echo "Search"; ?></button>
            </div> 

        </form>
    </div>
</div>

<div class='admin_search'>
    <?php echo $this->formFilter->render($this) ?>
</div>

<div class='admin_members_results mtop10'>
    <?php if (!empty($this->paginator)) : ?>
        <?php $counter = $this->paginator->getTotalItemCount(); ?>
    <?php endif; ?>

    <?php if (!empty($counter)): ?>
        <div>
            <br />

            <?php echo $this->translate(array('%s project commission detail found.', '%s projects commission details found.', $counter), $this->locale()->toNumber($counter)) ?>

        </div>
    <?php else: ?>
        <div class="tip mtop10">
            <span>
               There are no commission details available yet.
            </span>
        </div>
    <?php endif; ?> 
</div>
<br />

<?php if (!empty($counter)): ?>
    <div class="clr">
        <table class='admin_table seaocore_admin_table' width="100%">
            <thead>
                <tr> 
                    <?php $class = ( $this->order == 'project_id' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
                    <th class="<?php echo $class ?>" ><a href="javascript:void(0);" onclick="javascript:changeOrder('project_id', 'ASC');">Project ID</a></th> 

                    <?php $class = ( $this->order == 'title' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
                    <th class="<?php echo $class ?>" ><a href="javascript:void(0);" onclick="javascript:changeOrder('title', 'ASC');">Project Title</a></th>

                    <?php $class = ( $this->order == 'username' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
                    <th class="<?php echo $class ?>" ><a href="javascript:void(0);" onclick="javascript:changeOrder('username', 'ASC');">Project Owner</a></th>

                    <?php $class = ( $this->order == 'total_backed_amount' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
                    <th class="<?php echo $class ?>" ><a href="javascript:void(0);" onclick="javascript:changeOrder('total_backed_amount', 'ASC');">Backed Amount</a></th>

                    <?php $class = ( $this->order == 'total_commission' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
                    <th class="<?php echo $class ?>" ><a href="javascript:void(0);" onclick="javascript:changeOrder('total_commission', 'ASC');">Commission Amount</a></th> 
                    <th><?php echo 'Commission Paid'; ?></th>
                    <th><?php echo 'Remaining Commission'; ?></th>
                      
                    <?php $class = ( $this->order == 'state' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
                    <th class="<?php echo $class ?>" ><a href="javascript:void(0);" onclick="javascript:changeOrder('state', 'ASC');"><?php echo $this->translate('Project Status'); ?></a></th>
 
                    <th class='admin_table_short'><?php echo $this->translate('Options'); ?></th>
                </tr> 
            </thead>
            <?php foreach ($this->paginator as $item): ?>
                <tbody>  

                    <?php $project = $this->item('sitecrowdfunding_project', $item->project_id); ?>

                <td class="admin_table_centered"><?php echo $item->project_id ?></td>

                <td><?php echo $this->htmlLink($project->getHref(), $this->string()->truncate($this->string()->stripTags($project->getTitle()), 15), array('title' => $project->getTitle(), 'target' => '_blank')) ?></td>


                <td>
                    <?php echo $this->htmlLink($item->getOwner(), $this->string()->truncate($this->string()->stripTags($item->getOwner()), 15), array('title' => $item->username, 'target' => '_blank')) ?>
                </td>

                <td><?php echo Engine_Api::_()->sitecrowdfunding()->getPriceWithCurrencyAdmin($item->total_backed_amount) ?></td>          
                <td><?php echo Engine_Api::_()->sitecrowdfunding()->getPriceWithCurrencyAdmin($item->total_commission)
                    ?></td> 
                <td>             
                  <?php if (!empty($this->projectPaidCommission[$item->project_id]['paid_commission'])) : ?>
                    <?php echo Engine_Api::_()->sitecrowdfunding()->getPriceWithCurrencyAdmin($this->projectPaidCommission[$item->project_id]['paid_commission']) ?>
                    <?php $remaining_commission = $item->total_commission - $this->projectPaidCommission[$item->project_id]['paid_commission']; ?>
                  <?php else:  ?>
                    <?php if(Engine_Api::_()->hasModuleBootstrap('sitegateway')):?>
                      <?php $commissionPaid = Engine_Api::_()->sitegateway()->getSplitNEscrowGatewayCommission(array('resource_type' => 'sitecrowdfunding_backer', 'resource_id' => $item->project_id, 'resource_key' => 'project_id'))?>
                      <?php echo Engine_Api::_()->sitecrowdfunding()->getPriceWithCurrency($commissionPaid) ?>
                      <?php $remaining_commission = $item->total_commission - $commissionPaid ?>

                    <?php else:?>
                      <?php echo Engine_Api::_()->sitecrowdfunding()->getPriceWithCurrencyAdmin(0) ?>
                      <?php $remaining_commission = $item->total_commission; ?>
                    <?php endif; ?>
                  <?php endif; ?>
                </td>
                <td>
                  <?php if ($remaining_commission > 0) : ?>
                    <?php echo Engine_Api::_()->sitecrowdfunding()->getPriceWithCurrencyAdmin($remaining_commission) ?>
                  <?php else: ?>
                    <?php echo Engine_Api::_()->sitecrowdfunding()->getPriceWithCurrencyAdmin(0) ?>
                  <?php endif; ?>
                </td>
                <td><?php echo $project->getProjectState(); ?></td>
                <td> 
                    <a href="<?php echo $this->url(array('action' => 'your-bill', 'project_id' => $item->project_id, 'menuId' => 56), 'sitecrowdfunding_backer', false) ?>" target="_blank"><?php echo "view project bill"; ?></a> |
                    <a href="<?php echo $this->url(array('action' => 'project-transactions', 'project_id' => $item->project_id), 'sitecrowdfunding_dashboard', false) ?>" target="_blank"><?php echo "View project transactions"; ?></a>
                </td>

                </tbody>
            <?php endforeach; ?>
        </table>
    </div>
    <div class="clr mtop10">
        <?php
        echo $this->paginationControl($this->paginator, null, null, array(
            'pageAsQuery' => true,
            'query' => $this->formValues,
        ));
        ?>
    </div>
<?php endif; ?> 
<script type="text/javascript">

    var currentOrder = '<?php echo $this->order ?>';
    var currentOrderDirection = '<?php echo $this->order_direction ?>';
    var changeOrder = function (order, default_direction) {
        if (order == currentOrder) {
            $('order_direction').value = (currentOrderDirection == 'ASC' ? 'DESC' : 'ASC');
        }
        else {
            $('order').value = order;
            $('order_direction').value = default_direction;
        }
        $('filter_form').submit();
    }

</script>
