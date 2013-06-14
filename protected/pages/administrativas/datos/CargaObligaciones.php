<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class CargaObligaciones {

    /**
     * Carga las obligaciones de los morosos, el origen de la informacion
     * puede ser un archivo csv, xls, xlsx, 
     * @param string $filename El nombre del archivo
     * @param string $filetemp La ruta temporal del archivo en el servidor
     * @param string $filetype La extencion del archivo
     * @param boolean $hasfile Si fue cargado un archivo
     * @param objeto $page La Vista actual
     * */
    private static $Mensaje = "";

    public static function CargarObligacion($filename, $filetemp, $filetype, $hasfile, $page) {

        $error = false;

        //Array para cachear la informacion de todos los terceros y saber si se crea o se actualiza.


        $Terceros = new TercerosRecord();

        $Terceros = TercerosRecord::finder()->FindAll();
        $CacheTerceros = array();

        foreach ($Terceros as $value) {
            $CacheTerceros[] = $value->Identificacion;
        }


        if ($filename[1] == "csv") {
            if ($page->fupArchivo->HasFile) {
                set_time_limit(0);
                $row = 0;
                $fp = fopen($filetemp, "r");

                $Transaccion = ObligacionesRecord::Transaccion();

                try {
                    while (($data = fgetcsv($fp, 1000, ";")) !== FALSE) {
                        $row++;
                        $Obligacion = new ObligacionesRecord();

                        if ($data[0] != "")
                            $Obligacion->NroObligacion = $data[0];

                        if ($data[1] != "")
                            $Obligacion->FhObligacion = $data[1];

                        if ($data[2] != "")
                            $Obligacion->FhVencimientoFactura = $data[2];

                        if ($data[3] != "")
                            $Obligacion->FhUltimoPago = $data[3];

                        if ($data[4] != "")
                            $Obligacion->IdTercero = $data[4];

                        if ($data[5] != "")
                            $Obligacion->IdTerceroPertenece = $data[5];

                        if ($data[6] != "")
                            $Obligacion->ValorCuota = $data[6];

                        if ($data[7] != "")
                            $Obligacion->ValorFactura = $data[7];

                        if ($data[8] != "")
                            $Obligacion->ValorReporte = $data[8];

                        if ($data[8] != "")
                            $Obligacion->Saldo = $data[8];

//                        if ($data[9] != "")
//                            $Obligacion->Estado = $data[9];
//                        if ($data[11] != "")
//                            $Obligacion->Intereses = $data[11];

                        $Obligacion->save();
                    }

                    $Total = $row - 1;

                    $Transaccion->commit();

                    LibGeneral::MsCargaCompleta($page, $Total);
                } catch (Exception $e) {

                    $Transaccion->rollBack();
                    LibGeneral::MsErrorCarga($page, $e);
                }

                fclose($fp);
                set_time_limit(60);
            }
        } else {
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

            try {

                set_time_limit(0);

                $i = 2;

                $Transaccion = ObligacionesRecord::Transaccion();


                while ($objPHPExcel->getActiveSheet()->getCell("A" . $i)->getValue() != '' && $error == false) {

                    $Obligacion = new ObligacionesRecord();

                    $Obligacion->FhReporte = date('Y-m-d H:i:s');


                    if ($objPHPExcel->getActiveSheet()->getCell("A" . $i)->getValue() != ''
                            && $objPHPExcel->getActiveSheet()->getCell("B" . $i)->getValue() != ''
                            && $objPHPExcel->getActiveSheet()->getCell("C" . $i)->getValue() != ''
                            && $objPHPExcel->getActiveSheet()->getCell("E" . $i)->getValue() != ''
                            && $objPHPExcel->getActiveSheet()->getCell("G" . $i)->getValue() != ''
                            && $objPHPExcel->getActiveSheet()->getCell("H" . $i)->getValue() != '') {

                        $Obligacion->NrObligacion = $objPHPExcel->getActiveSheet()->getCell("A" . $i)->getValue();
                        $Obligacion->FhObligacion = PHPExcel_Style_NumberFormat::toFormattedString($objPHPExcel->getActiveSheet()->getCell("B" . $i)->getValue(), "YYYY/M/D");
                        $Obligacion->FhVencimientoObligacion = PHPExcel_Style_NumberFormat::toFormattedString($objPHPExcel->getActiveSheet()->getCell("C" . $i)->getValue(), "YYYY/M/D");
                        $Obligacion->IdTercero = $objPHPExcel->getActiveSheet()->getCell("E" . $i)->getValue();
                        $Obligacion->ValorFactura = $objPHPExcel->getActiveSheet()->getCell("G" . $i)->getValue();
                        $Obligacion->ValorReporte = $objPHPExcel->getActiveSheet()->getCell("H" . $i)->getValue();
                    } else {
                        $error = true;
                    }

                    //Validamos si los datos  tienen el tipo de dato adecuado
                    if (!is_numeric($Obligacion->ValorFactura) || !is_numeric($Obligacion->ValorReporte)) {
                        $error = true;
                    }

                    //Validamos que el IdTerceroPertenece si sea un Moroso
                    if (!in_array((String) $Obligacion->IdTercero, $CacheTerceros)) {
                        $error = true;
                    }

                    if ($error == false) {

                        if ($objPHPExcel->getActiveSheet()->getCell("D" . $i)->getValue() != '') {
                            $Obligacion->FhUltimoPago = PHPExcel_Style_NumberFormat::toFormattedString($objPHPExcel->getActiveSheet()->getCell("D" . $i)->getValue(), "YYYY/M/D");
                        }

                        if ($objPHPExcel->getActiveSheet()->getCell("F" . $i)->getValue() != '') {
                            $Obligacion->ValorCuota = $objPHPExcel->getActiveSheet()->getCell("F" . $i)->getValue();
                        }


                        if ($objPHPExcel->getActiveSheet()->getCell("H" . $i)->getValue() != '') {
                            $Obligacion->Saldo = $objPHPExcel->getActiveSheet()->getCell("H" . $i)->getValue();
                        }


                        if ($Obligacion->save()) {
                            $i++;
                        }
                    }
                }

                if ($error == false) {

                    $Total = $i - 2;

                    $Transaccion->commit();
                    self::$Mensaje = "Se ha cargado Correctamente la informaci&oacute;n. Registros:" . ($i - 2);

                    return true;
                } else {

                    $Transaccion->rollBack();
                    self::$Mensaje = "Los campos obligatorios para la carga de Obligaciones no han sido diligenciados correctamente verifique el registro #" . ($i - 1);
                    return false;
                }
            } catch (Exception $e) {

                $Transaccion->rollBack();
                self::$Mensaje = $e->getMessage();
                return false;
            }

            set_time_limit(60);
        }
    }

    public static function ObtenerMensaje() {
        return self::$Mensaje;
    }

}

?>
