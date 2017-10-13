<?php

namespace common\helper;

use Yii;

class ExcelHelper
{
    private $error = array();

    /**
     * get the error message
     */
    public function getErrors()
    {
        return $this->error;
    }

    /**
     * add or update error message by key
     * @param string $key
     * @param string $value
     */
    private function addError($key, $value)
    {
        $this->error[$key] = $value;
    }

    function format()
    {
        $args = func_get_args();
        if (count($args) == 0) {
            return false;
        }
        if (count($args) == 1) {
            return $args[0];
        }

        $str = array_shift($args);

        $str = preg_replace_callback('/\\{(0|[1-9]\\d*)\\}/', create_function('$match', '$args = ' . var_export($args, true) . '; return isset($args[$match[1]]) ? $args[$match[1]] : $match[0];'), $str);

        return $str;
    }

    /**
     * get the excels data to array
     * @param string $path
     * @param string $config
     * @return mixed
     */
    public function ReadExcel($path, $config)
    {
        if (empty($path) || !file_exists($path)) {
            $this->addError('NotExist', Yii::t('app', 'FileDoNotExist'));
            return false;
        }

        if (empty($config) || !isset(Yii::$app->params[$config]) || empty(Yii::$app->params[$config])) {
            $this->addError('MissConfig', Yii::t('app', 'MissConfig'));
            return false;
        }

        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        if ($extension == 'xlsx' || $extension == 'xls') {
            $objPHPExcel = \PHPExcel_IOFactory::load($path);
        } else if ($extension == 'csv') {
            $objReader = \PHPExcel_IOFactory::createReader('CSV')
                ->setDelimiter(',')
                ->setInputEncoding('GBK')
                ->setEnclosure('"')
                //->setLineEnding("\r\n")
                ->setSheetIndex(0);
            $objPHPExcel = $objReader->load($path);
        } else {
            $this->addError('ErrorFileType', Yii::t('app', 'ErrorFileType'));
            return false;
        }

        $sheet = $objPHPExcel->getSheet(0);

        //获取行数与列数,注意列数需要转换
        $highestRowNum = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();
        $highestColumnNum = \PHPExcel_Cell::columnIndexFromString($highestColumn);

        $arrConfig = Yii::$app->params[$config];

        $headRow = intval($arrConfig['headrow']);

        if ($headRow < 1) {
            $headRow = 0;
        }

        $filed = array();

        for ($i = 0; $i < count($arrConfig['columns']); $i++) {
            if ($i > $highestColumnNum) {
                break;
            }

            $filed[$arrConfig['columns'][$i]['name']] = $this->getHeaderTitle($sheet, $i, $headRow);
        }

        //开始取出数据并存入数组
        $data = array();
        for ($i = $headRow + 1; $i <= $highestRowNum; $i++) {
            $row = array();
            $rowError = '';
            $bEmpty = true;

            for ($j = 0; $j < count($arrConfig['columns']); $j++) {
                try {
                    $columnConfig = $arrConfig['columns'][$j];

                    $cellName = \PHPExcel_Cell::stringFromColumnIndex($j) . $i;
                    $cellVal = $sheet->getCell($cellName)->getCalculatedValue();
                    $row[$columnConfig['name']] = $cellVal;

                    if (!empty($cellVal) && $bEmpty) {
                        $bEmpty = false;
                    }

                    //required
                    $required = isset($columnConfig['required']) ? $columnConfig['required'] : false;

                    if ($required && trim($cellVal) == '') {
                        $rowError .= $filed[$columnConfig['name']] . Yii::t('app', 'required') . "\r\n";
                        continue;
                    }

                    //relation
                    if (isset($columnConfig['relation']) && is_array($columnConfig['relation'])) {
                        if (!empty($columnConfig['relation']['field']) && !empty($columnConfig['relation']['model'])
                            && !empty($columnConfig['relation']['where'])) {
                            $className = $columnConfig['relation']['model'];
                            $objModel = new $className();
                            $objQuery = $objModel->find()->where($this->format($columnConfig['relation']['where'], $cellVal))->one();

                            if (!empty($objQuery) && isset($objQuery[$columnConfig['relation']['field']])) {
                                $row[$columnConfig['name']] = $objQuery[$columnConfig['relation']['field']];
                            }
                        }
                    }

                    //data type
                    if (isset($columnConfig['datatype'])) {
                        switch ($columnConfig['datatype']) {
                            case "int":
                                $row[$columnConfig['name']] = intval($row[$columnConfig['name']]);
                                break;
                            case "number":
                                $row[$columnConfig['name']] = doubleval($row[$columnConfig['name']]);
                                break;
                            case "date":
                                if (is_null($row[$columnConfig['name']])) {
                                    $row[$columnConfig['name']] = "";
                                } else {
                                    $row[$columnConfig['name']] = strtotime($row[$columnConfig['name']]);
                                }

//                                $row[$columnConfig['name']] = $this->excelTime($row[$columnConfig['name']]);
                                break;
                            default:
                                $row[$columnConfig['name']] = strval($row[$columnConfig['name']]);
                                break;
                        }
                    } else
                        $row[$columnConfig['name']] = strval($row[$columnConfig['name']]);
                } catch (\Exception $ex) {
                    $rowError .= $ex->getMessage() . "\r\n";
                    continue;
                }
            }

            if (!$bEmpty) {
                if (!empty($rowError)) {
                    $row["errorinfo"] = $rowError;
                    $data['error'][] = $row;
                } else
                    $data['success'][] = $row;
            }
        }
        return $data;
    }

