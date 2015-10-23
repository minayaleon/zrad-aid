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
class ZradAid_Form_Element_Producto extends Zend_Form_Element_Xhtml
{
    public $helper = 'formProducto';
    protected $_productoFormato = '%nombre1%<br />%nombre2%';
    protected $_nombre1 = null;
    protected $_nombre2 = '';

    public function setNombre1($nombre1)
    {
        $this->_nombre1 = $nombre1;
    }

    public function setNombre2($nombre2)
    {
        $this->_nombre2 = $nombre2;
    }

    public function getNombre1()
    {
        return $this->_nombre1;
    }

    public function getNombre2()
    {
        return $this->_nombre2;
    }

    public function setValue($value)
    {
        if (is_string($value)) {
            $partes = explode('<br />', $value);
            $this->setNombre1($partes[0]);
            if (isset($partes[1])) {
                $this->setNombre2($partes[1]);
            }
        } else if (is_array($value) && (isset($value['nombre1']))) {
            $this->setNombre1($value['nombre1']);
            if (isset($value['nombre2'])) {
                $this->setNombre2($value['nombre2']);
            }
        } else {
            throw new Exception('Fecha invalida');
        }
        return $this;
    }

    public function getValue()
    {        
        if ($this->isRequired() || ((null !== $this->getNombre1()))) {
            return str_replace(
                array('%nombre1%', '%nombre2%'), array($this->getNombre1(), $this->getNombre2()), $this->_productoFormato
            );
        } else {
            return null;
        }
    }
}
