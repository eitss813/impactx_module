       	
						<?php echo $this->itemPhoto($sitepage, 'thumb.icon') ?>
            <p class="ui-li-aside">
							<span>
                <?php if(false):?>
                  <?php if( $sitepage->closed ): ?>
                    <?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/images/close.png', '', array('class' => 'icon', 'title' => $this->translate('Closed'))) ?>
                  <?php endif;?>               
                  <?php if ($sitepage->sponsored == 1): ?>
                    <?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/images/sponsored.png', '', array('class' => 'icon', 'title' => $this->translate('Sponsored'))) ?>
                  <?php endif; ?>
                  <?php if ($sitepage->featured == 1): ?>
                    <?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/images/sitepage_goldmedal1.gif', '', array('class' => 'icon', 'title' => $this->translate('Featured'))) ?>
                  <?php endif; ?>
                <?php endif; ?>
							</span>
              </p>
            
							<h3><?php  echo $sitepage->getTitle(); ?></h3>				
              <p>
                <?php echo $this->timestamp(strtotime($sitepage->creation_date)) ?> - <?php echo $this->translate('posted by'); ?>
                <?php if ($postedBy): ?>
                <b><?php echo $sitepage->getOwner()->getTitle() ?></b>
              <?php endif; ?>
              </p>
              
              <?php if(false):?>
              <p>
							<?php echo $this->translate(array('%s like', '%s likes', $sitepage->like_count), $this->locale()->toNumber($sitepage->like_count)) ?>
                 <?php $sitepagereviewEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagereview'); ?>               
						    <?php if (!empty($sitepagereviewEnabled)): ?>
                -
								<?php echo $this->translate(array('%s review', '%s reviews', $sitepage->review_count), $this->locale()->toNumber($sitepage->review_count)) ?>                
							<?php endif; ?>
                -
							<?php echo $this->translate(array('%s comment', '%s comments', $sitepage->comment_count), $this->locale()->toNumber($sitepage->comment_count)) ?>
                -
							<?php echo $this->translate(array('%s view', '%s views', $sitepage->view_count), $this->locale()->toNumber($sitepage->view_count)) ?>
							</p>	
              <?php endif; ?>

			