    private function excelTime($date, $time = false)
    {
        if (is_numeric($date)) {
            $jd = GregorianToJD(1, 1, 1970);
            $gregorian = JDToGregorian($jd + intval($date) - 25569);
            $date = explode('/', $gregorian);
            $date_str = str_pad($date[2], 4, '0', STR_PAD_LEFT)
                . "-" . str_pad($date[0], 2, '0', STR_PAD_LEFT)
                . "-" . str_pad($date[1], 2, '0', STR_PAD_LEFT)
                . ($time ? " 00:00:00" : '');
            return $date_str;
        }
        return $date;
    }

    private function getHeaderTitle($sheet, $column, $currRow)
    {
        $cellName = \PHPExcel_Cell::stringFromColumnIndex($column) . $currRow;

        $cellVal = $sheet->getCell($cellName)->getCalculatedValue();//取得列内容

        if ($currRow > 1) {
            $cellVal = $this->getHeaderTitle($sheet, $column, --$currRow) . $cellVal;
        }

        return $cellVal;
    }

    /**
     * export excel files by array
     * @param string $path
     * @param string $arr
     * @param string $template
     * @param int $sign
     * @return string
     */
    public function WriteExcel($path, $arr, $template = '', $sign = 0)
    {
        if (empty($arr) || !is_array($arr)) {
            return "";
        }

        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }

        $path = $path . "/" . date('YmdHis') . rand(100, 999) . ".xls";

        //check template file exist
        $objPHPExcel = null;
        if (!empty($template) && file_exists($template)) {
            if (copy($template, $path)) {
                $objPHPExcel = \PHPExcel_IOFactory::load($path);
            }
        }

        if ($objPHPExcel == null) {
            $objPHPExcel = new \PHPExcel();
            $objPHPExcel->getProperties()
                ->setCreator("tony")
                ->setLastModifiedBy("tony")
                ->setTitle(date('YmdHis'))
                ->setSubject("")
                ->setDescription("")
                ->setKeywords("")
                ->setCategory("");
        }

        $sheet = $objPHPExcel->setActiveSheetIndex(0);
        $tmpSheet = clone $sheet;
        //get the header row count
        $highestRowNum = $sheet->getHighestRow();

        //如果是三维数组则创建多个sheet，如果是二维数组则创建一个sheet
        $bFlag = false;

        if (is_array(end($arr))) {
            $lastItem = end($arr);
            if (is_array(end($lastItem)))
                $bFlag = true;
        }

        $sheetIndex = 0;
        foreach ($arr as $sheetname => $tmp) {
            if (!is_array($tmp))
                continue;

            $activeSheet = $sheet;

            if ($bFlag) {
                if ($sheetIndex > 0) {
                    $cloneSheet = clone $tmpSheet;
                    $cloneSheet->setTitle($sheetname . '');

                    if ($sign != 1)
                        $activeSheet = $objPHPExcel->addSheet($cloneSheet, $sheetIndex);

                    $activeSheet = $objPHPExcel->setActiveSheetIndex($sheetIndex);
                }
                $activeSheet->setTitle($sheetname . '');

                $sheetIndex++;
                $highestRowNum = $activeSheet->getHighestRow();
                foreach ($tmp as $lastarr) {
                    if (!is_array($lastarr))
                        continue;

                    $this->writeBySheet($activeSheet, $lastarr, ++$highestRowNum);
                }
            } else {
                $this->writeBySheet($activeSheet, $tmp, ++$highestRowNum);
            }
        }

        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');

        $objWriter->save($path);

