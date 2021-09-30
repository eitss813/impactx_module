<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.php 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
$baseUrl = $this->layout()->staticBaseUrl;
$this->headLink()->appendStylesheet($baseUrl . 'application/modules/Sitecrowdfunding/externals/styles/style_sitecrowdfunding.css');
?>
<?php if ($this->loaded_by_ajax): ?>
    <script type="text/javascript">
        var params = {
            requestParams:<?php echo json_encode($this->params) ?>,
            responseContainer: $$('.layout_sitecrowdfunding_specification_project')
        }
        en4.sitecrowdfunding.ajaxTab.attachEvent('<?php echo $this->identity ?>', params);
    </script>
<?php endif; ?>  
    <div class='profile_fields'>
        <h4 id='show_basicinfo'>
            <?php echo $this->translate('Basic Information'); ?>
        </h4> 
        <ul>
            <li>
                <span><?php echo $this->translate('Created By :'); ?> </span>
                <span><?php echo $this->htmlLink($this->project->getOwner()->getHref(), $this->translate($this->project->getOwner()->getTitle())) ?></span>
            </li>
            <li>
                <span><?php echo $this->translate('Published On :'); ?></span>
                <span><?php echo $this->translate($this->project->getStartDate()) ?></span>
            </li>
            <?php if($this->project->isFundingApproved()): ?>
            <li>
                <span><?php echo $this->translate('Funding Ends :'); ?></span>
                <span>
                    <?php if($this->project->isExpired()): ?>
                        <?php echo $this->translate('Closed'); ?>
                    <?php else: ?>
                        <?php echo $this->project->getExpiryDate(); ?>
                    <?php endif; ?>
                </span>
            </li>
            <?php endif; ?>
            <?php if ($this->project->category_id) : ?>
                <li>
                    <span><?php echo $this->translate('Category :'); ?></span> 
                    <span>
                        <?php
                        $category = Engine_Api::_()->getItem('sitecrowdfunding_category', $this->project->category_id);
                        echo $this->htmlLink($category->getHref(), $this->translate($category->getTitle()));
                        ?>
                    </span>
                </li>
            <?php endif; ?>  

            <!--<li>
                <span><?php echo $this->translate(array('%s Like', '%s Likes', $this->project->like_count), ''); ?> :</span>
                <span><?php echo $this->project->like_count ?></span>
            </li> -->
            <?php if($this->project->isFundingApproved()): ?>
            <li>
                <span><?php echo $this->translate(array('%s Backer', '%s Backers', $this->total_backer_count), ''); ?> :</span>
                <span><?php echo $this->total_backer_count ?></span>
            </li>
            <?php endif; ?>
            <li>
                <span><?php echo $this->translate('Address'); ?> :</span>
                <span><?php echo !empty($this->address) ? $this->address : ' - ' ?></span>
            </li>

            <li>
                <span><?php echo $this->translate('Phone'); ?> :</span>
                <span><?php echo !empty($this->phone) ? $this->phone : ' - ' ?></span>
            </li>
            <li>
                <span><?php echo $this->translate('Email'); ?> :</span>
                <span><?php echo !empty($this->email) ? $this->email : ' - ' ?></span>
            </li>
            <li>
                <span><?php echo $this->translate('Description'); ?> :</span>
                <span><?php echo $this->viewMore($this->translate($this->project->description), 300, 5000) ?></span>
            </li>
        </ul>
        </div>
        <?php if (!empty($this->show_fields)) : ?>
            <h4><?php echo $this->translate('Profile Information'); ?></h4>
            <?php echo $this->translate($this->show_fields) ?>
        <?php endif; ?>
 <style>
     .generic_layout_container.layout_sitecrowdfunding_specification_project {
         display: none;
     }
 </style>