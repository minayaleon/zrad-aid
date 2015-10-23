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
require_once 'ZradAid/Image/PHPThumb/ThumbLib.inc.php';

class ZradAid_Filter_File_Resize implements Zend_Filter_Interface
{

    private $_entity;
    private $_field;

    public function __construct($entity, $field)
    {
        $this->_entity = $entity;
        $this->_field = $field;
    }

    public function filter($value)
    {
        if (file_exists($value) && Zend_Registry::isRegistered('configImages')) {
            // Get entity
            $configImages = Zend_Registry::get('configImages');
            $entity = $this->_entity;
            $field = $this->_field;
            $image = $configImages->thumb->$entity->$field;
            // Resize
            $type = $image->resize->type;
            $thumb = PhpThumbFactory::create($value);
            $width = $image->resize->width;
            $height = $image->resize->height;
            if ($type == 'exact') {
                //echo 'adaptiveResize';
                $thumb->adaptiveResize($width, $height);
            } else {
                $thumb->resize($width, $height);
            }
            // Save
            $thumb->save($value);

            // Eliminamos el archivo anterior
            $formSession = new Zend_Session_Namespace('formSession');
            if (isset($formSession->elements[$field]['path'])) {
                $filename = $formSession->elements[$field]['path'];
                // verificamos si esta vacio
                if (!empty($filename) && file_exists($filename)) {
                    //echo 'Eliminado:' . $filename;
                    unlink($filename);
                }
            }
            return $value;
        }
    }

}
