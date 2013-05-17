<?php

class LaporanController extends Controller
{
	public $layout = '//layouts/main';
	
	public function filters()
	{
		return array(
			'accessControl',
		);
	}	

	public function accessRules() {
		return array(
				array('allow',
					'actions'=>array('download','LoadProcessingtrendmatrix'),
					'users' => array('@'),
					),
				
				array('deny',  // deny all other users
						'users'=>array('*'),
						),
				);
	}
	
  public function actionLoadProcessingtrendmatrix($dept_id){
		
    $rekomendasi = 'P';
    $kinerja = 'B';
    
    $criteria=new CDbCriteria;
		$criteria->compare('id_departemen',$dept_id);
    $criteria->together = true;
    $criteria->with = array('penilaian');
    
		
		if ( !empty($_GET['sSearch'])){
			$criteria->compare('nama_peserta',$_GET['sSearch'],true,'AND',TRUE);
		}
    
    /* filter rekomendaSI */
    if ( !empty($_GET['sSearch_20'])){
      $criteria->compare('penilaian.rekomendasi',json_decode($_GET['sSearch_20'])->rekomendasi);
      $criteria->compare('penilaian.data_kinerja',json_decode($_GET['sSearch_20'])->data_kinerja);
    }
   
    
		if ( !empty($_GET['iSortCol_0'])){
			 $criteria->order = $DEFAULTCOL[$_GET['iSortCol_0']].' '.$_GET['sSortDir_0'];
			//$criteria->ORDER('nama_peserta',$_GET['sSearch'],true,'AND',TRUE);
		}
		
		$Count = Peserta::model()->count($criteria);
		
		$criteria->offset = $_GET['iDisplayStart'];
		
		$criteria->limit = $_GET['iDisplayLength'];
		
		
		$items = Peserta::model()
			->findAll($criteria);
			
		/*$items = Pesertaasesor::model()
			->findAll($criteria);
		*/	
		$output = array(
			"sEcho" => intval($_GET['sEcho']),
			"iTotalRecords" => $Count,
			"iTotalDisplayRecords" => $Count,
			"aaData" => array()
		);

		foreach ($items as $i=>$iskj){
			unset($row);
			
			foreach ($iskj as $field => $vale){
				
				$row[$field] = $vale;
				
			}
      
			foreach ($iskj->penilaian->detail as $nilai){
        $row['komp_'.$nilai->kompetensi_id] = $nilai->nilai;
      }
			$row['penilaian'] = $iskj->penilaian['persentase_pemenuhan'];
			
      $rekomendasi = Assessment::model()->rekomendasi($dept_id,$row['penilaian']);
      $row['rekomendasi'] = $rekomendasi['result']['caption'];
      $row['kinerja'] = $iskj->penilaian->data_kinerja;
      
			$row['ids'] = $iskj->id;//for else
			$output['aaData'][] = $row;
		}
		
		//print_r($output);
		echo json_encode($output);
	}
  
	public function actionIndex()
	{
		//echo Yii::app()->request->getQuery('user');
		/*$model=new Masterskj('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Masterskj']))
			$model->attributes=$_GET['Masterskj'];
		*/	
		$this->render('home',array(
			//'model'=>$model,
		));
	}
	
