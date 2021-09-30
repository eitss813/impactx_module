<?php foreach ($this->paginator as $item): ?>

    <?php if( !empty($item['type']) && !empty($item['id']) ):?>

        <?php $item = $this->item($item['type'], $item['id']); ?>

        <?php $type=$item->getType(); ?>

        <div class="search_result" onclick="location.href='<?php echo $item->getHref();?>';" style="cursor: pointer;">

            <div class="search_photo">
                <?php if( $type == 'sitepage_initiative' ): ?>
                    <?php $itemDetails = Engine_Api::_()->getItem('sitepage_initiative', $item->getIdentity());?>
                    <?php $defaultLogo = $this->layout()->staticBaseUrl.'application/modules/Sitepage/externals/images/nophoto_page_thumb_profile.png'; ?>
                    <?php $itemDetailsPhotoUrl = !empty($itemDetails['logo']) ? $itemDetails->getLogoUrl('thumb.cover') : $defaultLogo; ?>
                    <a href="<?php echo $this->url(array('action' => 'landing-page','page_id' => $itemDetails->page_id,'initiative_id' => $item->getIdentity()), 'sitepage_initiatives', true) ?>">
                        <img src="<?php echo $itemDetailsPhotoUrl;?>" alt="" class="thumb_cover item_photo_sitepage_initiative">
                    </a>
                <?php else: ?>
                    <?php echo $this->htmlLink($item->getHref(), $this->itemPhoto($item, 'thumb.cover')) ?>
                <?php endif; ?>
            </div>

            <div class="search_info">

                <!-- Title-->
                <?php if( $type == 'sitepage_initiative' ): ?>
                        <?php $itemDetails = Engine_Api::_()->getItem('sitepage_initiative', $item->getIdentity());?>
                        <?php if(!empty($this->query)): ?>
                            <?php echo $this->htmlLink(array(
                                'route' => 'sitepage_initiatives',
                                'controller' => 'initiatives',
                                'action' => 'landing-page',
                                'page_id' => $itemDetails->page_id,
                                'initiative_id' => $item->getIdentity(),
                                ),
                                $this->highlightText($item->getTitle(), $this->query),
                                array('class' => 'search_title')) ?>
                        <?php else: ?>
                            <?php echo $this->htmlLink(array(
                                'route' => 'sitepage_initiatives',
                                'controller' => 'initiatives',
                                'action' => 'landing-page',
                                'page_id' => $itemDetails->page_id,
                                'initiative_id' => $item->getIdentity(),
                                ),
                                $item->getTitle(),
                                array('class' => 'search_title')) ?>
                        <?php endif; ?>
                <?php else: ?>
                    <?php if(!empty($this->query)): ?>
                        <?php echo $this->htmlLink($item->getHref(), $this->highlightText($item->getTitle(), $this->query), array('class' => 'search_title')) ?>
                    <?php else: ?>
                        <?php echo $this->htmlLink($item->getHref(), $item->getTitle(), array('class' => 'search_title')) ?>
                    <?php endif; ?>
                <?php endif; ?>

                <!-- Type-->
                <div class="search_type">
                    <?php if( $type == 'sitepage_page' ): ?>
                        <p id="organization_type">Organisation</p>
                    <?php endif; ?>
                    <?php if( $type == 'sitecrowdfunding_project' ): ?>
                        <p id="project_type">Project</p>
                    <?php endif; ?>
                    <?php if( $type == 'sitepage_initiative' ): ?>
                        <p id="initiative_type">Initiative</p>
                    <?php endif; ?>
                    <?php if( $type == 'user' ): ?>
                     <div>
                         <p id="user_type" style="margin-bottom: 8px;">Member </p>
                         <div class="browsemembers_results_links">
                             <?php
                                  $viewer = Engine_Api::_()->user()->getViewer();
                                  $direction = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('user.friends.direction', 1);
                                 if( !$direction ) {
                                 $row = $item->membership()->getRow($viewer);
                                 }
                                 else $row = $viewer->membership()->getRow($item);
                                 ?>
                                 <?php
                                 if( null === $row ) {
                                   echo   $this->htmlLink(array('route' => 'user_extended', 'controller' => 'friends', 'action' => 'add', 'user_id' => $item->getIdentity()), $this->translate('Add Friend'), array(
                                   'class' => 'buttonlink smoothbox icon_friend_add'
                                    ));
                                 } else if( $row->user_approved == 0 ) {
                                    echo $this->htmlLink(array('route' => 'user_extended', 'controller' => 'friends', 'action' => 'cancel', 'user_id' => $item->getIdentity()), $this->translate('Cancel Request'), array(
                                    'class' => 'buttonlink smoothbox icon_friend_cancel'
                                    ));
                                 }
                                 else if( $row->resource_approved == 0 ) {
                                    echo $this->htmlLink(array('route' => 'user_extended', 'controller' => 'friends', 'action' => 'confirm', 'user_id' => $item->getIdentity()), $this->translate('Accept Request'), array(
                                    'class' => 'buttonlink smoothbox icon_friend_add'
                                    ));
                                 } else if( $row->active) {
                                   echo $this->htmlLink(array('route' => 'user_extended', 'controller' => 'friends', 'action' => 'remove', 'user_id' =>  $item->getIdentity()), $this->translate('Remove Friend'), array(
                                   'class' => 'buttonlink smoothbox icon_friend_remove'
                                   ));
                                 }

                             ?>

                         </div>
                     </div>
                    <?php endif; ?>
                </div>

                <!-- Description-->
                <?php if( $type !== 'user' ): ?>
                    <p class="search_description">
                        <?php echo Engine_Api::_()->seaocore()->seaocoreTruncateText($item->getDescription(), 130);?>
                    </p>
                <?php endif; ?>

            </div>
        </div>

    <?php endif; ?>

<?php endforeach; ?>