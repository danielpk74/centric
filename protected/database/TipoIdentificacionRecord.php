<?php
/**
 * Auto generated by prado-cli.php on 2011-03-26 03:18:15.
 */
class TipoIdentificacionRecord extends TActiveRecord
{
	const TABLE='tipoidentificacion';

	public $Id;
	public $TpIdentificacion;

	public static function finder($className=__CLASS__)
	{
		return parent::finder($className);
	}

        public static function ObtenerTpIdentificacion($TpBusqueda, $Tipo) {
        $TpIdentificacion = new TipoIdentificacionRecord();

        //Identificacion
        if ($TpBusqueda == '1')
            $TpIdentificacion = TipoIdentificacionRecord::finder()->findByPk($Tipo);

        return $TpIdentificacion;
        }
}
?>