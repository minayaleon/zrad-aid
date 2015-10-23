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
class ZradAid_Form_Element_Birth extends Zend_Form_Element_Xhtml
{

    public $helper = 'formBirth';    
    protected $_dateFormat = '%day%-%month%-%year%';

    /**
     * @var int
     */
    protected $_day = null;
    protected $_month = null;
    protected $_year = null;
    protected $_yearStart = null;
    protected $_yearEnd = null;

    public function setYearStart($yearStart)
    {
        $this->_yearStart = $yearStart;
    }

    public function setYearEnd($yearEnd)
    {
        $this->_yearEnd = $yearEnd;
    }

    public function setDay($day)
    {
        $this->_day = $day;
        return $this;
    }

    public function getDay()
    {
        return $this->_day;
    }

    public function setMonth($month)
    {
        $this->_month = $month;
        return $this;
    }

    public function getMonth()
    {
        return $this->_month;
    }

    public function setYear($year)
    {
        $this->_year = $year;
        return $this;
    }

    public function getYear()
    {
        return $this->_year;
    }

    public function setValue($value)
    {
        if (is_int($value)) {
            $this->setDay(date('d', $value))
                ->setMonth(date('m', $value))
                ->setYear(date('Y', $value));
        } else if (is_string($value)) {
            if (!Zend_Date::isDate($value, 'yyyy-MM-dd')) {
                $date = new Zend_Date($value, 'd/M/yyyy');
                $value = $date->get('yyyy-MM-dd');
            }            
            $date = strtotime($value);
            $this->setDay(date('d', $date))
                ->setMonth(date('m', $date))
                ->setYear(date('Y', $date));
        } else if (is_array($value) && (isset($value['day']) && isset($value['month']) && isset($value['year']))) {            
            $this->setDay($value['day'])
                ->setMonth($value['month'])
                ->setYear($value['year']);
        } else {
            throw new Exception('Fecha invalida');
        }
        return $this;
    }

    public function getValue()
    {
		$yearP = $this->getYear();
		$monthP = $this->getMonth();
		$dayP = $this->getDay();	
        
		if ($this->isRequired() || ((!empty($yearP)) && (!empty($monthP)) && (!empty($dayP)))) {
            return str_replace(
                    array('%day%', '%month%', '%year%'), array($this->getDay(), $this->getMonth(), $this->getYear()), $this->_dateFormat
            );
        } else {
            return null;
        }
    }

}
