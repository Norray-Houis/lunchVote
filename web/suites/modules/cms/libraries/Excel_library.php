<?php

/**
 * Created by PhpStorm.
 * User: Houis
 * Date: 18/3/29
 * Time: 下午2:44
 */
class Excel_library
{
    private $cellKey = array(
        'A','B','C','D','E','F','G','H','I','J','K','L','M',
        'N','O','P','Q','R','S','T','U','V','W','X','Y','Z',
        'AA','AB','AC','AD','AE','AF','AG','AH','AI','AJ','AK','AL','AM',
        'AN','AO','AP','AQ','AR','AS','AT','AU','AV','AW','AX','AY','AZ'
    );

    private $excel;
    private $sheetIndex = 0;
    private $sheetRow = 1;

    /**
     * Constructor
     */
    public function __construct()
    {
        include_once 'PHPExcel/Classes/PHPExcel.php';
        include_once 'PHPExcel/Classes/PHPExcel/Writer/Excel2007.php';
        $this->excel  = new PHPExcel();
    }

    /**
     * @param $fileName 输出的文件名
     * @param string $fileType  输出的文件后缀名  默认 xls
     * 把Excel输出到浏览器中
     */
    public function export($fileName,$fileType = 'xls'){
        //创建Excel输入对象
        $write = new PHPExcel_Writer_Excel5($this->excel);
        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control:must-revalidate, post-check=0, pre-check=0");
        header("Content-Type:application/force-download");
        header("Content-Type:application/vnd.ms-execl");
        header("Content-Type:application/octet-stream");
        header("Content-Type:application/download");;
        header('Content-Disposition:attachment;filename="'.$fileName.'.'.$fileType.'"');
        header("Content-Transfer-Encoding:binary");
        $write->save('php://output');
    }



    /**
     * @param $obj
     * 写入Excel第一行数据
     */
    private function setExcelFristCell($firstCell){
        for($i = 0;$i < count($firstCell);$i++) {
            $this->excel->getActiveSheet()->setCellValue($this->cellKey[$i]."1",$firstCell[$i]);
        }
    }

    /**
     * @param $obj
     * 写入Excel第一行以外的数组
     */
    private function setExcelOtherCell($otherCell){
        for ($i = 2;$i <= count($otherCell) + 1;$i++) {
            $j = 0;
            foreach ($otherCell[$i - 2] as $key=>$value) {
                $this->excel->getActiveSheet()->setCellValue($this->cellKey[$j].$i,"$value");
                $j++;
            }
        }

    }

    /**
     * @param int $sheetIndex
     * @param $fristCell 第一行栏目内容
     * @param $otherCell 第一行以外栏目内容
     * 生成sheet内容
     */
    public function createSheet($title){
        if($this->sheetIndex != 0){
            //第一个sheet默认已经create
            $this->excel->createSheet();
        }
        $this->excel->setActiveSheetIndex($this->sheetIndex);
        $this->excel->getActiveSheet()->setTitle($title);

        $this->sheetIndex = $this->sheetIndex+1;

        $this->sheetRow = 1;

    }

