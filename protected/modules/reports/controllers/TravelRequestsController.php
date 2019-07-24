<?php


class TravelRequestsController extends Controller
{
    public function accessRules()
    {
        return array(
            array('allow', // allow authenticated user to perform 'create' and 'update' actions
                'actions'=>array(
                    'index',
                ),
                'users'=>array('@'),
            ),
            array('deny',  // deny all users
                'users'=>array('*'),
            ),
        );
    }

    public function actionIndex(){
        $searchArray = isset($_POST['TravelReport']) ? $_POST['TravelReport'] : Utils::getSearchSession();
        $this->action_menu = array_map("unserialize", array_unique(array_map("serialize", array_merge(
            $this->action_menu,
            array(
                '/reports/travelRequests/index' => array(
                    'label'=>Yii::t('translations', 'Travel Requests Report'),
                    'url' => array('/reports/travelRequests/index'),
                    'itemOptions'=>array('class'=>'link'),
                    'subtab' => -1,
                    'order' => Utils::getMenuOrder()+1,
                    'search' => $searchArray,
                )
            )
        ))));
        Yii::app()->session['menu'] = $this->action_menu;

        $model = new TravelReport('search');
        $message = "";
        if (isset($_POST['TravelReport'])){
            $filters = "";
            if (isset($_POST['TravelReport']['id_user']) && !empty($_POST['TravelReport']['id_user'])){
                $name = explode(' ', $_POST['TravelReport']['id_user'], 2);
                $filters .= " AND (users.firstname like '%".trim($name[0])."%' AND users.lastname like '%".trim($name[1])."%')";
            }
            if (isset($_POST['TravelReport']['year']) && !empty($_POST['TravelReport']['year'])){
                $search_year = (int)$_POST['TravelReport']['year'];
                $filters .= " AND YEAR(booking.adddate)={$search_year}";
            }
            if (isset($_POST['TravelReport']['branch']) && !empty($_POST['TravelReport']['branch'])){
                $filters .= " AND user_personal_details.branch={$_POST['TravelReport']['branch']}";
            }
            if (!empty($filters)){
                $travels = Booking::model()->findAllBySql(
                    str_ireplace('WHERE AND','WHERE',
                        "SELECT booking.* FROM booking 
                        JOIN users ON users.id=booking.traveler 
                        JOIN user_personal_details ON user_personal_details.id_user=users.id
                        WHERE{$filters}")
                );
            }else{
                $travels = Booking::model()->findAll();
            }

        }else{
            $travels = Booking::model()->findAll();
        }

        if(isset($_POST['TravelReport']['format']) && !empty($_POST['TravelReport']['format']) && !empty($travels)){
            if($_POST['TravelReport']['format'] == 'Excel'){
                self::generateExel($travels);
            }elseif ($_POST['TravelReport']['format'] == 'Pdf'){
                self::createPdf($travels);
            }
        }

        if (empty($travels)) $message = "No search results found";

        $this->render('index',array(
            'model' => $model,
            'travels' =>$travels,
            'message' =>$message,
        ));
    }

    public function createPdf($resp){
        $data = [
            'resp' => $resp,
            'istravelreport' => true
        ];
        $this->generatePdf('reports',$data,'application.modules.reports.views.travelrequests.','L');
        $file = Utils::getFileReport();
        if ($file !== null)
        {
            header('Content-disposition: attachment; filename=Travels_REPORT.pdf');
            header('Content-type: application/pdf');
            readfile(str_ireplace('\\','/',$file));
        }
    }

    public function generateExel($data){
        Yii::import('ext.phpexcel.XPHPExcel');
        if (PHP_SAPI == 'cli')
            die('This example should only be run from a Web Browser');


        // Create new PHPExcel object
        $objPHPExcel = XPHPExcel::createPHPExcel();

        // Set document properties
        $objPHPExcel->getProperties()->setCreator("Seve Alex")
            ->setLastModifiedBy("Seve Alex")
            ->setTitle("Office 2007 XLSX Test Document")
            ->setSubject("Office 2007 XLSX Test Document")
            ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
            ->setKeywords("office 2007 openxml php")
            ->setCategory("Test result file");

        $objDrawing = new PHPExcel_Worksheet_Drawing();
        $objDrawing->setName('PHPExcel logo');
        $objDrawing->setDescription('PHPExcel logo');
        $objDrawing->setPath(dirname(Yii::app()->request->scriptFile).'/images/logo_pdf.png');       // filesystem reference for the image file
        $objDrawing->setHeight(36);                 // sets the image height to 36px (overriding the actual image height);
        $objDrawing->setCoordinates('A1');    // pins the top-left corner of the image to cell D24
        $objDrawing->setOffsetX(10);                // pins the top left corner of the image at an offset of 10 points horizontally to the right of the top-left corner of the cell
        $objDrawing->setWorksheet($objPHPExcel->getActiveSheet());

        //My styles
        $styleArray = array('font' => array('italic' => false, 'bold'=> true,    ),
            'borders' => array(
                'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN,),),);

        $styleArray1 = array('font' => array('italic' => false, 'bold'=> false,    ),
            'borders' => array(
                'bottom' => array('color' => array('argb' => '11666739')),
                'top' => array('color' => array('argb' => '11666739')),
                'right' => array('color' => array('argb' => '11666739')),
            ),);

        $styleLeft = array('font' => array('italic' => false, 'bold'=> true,    ),
            'borders' => array(
                'right' => array('style' => PHPExcel_Style_Border::BORDER_THIN,'color' => array('argb' => '11666739')),
            ),);

        $sheetId = 0;
        $objPHPExcel->getActiveSheet()->getStyle('A4:H4')->applyFromArray($styleArray);
        $objPHPExcel->setActiveSheetIndex($sheetId)
            ->setCellValue('A'.'4', 'Traveler')
            ->setCellValue('B'.'4', 'Departure Date')
            ->setCellValue('C'.'4', 'Return Date')
            ->setCellValue('D'.'4', 'Period (days)')
            ->setCellValue('E'.'4', 'Project')
            ->setCellValue('F'.'4', 'Customer')
            ->setCellValue('G'.'4', 'Destination')
            ->setCellValue('H'.'4', 'Status');

        $objPHPExcel->getActiveSheet()->getStyle('A4:H4')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('b20532');
        $styleArray2 = array(
            'font' => array(
                'color' => array('rgb' => 'FFFFFF'
                )
            ),
            'borders' => array(
                'allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN,'color' => array(    'argb' => '000000')
                )
            ));

        $objPHPExcel->getActiveSheet()->getStyle('A4:H4')->applyFromArray($styleArray2);
        $ct = 5;

        foreach($data as $key=> $item){
            $datetime1 = new DateTime(str_ireplace('/','-',$item['departure_date']));
            $datetime2 = new DateTime(str_ireplace('/','-',$item['return_date']));
            $period = $datetime1->diff($datetime2);

            $objPHPExcel->getActiveSheet()->getStyle('A'.$ct.':C'.$ct)->applyFromArray($styleArray1);

            $objPHPExcel->setActiveSheetIndex($sheetId)
                ->setCellValue('A'.$ct, Users::getNameById((int)$item['traveler']))
                ->setCellValue('B'.$ct, $item['departure_date'])
                ->setCellValue('C'.$ct, $item['return_date'])
                ->setCellValue('D'.$ct, $period->format('%a'))
                ->setCellValue('E'.$ct, $item['purpose'])
                ->setCellValue('F'.$ct, $item['id_customer'])
                ->setCellValue('G'.$ct, Codelkups::getCodelkup((int)$item['destination']))
                ->setCellValue('H'.$ct, Booking::getStatusLabel((int)$item['status']));


            $ct=$ct+1;
        }



        // Rename worksheet
        $objPHPExcel->getActiveSheet()->setTitle('Travels Reports');

        //$objWorkSheet = $objPHPExcel->createSheet(1);
        //$sheetId = 1;



        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);


        // Redirect output to a client’s web browser (Excel5)
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="travelsReport.xls"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');

        // If you're serving to IE over SSL, then the following may be needed
        header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
        header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header ('Pragma: public'); // HTTP/1.0


        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
        exit;
    }
}