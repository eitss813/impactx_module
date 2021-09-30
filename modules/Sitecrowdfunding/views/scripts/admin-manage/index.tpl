<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php $hasPackageEnable = Engine_Api::_()->sitecrowdfunding()->hasPackageEnable(); ?>
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

    function multiDelete()
    {
        return confirm('<?php echo $this->string()->escapeJavascript("Are you sure you want to delete selected projects ?") ?>');
    }

    function selectAll()
    {
        var i;
        var multidelete_form = $('multidelete_form');
        var inputs = multidelete_form.elements;

        for (i = 1; i < inputs.length - 1; i++) {
            if (!inputs[i].disabled) {
                inputs[i].checked = inputs[0].checked;
            }
        }
    }
</script>

<?php if ($this->contentModule == 'sitepage'): ?>
    <h2>
        Directory / Pages Plugin
    </h2>
<?php elseif ($this->contentModule == 'sitebusiness'): ?>
    <h2>
        Directory / Businesses Plugin
    </h2>
<?php elseif ($this->contentModule == 'sitegroup'): ?>
    <h2>
        Groups / Communities Plugin
    </h2>
<?php elseif ($this->contentModule == 'siteevent'): ?>
    <h2>
        Advanced Events Plugin
    </h2>
<?php else: ?>
    <h2>
        Crowdfunding / Fundraising / Donations Plugin
    </h2>
<?php endif; ?>

<?php if (count($this->navigation)): ?>
    <div class='seaocore_admin_tabs'>
        <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render() ?>
    </div>
<?php endif; ?>

<h2>Manage Projects</h2>
<h4>This page lists all the Projects your users have created. You can use this page to monitor these Projects and delete offensive material if necessary. Entering criteria into the filter fields will help you find specific Project entries. Leaving the filter fields blank will show all the Project entries on your social network. Here, you can also mark Project as featured / un-featured, sponsored / un-sponsored, and approve / dis-approve them.</h4><br />

