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
class ZradAid_View_Helper_FormProducto extends Zend_View_Helper_FormElement
{
    protected $_html = '';

    public function formProducto($name, $value = null, $attribs = null)
    {
        $nombre1 = $nombre2 = '';

        $classAttribs['class'] = isset($attribs['class']) ? $attribs['class'] : array();
        $classAttribs['data-prompt-position'] = isset($attribs['data-prompt-position']) ? $attribs['data-prompt-position'] : array();

        if (is_array($value)) {
            $nombre1 = $value['nombre1'];
            $nombre2 = $value['nombre2'];            
        } else if (is_string($value)) {                        
            list($nombre1, $nombre2) = explode('<br />', $value);
        }

        $this->_html .= '<div class="ui-input-50">' . $this->view->formText($name . '[nombre1]', $nombre1, $classAttribs) . '</div>';
        $this->_html .= '<div class="ui-input-50">' . $this->view->formText($name . '[nombre2]', $nombre2, $classAttribs) . '</div>';        
        return $this->_html;
    }
}
