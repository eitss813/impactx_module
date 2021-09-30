<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sesblog/externals/scripts/core.js'); ?> 

<?php $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sesblogpackage/externals/styles/styles.css'); ?>
<div class="sesbasic_clearfix sesblog_packages_transactions_header">
	<h3 class="floatL"><?php echo $this->translate("View Transactions of Blog Packages"); ?></h3>
	<a href="<?php echo $this->url(array('action' => 'package'),'sesblog_general',true);?>" class="sesbasic_icon_back sesbasic_link_btn floatR"><?php echo $this->translate("Back to Package Page");?></a>
</div>
<div class="sesblog_packages_transactions_table">
  <table>
    <thead>
      <tr>
        <th><a href="javascript:void(0);"><?php echo $this->translate('ID');?></a> </th>
        <th> <a href="javascript:void(0);"><?php echo $this->translate('Blog ID');?></a></th>
        <th> <a href="javascript:void(0);"><?php echo $this->translate('Blog Title');?></a> </th>
        <th> <a href="javascript:void(0);"><?php echo $this->translate('Package');?></a> </th>
        <th class="centerT"> <a href="javascript:void(0);"><?php echo $this->translate('Gateway');?></a> </th>
        <th class="centerT"> <a href="javascript:void(0);"><?php echo $this->translate('Status');?></a> </th>
        <th> <a href="javascript:void(0);"><?php echo $this->translate('Amount');?></a> </th>
        <th> <a href="javascript:void(0);"><?php echo $this->translate('Date');?></a> </th>
      </tr>
    </thead>
    <tbody>
      <?php foreach($this->paginator as $item):?>
        <?php $user = Engine_Api::_()->getItem('user',$item->owner_id);?>
        <?php $blog = Engine_Api::_()->getItem('sesblog_blog',$item->blog_id);?>
        <?php $package = Engine_Api::_()->getItem('sesblogpackage_package',$item->package_id);?>
        <tr>
          <td><?php echo $item->transaction_id ?></td>
          <td><?php echo $item->blog_id ?></td>
          <td><a href="<?php echo $blog->getHref(); ?>"  target='_blank' title="<?php echo  ucfirst($blog->getTitle()) ?>">
            <?php echo $this->translate(Engine_Api::_()->sesbasic()->textTruncation($blog->getTitle(),25)) ?>
          </a>
          </td> 
          <td><?php echo $this->translate(Engine_Api::_()->sesbasic()->textTruncation($package->title,25)) ?></td>
          <td class="centerT"><?php echo $item->gateway_type; ?></td>
          <td class="centerT"><?php echo $this->translate(ucfirst($item->state)) ?></td>
          <td><?php echo $package->getPackageDescription(); ?></td>
          <td><?php echo $this->locale()->toDateTime($item->creation_date) ?></td>
        </tr>
      <?php endforeach;?>
    </tbody>
  </table>
</div>
