<?php

/**
 * Zend Rad Aid
 *
 * LICENCIA
 *
 * Este archivo está sujeta a la licencia CC(Creative Commons) que se incluye
 * en LICENCIA.txt.
 * Tambien esta disponible a traves de la Web en la siguiente direccion
 * http://www.zend-rad.com/licencia/
 * Si usted no recibio una copia de la licencia por favor envie un correo
 * electronico a <licencia@zend-rad.com> para que podamos enviarle una copia
 * inmediatamente.
 *
 * @author Juan Minaya Leon <info@juanminaya.com>
 * @copyright Copyright (c) 2011-2012 , Juan Minaya Leon
 * (http://www.zend-rad.com)
 * @licencia http://www.zend-rad.com/licencia/   CC licencia Creative Commons
 */
class ZradAid_Validate_CompareDate extends Zend_Validate_Abstract
{

    const MSG_COMPARE = 'msgCompare';

    private $_date = null;
    
    protected $_messageTemplates = array(
        self::MSG_COMPARE => "fecha de salida es menor a la fecha de arribo",
    );

    public function __construct($date)
    {
        $date = explode('/', $date);
        $this->_date = $date[2] . '-' . $date[1] . '-' . $date[0];
    }

    public function isValid($value)
    {
        $isValid = true;
        $this->_setValue($value);
        $value = $pieces = explode('/', $value);
        $value = $value[2] . '-' . $value[1] . '-' . $value[0];
        $d1 = $value;
        $d2 = $this->_date;

        $d1 = (is_string($d1) ? strtotime($d1) : $d1);
        $d2 = (is_string($d2) ? strtotime($d2) : $d2);
        $diff_secs = ($d1 - $d2);

        if ($diff_secs < 0) {
            $this->_error(self::MSG_COMPARE);
            $isValid = false;
        }

        return $isValid;
    }

}
