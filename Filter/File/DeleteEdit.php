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
class ZradAid_Filter_File_DeleteEdit implements Zend_Filter_Interface
{
    
    /**
     * @var string
     */
    private $_field;

    public function __construct($field)
    {        
        $this->_field = $field;
    }

    public function filter($value)
    {
        $field = $this->_field;
        if (file_exists($value) && Zend_Registry::isRegistered('configFiles')) {
            // Eliminamos el archivo anterior
            $formSession = new Zend_Session_Namespace('formSession');
            if (isset($formSession->elements[$field]['path'])) {
                $filename = $formSession->elements[$field]['path'];
                // verificamos si esta vacio
                if (!empty ($filename)) {
                    unlink($filename);
                }                
            }
            return $value;
        }
    }
}
