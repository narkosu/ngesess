<div class="speedbar">
    <div class="speedbar-content">
		
		<Div style="float:left;">
			<?php /*<ul class="menu-drop">
				<li><a href="#"><i class="icon-chevron-down"></i></a>
					<ul>
						<li><a href="<?php echo Yii::app()->createUrl('masters/departement')?>">Departement</a></li>
						<li><a href="<?php echo Yii::app()->createUrl('masters/deputi')?>">Deputi</a></li>
						<li><a href="<?php echo Yii::app()->createUrl('masters/unitkerja')?>">Unit Kerja</a></li>
						<li><a href="<?php echo Yii::app()->createUrl('masters/jabatan')?>">Jabatan</a></li>
						<li><a href="<?php echo Yii::app()->createUrl('masters/tingkatjabatan')?>">Tingkat Jabatan</a></li>
						<li><a href="<?php echo Yii::app()->createUrl('masters/jeniskompetensi')?>">Jenis Kompetensi</a></li>
						<li><a href="<?php echo Yii::app()->createUrl('masters/kompetensi')?>">Kompetensi</a></li>
					</ul>  
				</li>
			</ul>*/?>
			<div style="float:left;text-transform:uppercase;padding:5px;"><?php echo $this->module->id?> > Item SKJ: </div>
			
			<span class="button-group">
				<a href="<?php echo Yii::app()->createUrl('skj')?>" class="button icon home">SKJ</a>
				<a href="<?php echo Yii::app()->createUrl($this->module->id.'/itemskj/create/skjid/'.$params['skjid'])?>" class="button icon add">Tambah</a>
				<a href="<?php echo Yii::app()->createUrl($this->module->id.'/itemskj/index/id/'.$params['skjid'])?>" class="button icon log">Daftar</a>
			</span>
			
		</Div>
		<div style="clear:both;"></div>
	</div>
</div>