        return $path;
    }

    private function writeBySheet($sheet, $tmp, $n)
    {
        $i = 0;
        foreach ($tmp as $k => $v) {
            $cellName = \PHPExcel_Cell::stringFromColumnIndex($i) . $n;
            // print_r($v);exit;
            $sheet->setCellValueExplicit($cellName, $v, \PHPExcel_Cell_DataType::TYPE_STRING);
            //$sheet->setCellValue($cellName,$v);

            if ($k == "errorinfo") {
                $objStyle = $sheet->getStyle($cellName);

                $objFill = $objStyle->getFill();
                $objFill->setFillType(\PHPExcel_Style_Fill::FILL_SOLID);
                $objFill->getStartColor()->setARGB('FFFF0000');
            }

            $i++;
        }
    }

    /**
     * clear the path
     * @param unknown $path
     */
    public function ClearPath($path)
    {

    }

    /**
     * export csv files by array
     * @param unknown $path
     * @param unknown $arr
     * @return string
     */
    public function WriteCsv($path, $arr, $csvhead)
    {
        if (empty($arr) || !is_array($arr))
            return "";

        if (empty($csvhead) || !is_array($csvhead))
            return "";

        if (!is_dir($path))
            mkdir($path, 0777, true);

        $csvstr = implode(',', $csvhead) . "\n";
        $row = '';
        foreach ($arr as $v) {
            $row .= str_replace("\t\n", '', str_replace("\n", '', implode(',', $v))) . "\n";
        }

        $csvstr .= trim($row, "\n");
        $csvstr = iconv('utf-8', 'gbk', $csvstr);
        $filename = $path . '/' . date('YmdHis') . rand(100, 999) . '.csv';

        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header('Content-Type: application/download');
        header("Content-type: text/csv; charset=UTF-8");
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header("Content-Encoding: binary");
        header("Pragma: no-cache");
        header("Expires: 0");

        $fp = fopen($filename, "a");
        fwrite($fp, $csvstr);
        fclose($fp);

        return $filename;
    }

    /**
     * 导出客户签收的xml版的Excel
     * @param unknown $path
     * @param unknown $arr
     * @param string $template
     * @return string
     */
    public function WriteXmlExcel($path, $arr, $template = '')
    {
        if (empty($arr) || !is_array($arr))
            return "";

        if (!is_dir($path))
            mkdir($path, 0777, true);

        $filepath = $path . "/" . date('YmdHis') . rand(100, 999) . ".xls";
        copy($template, $filepath);
        $file = fopen($filepath, "w");

        $xmlexcel = '';
        $xmlexcel .= "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<Workbook xmlns=\"urn:schemas-microsoft-com:office:spreadsheet\" xmlns:x=\"urn:schemas-microsoft-com:office:excel\" xmlns:ss=\"urn:schemas-microsoft-com:office:spreadsheet\" xmlns:html=\"http://www.w3.org/TR/REC-html40\">\n";

        $xmlexcel .= "<Worksheet ss:Name=\"Sheet1\">\n";
        $xmlexcel .= "<Table>\n";

        $xmlexcel .= "<Row>\n";
        $xmlexcel .= '<Cell><Data ss:Type="String">客户</Data></Cell>' . "\n";
        $xmlexcel .= '<Cell><Data ss:Type="String">收货人</Data></Cell>' . "\n";
        $xmlexcel .= '<Cell><Data ss:Type="String">收货人省份</Data></Cell>' . "\n";
        $xmlexcel .= '<Cell><Data ss:Type="String">收货人城市</Data></Cell>' . "\n";
        $xmlexcel .= '<Cell><Data ss:Type="String">主要货物名称</Data></Cell>' . "\n";
        $xmlexcel .= '<Cell><Data ss:Type="String">实际重量</Data></Cell>' . "\n";
        $xmlexcel .= '<Cell><Data ss:Type="String">支付金额</Data></Cell>' . "\n";
        $xmlexcel .= '<Cell><Data ss:Type="String">主单号</Data></Cell>' . "\n";
        $xmlexcel .= '<Cell><Data ss:Type="String">分单号</Data></Cell>' . "\n";
        $xmlexcel .= '<Cell><Data ss:Type="String">国内单号</Data></Cell>' . "\n";
        $xmlexcel .= '<Cell><Data ss:Type="String">国内仓发货时间</Data></Cell>' . "\n";
        $xmlexcel .= '<Cell><Data ss:Type="String">签收时间</Data></Cell>' . "\n";
        $xmlexcel .= '<Cell><Data ss:Type="String">签收人</Data></Cell>' . "\n";
        $xmlexcel .= "</Row>\n";

        foreach ($arr as $v) {
            $xmlexcel .= "<Row>\n";
            $xmlexcel .= '<Cell><Data ss:Type="String">' . $v['TITLE'] . '</Data></Cell>' . "\n";
            $xmlexcel .= '<Cell><Data ss:Type="String">' . $v['RECEIVEPEOPLE'] . '</Data></Cell>' . "\n";
            $xmlexcel .= '<Cell><Data ss:Type="String">' . $v['RECEIVEPROVINCE'] . '</Data></Cell>' . "\n";
            $xmlexcel .= '<Cell><Data ss:Type="String">' . $v['RECEIVECITY'] . '</Data></Cell>' . "\n";
            $xmlexcel .= '<Cell><Data ss:Type="String">' . $v['GOODSNAME'] . '</Data></Cell>' . "\n";
            $xmlexcel .= '<Cell><Data ss:Type="Number">' . $v['ACTUALWEIGHT'] . '</Data></Cell>' . "\n";
            $xmlexcel .= '<Cell><Data ss:Type="Number">' . $v['PAYMONEY'] . '</Data></Cell>' . "\n";
            $xmlexcel .= '<Cell><Data ss:Type="String">' . $v['MAWB'] . '</Data></Cell>' . "\n";
            $xmlexcel .= '<Cell><Data ss:Type="String">' . $v['TRACKINGNO'] . '</Data></Cell>' . "\n";
            $xmlexcel .= '<Cell><Data ss:Type="String">' . $v['LOCALEXPRESSNO'] . '</Data></Cell>' . "\n";
            $xmlexcel .= '<Cell><Data ss:Type="String">' . $v['INLANDSENDTIME'] . '</Data></Cell>' . "\n";
            $xmlexcel .= '<Cell><Data ss:Type="String">' . $v['CREATETIME'] . '</Data></Cell>' . "\n";
            $xmlexcel .= '<Cell><Data ss:Type="String">' . $v['SIGNNAME'] . '</Data></Cell>' . "\n";
            $xmlexcel .= "</Row>\n";
        }

        $xmlexcel .= "</Table>\n";
        $xmlexcel .= "</Worksheet>\n";
        $xmlexcel .= "</Workbook>";

        fwrite($file, $xmlexcel);
        fclose($file);

        return $filepath;
    }

    /**
     * 导出文件打包成xls或zip文件
     * @param unknown $Data
     * @param unknown $TemplateFileName
     * @param unknown $sign 多个sheet
     * @param unknown $Multsheet 判断sheet
     * @return array
     */
    public function Packingcompression($Data, $TemplateFileName, $sign = "", $Multsheet = "")
    {
        try {
            //多个sheet处理
//             if($Multsheet == 1)
//             {
//                 $DataArr = [];
//                 foreach ($Data as $key=>$k)
//                 {                    
//                     for($i = 0; (count($k) >= ($i*50000)); $i++)
//                     {
//                         $DataArr[$i][$key] = array_slice($k,$i*50000,50000);                        
//                     }  
//                 }
//             }

            if (count($Data) <= 50000) {
                $DictName = date('Ymd') . '/' . Yii::$app->user->identity->ID . rand(10000, 99999);
                $downPath = 'downloads/' . $DictName;

                $path = $this->WriteExcel($downPath, $Data, $TemplateFileName, $sign);

                $result['success'] = 1;
                $result['url'] = $path;
            } else {
                $DataArr = [];
                for ($i = 0; (count($Data) >= ($i * 50000)); $i++) {
                    $DataArr[$i] = array_slice($Data, $i * 50000, 50000);
                }

                foreach ($DataArr as $key => $v) {
                    $DictName = date('Ymd') . '/' . Yii::$app->user->identity->ID . rand(10000, 99999);
                    $downPath = 'downloads/' . $DictName;

                    $path = $this->WriteExcel($downPath, $v, $TemplateFileName, $sign);
                    $fileNameArr[] = $path;
                }

                $Compressname = date('Ymd') . '/' . Yii::$app->user->identity->ID . rand(10000, 99999); // 压缩文件的最终生成文件名（含路径）
                $Compressname = 'downloads/' . $DictName . '.zip';

                // 生成文件
                $zip = new \ZipArchive (); // 使用本类，linux需开启zlib，windows需取消php_zip.dll前的注释
                if ($zip->open($Compressname, \ZIPARCHIVE::CREATE) !== TRUE) {
                    $result['message'] = "无法打开文件，或者文件创建失败!";
                    $result['success'] = 0;
                } else {
                    //$fileNameArr 就是一个存储文件路径的数组 比如 array('/a/1.jpg,/a/2.jpg....');
                    foreach ($fileNameArr as $val) {
                        $zip->addFile($val, basename($val)); // 第二个参数是放在压缩包中的文件名称，如果文件可能会有重复，就需要注意一下
                    }

                    $url = substr($zip->filename, -29);
                    $zip->close(); // 关闭

                    $result['success'] = 1;
                    $result['url'] = $url;
                }
            }
        } catch (\Exception $ex) {
            $result['message'] = $ex->getMessage();
        }
        return $result;
    }

}