<?php
/**
 * 
 */
class ConceptosCobranzaRecord extends TActiveRecord
{
	const TABLE='conceptoscobranza';

	public $CodConcepto;
	public $Descripcion;
	public $Comentarios;
        public $Tipo;

	public static function finder($className=__CLASS__)
	{
		return parent::finder($className);
	}
        
        /**
         * Devuelve un objeto con los conceptos de gestion.
         * @param integer $intTipo El tipo de conceptos a mostar
         * @example 1 Conceptos de cobranza, 2 Conceptos de compromisos de pago
         * @return Objeto con todos los conceptos de gestion de cobranza
         **/
        public static function getConceptosGestion($intTipo)    
        {
            $Conceptos = new ConceptosCobranzaRecord();
            $Conceptos = ConceptosCobranzaRecord::finder()->findAllBy_CodConcepto($intTipo);
            
            return $Conceptos;
        }
        
        public static function getConceptosCierreCompromiso()
        {
            $Concepto = new ConceptosCobranzaRecord();
            //$Concepto = ConceptosCobranzaRecord::finder()->findAllBy_Tipo(3);
            return $Concepto;
        }
}
?>