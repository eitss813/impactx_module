<?php
/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesnewsletter
 * @package    Sesnewsletter
 * @copyright  Copyright 2018-2019 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: index.tpl  2018-12-03 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
 ?>
<?php
$title = $this->fotrlogositetext;
$logo  = Engine_Api::_()->sesnewsletter()->getFileUrl($this->fotrlogo);
$unsubscribelink = Zend_Controller_Front::getInstance()->getRouter()->assemble(array('module' => 'sesnewsletter', 'controller' => 'index', 'action' => 'unsubscribe', 'token' => base64_encode(time() . ":" . $email->email)), 'sesnewsletter_unsubscribe', true);
?>
<table class="row" border="0" align="center" cellpadding="0" cellspacing="0" style="width:100%;max-width:100%;background-color: <?php echo $this->fotrbgcolor?>;">
<tbody>
  <tr>
    <td align="center" valign="top"><table align="center" border="0" cellpadding="0" cellspacing="0" width="100%" style="border-collapse:collapse;max-width:100%;width:100%;">
        <tbody>
          <tr>
            <td valign="top"><table border="0" cellpadding="0" cellspacing="0" width="100%" style="min-width:100%;border-collapse:collapse">
                <tbody>
                  <tr>
                    <td align="center" valign="top" style="padding:15px 10px"><table border="0" cellpadding="0" cellspacing="0" width="100%" style="min-width:100%;border-collapse:collapse">
                        <tbody>
                          <tr>
                            <td align="center" style="padding-left:9px;padding-right:9px"><table border="0" cellpadding="0" cellspacing="0" width="100%" style="min-width:100%;border-collapse:collapse">
                                <tbody>
                                  <tr>
                                    <td align="center" valign="top"><table align="center" border="0" cellpadding="0" cellspacing="0" style="border-collapse:collapse">
                                        <tbody>
                                          <tr>
                                            <td align="center" valign="top"><?php if($this->facebook) { ?>
                                              <?php $facebook = (preg_match("#https?://#", $this->facebook) === 0) ? 'http://'.$this->facebook : $this->facebook; ?>
                                              <table align="left" border="0" cellpadding="0" cellspacing="0" style="display:inline;border-collapse:collapse">
                                                <tbody>
                                                  <tr>
                                                    <td valign="top"><table border="0" cellpadding="0" cellspacing="0" width="100%" style="border-collapse:collapse">
                                                        <tbody>
                                                          <tr>
                                                            <td align="left" valign="middle" style="padding:5px;"><table align="left" border="0" cellpadding="0" cellspacing="0" width="" style="border-collapse:collapse">
                                                                <tbody>
                                                                  <tr>
                                                                    <td align="center" valign="middle" width="24"><a href="<?php echo $facebook; ?>" target="_blank"><img src="<?php echo $this->absoluteUrl($this->baseUrl().'/application/modules/Sesnewsletter/externals/images/footer-social-1.png'); ?>" style="display:block;border:0;height:auto;outline:none;text-decoration:none" height="24" width="24" class="CToWUd"></a></td>
                                                                  </tr>
                                                                </tbody>
                                                              </table></td>
                                                          </tr>
                                                        </tbody>
                                                      </table></td>
                                                  </tr>
                                                </tbody>
                                              </table>
                                              <?php } ?>
                                              <?php if($this->twitter) { ?>
                                              <?php $twitter = (preg_match("#https?://#", $this->twitter) === 0) ? 'http://'.$this->twitter : $this->twitter; ?>
                                              <table align="left" border="0" cellpadding="0" cellspacing="0" style="display:inline;border-collapse:collapse">
                                                <tbody>
                                                  <tr>
                                                    <td valign="top"><table border="0" cellpadding="0" cellspacing="0" width="100%" style="border-collapse:collapse">
                                                        <tbody>
                                                          <tr>
                                                            <td align="left" valign="middle" style="padding:5px;"><table align="left" border="0" cellpadding="0" cellspacing="0" width="" style="border-collapse:collapse">
                                                                <tbody>
                                                                  <tr>
                                                                    <td align="center" valign="middle" width="24"><a href="<?php echo $twitter; ?>" target="_blank"><img src="<?php echo $this->absoluteUrl($this->baseUrl().'/application/modules/Sesnewsletter/externals/images/footer-social-2.png'); ?>" style="display:block;border:0;height:auto;outline:none;text-decoration:none" height="24" width="24" class="CToWUd"></a></td>
                                                                  </tr>
                                                                </tbody>
                                                              </table></td>
                                                          </tr>
                                                        </tbody>
                                                      </table></td>
                                                  </tr>
                                                </tbody>
                                              </table>
                                              <?php } ?>
                                              <?php if($this->youtube) { ?>
                                              <?php $youtube = (preg_match("#https?://#", $this->youtube) === 0) ? 'http://'.$this->youtube : $this->youtube; ?>
                                              <table align="left" border="0" cellpadding="0" cellspacing="0" style="display:inline;border-collapse:collapse">
                                                <tbody>
                                                  <tr>
                                                    <td valign="top"><table border="0" cellpadding="0" cellspacing="0" width="100%" style="border-collapse:collapse">
                                                        <tbody>
                                                          <tr>
                                                            <td align="left" valign="middle" style="padding:5px;"><table align="left" border="0" cellpadding="0" cellspacing="0" style="border-collapse:collapse">
                                                                <tbody>
                                                                  <tr>
                                                                    <td align="center" valign="middle" width="24"><a href="<?php echo $youtube; ?>" target="_blank"><img src="<?php echo $this->absoluteUrl($this->baseUrl().'/application/modules/Sesnewsletter/externals/images/footer-social-3.png'); ?>" style="display:block;border:0;height:auto;outline:none;text-decoration:none" height="24" width="24" class="CToWUd"></a></td>
                                                                  </tr>
                                                                </tbody>
                                                              </table></td>
                                                          </tr>
                                                        </tbody>
                                                      </table></td>
                                                  </tr>
                                                </tbody>
                                              </table>
                                              <?php } ?>
                                              <?php if($this->websiteurl) { ?>
                                              <?php $websiteurl = (preg_match("#https?://#", $this->websiteurl) === 0) ? 'http://'.$this->websiteurl : $this->websiteurl; ?>
                                              <table align="left" border="0" cellpadding="0" cellspacing="0" style="display:inline;border-collapse:collapse">
                                                <tbody>
                                                  <tr>
                                                    <td valign="top"><table border="0" cellpadding="0" cellspacing="0" width="100%" style="border-collapse:collapse">
                                                        <tbody>
                                                          <tr>
                                                            <td align="left" valign="middle" style="padding:5px;"><table align="left" border="0" cellpadding="0" cellspacing="0" style="border-collapse:collapse">
                                                                <tbody>
                                                                  <tr>
                                                                    <td align="center" valign="middle" width="24"><a href="<?php echo $websiteurl; ?>" target="_blank"><img src="<?php echo $this->absoluteUrl($this->baseUrl().'/application/modules/Sesnewsletter/externals/images/footer-social-4.png'); ?>" style="display:block;border:0;height:auto;outline:none;text-decoration:none" height="24" width="24" class="CToWUd"></a></td>
                                                                  </tr>
                                                                </tbody>
                                                              </table></td>
                                                          </tr>
                                                        </tbody>
                                                      </table></td>
                                                  </tr>
                                                </tbody>
                                              </table>
                                              <?php } ?></td>
                                          </tr>
                                        </tbody>
                                      </table></td>
                                  </tr>
                                </tbody>
                              </table></td>
                          </tr>
                        </tbody>
                      </table></td>
                  </tr>
                </tbody>
              </table></td>
          </tr>
        </tbody>
      </table>
      <table border="0" cellpadding="0" cellspacing="0" width="100%" style="min-width:100%;border-collapse:collapse;table-layout:fixed!important">
        <tbody>
          <tr>
            <td style="min-width:100%;padding:0 18px 10px"><table border="0" cellpadding="0" cellspacing="0" width="100%" style="min-width:100%;border-top:2px solid <?php echo $this->footerfontcolor?>;border-collapse:collapse">
                <tbody>
                  <tr>
                    <td><span></span></td>
                  </tr>
                </tbody>
              </table></td>
          </tr>
        </tbody>
      </table>
      <table border="0" cellpadding="0" cellspacing="0" width="100%" style="min-width:100%;border-collapse:collapse">
        <tbody>
          <tr>
            <td valign="middle"><?php if($this->footermenu) { ?>
              <ul class="navigation" style="list-style-type:none;margin:0;text-align:center;padding:10px;">
                <?php foreach( $this->navigation as $link ): ?>
                <li class="<?php echo $link->get('active') ? 'active' : '' ?>" style="display:inline-block;margin:0;"> <a href='<?php echo $this->absoluteUrl($this->baseUrl().$link->getHref()) ?>' style="text-decoration:none;font-family:'Arial',sans-serif;font-size:13px;color:<?php echo $this->footerfontcolor?>;" class="<?php echo $link->getClass() ? ' ' . $link->getClass() : ''  ?>"







        <?php if( $link->get('target') ): ?> target='<?php echo $link->get('target') ?>' <?php endif; ?> ><span style="padding:0 5px;"><?php echo $this->translate($link->getlabel()) ?></span></a> </li>
                <?php endforeach; ?>
              </ul>
              <?php } ?></td>
          </tr>
          <tr>
            <td align="center" valign="top"><table class="row" border="0" align="center" cellpadding="0" cellspacing="0" style="width:100%;max-width:100%;">
                <tbody>
                  <tr>
                    <td height="15" align="center" valign="top" style="font-size:30px;line-height:15px;">&nbsp;</td>
                  </tr>
                  <tr>
                    <td align="center" valign="middle" style="font-size:0;padding:0"><div class="row" style="display:inline-block;max-width:190px;vertical-align:middle;width:100%;max-height:50px;">
                        <table class="row" border="0" align="center" cellpadding="0" cellspacing="0">
                          <tbody>
                            <tr>
                              <td align="center" valign="top" style="font-family:'Arial',sans-serif;"><?php if($this->fotrenablelogo && $logo) { ?>
                                <a href="<?php echo $this->absoluteUrl($this->baseUrl()); ?>" style="text-decoration:none;"><img src="<?php echo $logo; ?>" alt="<?php echo $title; ?>" style="max-width:100px;height:auto;"></a>
                                <?php } else { ?>
                                <a href="" style="text-decoration:none;color:<?php echo $this->footerfontcolor?>;font-size:18px;"><?php echo $this->translate($title); ?></a>
                                <?php } ?></td>
                            </tr>
                            <tr>
                              <td height="15" align="center" valign="top" style="font-size:30px;line-height:15px;">&nbsp;</td>
                            </tr>
                          </tbody>
                        </table>
                      </div></td>
                  </tr>
                </tbody>
              </table></td>
          </tr>
        </tbody>
      </table></td>
  </tr>