<div class="admin_search sitecrowdfunding_admin_crowdfunding_search">
    <div class="search">
        <form method="post" class="global_form_box" action="">
            <div>
                <label>
                    Title
                </label>
                <?php if (empty($this->title)): ?>
                    <input type="text" name="title" /> 
                <?php else: ?>
                    <input type="text" name="title" value="<?php echo $this->title ?>"/>
                <?php endif; ?>
            </div>

            <div>
                <label>
                    Owner
                </label>	
                <?php if (empty($this->owner)): ?>
                    <input type="text" name="owner" /> 
                <?php else: ?> 
                    <input type="text" name="owner" value="<?php echo $this->owner ?>" />
                <?php endif; ?>
            </div>        

            <?php $categories = Engine_Api::_()->getDbTable('categories', 'sitecrowdfunding')->getCategories(array('category_id', 'category_name'), null, 0, 0, 1); ?>              
            <div class="form-wrapper" id="category_id-wrapper">
                <div class="form-label" id="category_id-label">
                    <label class="optional" for="category_id">Category</label>
                </div>
                <div class="form-element" id="category_id-element">
                    <select id="category_id" name="category_id" onchange='addOptions(this.value, "cat_dependency", "subcategory_id", 0);'>
                        <option value=""></option>
                        <?php if (count($categories) != 0): ?>
                            <?php
                            $categories_prepared[0] = "";
                            foreach ($categories as $category) {
                                $categories_prepared[$category->category_id] = $category->category_name;
                                ?>
                                <option value="<?php echo $category->category_id; ?>" <?php if ($this->category_id == $category->category_id) echo "selected"; ?>><?php echo $category->category_name; ?></option>
                            <?php } ?>
                        <?php endif; ?>
                    </select>
                </div>
            </div>

            <div class="form-wrapper" id="subcategory_id-wrapper" style='display:none;'>
                <div class="form-label" id="subcategory_id-label">
                    <label class="optional" for="subcategory_id">Sub-Category</label>
                </div>
                <div class="form-element" id="subcategory_id-element">
                    <select id="subcategory_id" name="subcategory_id" onchange='addOptions(this.value, "subcat_dependency", "subsubcategory_id", 0);'></select>
                </div>
            </div>

            <div class="form-wrapper" id="subsubcategory_id-wrapper" style='display:none;'>
                <div class="form-label" id="subsubcategory_id-label">
                    <label class="optional" for="subsubcategory_id">3<sup>rd</sup> Level Category</label>
                </div>
                <div class="form-element" id="subsubcategory_id-element">
                    <select id="subsubcategory_id" name="subsubcategory_id"></select>
                </div>
            </div>
            <!--PACKAGE NAME-->
            <?php if ($hasPackageEnable): ?>
                <div>
                    <label>
                        Package
                    </label>
                    <select id="package_id" name="package_id">
                        <option value="0" ></option>
                        <?php foreach ($this->packageList as $package): ?>
                            <option value="<?php echo $package->package_id ?>" <?php if ($this->package_id == $package->package_id) echo "selected"; ?> > <?php echo ucfirst($package->title) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            <?php endif; ?>

             <div>
                <label>
                    Featured
                </label>
                <select id="" name="featured">
                    <option value="0" >Select</option>
                    <option value="2" <?php if ($this->featured == 2) echo "selected"; ?> >Yes</option>
                    <option value="1" <?php if ($this->featured == 1) echo "selected"; ?> >No</option>
                </select>
            </div>

            <!--
            <div>
                <label>
                    Sponsored
                </label>
                <select id="sponsored" name="sponsored">
                    <option value="0"  >Select</option>
                    <option value="2" <?php if ($this->sponsored == 2) echo "selected"; ?> >Yes</option>
                    <option value="1"  <?php if ($this->sponsored == 1) echo "selected"; ?>>No</option>
                </select>
            </div> -->


            <div>
                <label>
                    Approved
                </label>
                <select id="sponsored" name="approved">
                    <option value="0" >Select</option>
                    <option value="2" <?php if ($this->approved == 2) echo "selected"; ?> >Yes</option>
                    <option value="1" <?php if ($this->approved == 1) echo "selected"; ?> >No</option>
                </select>
            </div>

            <div>
                <label>
                    State
                </label>
                <select id="" name="state">
                    <option value="0" >Select</option>
                    <option value="1" <?php if ($this->state == 1) echo "selected"; ?> >Draft</option>
                    <option value="2" <?php if ($this->state == 2) echo "selected"; ?> >Published</option>
                    <option value="3" <?php if ($this->state == 3) echo "selected"; ?> >Successful</option>
                    <option value="4" <?php if ($this->state == 4) echo "selected"; ?> >Failed</option>
                    <option value="5" <?php if ($this->state == 5) echo "selected"; ?> >Submit for approval</option>
                    <option value="6" <?php if ($this->state == 6) echo "selected"; ?> >Rejected</option>
                </select>
            </div>

            <div>
                <label>
                    Funding
                </label>
                <select id="funding" name="funding">
                    <option value="0" >Select</option>
                    <option value="2" <?php if ($this->funding == 2) echo "selected"; ?> >Yes</option>
                    <option value="1" <?php if ($this->funding == 1) echo "selected"; ?> >No</option>
                </select>
            </div>


            <div>
                <label>
                   Funding Approved
                </label>
                <select id="sponsored" name="funding_approved">
                    <option value="0" >Select</option>
                    <option value="2" <?php if ($this->funding_approved == 2) echo "selected"; ?> >Yes</option>
                    <option value="1" <?php if ($this->funding_approved == 1) echo "selected"; ?> >No</option>
                </select>
            </div>

            <div>
                <label>
                    Funding State
                </label>
                <select id="" name="funding_state">
                    <option value="0" >Select</option>
                    <option value="1" <?php if ($this->funding_state == 1) echo "selected"; ?> >Draft</option>
                    <option value="2" <?php if ($this->funding_state == 2) echo "selected"; ?> >Published</option>
                    <option value="3" <?php if ($this->funding_state == 3) echo "selected"; ?> >Successful</option>
                    <option value="4" <?php if ($this->funding_state == 4) echo "selected"; ?> >Failed</option>
                    <option value="5" <?php if ($this->funding_state == 5) echo "selected"; ?> >Submit for approval</option>
                    <option value="6" <?php if ($this->funding_state == 6) echo "selected"; ?> >Rejected</option>
                </select>
            </div>

            <div>
                <label>
                    Browse By
                </label>
                <select id="" name="projectbrowse">
                    <option value="0" >Select</option>
                    <option value="1" <?php if ($this->projectbrowse == 1) echo "selected"; ?> >Most Backed</option>
                    <option value="2" <?php if ($this->projectbrowse == 2) echo "selected"; ?> >Most Recent</option>
                </select>
            </div>
            <div class="clear mtop10">
                <button type="submit" name="search" >Search</button>
            </div>
        </form>
    </div>
</div>
<br />

<div class='admin_search'>
    <?php echo $this->formFilter->render($this) ?>
