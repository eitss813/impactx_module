<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<div id="sitepage_options"> 
    <ul>   
        <?php
        $c = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.showmore', 8);
        $i = 0;
        $flage = 0;
        $id = 0;
        $base_url = Zend_Controller_Front::getInstance()->getBaseUrl();
        ?>
        <?php $l = 0;
        $id_main = ''; ?>
        <?php foreach ($this->contentWigentLinks as $item) { ?>
            <?php if ($l == 0) : ?>
                <?php $id_main = $item['content_id']; ?>
            <?php endif; ?>
            <?php $l++; ?>
            <?php if (!empty($item['content_name'])): ?>    
                <?php if (empty($this->tab_id)) : ?>
            <?php $id = $id_main; ?>
                <?php else: ?>				
                    <?php $id = $this->tab_id; ?>
                <?php endif; ?>	
                <?php if ($i == $c): ?>			
                </ul> 					
                <ul id='hide' style="display:none;">
                    <?php
                    $flage = 1;
                endif;
                $i = $i + 1;
                ?>
                        <?php if (isset($item['content_resource'])): ?>
            <?php $url = $this->url(array('page_url' => Engine_Api::_()->sitepage()->getPageUrl($this->subject->page_id), 'tab' => $item['content_id'], 'resource_type' => $item['content_resource']), 'sitepage_entry_view', true); ?>
                <?php else: ?>
                    <?php $url = $this->url(array('page_url' => Engine_Api::_()->sitepage()->getPageUrl($this->subject->page_id), 'tab' => $item['content_id']), 'sitepage_entry_view', true); ?>
                <?php endif; ?>
                <li id='select_<?php echo $item['content_id']; ?>'>
                    <a class="tab_<?php echo $item['content_id']; ?> buttonlink <?php echo $item['content_class']; ?>" href="<?php echo $url; ?>" onclick="showselected('<?php echo $item['content_id']; ?>');
                                return false;"><?php echo $this->translate($item['content_name']); ?>	      
                    </a>
                </li>
    <?php endif; ?>
<?php } ?> 
<?php if ($flage): ?>
        </ul>
        <div>
            <a href='javascript:void(0);' onclick="linkdisplay();" class="buttonlink" style="font-weight:normal;"><span id="linkdisplay"> <?php echo $this->translate('More'); ?></span></a> 		</div>
<?php endif; ?>
</div>

<script type="text/javascript">
    var oldactive = 0;

    window.addEvent('domready', function () {
        if (document.getElementById('select_' + <?php echo $id ?>))
            document.getElementById('select_' + <?php echo $id ?>).className = "active";
    });

    function showselected(id) {
        if (document.getElementById('select_' + <?php echo $id ?>)) {
            document.getElementById('select_' + <?php echo $id ?>).className = "";
            if ($('profile_status')) {
                location.hash = 'profile_status';
            }
        }
        document.getElementById('select_' + id).className = "active";

        if ($('profile_status')) {
            location.hash = 'profile_status';
        }

        if (oldactive != 0) {
            document.getElementById('select_' + oldactive).className = "";
            if ($('profile_status')) {
                location.hash = 'profile_status';
            }
        }
        oldactive = id;
    }

    function linkdisplay() {
        var id = 'hide';
        if ($('linkdisplay')) {
            if ($(id).style.display == 'block') {
                $(id).style.display = 'none';
                $('linkdisplay').innerHTML = 'More';
            }
            else {
                $(id).style.display = 'block';
                $('linkdisplay').innerHTML = 'Less';
            }
        }
    }

</script>