  /*
   * download Action report
   * 
   * input is post only 
   */
  public function actionDownload($id)
	{
		$hasAccess 		= Userasesor::model()->hasAccess();
		$loadPeserta	= Peserta::model()->findByPk($id);
		$loadPenilaian 	= Penilaian::model()->find('peserta_id = :pid',array(':pid'=>$id));
		if ( !isset($loadPenilaian) ){
			$this->redirect(array('/masters/pesertaasesor/penilaian/id/'.$id));
			exit;
		}
		
		$model 			= $loadPenilaian;
		$detailNilai 	= Detailpenilaian::model()->kompetensinilaiArray($model->id);
		$detailUraian 	= Uraiankompetensi::model()->uraianKompetensiArray($model->id);
		$kompetensiForm = $this->renderFormKompetensi($model->skj_id,$model->itemskj_id,
							array('nilai'=>$detailNilai,
								  'uraian'=>$detailUraian,
								  'model'=>$model,
								  'preview'=>true, 'onview'=>'doc')
							);
		$kompetensiForm['ringkasan'] = $this->ringkasanProfil($detailNilai);
		$kompetensiForm['jabatan'] = Jabatan::model()->findByPk($model->itemskj->jabatan_id);
		
		$footer_text 	= strtoupper($loadPeserta->nama_peserta.'/Seleksi/'.$kompetensiForm['jabatan']->jabatan_name.'/'.$loadPeserta->nip);
		$filename 		= str_replace(',','',$loadPeserta->nama_peserta);
		
		if ( $model->persentase_pemenuhan > 90 ) {
			$saran = '<table cellspacing="0" cellpadding="0" style="border:1px solid #000;margin-top:2px;"><tr><td style="width:20px;height:20px;background:#000;color:#000">.</td></tr></table>';
		} else if ( $model->persentase_pemenuhan <= 90  && $model->persentase_pemenuhan > 70  ) {
			$timbang = '<table cellspacing="0" cellpadding="0" style="border:1px solid #000;margin-top:2px;"><tr><td style="width:20px;height:20px;background:#000;color:#000">.</td></tr></table>';
		} else if ( $model->persentase_pemenuhan <= 70 && !empty($model->persentase_pemenuhan) ) {
			$belum = '<table cellspacing="0" cellpadding="0" style="border:1px solid #000;margin-top:2px;"><tr><td style="width:20px;height:20px;background:#000;color:#000">.</td></tr></table>';
		}
		
		$empty = '<table cellspacing="0" cellpadding="0" style="border:1px solid #aaa;margin-top:2px;"><tr><td style="width:20px;height:20px;background:#fff;color:#fff">.</td></tr></table>';
		Yii::import('application.extensions.phpdocx.Phpdocx',array('options'=>array('filetemp'=>'satu')));
		
		$phpdocx = new Phpdocx(array('filetemp'=>$this->getCurrentViewPath()));
		$docx = new CreateDocx();
		
		
		/* load template */
		
		$docx->setTemporaryDirectory($this->getCurrentViewPath());
		$docx->addTemplate($this->getCurrentViewPath().'/templatekpk.docx');
		
		$docx->addTemplateVariable('nomortest', $loadPeserta->nip);
		$docx->addTemplateVariable('nip', '');
		$docx->addTemplateVariable('timbang', ($timbang ? $timbang : $empty),'html');
		$docx->addTemplateVariable('saran', ($saran ? $saran : $empty),'html');
		$docx->addTemplateVariable('belum', ($belum ? $belum : $empty),'html');
		$docx->addTemplateVariable('alamat', '');
		$docx->addTemplateVariable('pendidikan', '');
		$docx->addTemplateVariable('ttl', '');
		$docx->addTemplateVariable('jabatan', '');
		$docx->addTemplateVariable('tujuan', 'Seleksi');
		$docx->addTemplateVariable('tta', 'Jakarta,');
    $docx->addTemplateVariable('tgl_cetak', date('d F Y'));
    $docx->addTemplateVariable('nama_asesor', $model->asesor->nama_asesor );
		$docx->addTemplateVariable('footer', $footer_text);
		$docx->addTemplateVariable('namapeserta', $loadPeserta->nama_peserta);
		$docx->addTemplateVariable('hasilKompetensi', $kompetensiForm['loadkompetensi'],'html');
		$docx->addTemplateVariable('uraian_profile_kompetensi', $kompetensiForm['uraianKompetensi'],'html');
		$docx->addTemplateVariable('ringkasan_profile_kompetensi', $kompetensiForm['ringkasanKompetensi'],'html');
		$docx->addTemplateVariable('saran_pengembangan', $kompetensiForm['saranpengembangan'],'html');
		$docx->addTemplateVariable('kesimpulan',  nl2br($model->kesimpulan_umum),'html');
		$docx->addTemplateVariable('untuk_posisi', $kompetensiForm['jabatan']->jabatan_name);
		
		//$docx->addFooter($footer_text, $paramsFooter);
		/* halaman depan */
		$onlyfilename  = $filename.'.docx';
		$filename = $this->getCurrentViewPath().'/'.$filename;
		
		$filename_path = $docx->createDocx($filename);
		
		header('Content-Description: File Transfer');
		header('Content-Type: application/octet-stream');
		header('Content-Disposition: attachment; filename='.$onlyfilename);
		header('Content-Transfer-Encoding: binary');
		header('Expires: 0');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Pragma: public');
		header('Content-Length: ' . filesize($filename_path));
		ob_clean();
		flush();
		readfile($filename_path);
		unlink($filename_path);
		exit;
	}
	
