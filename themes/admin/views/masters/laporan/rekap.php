<?php
/* @var $this PesertaController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs=array(
	'Pesertas',
);

$this->menu=array(
	array('label'=>'Create Peserta', 'url'=>array('create')),
	array('label'=>'Manage Peserta', 'url'=>array('admin')),
);
$depid = $this->module->current_departement_id;
?>

<div class="header-page">
	
	<div style="float:left;vertical-align: middle;">
		<h2 class="textTitle">REKAPITULASI </h2>
	</div>
	<div style="float:right;">
		<?php $this->renderPartial('_submenulaporan',array('params'=>$params)); ?>
	</div>
	<div style="clear:both;"></div>
	
</div>

<div class="container-page">
  
  <div CLASS="rekapitulasi">
    <div>PENDIDIKAN</div>
    <table cellpadding="0" cellspacing="0" border="0" class="display" id="example_pendidikan" >
      <thead style="font-size:12px;">
        <tr>
          <th width="100">PENDIDIKAN</th>
          <th >COUNT</th>
          <th >% COUNT</th>

        </tr>
      </thead>
      <tbody style="font-size:12px;">

      </tbody>
    </table>
    
  </div>
  
  <div  CLASS="rekapitulasi">
    <div>TANGGAL ASSESSMENT</div>
    <table cellpadding="0" cellspacing="0" border="0" class="display" id="example_tanggal" >
      <thead style="font-size:12px;">
        <tr>
          <th width="100">TANGGAL</th>
          <th >COUNT</th>
          <th >% COUNT</th>

        </tr>
      </thead>
      <tbody style="font-size:12px;">

      </tbody>
    </table>
    
  </div>
  
  <div  CLASS="rekapitulasi" reltype="rekomendasi">
    <div>BERDASARKAN REKOMENDASI</div>
    <table cellpadding="0" cellspacing="0" border="0" class="display" id="example_rekomendasi" >
      <thead style="font-size:12px;">
        <tr>
          <th width="100">REKOMENDASI</th>
          <th >COUNT</th>
          <th >% COUNT</th>

        </tr>
      </thead>
      <tbody style="font-size:12px;" id="body_rekomendasi">

      </tbody>
    </table>
    
  </div>

  <div  CLASS="rekapitulasi">
    <div>BERDASARKAN KINERJA</div>
    <table cellpadding="0" cellspacing="0" border="0" class="display" id="example_kinerja" >
      <thead style="font-size:12px;">
        <tr>
          <th width="100">KINERJA</th>
          <th >COUNT</th>
          <th >% COUNT</th>

        </tr>
      </thead>
      <tbody style="font-size:12px;">

      </tbody>
    </table>
    
  </div>
</div>
<?php 
Yii::app()->clientScript->registerScript('REKAPITULASI', "
  /* ajax */
	$('.rekapitulasi').each(function() {
    if ( $(this).attr('reltype') != undefined ) {
      var typelaporan = $(this).attr('reltype');
      $.ajax(
        { url: '".Yii::app()->createUrl('masters/laporan/LoadRekapitulasi/departement_id/'.$depid)."' ,
          data:'typelaporan='+typelaporan,
          success:function(data){

            $('#body_rekomendasi').html(data);

          }	
          }
        );
      }
  });
  /* khsuus rekomendasi */
  
  
");
?>

