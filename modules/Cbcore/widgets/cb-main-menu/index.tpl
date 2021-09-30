<?php
/**
 * SocialEngine
 *
 * @category   Module
 * @package    Consecutive Bytes Core
 * @copyright  Copyright 2015 - 2017 Consecutive Bytes
 * @license    http://www.consecutivebytes.com/license/
 * @version    4.9.0
 * @author     Consecutive Bytes
 */
 ?>
<div class="layout_core_menu_main">
    
    <ul id="MainMenu" class="navigation">
    <?php
        $count = $this->count; 
        $module_name = Zend_Controller_Front::getInstance()->getRequest()->getModuleName();
        $action_name = Zend_Controller_Front::getInstance()->getRequest()->getActionName();
        $i = 0;

        foreach( $this->navigation as $item ):

            $label = $item->getLabel();
            $class = $item->getClass();

            if($i < $count){

            if((strstr(strtolower($label), $module_name) != "") || ($module_name == "core" && $label == "Home") || ($module_name == "user" && $label == "Home" && $action_name == "home") || ($module_name == "user" && $label == "Members" && $action_name == "browse")) { 
                $active = 'active';
            }else{
                $active = '';
                }
            ?>

            <li class="<?= $active;?>">
                <a href="<?php echo $item->getHref();?>"><?php echo $this->translate($item->getLabel());?></a>
            </li>
            <?php
                $i += 1;
            }else{
                if(strstr(strtolower($label), $module_name) != "") {
                    $active = 'active';
                }else{
                    $active = '';
                }
                $More[$i] = "<li class=".$active."><a class='".$item->getclass()."' href='" . $item->getHref() . "'>" . $this->translate($item->getLabel()) . "</a></li>";
                $i += 1;
            }
        endforeach;

        if($i>$count){ ?>
            <li id ="view_menu" class="view_menu"><a href="javascript: void(0)"><?php echo $this->translate("More"); ?></a>
                <ul id="submenu" class="submenu">
                <?php
                    for($j = 2; $j < $i; $j++){
                        if( isset($More[$j]) ){
                            echo $More[$j];
                        }  
                    }
                ?>
                </ul>
            </li>
        <?php } ?>    

        <?php if($this->search == 1): ?>
            <li class="search">
        <ul class="search">
            <li><?php echo $this->translate('Advanced'); ?><br /><?php echo $this->translate('Search'); ?></li>
            <li id="global_search_form_container">

            <form id="global_search_form" action="<?php echo $this->url(array('controller' => 'search'), 'default', true) ?>" method="get">
                <input type='text' class='search_input' name='query' id='global_search_field' size='20' maxlength='100' placeholder="Search" />
              <input type="submit" class="search_button" />
            </form>
          </li>
        </ul>
            </li>
        <?php endif; ?>

    </ul>
    
    <?php echo $this->content()->renderWidget('core.menu-mini'); ?>
</div>



<style type="text/css"  style="display:none;">
    ::-webkit-input-placeholder {
        color: #6F6F6F;
        font-style: italic;
    }
    :-moz-placeholder { /* Firefox 18- */
        color: #6F6F6F;
        font-style: italic;
    }
    ::-moz-placeholder {  /* Firefox 19+ */
        color: #6F6F6F;  
        font-style: italic;
    }
    :-ms-input-placeholder {  
        color: #6F6F6F;
        font-style: italic;
    }
</style>