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
class ZradAid_View_Helper_FormBirth extends Zend_View_Helper_FormElement
{
    protected $_html = '';

    public function formBirth($name, $value = null, $attribs = null)
    {
        //$info = $this->_getInfo($name, $value, $attribs);
        //extract($info); // name, id, value, attribs, yearStart, yearEnd, disable
        $day = $month = $year = '';

        $classAttribs['class'] = isset($attribs['class']) ? $attribs['class'] : array();
        $classAttribs['data-prompt-position'] = isset($attribs['data-prompt-position']) ? $attribs['data-prompt-position'] : array();

        if (is_array($value)) {
            $day = $value['day'];
            $month = $value['month'];
            $year = $value['year'];
        } else if (strtotime($value)) {
            //list($year, $month, $day) = explode('-', date('Y-m-d', strtotime($value)));
            list($year, $month, $day) = explode('-', date('Y-m-d', strtotime(str_replace('/', '-', $value))));
        }

        $maxAge = 90;
        if (isset($attribs['maxAge'])) {
            $maxAge = $attribs['maxAge'];
            unset($attribs['maxAge']);
        }

        $minAge = 5;
        if (isset($attribs['minAge'])) {
            $minAge = $attribs['minAge'];
            unset($attribs['minAge']);
        }

        $showMonths = 'text';
        if (isset($attribs['showMonths'])) {
            $showMonths = $attribs['showMonths'];
            unset($attribs['showMonths']);
        }

        $behavior = 'birth';
        if (isset($attribs['behavior'])) {
            $behavior = $attribs['behavior'];  // delivery            
        }

        $yearCurrent = date('Y');
        // Limites
        $yearEnd = $yearCurrent - $minAge;
        $yearStart = $yearCurrent - $maxAge;

        if ($behavior == 'delivery') {
            $yearStart = $yearCurrent;
            $yearEnd = $yearCurrent;
        }

        $default = false;
        if (isset($attribs['default'])) {
            $default = $attribs['default'];
            unset($attribs['default']);
        }

        $id = 1;
        if ($behavior == 'delivery') {
            $id = (int) date('j');
            $id++;
            //$months = array_slice($months, $monthCurrent - 1);
        }

        // Day
        $dayMultiOptions = ($default) ? array('' => 'Día') : array();
        for ($i = $id; $i <= 31; $i++) {
            // completa 0s a la izquierda
            $index = str_pad($i, 2, '0', STR_PAD_LEFT);
            $dayMultiOptions[$index] = $index;
        }

        // Month
        $months = array('Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre',
            'Octubre', 'Noviembre', 'Diciembre');

        $im = 1;
        if ($behavior == 'delivery') {
            $im = date('n');
            //$months = array_slice($months, $monthCurrent - 1);
        }

        $monthMultiOptions = ($default) ? array('' => 'Mes') : array();
        for ($i = $im; $i <= count($months); $i++) {
            // completa 0s a la izquierda
            $index = str_pad($i, 2, '0', STR_PAD_LEFT);
            //$monthMultiOptions[$index] = $months[$i - 1];
            
            switch ($showMonths) {
                case 'text': $monthMultiOptions[$index] =  $months[$i - 1]; 
                    break;
                case 'three': $monthMultiOptions[$index] = strtoupper(substr($months[$i - 1],0,3)); 
                    break;
                default: $monthMultiOptions[$index] = $index; 
                    break;
            }            
            //$monthMultiOptions[$index] = ($showMonths == 'text') ? $months[$i - 1] : $index;
        }

        $yearMultiOptions = ($default) ? array('' => 'Año') : array();
        for ($i = $yearStart; $i <= $yearEnd; $i++) {
            $yearMultiOptions[$i] = $i;
        }

        $this->_html .= $this->view->formSelect($name . '[day]', $day, $classAttribs, $dayMultiOptions) . "\n";
        $this->_html .= $this->view->formSelect($name . '[month]', $month, $classAttribs, $monthMultiOptions) . "\n";
        $this->_html .= $this->view->formSelect($name . '[year]', $year, $classAttribs, $yearMultiOptions);
        return $this->_html;
    }
}
