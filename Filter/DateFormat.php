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
class ZradAid_Filter_DateFormat implements Zend_Filter_Interface
{

    /**
     * Devuelve la fecha en formato ISO 8601 on en formato espanol
     */
    public function filter($value)
    {
        if (!empty($value)) {
            if (Zend_Date::isDate($value, 'yyyy-MM-dd')) {
                $date = new Zend_Date($value, 'yyyy-MM-dd');
                $value = $date->get('dd/MM/yyyy');
            } else {
                $date = new Zend_Date($value, 'd/M/yyyy');
                $value = $date->get('yyyy-MM-dd');
            }
        }
        return $value;
    }

}
