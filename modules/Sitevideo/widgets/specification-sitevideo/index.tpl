<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
$baseUrl = $this->layout()->staticBaseUrl;
$this->headLink()->appendStylesheet($baseUrl . 'application/modules/Sitevideo/externals/styles/style_sitevideo.css');
?>
<?php if ($this->loaded_by_ajax): ?>
    <script type="text/javascript">
        var params = {
            requestParams:<?php echo json_encode($this->params) ?>,
            responseContainer: $$('.layout_sitevideo_specification_sitevideo')
        }
        en4.sitevideo.ajaxTab.attachEvent('<?php echo $this->identity ?>', params);
    </script>
<?php endif; ?>

<?php if ($this->showContent): ?>
    <div class='profile_fields'>
        <h4 id='show_basicinfo'>
            <span><?php echo $this->translate('Basic Information'); ?></span>
        </h4>
        <ul>
            <li>
                <span><?php echo $this->translate('Created By:'); ?> </span>
                <span><?php echo $this->htmlLink($this->sitevideo->getOwner()->getHref(), $this->sitevideo->getOwner()->getTitle()) ?></span>
            </li>
            <li>
                <span><?php echo $this->translate('Created On:'); ?></span>
                <span><?php echo $this->translate(gmdate('M d, Y', strtotime($this->sitevideo->creation_date))) ?></span>
            </li>    
            <li>
                <span><?php echo $this->translate('Last Updated:'); ?></span>
                <span><?php echo $this->translate(gmdate('M d, Y', strtotime($this->sitevideo->modified_date))) ?></span>
            </li>
            <?php if ($this->sitevideo->category_id) : ?>
                <li>
                    <span><?php echo $this->translate('Category:'); ?></span> 
                    <span>
                        <?php
                        $category = Engine_Api::_()->getItem('sitevideo_channel_category', $this->sitevideo->category_id);
                        echo $this->htmlLink($category->getHref(), $category->getTitle());
                        ?>
                    </span>
                </li>
            <?php endif; ?>
            <li>
                <span><?php echo $this->translate('Videos'); ?>:</span>
                <span><?php echo $this->sitevideo->videos_count ?></span>
            </li>
            <li>
                <span><?php echo $this->translate('Views'); ?>:</span>
                <span><?php echo $this->sitevideo->view_count ?></span>
            </li>
            <li>
                <span><?php echo $this->translate('Likes'); ?>:</span>
                <span><?php echo $this->sitevideo->like_count ?></span>
            </li>
            <li>
                <span><?php echo $this->translate('Favourites'); ?>:</span>
                <span><?php echo $this->sitevideo->favourite_count ?></span>
            </li>
            <li>
                <span><?php echo $this->translate('Subscribers'); ?>:</span>
                <span><?php echo $this->sitevideo->subscribe_count ?></span>
            </li>
            <li>
                <span><?php echo $this->translate('Description'); ?>:</span>
                <span><?php echo $this->viewMore($this->sitevideo->description, 300, 5000) ?></span>
            </li>
        </ul>
        <?php if (!empty($this->show_fields)) : ?>
            <h4 >
                <span><?php echo $this->translate('Profile Information'); ?></span>
            </h4>
            <?php echo $this->show_fields ?>
        <?php endif; ?>
    <?php endif; ?>