<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitealbum
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: manage.tpl 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<script type="text/javascript">
    function more_subcats(cat_id) {
        $$('.subcats_' + cat_id).each(function(el) {
            el.style.display = 'block';
        });

        $('more_link_cats_' + cat_id).style.display = 'none';
        $('fewer_link_cats_' + cat_id).style.display = 'block';
    }

    function fewer_subcats(cat_id) {
        $$('.subcats_' + cat_id).each(function(el) {
            el.style.display = 'none';
        });

        $('fewer_link_cats_' + cat_id).style.display = 'none';
        $('more_link_cats_' + cat_id).style.display = 'block';
    }
</script>  

<h2>
    <?php echo $this->translate('Advanced Albums Plugin'); ?>
</h2>

<?php if (count($this->navigation)): ?>
    <div class='seaocore_admin_tabs'>
        <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render() ?>
    </div>
<?php endif; ?>

<div class='seaocore_settings_form'>
    <div class='settings'>
        <form class="global_form">
            <div>
                <h3><?php echo $this->translate("Category to Album Profile Mapping") ?> </h3>
                <p class="form-description">
                    <?php echo $this->translate("This mapping will associate an Album Profile Type with a Category / Sub-category. After such a mapping for a category, sub-category, album owner of albums belonging to that category, sub-category will be able to fill profile information fields for that profile type in their albums. With this mapping, you will also be able to associate a profile type with multiple categories / sub-categories.<br /><br />For information on album profile types, profile fields and to create new profile types or profile fields, please visit the 'Profile Fields' section. An example use case of this feature would be associating category books with profile type having profile fields related to books and so on.<br /><br /><b>Note:</b> If you map a Category, then all its sub-categories will be automatically mapped with the same Album Profile Type. If you want to map different Album Profile Types for sub-categories, then you can anytime remove the mapping from Category and add new mapping for sub-categories.") ?>
                </p>

                <?php if (count($this->categories) > 0): ?>
                    <table class='admin_table sitealbum_mapping_table' width="100%">
                        <thead>
                            <tr>
                                <th>
                        <div class="sitealbum_mapping_table_name fleft"><b class="bold"><?php echo $this->translate("Category Name") ?></b></div>
                        <div class="sitealbum_mapping_table_value fleft"><b class="bold"><?php echo $this->translate("Associated Profile") ?></b></div>
                        <div class="sitealbum_mapping_table_option fleft"><b class="bold"><?php echo $this->translate("Mapping") ?></b></div>
                        </th>
                        </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($this->categories as $category): ?>                    
                                <tr>
                                    <td>
                                        <div class="sitealbum_mapping_table_name fleft">
                                            <span><b class="bold"><?php echo $category['category_name']; ?></b></span>
                                            <?php if (Count($category['sub_categories']) >= 1): ?>
                                                <span id="fewer_link_cats_<?php echo $category['category_id']; ?>" >    
                                                    <a href="javascript:void(0)" onclick="fewer_subcats('<?php echo $category['category_id']; ?>');" title="<?php echo $this->translate('Click to hide sub-categories') ?>">[-]</a>
                                                </span>                      

                                                <span id="more_link_cats_<?php echo $category['category_id']; ?>" style="display:none;">    
                                                    <a href="javascript:void(0)" onclick="more_subcats('<?php echo $category['category_id']; ?>');" title="<?php echo $this->translate('Click to show sub-categories') ?>">[+]</a>
                                                </span>

                                            <?php endif; ?>
                                        </div>
                                        <div class="sitealbum_mapping_table_value fleft">
                                            <ul>
                                                <li><?php echo $this->translate($category['cat_profile_type_label']); ?></li>
                                            </ul>
                                        </div>

                                        <div class="sitealbum_mapping_table_option fleft">
                                            <?php if (empty($category['cat_profile_type_id'])): ?>
                                                <?php
                                                echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitealbum', 'controller' => 'profilemaps', 'action' => 'map', 'category_id' => $category['category_id']), $this->translate('Add'), array(
                                                    'class' => 'smoothbox',
                                                ))
                                                ?>
                                            <?php else: ?>

                                                <?php if ($this->totalProfileTypes > 1): ?>
                                                    <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitealbum', 'controller' => 'profilemaps', 'action' => 'edit', 'category_id' => $category['category_id'], 'profile_type' => $category['cat_profile_type_id']), $this->translate('Edit'), array('class' => 'smoothbox')) ?> | 
                                                <?php endif; ?>   

                                                <?php
                                                echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitealbum', 'controller' => 'profilemaps', 'action' => 'remove', 'category_id' => $category['category_id']), $this->translate('Remove'), array(
                                                    'class' => 'smoothbox',
                                                ))
                                                ?>
                                <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                                <?php foreach ($category['sub_categories'] as $subcategory) : ?>       
                                    <tr class="subcats_<?php echo $category['category_id'] ?>">
                                        <td>
                                            <div class="sitealbum_mapping_table_name fleft">
                                                <span><img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitealbum/externals/images/gray_bullet.png" alt=""></span>
                                                <span><?php echo $subcategory['sub_cat_name']; ?></span>
                                            </div>                      
                                            <div class="sitealbum_mapping_table_value fleft">
                                                <ul>
                                                    <li><?php echo $this->translate($subcategory['subcat_profile_type_label']); ?></li>
                                                </ul>
                                            </div>  
                                            <div class="sitealbum_mapping_table_option fleft">
                                                <?php if (!empty($category['cat_profile_type_id'])): ?>
                                                    <span title='<?php echo $this->translate("You can not map this sub-category as its parent category is already mapped to a profile type and sub-category inherits mapping from its parent category. If you want to map this sub-category then please remove parent category mapping first."); ?>'><?php echo $this->translate('Add'); ?></span>
                                                <?php elseif (empty($subcategory['subcat_profile_type_id'])): ?>
                                                    <?php
                                                    echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitealbum', 'controller' => 'profilemaps', 'action' => 'map', 'category_id' => $subcategory['sub_cat_id']), $this->translate('Add'), array(
                                                        'class' => 'smoothbox',
                                                    ))
                                                    ?>
                                                <?php else: ?>
                                                    <?php if ($this->totalProfileTypes > 1): ?>
                                                        <?php
                                                        echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitealbum', 'controller' => 'profilemaps', 'action' => 'edit', 'category_id' => $subcategory['sub_cat_id'], 'profile_type' => $subcategory['subcat_profile_type_id']), $this->translate('Edit'), array(
                                                            'class' => 'smoothbox',
                                                        ))
                                                        ?> | 
                                                <?php endif; ?>                                    

                                                <?php
                                                    echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitealbum', 'controller' => 'profilemaps', 'action' => 'remove', 'category_id' => $subcategory['sub_cat_id']), $this->translate('Remove'), array(
                                                        'class' => 'smoothbox',
                                                    ))
                                                    ?>
                                                <?php endif; ?>
                                            </div>  
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endforeach; ?>                  
                        </tbody>
                    </table>
                <?php else: ?>
                    <br/>
                    <div class="tip">
                        <span><?php echo $this->translate("There are currently no categories to be mapped.") ?></span>
                    </div>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>