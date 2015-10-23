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
class ZradAid_Filter_Age implements Zend_Filter_Interface
{

    /**
     * @param date $value En formato yyyy-mm-dd
     */
    public function filter($value)
    {
        $birth = new Zend_Date($value);
        $today = new Zend_Date();
        $diff = $today->sub($birth)->toValue();
        $value = floor($diff / 3600 / 24 / 365);
        return $value;
    }

}
