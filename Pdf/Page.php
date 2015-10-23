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
class ZradAid_Pdf_Page extends Zend_Pdf_Page implements ZradAid_Pdf_Interface
{

    /**
     * @var float
     */
    protected $_margin = 0.00;
    /**
     * @var array
     */
    protected $_header;
    /**
     * @var array
     */
    protected $_footer;

    public function getFooter()
    {
        return $this->_footer;
    }

    public function setFooter($footer)
    {
        $this->_footer = $footer;
    }

    /**
     * init function
     */
    public function init()
    {
        
    }

    /**
     * @param array $header
     */
    public function setHeader($header)
    {
        $this->_header = $header;
    }

    public function getHeader()
    {
        return $this->_header;
    }

    public function setMargin($margin)
    {
        $this->_margin = $margin;
    }

    public function getMargin()
    {
        return $this->_margin;
    }

    /**
     * Dibuja el footer del documento
     */
    public function drawFooter()
    {
        
    }

    public function drawHeader()
    {
        $position = array('x2' => 0, 'y2' => 0);
        return $position;
    }

}