</div>

<div class='admin_members_results mtop10'>
    <?php $counter = $this->paginator->getTotalItemCount(); ?>
    <?php if (!empty($counter)): ?>
        <div class="">
            <?php echo $this->translate(array('%s project found.', '%s projects found.', $counter), $this->locale()->toNumber($counter)) ?>
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
    <form id='multidelete_form' method="post" action="<?php echo $this->url(array('action' => 'multi-delete')); ?>" onSubmit="return multiDelete()">
        <div class="manageproject_table_scroll">
            <table class='admin_table seaocore_admin_table' width="100%">
                <thead>
                    <tr>
                        <th><input onclick="selectAll()" type='checkbox' class='checkbox'></th>

                        <?php $class = ( $this->order == 'project_id' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
                        <th class="<?php echo $class ?>" ><a href="javascript:void(0);" onclick="javascript:changeOrder('project_id', 'DESC');">ID</a></th>

                        <?php $class = ( $this->order == 'title' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
                        <th class="<?php echo $class ?>"  align="left"><a href="javascript:void(0);" onclick="javascript:changeOrder('title', 'ASC');">Title</a></th>

                        <?php $class = ( $this->order == 'username' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
                        <th class="<?php echo $class ?>"  align="left" ><a href="javascript:void(0);" onclick="javascript:changeOrder('username', 'ASC');">Owner</a></th>

                        <th   align="left" >Category</th>

                        <?php $class = ( $this->order == 'backer_count' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
                        <th class="<?php echo $class ?> admin_table_centered"><a href="javascript:void(0);" onclick="javascript:changeOrder('backer_count', 'DESC');" title="Backers">B</a></th>

                        <?php $class = ( $this->order == 'goal_amount' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
                        <th class="<?php echo $class ?> admin_table_centered"><a href="javascript:void(0);" onclick="javascript:changeOrder('goal_amount', 'DESC');" title="Goal Amount">G/A</a></th>

                        <th class="<?php echo $class ?> admin_table_centered"><a href="javascript:void(0);" title="Backed Amount">B/A</a></th>


                        <!--PACKAGE ENABLED THEN ADDED SOME MORE COLUMNS-->
                        <?php if ($hasPackageEnable): ?>
                            <th align="left"  title="Package" >Package</th>
                            <th align="left"> Status </th>
                            <th align="left" title="Payment">Payment</th>
                        <?php endif; ?>

                        <?php $class = ( $this->order == 'featured' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
                        <th class="<?php echo $class ?> admin_table_centered"  title="Featured"><a href="javascript:void(0);" onclick="javascript:changeOrder('featured', 'ASC');">F</a></th>

                        <!--<?php $class = ( $this->order == 'sponsored' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
                        <th class="<?php echo $class ?> admin_table_centered" title="Sponsored"><a href="javascript:void(0);" onclick="javascript:changeOrder('sponsored', 'DESC');">S</a></th>-->
                        <th class="<?php echo $class ?> admin_table_centered" title="Status"><a href="javascript:void(0);" onclick="javascript:changeOrder('state', 'ASC');">Status</a></th>
                        <?php $class = ( $this->order == 'approved' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
                        <!--<th class="<?php echo $class ?> admin_table_centered" title="Approve"><a href="javascript:void(0);" onclick="javascript:changeOrder('approved', 'ASC');">A</a></th>-->
                        <th class="<?php echo $class ?> admin_table_centered" title="Reject"><a href="javascript:void(0);" >R</a></th>
                        <th class="<?php echo $class ?> admin_table_centered"><a href="javascript:void(0);">Funding</a></th>
                        <th class="<?php echo $class ?> admin_table_centered" title="Funding Status"><a href="javascript:void(0);" >$-Status</a></th>
                        <!--<th class="<?php echo $class ?> admin_table_centered" title="Funding Approve"><a href="javascript:void(0);">$-A</a></th>
                        <th class="<?php echo $class ?> admin_table_centered" title="Funding Reject"><a href="javascript:void(0);" >$-R</a></th>-->
                        <?php $class = ( $this->order == 'creation_date' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
                        <th align="left" class="<?php echo $class ?>" title="Project Creation Date"><a href="javascript:void(0);" onclick="javascript:changeOrder('creation_date', 'DESC');">Creation Date</a></th>

                        <?php $class = ( $this->order == 'start_date' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
                        <th align="left" class="<?php echo $class ?>" title="Project Publish Date"><a href="javascript:void(0);" onclick="javascript:changeOrder('start_date', 'DESC');">Publish Date</a></th>

                        <?php $class = ( $this->order == 'expiration_date' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
                        <th align="left" class="<?php echo $class ?>" title="Project Expiration Date"><a href="javascript:void(0);" onclick="javascript:changeOrder('expiration_date', 'DESC');">Expiration Date</a></th>

                        <?php $class = ( $this->order == 'members' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
                        <th align="left" class="<?php echo $class ?>" title="Project Members"><a href="javascript:void(0);" onclick="javascript:changeOrder('joined', 'DESC');">M</a></th>

                        <th class="<?php echo $class ?>"  class='admin_table_centered'>Options</th>
                    </tr>
                </thead>

                <tbody>
                    <?php if (count($this->paginator) > 0): ?>
                        <?php foreach ($this->paginator as $item): ?> 
                            <tr> 
                                <td><input name='delete_<?php echo $item->project_id; ?>' type='checkbox' class='checkbox' value="<?php echo $item->project_id ?>"/></td>

                                <td><?php echo $item->project_id ?></td>

                                <td class='admin_table_bold' style="white-space:normal;" title="<?php echo $item->getTitle() ?>">
                                    <a href="<?php echo $this->url(array('project_id' => $item->project_id, 'slug' => $item->getSlug()), "sitecrowdfunding_entry_view") ?>"  target='_blank'>
                                        <?php echo Engine_Api::_()->seaocore()->seaocoreTruncateText($item->getTitle(), 10) ?></a>
                                </td>

                                <td class='admin_table_bold' title="<?php echo $item->getOwner()->getTitle() ?>"> <?php echo $this->htmlLink($item->getOwner()->getHref(), Engine_Api::_()->seaocore()->seaocoreTruncateText($item->getOwner()->getTitle(), 10), array('target' => '_blank')) ?>
                                </td>
                                <td align="center" class="admin_table_centered">
                                    <?php
                                    if ($item->category_id) {
                                        $category = Engine_Api::_()->getItem('sitecrowdfunding_category', $item->category_id);
                                        echo $this->htmlLink($category->getHref(), $category->getTitle());
                                    } else {
                                        echo '--';
                                    }
                                    ?>
                                </td>
                                <td align="center" class="admin_table_centered"><?php echo $item->is_fund_raisable ? $item->backer_count : ' - ' ?></td>
                                <td align="center" class="admin_table_centered"><?php echo $item->is_fund_raisable ? $item->goal_amount : ' - ' ?></td>
                                <td align="center" class="admin_table_centered"><?php echo $item->is_fund_raisable ? $item->getFundedAmount() : ' - ' ?></td>


                                <?php if ($hasPackageEnable):
                                    ?>
                                    <td align="left">		<?php
                                        echo $this->htmlLink(
                                                array('route' => 'admin_default', 'module' => 'sitecrowdfunding', 'controller' => 'packages', 'action' => 'package-detail', 'id' => $item->package_id), ucfirst(Engine_Api::_()->seaocore()->seaocoreTruncateText($item->getPackage()->title, 10)), array('class' => 'smoothbox', 'title' => ucfirst($item->getPackage()->title)));
                                        ?></td>
                                    <td align="left"><?php echo $item->getProjectState(); ?></td>
                                    <td align="center" class="admin_table_centered">
                                        <?php if ($item->getPackage() && !$item->getPackage()->isFree()): ?>
                                            <?php
                                            if ($item->status == "initial"):
                                                echo "No";
                                            elseif ($item->status == "active"):
                                                echo "Yes";
                                            else:
                                                echo ucfirst($item->status);
                                            endif;
                                            ?>
                                        <?php else: ?>
                                            <?php echo "NA (Free)"; ?>
                                        <?php endif ?>
                                    </td>
                                <?php endif; ?>
                                
                                <?php if ($item->featured == 1): ?>
                                    <td align="center" class="admin_table_centered"> <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitecrowdfunding', 'controller' => 'general', 'action' => 'featured', 'project_id' => $item->project_id), $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/images/featured.gif', '', array('title' => 'Make Un-featured'))) ?></td>
                                <?php else: ?>
                                    <td align="center" class="admin_table_centered"><?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitecrowdfunding', 'controller' => 'general', 'action' => 'featured', 'project_id' => $item->project_id), $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/images/unfeatured.gif', '', array('title' => 'Make Featured'))) ?></td>
                                <?php endif; ?>

                                <!--<<?php if ($item->sponsored == 1): ?>
                                    <td align="center" class="admin_table_centered"> <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitecrowdfunding', 'controller' => 'general', 'action' => 'sponsored', 'project_id' => $item->project_id), $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/images/sponsored.png', '', array('title' => 'Make Unsponsored'))); ?></td>
                                <?php else: ?>
                                    <td align="center" class="admin_table_centered"> <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitecrowdfunding', 'controller' => 'general', 'action' => 'sponsored', 'project_id' => $item->project_id), $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/images/unsponsored.png', '', array('title' => 'Make Sponsored'))); ?>
                                    <?php endif; ?>   
                                -->
                                <td align="center" class="admin_table_centered"><?php echo $item->state; ?></td>
                                <?php /*if ($item->approved === 1): ?>
                                    <td align="center" class="admin_table_centered">Approved</td>
                                <?php else: ?>
                                    <td align="center" class="admin_table_centered"> <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitecrowdfunding', 'controller' => 'general', 'action' => 'approved', 'project_id' => $item->project_id, 'is_funding' => 0), $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/images/approved.gif', '', array('title' => 'Make Approved'))) ?></td>
                                <?php endif;*/ ?>
                                <td align="center" class="admin_table_centered"> <?php echo $this->htmlLink(
                                    array(
                                    'route' => 'admin_default',
                                    'module' => 'sitecrowdfunding',
                                    'controller' => 'manage',
                                    'action' => 'reject',
                                    'project_id' => $item->project_id,
                                    'is_funding' => 0
                                    ),

                                    $this->htmlImage(
                                    $this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/images/disapproved.gif', '',
                                    array('title' => 'Make Dis-Approved')
                                    ),
                                    array('class' => 'smoothbox')
                                    ) ?></td>
                                <td align="center" class="admin_table_centered"><?php echo $item->is_fund_raisable ? 'yes' : 'no'; ?></td>
                                <td align="center" class="admin_table_centered"><?php echo $item->is_fund_raisable ? $item->funding_state : ' - '; ?></td>
                                <?php /* if ($item->funding_approved === 1): ?>
                                    <td align="center" class="admin_table_centered">Approved</td>
                                <?php else: ?>
                                    <td align="center" class="admin_table_centered"> <?php echo $item->is_fund_raisable ? $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitecrowdfunding', 'controller' => 'general', 'action' => 'approved', 'project_id' => $item->project_id, 'is_funding' => 1), $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/images/approved.gif', '', array('title' => 'Make Funding Approved'))) : ' - ' ?></td>
                                <?php endif;*/ ?>
                                <!-- <td align="center" class="admin_table_centered"> <?php echo $item->is_fund_raisable ? $this->htmlLink(
                                    array(
                                    'route' => 'admin_default',
                                    'module' => 'sitecrowdfunding',
                                    'controller' => 'manage',
                                    'action' => 'reject',
                                    'project_id' => $item->project_id,
                                    'is_funding' => 1
                                    ),

                                    $this->htmlImage(
                                    $this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/images/disapproved.gif', '',
                                    array('title' => 'Make Funding Dis-Approved')
                                    ),
                                    array('class' => 'smoothbox')
                                    ): ' - ' ?></td>-->
                                <td><?php echo gmdate('M d,Y', strtotime($item->creation_date)) ?></td>
                                <td><?php echo gmdate('M d,Y', strtotime($item->start_date)) ?></td>  
                                <td align="left" ><?php echo $item->is_fund_raisable ? gmdate('M d,Y', strtotime($item->getExpiryDate())) : ' - ' ?></td>

                                <td align="left" >
                                    <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'sitecrowdfunding', 'controller' => 'admin-manage', 'action' => 'list-project-members', 'project_id' => $item->project_id), $this->translate($this->locale()->toNumber(Engine_Api::_()->getDbtable('memberships', 'sitecrowdfunding')->getMembersCount($item->project_id))), array('onclick' => 'openMembersList(this);return false')); ?>
                                </td>

                                <td class='admin_table_options'>
                                    <?php echo $this->htmlLink($this->url(array('project_id' => $item->project_id, 'slug' => $item->getSlug()), "sitecrowdfunding_entry_view"), 'View', array('target' => '_blank')) ?> 
                                    |
                                    <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitecrowdfunding', 'controller' => 'manage', 'action' => 'backers', 'project_id' => $item->project_id), 'View Backers', array('target' => '_blank')) ?>   
                                    |
                                    <?php echo $this->htmlLink(array('route' => 'sitecrowdfunding_specific', 'action' => 'edit', 'project_id' => $item->project_id), 'Edit', array('target' => '_blank')) ?>                             
                                    |
                                    <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitecrowdfunding', 'controller' => 'general', 'action' => 'delete', 'project_id' => $item->project_id), 'Delete', array('class' => 'smoothbox')) ?>
                                    <?php if ($this->paymentSetting == 'mannual' && empty($item->payout_status)): ?>
                                        <?php if ($item->payoutButton() == 'successful'): ?>
                                            |
                                            <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitecrowdfunding', 'controller' => 'general', 'action' => 'payout', 'project_id' => $item->project_id), 'Payout', array('class' => 'smoothbox')) ?>
                                        <?php elseif ($item->payoutButton() == 'failed') : ?>
                                            |
                                            <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitecrowdfunding', 'controller' => 'general', 'action' => 'payout', 'project_id' => $item->project_id), 'Payout', array('class' => 'smoothbox')) ?>
                                            |
                                            <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitecrowdfunding', 'controller' => 'general', 'action' => 'refund', 'project_id' => $item->project_id), 'Refund', array('class' => 'smoothbox')) ?> 
                                        <?php endif; ?>
                                    <?php endif; ?>
                                    <?php if (!empty($item->message)) : ?>
                                        |
                                        <a href="javascript:void(0);" onclick='showMessage("<?php echo $item->message; ?>")'>Message</a>
                                    <?php endif; ?> 
                                </td>
                            </tr>

                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <br />
        <div class='buttons'>
            <button type='submit'>Delete Selected</button>
        </div>
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

    function openMembersList(thisobj) {
        var Obj_Url = thisobj.href ;
        Smoothbox.open(Obj_Url);
    }

    function addOptions(element_value, element_type, element_updated, domready) {

        var element = $(element_updated);
        if (domready == 0) {
            switch (element_type) {
                case 'cat_dependency':
                    $('subcategory_id' + '-wrapper').style.display = 'none';
                    clear($('subcategory_id'));
                    $('subcategory_id').value = 0;

                case 'subcat_dependency':
                    $('subsubcategory_id' + '-wrapper').style.display = 'none';
                    clear($('subsubcategory_id'));
                    $('subsubcategory_id').value = 0;
            }
        }

        if (element_value <= 0)
            return;

        var url = '<?php echo $this->url(array('module' => 'sitecrowdfunding', 'controller' => 'general', 'action' => 'categories'), "admin_default", true); ?>';
        en4.core.request.send(new Request.JSON({
            url: url,
            data: {
                format: 'json',
                element_value: element_value,
                element_type: element_type
            },
            onSuccess: function (responseJSON) {
                var categories = responseJSON.categories;
                var option = document.createElement("OPTION");
                option.text = "";
                option.value = 0;
                element.options.add(option);
                for (i = 0; i < categories.length; i++) {
                    var option = document.createElement("OPTION");
                    option.text = categories[i]['category_name'];
                    option.value = categories[i]['category_id'];
                    element.options.add(option);
                }

                if (categories.length > 0)
                    $(element_updated + '-wrapper').style.display = 'block';
                else
                    $(element_updated + '-wrapper').style.display = 'none';

                if (domready == 1) {
                    var value = 0;
                    if (element_updated == 'category_id') {
                        value = search_category_id;
                    } else if (element_updated == 'subcategory_id') {
                        value = search_subcategory_id;
                    } else {
                        value = search_subsubcategory_id;
                    }
                    $(element_updated).value = value;
                }
            }

        }), {'force': true});
    }

    function clear(element)
    {
        for (var i = (element.options.length - 1); i >= 0; i--) {
            element.options[ i ] = null;
        }
    }

    var search_category_id, search_subcategory_id, search_subsubcategory_id;
    window.addEvent('domready', function () {

        search_category_id = '<?php echo $this->category_id ? $this->category_id : 0 ?>';

        if (search_category_id != 0) {
            search_subcategory_id = '<?php echo $this->subcategory_id ? $this->subcategory_id : 0 ?>';

            addOptions(search_category_id, 'cat_dependency', 'subcategory_id', 1);

            if (search_subcategory_id != 0) {
                search_subsubcategory_id = '<?php echo $this->subsubcategory_id ? $this->subsubcategory_id : 0 ?>';
                addOptions(search_subcategory_id, 'subcat_dependency', 'subsubcategory_id', 1);
            }
        }
    });

</script>

<script type="text/javascript">
    function showMessage(message) {
        Smoothbox.open('<div><span>' + message + '</span></div>');
    }
</script>
