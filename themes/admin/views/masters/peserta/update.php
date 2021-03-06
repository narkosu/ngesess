<?php
/* @var $this PesertaController */
/* @var $model Peserta */

$this->breadcrumbs=array(
	'Pesertas'=>array('index'),
	$model->id=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'List Peserta', 'url'=>array('index')),
	array('label'=>'Create Peserta', 'url'=>array('create')),
	array('label'=>'View Peserta', 'url'=>array('view', 'id'=>$model->id)),
	array('label'=>'Manage Peserta', 'url'=>array('admin')),
);
?>
<div class="header-page">
	
	<div style="float:left;vertical-align: middle;">
		<h2 class="textTitle">Update Peserta <?php echo $model->nama_peserta; ?></h2>
	</div>
	<div style="float:right;">
		<?php $this->renderPartial('_submenu',array('params'=>$params)); ?>
	</div>
	<div style="clear:both;"></div>
	
</div>

<div class="container-page">

<?php echo $this->renderPartial('_form', array('model'=>$model,'user'=>$user,'userpeserta'=>$userpeserta)); ?>
<div style="clear: both"></div>
</div>