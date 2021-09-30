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
  $title = $this->logositetext;
   $logo  = Engine_Api::_()->sesnewsletter()->getFileUrl($this->helogo);
?>
<table class="row" border="0" align="center" cellpadding="0" cellspacing="0" style="width:100%;max-width:100%;">
  <tbody>
    <tr>
      <td align="center" valign="top"><table class="row" border="0" align="center" cellpadding="0" cellspacing="0" style="width:100%;max-width:100%;background-color: <?php echo $this->tophebgcolor?>;">
          <tbody>
            <tr>
              <td height="15" align="center" valign="top" style="font-size:10px;line-height:15px;">&nbsp;</td>
            </tr>
            <tr>
              <td align="justify" valign="top" style="font-size:0;padding:0 10px"><div class="row" style="display:inline-block;max-width:60%;vertical-align:middle;width:100%">
                  <table class="row" border="0" align="left" cellpadding="0" cellspacing="0">
                    <tbody>
                      <tr>
                        <td align="center" valign="top"><?php if($this->phonenumber) { ?>
                          <table class="centerFloat" border="0" align="left" cellpadding="0" cellspacing="0">
                            <tbody>
                              <tr>
                                <td width="35" align="center" valign="middle" style="font-family:'Arial',sans-serif; font-size:13px; font-weight:400; line-height:15px;"><a href="#"> <img src="<?php echo $this->absoluteUrl($this->baseUrl().'/application/modules/Sesnewsletter/externals/images/light-phone.png'); ?>" width="25" alt="" style="border:0;width:25px;"> </a></td>
                                <td data-size="Pre Header Text" mc:hideable="" mc:edit="" class="webViewLight" align="center" valign="middle" style="font-family:'Arial',sans-serif; font-size:13px; font-weight:400; color: <?php echo $this->topheaderfontcolor?>; line-height:24px;"><a data-color="Pre Header Text" href="#" style="text-decoration:none; color: <?php echo $this->topheaderfontcolor?>;"> <?php echo $this->phonenumber; ?></a></td>
                              </tr>
                            </tbody>
                          </table>
                          <?php } ?>
                          <?php if($this->email) { ?>
                          <table border="0" align="left" cellpadding="0" cellspacing="0">
                            <tbody>
                              <tr>
                                <td width="35" align="center" valign="middle" style="font-family:'Arial',sans-serif; font-size:13px; font-weight:400; color:#FFFFFF; line-height:15px;"><a href="#"> <img src="<?php echo $this->absoluteUrl($this->baseUrl().'/application/modules/Sesnewsletter/externals/images/light-email.png'); ?>" width="25" alt="" style="border:0;width:25px;"> </a></td>
                                <td data-size="Pre Header Text" mc:hideable="" mc:edit="" class="webViewLight" align="center" valign="middle" style="font-family:'Arial',sans-serif; font-size:13px; font-weight:400; line-height:24px;"><a data-color="Pre Header Text" href="mailto:<?php echo $this->email ?>" style="text-decoration:none; color: <?php echo $this->topheaderfontcolor?>;"><?php echo $this->email; ?></a></td>
                              </tr>
                            </tbody>
                          </table>
                          <?php } ?></td>
                      </tr>
                    </tbody>
                  </table>
                </div>
                <div class="row" style="display:inline-block;max-width:40%;vertical-align:middle;width:100%">
                  <table class="centerFloat" border="0" align="right" cellpadding="0" cellspacing="0">
                    <tbody>
                      <tr>
                        <?php if($this->twitter) { ?>
                        <?php $twitter = (preg_match("#https?://#", $this->twitter) === 0) ? 'http://'.$this->twitter : $this->twitter; ?>
                        <td width="35" align="center" valign="top"><a href="<?php echo $twitter; ?>" target="_blank"> <img src="<?php echo $this->absoluteUrl($this->baseUrl().'/application/modules/Sesnewsletter/externals/images/social-twitter-light.png'); ?>" width="25" alt="" style="border:0;width:25px;"> </a></td>
                        <?php } ?>
                        <?php if($this->facebook) { ?>
                        <?php $facebook = (preg_match("#https?://#", $this->facebook) === 0) ? 'http://'.$this->facebook : $this->facebook; ?>
                        <td width="35" align="center" valign="top"><a href="<?php echo $facebook; ?>" target="_blank"> <img src="<?php echo $this->absoluteUrl($this->baseUrl().'/application/modules/Sesnewsletter/externals/images/social-facebook-light.png'); ?>" width="25" alt="" style="border:0;width:25px;"> </a></td>
                        <?php } ?>
                        <?php if($this->linkedin) { ?>
                        <?php $linkedin = (preg_match("#https?://#", $this->linkedin) === 0) ? 'http://'.$this->linkedin : $this->linkedin; ?>
                        <td width="35" align="center" valign="top"><a href="<?php echo $linkedin; ?>" target="_blank"> <img src="<?php echo $this->absoluteUrl($this->baseUrl().'/application/modules/Sesnewsletter/externals/images/social-linkedin-light.png'); ?>" width="25" alt="" style="border:0;width:25px;"> </a></td>
                        <?php } ?>
                        <?php if($this->pinterest) { ?>
                        <?php $pinterest = (preg_match("#https?://#", $this->pinterest) === 0) ? 'http://'.$this->pinterest : $this->pinterest; ?>
                        <td width="35" align="center" valign="top"><a href="<?php echo $pinterest; ?>" target="_blank"> <img src="<?php echo $this->absoluteUrl($this->baseUrl().'/application/modules/Sesnewsletter/externals/images/social-pinterest-light.png'); ?>" width="25" alt="" style="border:0;width:25px;"> </a></td>
                        <?php } ?>
                      </tr>
                    </tbody>
                  </table>
                </div></td>
            </tr>
            <tr>
              <td height="15" align="center" valign="top" style="font-size:10px;line-height:15px;">&nbsp;</td>
            </tr>
          </tbody>
        </table></td>
    </tr>
    <tr>
      <td align="center" valign="top"><table class="row" border="0" align="center" cellpadding="0" cellspacing="0" style="width:100%;max-width:100%;">
          <tbody>
            <tr>
              <td align="center" valign="top" style="background-color: <?php echo $this->hebgcolor?>;padding:0 20px;"><table class="row" border="0" align="center" cellpadding="0" cellspacing="0" style="width:100%;max-width:100%;">
                  <tbody>
                    <tr>
                      <td height="20" align="center" valign="top" style="font-size:30px;line-height:20px;">&nbsp;</td>
                    </tr>
                    <tr>
                      <td align="left" valign="middle"><div class="row" style="display:inline-block;max-width:190px;vertical-align:middle;width:100%;max-height:50px;">
                          <table class="row" border="0" align="left" cellpadding="0" cellspacing="0">
                            <tbody>
                              <tr>
                                <td align="left" valign="top" style="font-family:'Arial',sans-serif;"><?php if($this->enablelogo && $logo) { ?>
                                  <a href="<?php echo $this->absoluteUrl($this->baseUrl()); ?>" style="text-decoration:none;"><img src="<?php echo $logo; ?>" alt="<?php echo $title; ?>" style="max-width:150px;height:auto;"></a>
                                  <?php } else { ?>
                                  <a href="" style="text-decoration:none;color:<?php echo $this->headerfontcolor?>;font-size:18px;"><?php echo $this->translate($title); ?></a>
                                  <?php } ?></td>
                              </tr>
                            </tbody>
                          </table>
                        </div></td>
                      <td align="right" valign="middle"><div class="row" style="display:inline-block;vertical-align:middle;width:100%;">
                          <table class="row" border="0" align="right" cellpadding="0" cellspacing="0">
                            <tbody>
                              <tr>
                                <td align="right" valign="top"><?php if($this->headermenu) { ?>
                                  <ul class="navigation" style="list-style-type:none;margin:0;">
                                    <?php foreach( $this->navigation as $link ): ?>
                                    <li class="<?php echo $link->get('active') ? 'active' : '' ?>" style="display:inline-block;"> <a href='<?php echo $this->absoluteUrl($this->baseUrl(). $link->getHref()) ?>' style="text-decoration:none;font-family:'Arial',sans-serif;padding-left:10px;color:<?php echo $this->headerfontcolor?>;" class="<?php echo $link->getClass() ? ' ' . $link->getClass() : ''  ?>"



                              <?php if( $link->get('target') ): ?> target='<?php echo $link->get('target') ?>' <?php endif; ?>> <span><?php echo $this->translate($link->getlabel()) ?></span> </a> </li>
                                    <?php endforeach; ?>
                                  </ul>
                                  <?php } ?></td>
                              </tr>
                            </tbody>
                          </table>
                        </div></td>
                    </tr>
                    <tr>
                      <td height="20" align="center" valign="top" style="font-size:30px;line-height:20px;">&nbsp;</td>
                    </tr>
                  </tbody>
                </table></td>
            </tr>
          </tbody>
        </table></td>
    </tr>
  </tbody>
</table>