  private function renderFormKompetensi($skjid,$itemskjid,$params = array()){
		$modelkompetensisjk = Kompetensiskj::model()->getItemKompetensiById($skjid,$itemskjid);
		$params['itemkompetensi'] = $modelkompetensisjk;
		switch($params['onview']){
		case 'doc':
			$output['loadkompetensi'] = $this->renderPartial('//masters/pesertaasesor/_cetak_penilaian_kompetensi',$params,true,true);
			
			$output['uraianKompetensi'] = $this->renderPartial('//masters/pesertaasesor/_cetak_penilaian_uraian_kompetensi',$params,true,true);
			$params['ringkasan'] = $this->ringkasanProfil($params['nilai']);
			$output['ringkasanKompetensi'] = $this->renderPartial('//masters/pesertaasesor/_cetak_ringkasanpenilaian_kompetensi',$params,true,true);
			$output['saranpengembangan'] = $this->renderPartial('//masters/pesertaasesor/_cetak_saran_pengembangan',$params,true,true);
			break;
		default:
			
			$output['loadkompetensi'] = $this->renderPartial('masters/pesertaasesor/_form_penilaian_kompetensi',$params,true,true);
			
			$output['uraianKompetensi'] = $this->renderPartial('masters/pesertaasesor/_form_penilaian_uraian_kompetensi',$params,true,true);
			$params['ringkasan'] = $this->ringkasanProfil($params['nilai']);
			$output['ringkasanKompetensi'] = $this->renderPartial('masters/pesertaasesor/_ringkasan_penilaian_kompetensi',$params,true,true);
			$output['saranpengembangan'] = $this->renderPartial('masters/pesertaasesor/_penilaian_saran_pengembangan',$params,true,true);
			break;
		}
		return $output;
	}
  
  
  private function ringkasanProfil($detailNilai){
		$kuat = 0;
		$lemah = 0;
		foreach ((array) $detailNilai as $jk=>$gkomp){
			foreach ((array) $gkomp as $kompid=>$values){
				if ( $values['nilai_akhir'] < 0 ){
					$output['weak'] .= '<li>'.$values['title'].'</li>';
					$output['weakArray'][$jk][$kompid] =  $values['title'];
					$lemah++;
				}else{
					$output['strong'] .= '<li>'.$values['title'].'</li>';
					$output['strongArray'][$jk][$kompid] =  $values['title'];
					$kuat++;
				}
			}
		}
		
		/*if ( $kuat > $lemah){
			$output['recomend'] = 'disarankan';
		}else if ( $kuat < $lemah ) {
			$output['recomend'] = 'belumdisarankan';
		}else{
			$output['recomend'] = 'dipertimbangkan';
		}
		*/
		return $output;
	}
  
  /*
   * Ajax Action for calculate assessment
   * 
   * input is post only 
   */
  
  public function actionRecalculate()
	{
    $_POST['pid'] = 10;
    $PenilaianDetail  = Detailpenilaian::model()
                          ->findAll('penilaian_id = :pid'
                            ,array(':pid'=>$_POST['pid'])
                            );
    foreach ((array) $PenilaianDetail as $key=>$data){
      $asses = Assessment::model();
      //$inputNilai,$standard,$jenisNilai,$departement,$jumlahkompetensi,
      $jumlahkompetensi =  $data->penilaian->jumlahKompetensiAvailable($data->jeniskompetensi_id);
      
      $asses->inputNilai = $data->nilai;
      $asses->standard = $data->nilai_default;
      $asses->departement = $data->penilaian->departement_id;
      $asses->jumlahkompetensi = $jumlahkompetensi;
      
      $result += $asses->calculate();
    }
    
    echo $result;
	}
  
	public function actionLoadProcessing(){
		$criteria=new CDbCriteria;
		if ($this->module->current_departement_id)
			$criteria->compare('departement_id',$this->module->current_departement_id);
		
		$Count = Masterskj::model()->count($criteria);
		
		//$criteria->with = array('dept');
		$criteria->offset = $_GET['iDisplayStart'];
		
		$criteria->limit = $_GET['iDisplayLength'];
		
		
		$items = Masterskj::model()
			->findAll($criteria);
		$output = array(
			"sEcho" => intval($_GET['sEcho']),
			"iTotalRecords" => $Count,
			"iTotalDisplayRecords" => $Count,
			"aaData" => array()
		);

		foreach ($items as $i=>$iskj){
			unset($row);
			
			foreach ($iskj as $field => $vale){
				/*if ( $field == 'id_departemen') {
					$row[] = $iskj->dept->name;
				}else 
				if ( $field == 'deputi_id') {
					$row[] = $iskj->deput->deputi_name;
					
				} else
				if ( $field == 'unitkerja_id') {
					$row[] = $iskj->uk->unitkerja_name;
					
				} else if ( $field == 'jabatan_id') {
					$row[] = $iskj->jab->jabatan_name;
					
				} else if ( $field == 'tingkatjabatan_id') {
					$row[] = $iskj->t_jab->tingkat_jabatan;
					
				} else if ( $field == 'rumpunjabatan_id') {
					$row[] = $iskj->r_jab->rumpun_jabatan;
					
				} else {*/
					$row[] = $vale;
				//}
			}
			$row[] = $iskj->id;//for else
			$output['aaData'][] = $row;
		}
		
		//print_r($_GET);
		echo json_encode($output);
	}
	
	public function getCurrentViewPath() {
			$controllername = $this->getId();
			$modulename = $this->module->getName();
			$newPath = "application.modules.{$modulename}.views.{$controllername}";
			$newPath = YiiBase::getPathOfAlias($newPath);
			return $newPath;
	}
}