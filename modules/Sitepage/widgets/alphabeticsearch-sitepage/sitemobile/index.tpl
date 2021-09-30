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

<section class="sm-widget-block p_b_search">
  <?php $url =  $this->url(array('page'=>'1')); //$this->url(array('action' => 'index'), 'sitepage_general', true); ?>
  <a href="<?php echo $url . '?alphabeticsearch=' . urlencode('all')?>" <?php if( isset($_GET['alphabeticsearch']) && $_GET['alphabeticsearch'] == 'all' ):?>class="bold"<?php endif;?>><?php echo $this->translate('LANG_FLAG_All');?></a>
  <a href="<?php echo $url . '?alphabeticsearch=' . urlencode('a')?>" <?php if( isset($_GET['alphabeticsearch']) && $_GET['alphabeticsearch'] == 'a'):?>class="bold"<?php endif;?>><?php echo $this->translate('LANG_FLAG_A');?></a>
  <a href="<?php echo $url . '?alphabeticsearch=' . urlencode('b')?>"<?php if( isset($_GET['alphabeticsearch']) && $_GET['alphabeticsearch'] == 'b' ):?>class="bold"<?php endif;?>><?php echo $this->translate('LANG_FLAG_B');?></a>
  <a href="<?php echo $url . '?alphabeticsearch=' . urlencode('c')?>"<?php if(isset($_GET['alphabeticsearch']) && $_GET['alphabeticsearch'] == 'c' ):?>class="bold"<?php endif;?>><?php echo $this->translate('LANG_FLAG_C');?></a>
  <a href="<?php echo $url . '?alphabeticsearch=' . urlencode('d')?>"<?php if(isset($_GET['alphabeticsearch']) && $_GET['alphabeticsearch'] == 'd' ):?>class="bold"<?php endif;?>><?php echo $this->translate('LANG_FLAG_D');?></a>
  <a href="<?php echo $url . '?alphabeticsearch=' . urlencode('e')?>"<?php if(isset($_GET['alphabeticsearch']) && $_GET['alphabeticsearch'] == 'e' ):?>class="bold"<?php endif;?>><?php echo $this->translate('LANG_FLAG_E');?></a>
  <a href="<?php echo $url . '?alphabeticsearch=' . urlencode('f')?>"<?php if(isset($_GET['alphabeticsearch']) && $_GET['alphabeticsearch'] == 'f' ):?>class="bold"<?php endif;?>><?php echo $this->translate('LANG_FLAG_F');?></a>
  <a href="<?php echo $url . '?alphabeticsearch=' . urlencode('g')?>"<?php if(isset($_GET['alphabeticsearch']) && $_GET['alphabeticsearch'] == 'g' ):?>class="bold"<?php endif;?>><?php echo $this->translate('LANG_FLAG_G');?></a>
  <a href="<?php echo $url . '?alphabeticsearch=' . urlencode('h')?>"<?php if(isset($_GET['alphabeticsearch']) && $_GET['alphabeticsearch'] == 'h' ):?>class="bold"<?php endif;?>><?php echo $this->translate('LANG_FLAG_H');?></a>
  <a href="<?php echo $url . '?alphabeticsearch=' . urlencode('i')?>"<?php if(isset($_GET['alphabeticsearch']) && $_GET['alphabeticsearch'] == 'i' ):?>class="bold"<?php endif;?>><?php echo $this->translate('LANG_FLAG_I');?></a>
  <a href="<?php echo $url . '?alphabeticsearch=' . urlencode('j')?>"<?php if(isset($_GET['alphabeticsearch']) && $_GET['alphabeticsearch'] == 'j' ):?>class="bold"<?php endif;?>><?php echo $this->translate('LANG_FLAG_J');?></a>
  <a href="<?php echo $url . '?alphabeticsearch=' . urlencode('k')?>"<?php if(isset($_GET['alphabeticsearch']) && $_GET['alphabeticsearch'] == 'k' ):?>class="bold"<?php endif;?>><?php echo $this->translate('LANG_FLAG_K');?></a>
  <a href="<?php echo $url . '?alphabeticsearch=' . urlencode('l')?>"<?php if(isset($_GET['alphabeticsearch']) && $_GET['alphabeticsearch'] == 'l' ):?>class="bold"<?php endif;?>><?php echo $this->translate('LANG_FLAG_L');?></a>
  <a href="<?php echo $url . '?alphabeticsearch=' . urlencode('m')?>"<?php if(isset($_GET['alphabeticsearch']) && $_GET['alphabeticsearch'] == 'm' ):?>class="bold"<?php endif;?>><?php echo $this->translate('LANG_FLAG_M');?></a>
  <a href="<?php echo $url . '?alphabeticsearch=' . urlencode('n')?>"<?php if(isset($_GET['alphabeticsearch']) && $_GET['alphabeticsearch'] == 'n' ):?>class="bold"<?php endif;?>><?php echo $this->translate('LANG_FLAG_N');?></a>
  <a href="<?php echo $url . '?alphabeticsearch=' . urlencode('o')?>"<?php if(isset($_GET['alphabeticsearch']) && $_GET['alphabeticsearch'] == 'o' ):?>class="bold"<?php endif;?>><?php echo $this->translate('LANG_FLAG_O');?></a>
  <a href="<?php echo $url . '?alphabeticsearch=' . urlencode('p')?>"<?php if(isset($_GET['alphabeticsearch']) && $_GET['alphabeticsearch'] == 'p' ):?>class="bold"<?php endif;?>><?php echo $this->translate('LANG_FLAG_P');?></a>
  <a href="<?php echo $url . '?alphabeticsearch=' . urlencode('q')?>"<?php if(isset($_GET['alphabeticsearch']) && $_GET['alphabeticsearch'] == 'q' ):?>class="bold"<?php endif;?>><?php echo $this->translate('LANG_FLAG_Q');?></a>
  <a href="<?php echo $url . '?alphabeticsearch=' . urlencode('r')?>"<?php if(isset($_GET['alphabeticsearch']) && $_GET['alphabeticsearch'] == 'r' ):?>class="bold"<?php endif;?>><?php echo $this->translate('LANG_FLAG_R');?></a>
  <a href="<?php echo $url . '?alphabeticsearch=' . urlencode('s')?>"<?php if(isset($_GET['alphabeticsearch']) && $_GET['alphabeticsearch'] == 's' ):?>class="bold"<?php endif;?>><?php echo $this->translate('LANG_FLAG_S');?></a>
  <a href="<?php echo $url . '?alphabeticsearch=' . urlencode('t')?>"<?php if(isset($_GET['alphabeticsearch']) && $_GET['alphabeticsearch'] == 't' ):?>class="bold"<?php endif;?>><?php echo $this->translate('LANG_FLAG_T');?></a>
  <a href="<?php echo $url . '?alphabeticsearch=' . urlencode('u')?>"<?php if(isset($_GET['alphabeticsearch']) && $_GET['alphabeticsearch'] == 'u' ):?>class="bold"<?php endif;?>><?php echo $this->translate('LANG_FLAG_U');?></a>
  <a href="<?php echo $url . '?alphabeticsearch=' . urlencode('v')?>"<?php if(isset($_GET['alphabeticsearch']) && $_GET['alphabeticsearch'] == 'v' ):?>class="bold"<?php endif;?>><?php echo $this->translate('LANG_FLAG_V');?></a>
  <a href="<?php echo $url . '?alphabeticsearch=' . urlencode('w')?>"<?php if(isset($_GET['alphabeticsearch']) && $_GET['alphabeticsearch'] == 'w' ):?>class="bold"<?php endif;?>><?php echo $this->translate('LANG_FLAG_W');?></a>
  <a href="<?php echo $url . '?alphabeticsearch=' . urlencode('x')?>"<?php if(isset($_GET['alphabeticsearch']) && $_GET['alphabeticsearch'] == 'x' ):?>class="bold"<?php endif;?>><?php echo $this->translate('LANG_FLAG_X');?></a>
  <a href="<?php echo $url . '?alphabeticsearch=' . urlencode('y')?>"<?php if(isset($_GET['alphabeticsearch']) && $_GET['alphabeticsearch'] == 'y' ):?>class="bold"<?php endif;?>><?php echo $this->translate('LANG_FLAG_Y');?></a>
  <a href="<?php echo $url . '?alphabeticsearch=' . urlencode('z')?>"<?php if(isset($_GET['alphabeticsearch']) && $_GET['alphabeticsearch'] == 'z' ):?>class="bold"<?php endif;?>><?php echo $this->translate('LANG_FLAG_Z');?></a>
  <a href="<?php echo $url . '?alphabeticsearch=' . '@'?>"<?php if(isset($_GET['alphabeticsearch']) && $_GET['alphabeticsearch'] == '@'):?>class="bold"<?php endif;?>><?php echo $this->translate('#');?></a>
</section>