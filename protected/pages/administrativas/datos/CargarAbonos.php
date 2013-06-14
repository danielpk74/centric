<?php

/**
 * Carga los abonos a las obligaciones. 
 * Estas pueden estar en formato csv o MS Excel
 **/

prado::using("Application.librerias.Clases.PHPExcel");
prado::using("Application.librerias.Clases.PHPExcel.Reader.Excel2007");

/**
 * Description of CargarAbonos
 *
 * @author daniel
 * */
class CargarAbonos {

    public static function CargarAbonos($filename, $filetemp, $filetype, $hasfile, $page) {
        
        $I = 0;
        // Comienza la carga si el archivo es de texto plano
        if ($filename[1] == "csv") {
            if ($hasfile) {
                $row = 0;
                $fp = fopen($filetemp, "r");

                try {

                    while (($data = fgetcsv($fp, 1000, ";")) !== FALSE) {
                        $row++;
                        $Tercero = new AbonosRecord();

                        if ($data[0] != "")
                            $Tercero->FhAbono = $data[0];
                        
                        if ($data[1] != "")
                            $Tercero->IdTercero = $data[1];
                        
                        if ($data[2] != "")
                            $Tercero->NrObligacion = $data[2];
                        
                        if ($data[3] != "")
                            $Tercero->CuentaAbono = $data[3];
                        
                        if ($data[4] != "")
                            $Tercero->ValorAbono = $data[4];
                        
                        if ($data[5] != "")
                            $Tercero->CodFormaPago = $data[5];
                        
                        if ($data[6] != "")
                            $Tercero->CodBanco = $data[6];
                        
                        if ($data[7] != "")
                            $Tercero->Observaciones = $data[7];
                        
//                        $Saldo -= $data[4];
                        
                        $Total = $row + 1;
                    }

                    $Transaccion->commit();

                    $Transaccion = AbonosRecord::Transaccion();
                    set_time_limit(0);
                    return $Total;
                } catch (Exception $e) {
                    $Transaccion->rollBack();

                    return false;
                }

                set_time_limit(60);

                fclose($fp);
            }
            // Comienza la carga si el archivo es un archivo de excel.
            else {
                if (!file_exists($filetemp)) {
                    exit("Please run 14excel5.php first.\n");
                }

                if ($filename[1] == "xlsx") {
                    $objReader = new PHPExcel_Reader_Excel2007();
                }

                if ($filename[1] == "xls") {
                    $objReader = new PHPExcel_Reader_Excel5();
                }

                $objPHPExcel = $objReader->load($filetemp);

                $objPHPExcel->setActiveSheetIndex(0);

                $Transaccion = AbonosRecord::Transaccion();
                try {

                    set_time_limit(0);

                    $i = 2;
                    while ($objPHPExcel->getActiveSheet()->getCell("A" . $i)->getValue() != '') {

                        $Abono = new AbonosRecord();

                        $Dato = $objPHPExcel->getActiveSheet()->getCell("A" . $i)->getValue();

                        if ($objPHPExcel->getActiveSheet()->getCell("A" . $i)->getValue() != '')
                            $Abono->FhAbono = $objPHPExcel->getActiveSheet()->getCell("A" . $i)->getValue();
                        
                        if ($objPHPExcel->getActiveSheet()->getCell("B" . $i)->getValue() != '')
                            $Abono->IdTercero = $objPHPExcel->getActiveSheet()->getCell("B" . $i)->getValue();
                        
                        if ($objPHPExcel->getActiveSheet()->getCell("C" . $i)->getValue() != '')
                            $Abono->NrObligacion = $objPHPExcel->getActiveSheet()->getCell("C" . $i)->getValue();
                        
                        if ($objPHPExcel->getActiveSheet()->getCell("D" . $i)->getValue() != '')
                            $Abono->CuentaAbono = $objPHPExcel->getActiveSheet()->getCell("D" . $i)->getValue();
                        
                        if ($objPHPExcel->getActiveSheet()->getCell("E" . $i)->getValue() != '')
                            $Abono->ValorAbono = $objPHPExcel->getActiveSheet()->getCell("E" . $i)->getValue();
                        
                        if ($objPHPExcel->getActiveSheet()->getCell("F" . $i)->getValue() != '')
                            $Abono->CodBanco = $objPHPExcel->getActiveSheet()->getCell("F" . $i)->getValue();
                        
                        if ($objPHPExcel->getActiveSheet()->getCell("G" . $i)->getValue() != '')
                            $Abono->CodFormaPago = $objPHPExcel->getActiveSheet()->getCell("G" . $i)->getValue();
                        
                        if ($objPHPExcel->getActiveSheet()->getCell("H" . $i)->getValue() != '')
                            $Abono->Observaciones = $objPHPExcel->getActiveSheet()->getCell("H" . $i)->getValue();
                        
                        if ($Abono->save())
                        $i++;
                    }

                    $Total = $i;

                    $Transaccion->commit();
                    
                    return $Total;
                } catch (Exception $e) {
                    $Transaccion->rollBack();
                    return false;
                }
            }
        }
    }

}

?>
