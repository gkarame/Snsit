<?php
require_once(Yii::app()->basePath  . DIRECTORY_SEPARATOR . 'vendors' .DIRECTORY_SEPARATOR  . 'tcpdf' . DIRECTORY_SEPARATOR .'tcpdf.php');
require_once(Yii::app()->basePath  . DIRECTORY_SEPARATOR . 'vendors' .DIRECTORY_SEPARATOR  . 'fpdi' . DIRECTORY_SEPARATOR . 'fpdi.php' );			
class concat_pdf extends FPDI {
     var $files = array();
     function setFiles($files) {
          $this->files = $files;
     }
     function concat() {
          foreach($this->files AS $file) {
               $pagecount = $this->setSourceFile($file);
               for ($i = 1; $i <= $pagecount; $i++) {
               		$tplidx = $this->ImportPage($i);
                    $s = $this->getTemplatesize($tplidx);
                    $this->AddPage('P', array($s['w'], $s['h']));
                    $this->useTemplate($tplidx);
               }
          }
     }
}