<?php

/**
 * Auto generated by prado-cli.php on 2011-03-15 10:34:11.
 */
class BancosRecord extends TActiveRecord {

    const TABLE = 'bancos';

    public $CodBanco;
    public $Nit;
    public $NmBanco;
    public $Cuenta;
    public $IdTipoCuenta;

    public static function finder($className = __CLASS__) {
        return parent::finder($className);
    }

    public static function Transaccion() {

        $Finder = BancosRecord::finder();
        $Finder->DbConnection->Active = true; //open if necessary
        $Transaction = $Finder->DbConnection->beginTransaction();

        return $Transaction;
    }

}

?>