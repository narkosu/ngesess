<?php $here = Yii::app()->controller->action->id;?>
<div class="speedbar">
    <div class="speedbar-content">
		<span class="button-group">
			<?php if ( !$model->isNewRecord ) { ?> 
        <a href="<?php echo Yii::app()->createUrl('masters/'.$this->id.'/preview/id/'.$model->peserta_id)?>" <?php echo ( $here == 'preview') ? 'class = "button icon act_link"':'class = "button icon act_link"' ?>>Preview</a>
        <a href="<?php echo Yii::app()->createUrl('penilaian/laporan/download/id/'.$model->peserta_id)?>" <?php echo ( $here == 'print') ? 'class = "button icon act_link"':'class = "button icon act_link"' ?>>Download Doc</a>
        <a href="<?php echo Yii::app()->createUrl('penilaian/laporan/download/id/'.$model->peserta_id)?>" <?php echo ( $here == 'print') ? 'class = "button icon act_link"':'class = "button icon act_link"' ?>>IDP</a>
        <?php } ?>
		</span>
		<?php /*
		<ul class="menu-speedbar">
			<li><a href="<?php echo Yii::app()->createUrl('masters/peserta/asesor')?>">Daftar Peserta</a></li>
			<li><a href="<?php echo Yii::app()->createUrl('masters/'.$this->id.'/penilaian/id/'.$model->peserta_id)?>" <?php echo ( $here == 'penilaian') ? 'class = "act_link"':'' ?>>Form</a></li>
			<?php if ( !$model->isNewRecord ) { ?> 
			<li><a href="<?php echo Yii::app()->createUrl('masters/'.$this->id.'/preview/id/'.$model->peserta_id)?>" <?php echo ( $here == 'preview') ? 'class = "act_link"':'' ?>>Preview</a></li>
			<li><a href="<?php echo Yii::app()->createUrl('masters/'.$this->id.'/download/id/'.$model->peserta_id)?>" <?php echo ( $here == 'print') ? 'class = "act_link"':'' ?>>Download Doc</a></li>
			<?php } ?>
			
		</ul>
		*/?>
	</div>
</div>