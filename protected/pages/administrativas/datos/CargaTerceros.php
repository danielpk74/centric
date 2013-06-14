<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
//
prado::using("Application.librerias.Clases.PHPExcel");
prado::using("Application.librerias.Clases.PHPExcel.Reader.Excel2007");

class CargaTerceros {

    /**
     * Carga los datos de los tercero, el origen de la informacion
     * puede ser un archivo csv, xls, xlsx, 
     * @param string $filename El nombre del archivo
     * @param string $filetemp La ruta temporal del archivo en el servidor
     * @param string $filetype La extencion del archivo
     * @param boolean $hasfile Si fue cargado un archivo
     * @param objeto $page La Vista actual
     * */
    private static $Mensaje = "";

    public static function ImportarTerceros($filename, $filetemp, $filetype, $hasfile, $page) {

        $LibGeneral = new LibGeneral();
        $error = false;

        //Array para cachear la informacion de todos los terceros y saber si se crea o se actualiza.


        $Terceros = new TercerosRecord();

        $Terceros = TercerosRecord::finder()->FindAll();
        $CacheTerceros = array();

        foreach ($Terceros as $value) {
            $CacheTerceros[] = $value->Identificacion;
        }

        //Array para cachear la informacion de todas ciudades para la previa validación

        $Ciudades = new CiudadesRecord();
        $Ciudades = CiudadesRecord::finder()->FindAll();
        $CacheCiudades = array();

        foreach ($Ciudades as $value2) {
            $CacheCiudades[] = $value2->CodCiudad;
        }



        // Comienza la carga si el archivo es de texto plano
        if ($filename[1] == "csv") {
            if ($hasfile) {
                $row = 0;
                $fp = fopen($filetemp, "r");

                try {

                    $Transaccion = TercerosRecord::Transaccion();
                    set_time_limit(0);

                    while (($data = fgetcsv($fp, 1000, ";")) !== FALSE) {
                        $row++;
                        $Tercero = new TercerosRecord();

//                        if ($data[0] != "")
//                            $Tercero->IdTercero = $data[0];

                        if ($data[0] != "")
                            $Tercero->TpIdentificacion = $data[0];

                        if ($data[1] != "")
                            $Tercero->IdTerceroPertenece = $data[1];

                        if ($data[2] != "")
                            $Tercero->DigitoVerificacion = $data[2];

                        $Tercero->FhCreacion = date('Y-m-d H:i:s');

                        if ($data[3] != "")
                            $Tercero->FhCreacion = $data[3];

                        if ($data[4] != "")
                            $Tercero->NombreCorto = $data[4];

                        if ($data[5] != "")
                            $Tercero->NombreExtendido = $data[5];

                        if ($data[6] != "")
                            $Tercero->Nombre = $data[6];

                        if ($data[7] != "")
                            $Tercero->Nombre2 = $data[7];

                        if ($data[8] != "")
                            $Tercero->Apellido1 = $data[8];

                        if ($data[9] != "")
                            $Tercero->Apellido2 = $data[9];

                        if ($data[10] != "")
                            $Tercero->Direccion = $data[10];

                        if ($data[11] != "")
                            $Tercero->Telefono = $data[11];

                        if ($data[12] != "")
                            $Tercero->Telefono2 = $data[12];

                        if ($data[13] != "")
                            $Tercero->Fax = $data[13];

                        if ($data[14] != "")
                            $Tercero->Email = $data[14];

                        if ($data[15] != "")
                            $Tercero->Contacto = $data[15];

                        if ($data[16] != "")
                            $Tercero->CargoContacto = $data[16];

                        if ($data[17] != "")
                            $Tercero->Comentarios = $data[17];

                        $Tercero->save();
                    }

                    $Total = $row + 1;

                    $Transaccion->commit();

                    return $Total;
                } catch (Exception $e) {
                    $Transaccion->rollBack();

                    return false;
                }

                set_time_limit(60);

                fclose($fp);
            }
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

            $Transaccion = TercerosRecord::Transaccion();

            try {

                set_time_limit(0);

                $i = 2;
                while ($objPHPExcel->getActiveSheet()->getCell("A" . $i)->getValue() != '' && $error == false) {

                    //Se comienza por validar que el registro tenga los campos obligatorios diligenciados además de validad si la ciudad ingresada exite


                    $Tercero = new TercerosRecord();

                    $Tercero->FhCreacion = date('Y-m-d H:i:s');

                    if ($objPHPExcel->getActiveSheet()->getCell("A" . $i)->getValue() != ''
                            && $objPHPExcel->getActiveSheet()->getCell("B" . $i)->getValue() != ''
                            && $objPHPExcel->getActiveSheet()->getCell("D" . $i)->getValue() != ''
                            && $objPHPExcel->getActiveSheet()->getCell("K" . $i)->getValue() != ''
                            && $objPHPExcel->getActiveSheet()->getCell("L" . $i)->getValue() != '') {
                        
                        $Tercero->Identificacion = $objPHPExcel->getActiveSheet()->getCell("A" . $i)->getValue();
                        $Tercero->TpIdentificacion = $objPHPExcel->getActiveSheet()->getCell("B" . $i)->getValue();
                        $Tercero->NombreCorto = $objPHPExcel->getActiveSheet()->getCell("D" . $i)->getValue();
                        $Tercero->CodCiudad = $objPHPExcel->getActiveSheet()->getCell("K" . $i)->getValue();
                        $Tercero->Telefono = $objPHPExcel->getActiveSheet()->getCell("L" . $i)->getValue();
                    } else {
                        $error = true;
                    }
                    
                    
                    //Validamos la exitencia de la ciudad, de lo contrario envia error
                    
                    if(!in_array((String)$Tercero->CodCiudad, $CacheCiudades)){
                        $error=true;
                    }


                    //Validamos si los datos  tienen el tipo de dato adecuado
                    if (!is_numeric($Tercero->Identificacion) || !is_numeric($Tercero->TpIdentificacion)) {
                        $error = true;
                    }
                    
             


                    if ($error == false) {

                        if ($objPHPExcel->getActiveSheet()->getCell("C" . $i)->getValue() != '') {
                            $Tercero->DigitoVerificacion = $objPHPExcel->getActiveSheet()->getCell("C" . $i)->getValue();
                        }

                        if ($objPHPExcel->getActiveSheet()->getCell("E" . $i)->getValue() != '') {
                            $Tercero->NombreExtendido = $objPHPExcel->getActiveSheet()->getCell("E" . $i)->getValue();
                        }

                        if ($objPHPExcel->getActiveSheet()->getCell("F" . $i)->getValue() != '') {
                            $Tercero->Nombre = $objPHPExcel->getActiveSheet()->getCell("F" . $i)->getValue();
                        }

                        if ($objPHPExcel->getActiveSheet()->getCell("G" . $i)->getValue() != '') {
                            $Tercero->Nombre2 = $objPHPExcel->getActiveSheet()->getCell("G" . $i)->getValue();
                        }

                        if ($objPHPExcel->getActiveSheet()->getCell("H" . $i)->getValue() != '') {
                            $Tercero->Apellido1 = $objPHPExcel->getActiveSheet()->getCell("H" . $i)->getValue();
                        }

                        if ($objPHPExcel->getActiveSheet()->getCell("I" . $i)->getValue() != '') {
                            $Tercero->Apellido2 = $objPHPExcel->getActiveSheet()->getCell("I" . $i)->getValue();
                        }

                        if ($objPHPExcel->getActiveSheet()->getCell("J" . $i)->getValue() != '') {
                            $Tercero->Direccion = $objPHPExcel->getActiveSheet()->getCell("J" . $i)->getValue();
                        }

                        if ($objPHPExcel->getActiveSheet()->getCell("M" . $i)->getValue() != '') {
                            $Tercero->Telefono2 = $objPHPExcel->getActiveSheet()->getCell("M" . $i)->getValue();
                        }

                        if ($objPHPExcel->getActiveSheet()->getCell("N" . $i)->getValue() != '') {
                            $Tercero->Fax = $objPHPExcel->getActiveSheet()->getCell("N" . $i)->getValue();
                        }

                        if ($objPHPExcel->getActiveSheet()->getCell("O" . $i)->getValue() != '') {
                            $Tercero->Email = $objPHPExcel->getActiveSheet()->getCell("O" . $i)->getValue();
                        }


                        if ($objPHPExcel->getActiveSheet()->getCell("P" . $i)->getValue() != '') {
                            $Tercero->Contacto = $objPHPExcel->getActiveSheet()->getCell("P" . $i)->getValue();
                        }

                        if ($objPHPExcel->getActiveSheet()->getCell("Q" . $i)->getValue() != '') {
                            $Tercero->CargoContacto = $objPHPExcel->getActiveSheet()->getCell("Q" . $i)->getValue();
                        }
                        if ($objPHPExcel->getActiveSheet()->getCell("R" . $i)->getValue() != '') {
                            $Tercero->Comentarios = $objPHPExcel->getActiveSheet()->getCell("R" . $i)->getValue();
                        }
                        if ($Tercero->save()) {
                            $i++;
                        }
                    }
                }

                if ($error == false) {

                    $Total = $i;

                    $Transaccion->commit();

                    self::$Mensaje = "Se ha cargado Correctamente la informaci&oacute;n. Registros:" . ($i - 2);

                    return true;
                } else {

                    $Transaccion->rollBack();
                    self::$Mensaje = "Los campos obligatorios para la carga de terceros no han sido diligenciados correctamente verifique el registro #" . ($i - 1);
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