    public function setSheetData($data){
        foreach ($data as $v){
            $column = 0;
            foreach ($v as $k){
                switch (gettype($k)){
                    case 'string':
                    case 'integer':
                    case 'NULL':
                        $this->excel->getActiveSheet()->setCellValue($this->cellKey[$column].$this->sheetRow,$k);
                        $column++;
                        break;
                    case 'array':
                        $this->excel->getActiveSheet()->setCellValue($this->cellKey[$column].$this->sheetRow,$k['value']);
                        if(isset($k['align'])){
                            switch ($k['align']){
                                case 'center':
                                    $this->excel->getActiveSheet()->getStyle($this->cellKey[$column].$this->sheetRow.":".$this->cellKey[$column+$k['colspan']-1].$this->sheetRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                                    break;
                            }
                        }
                        if(isset($k['colspan'])){
                            $this->excel->getActiveSheet()->mergeCells($this->cellKey[$column].$this->sheetRow.":".$this->cellKey[$column+$k['colspan']-1].$this->sheetRow);
                            $column = $column+$k['colspan'];
                        }else{
                            $column++;
                        }

                        break;
                    default:
                        throw new Exception(gettype($k).' is unsupported types');
                        break;
                }
            }

            $this->sheetRow++;
        }
    }

    /**
     * @param $data
     */
    public function setDailySheetData($data){

        $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
        $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
        $this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
        $this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(10);
        $this->excel->getActiveSheet()->getDefaultRowDimension()->setRowHeight(25);
        $this->excel->getActiveSheet()->getStyle()->getFont()->setSize(19); //字体
        $dailyTitle = array(
            "序号","日期","午餐名称","午餐数量","汤","饮料","总金额"
        );

        $this->excel->getActiveSheet()->setCellValue($this->cellKey[0].$this->sheetRow,"玖晔午餐每天订购统计")
                                      ->mergeCells($this->cellKey[0].$this->sheetRow.":".$this->cellKey[count($dailyTitle)-1].$this->sheetRow)
                                      ->getStyle($this->cellKey[0].$this->sheetRow.":".$this->cellKey[count($dailyTitle)-1].$this->sheetRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $this->sheetRow++;


        for ($i=0;$i<count($dailyTitle);$i++){
            $this->excel->getActiveSheet()->setCellValue($this->cellKey[$i].$this->sheetRow,$dailyTitle[$i])
                                            ->getStyle($this->cellKey[$i].$this->sheetRow,$dailyTitle[$i])->getAlignment()
                                                ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                                                ->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        }
        $this->sheetRow++;

        $index = 1;
        foreach ($data as $v){
            $rowSpan = count($v['option'])-1;
            //序号
            $this->excel->getActiveSheet()->setCellValue($this->cellKey[0].$this->sheetRow,$index)
                                            ->mergeCells($this->cellKey[0].$this->sheetRow.":".$this->cellKey[0].($this->sheetRow+$rowSpan))
                                            ->getStyle($this->cellKey[0].$this->sheetRow.":".$this->cellKey[0].($this->sheetRow+$rowSpan))->getAlignment()
                                                    ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                                                    ->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            //日期
            $this->excel->getActiveSheet()->setCellValue($this->cellKey[1].$this->sheetRow,$v['date'])
                ->mergeCells($this->cellKey[1].$this->sheetRow.":".$this->cellKey[1].($this->sheetRow+$rowSpan))
                ->getStyle($this->cellKey[1].$this->sheetRow.":".$this->cellKey[1].($this->sheetRow+$rowSpan))->getAlignment()
                ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

            foreach ($v['option'] as $k){
                $column = 2;
                foreach ($k as $item){
                    $this->excel->getActiveSheet()->setCellValue($this->cellKey[$column].$this->sheetRow,$item);
                    $column++;
                }
                $this->sheetRow++;
            }

            $this->excel->getActiveSheet()->setCellValue($this->cellKey[5].$this->sheetRow,'总计：');
            $this->excel->getActiveSheet()->setCellValue($this->cellKey[6].$this->sheetRow,$v['totalMoney']);
            $this->sheetRow++;
            $index++;

            //隔开一行
            $this->sheetRow++;
        }
    }

    /**
     * @param $data
     */
    public function setMonthlySheetData($data){
//        p($data);exit;
        $this->excel->getActiveSheet()->getStyle()->getFont()->setSize(19); //字体
        $monthlyTitle = array(
            "序号","日期","上月结余","午餐数量","总金额","小票","经手人","备注"
        );

        $this->excel->getActiveSheet()->setCellValue($this->cellKey[0].$this->sheetRow,"玖晔午餐月末订购统计")
            ->mergeCells($this->cellKey[0].$this->sheetRow.":".$this->cellKey[count($monthlyTitle)-1].$this->sheetRow)
            ->getStyle($this->cellKey[0].$this->sheetRow.":".$this->cellKey[count($monthlyTitle)-1].$this->sheetRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $this->sheetRow++;

        for ($i=0;$i<count($monthlyTitle);$i++){
            $this->excel->getActiveSheet()->setCellValue($this->cellKey[$i].$this->sheetRow,$monthlyTitle[$i])
                ->getStyle($this->cellKey[$i].$this->sheetRow,$monthlyTitle[$i])->getAlignment()
                ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        }
        $this->sheetRow++;

        $index = 1;
        foreach ($data as $v){

            if(!is_array($v)){
                $this->excel->getActiveSheet()->setCellValue($this->cellKey[0].$this->sheetRow,"小计");
                continue;
            }

            $column=1;
            $this->excel->getActiveSheet()->setCellValue($this->cellKey[0].$this->sheetRow,$index);
            foreach ($v as $item){
                $this->excel->getActiveSheet()->setCellValue($this->cellKey[$column].$this->sheetRow,$item);
                $column++;
            }
            $this->sheetRow++;
            $index++;
        }

    }
}