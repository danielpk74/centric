<?php

/**
 * Auto generated by prado-cli.php on 2011-04-07 06:26:19.
 **/
class CompromisosPagoRecord extends TActiveRecord {

    const TABLE = 'compromisospago';

    public $CodCompromiso;
    public $NroObligacion;
    public $CodGestion;
    public $CodTarea;
    public $FhCompromiso;
    public $FhPagoCompromiso;
    public $CodObligacion;
    public $CodCampania;
    public $CodConceptoCompromiso;
    public $CodConceptoResultCobro;
    public $IdTercero;
    public $Valor;
    public $Cuotas;
    public $Usuario;
    public $Observaciones;
    public $ObservacionesResultado;
    public $Activo;
    public $Incumplido;
    
    public static $RELATIONS = array(
        'ConceptoCompromiso' => array(self::BELONGS_TO, 'SubConceptosCobranzaRecord', 'CodConceptoCompromiso'),
        'ConceptoResultado' => array(self::BELONGS_TO, 'SubConceptosCobranzaRecord', 'CodConceptoResultCobro'),
    );

    public static function finder($className = __CLASS__) {
        return parent::finder($className);
    }

    public static function DevCompromisos($CodGestion) {
        $sql = "SELECT compromisospago.CodCompromiso, compromisospago.CodGestion, 
                       compromisospago.FhCompromiso, compromisospago.FhPagoCompromiso,
                       compromisospago.NroObligacion, compromisospago.ObservacionesResultado, 
                       ConceptoCompromiso.Descripcion AS ConceptoCompromiso, ConceptoResultado.Descripcion AS ConceptoResultado,
                       compromisospago.Cuotas, compromisospago.Valor, compromisospago.Observaciones
                FROM ((compromisospago LEFT JOIN subconceptoscobranza as ConceptoCompromiso ON 
                       ConceptoCompromiso.CodSubConcepto = compromisospago.CodConceptoCompromiso) 
                LEFT JOIN conceptoscobranza as ConceptoResultado ON 
                       ConceptoResultado.CodConcepto = compromisospago.CodConceptoResultCobro)
                WHERE compromisospago.CodGestion = $CodGestion";
        $Compromiso = new CompromisosPagoRecord();
        $Compromiso = CompromisosPagoRecord::finder('CompromisosPagoExtend')->findAllBySql($sql);

        return $Compromiso;
    }

    /**
     * Almacena en BBDD el concepto de cierre de un compromiso, el usuario que cierra, la fecha de cierre, y una 
     * observacion.
     * @param integer $CodCompromiso El codigo que identifica el compromiso
     * @param integer $Concepto El codigo del concepto de cierre
     * @param string $Observaciones Comentarios correspondientes al cierre, por defecto null
     * @param datetime $FechaCierre Fecha en la que se cierra el compromiso
     * @param string $Usuario el usuario que realiza el cierre
     * */
    public static function CerrarCompromiso($CodCompromiso, $Concepto, $Observaciones = NULL, $FechaResultado, $Usuario, $page) {
        $Compromiso = new CompromisosPagoRecord();
        $Compromiso = CompromisosPagoRecord::finder()->findByPk($CodCompromiso);
        $Compromiso->CodConceptoResultCobro = $Concepto;
        $Compromiso->ObservacionesResultado = $Observaciones;
        $Compromiso->UsuarioResultado = $Usuario;
        $Compromiso->FechaResultado = $FechaResultado;

        try {
            $Compromiso->save();
            LibGeneral::Completado($page, "Se ha cerrado el compromiso de pago");
        } catch (Exception $e) {
            LibGeneral::Error($this, "Se presento un error durante el proceso, por favor vuelva a intentarlo.");
        }
    }
    
    public static function Transaccion() {

        $Finder = CompromisosPagoRecord::finder();
        $Finder->DbConnection->Active = true; //open if necessary
        $Transaction = $Finder->DbConnection->beginTransaction();

        return $Transaction;
    }
}

class CompromisosPagoExtend extends CompromisosPagoRecord {

    public $ConceptoCompromiso;
    public $ConceptoResultado;

}

?>