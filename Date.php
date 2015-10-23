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
class ZradAid_Date
{

    public static function invert($date, $case = 1)
    {
        $dateInvertida = null;
        if (!empty($date)) {
            if ($case == 1) {   //tipo de 01-12-2005 a 2005-04-10
                $dateInvertida = substr($date, 6, 4) . "-" . substr($date, 3, 2) . "-" . substr($date, 0, 2);
            } elseif ($case == 2) {  //tipo de 2006-12-04 a 10-04-2005
                $dateInvertida = substr($date, 8, 2) . "/" . substr($date, 5, 2) . "/" . substr($date, 0, 4);
            }
        }
        return $dateInvertida;
    }

    /**
     * @param string $birthDate dd/mm/yyyy o yyyy/mm/dd
     * @return int $dYear
     */
    public static function getAge($birthDate)
    {
        $today = new Zend_Date();
        $birth = new Zend_Date($birthDate);
        $dYear = $today->get(Zend_Date::YEAR) - $birth->get(Zend_Date::YEAR);
        $dMonth = $today->get(Zend_Date::MONTH) - $birth->get(Zend_Date::MONTH);
        $dDay = $today->get(Zend_Date::DAY) - $birth->get(Zend_Date::DAY);
        if ($dMonth < 0) {
            $dYear--;
        } else if (($dMonth == 0) && ($dDay < 0)) {
            $dYear--;
        }
        return $dYear;
    }

    /**
     * @param date $date formato mes/dia/anio
     * @return date formato dia/mes/anio
     */
    public static function facebook($date)
    {
        //  fecha 01/31/1985
        $date = substr($date, 3, 2) . '/' . substr($date, 0, 2) . '/' . substr($date, 6, 4);
        return $date;
    }

    public static function getDateTime()
    {
        return date("Y-m-d H:i:s");
    }

    public function getNameDay($nDia, $idioma = 1)
    {
        $dia = "";
        switch ($nDia) {
            case 0 :
                $dia_esp = "Domingo";
                $dia_esp_a = "Dom";
                $dia_eng = "Sunday";
                $dia_eng_a = "Sun";
                break;
            case 1 :
                $dia_esp = "Lunes";
                $dia_esp_a = "Lun";
                $dia_eng = "Monday";
                $dia_eng_a = "Mon";
                break;
            case 2 :
                $dia_esp = "Martes";
                $dia_esp_a = "Mar";
                $dia_eng = "Tuesday";
                $dia_eng_a = "Tue";
                break;
            case 3 :
                $dia_esp = "Miercoles";
                $dia_esp_a = "Mie";
                $dia_eng = "Wednesday";
                $dia_eng_a = "Wed";
                break;
            case 4 :
                $dia_esp = "Jueves";
                $dia_esp_a = "Jue";
                $dia_eng = "Thursday";
                $dia_eng_a = "Thu";
                break;
            case 5 :
                $dia_esp = "Viernes";
                $dia_esp_a = "Vie";
                $dia_eng = "Friday";
                $dia_eng_a = "Fri";
                break;
            case 6 :
                $dia_esp = "Sabado";
                $dia_esp_a = "Sab";
                $dia_eng = "Saturday";
                $dia_eng_a = "Sat";
                break;
        }
        switch ($idioma) {
            case 1 : $dia = $dia_esp;
                break; //Enero
            case 2 : $dia = $dia_esp_a;
                break; //Ene
            case 3 : $dia = $dia_eng;
                break; //January
            case 4 : $dia = $dia_eng_a;
                break; //Jan
        }
        return $dia;
    }

    public function getNameMonth($nMes, $formato = 1)
    {

        $mes = "";
        switch ($nMes) {
            case 1 : $mes_esp = "Enero";
                $mes_esp_a = "Ene";
                $mes_eng = "January";
                $mes_eng_a = "Jan";
                break;
            case 2 : $mes_esp = "Febrero";
                $mes_esp_a = "Feb";
                $mes_eng = "February";
                $mes_eng_a = "Feb";
                break;
            case 3 : $mes_esp = "Marzo";
                $mes_esp_a = "Mar";
                $mes_eng = "March";
                $mes_eng_a = "Mar";
                break;
            case 4 : $mes_esp = "Abril";
                $mes_esp_a = "Abr";
                $mes_eng = "April";
                $mes_eng_a = "Apr";
                break;
            case 5 : $mes_esp = "Mayo";
                $mes_esp_a = "May";
                $mes_eng = "May";
                $mes_eng_a = "May";
                break;
            case 6 : $mes_esp = "Junio";
                $mes_esp_a = "Jun";
                $mes_eng = "June";
                $mes_eng_a = "Jun";
                break;
            case 7 : $mes_esp = "Julio";
                $mes_esp_a = "Jul";
                $mes_eng = "July";
                $mes_eng_a = "Jul";
                break;
            case 8 : $mes_esp = "Agosto";
                $mes_esp_a = "Ago";
                $mes_eng = "August";
                $mes_eng_a = "Aug";
                break;
            case 9 : $mes_esp = "Setiembre";
                $mes_esp_a = "Sep";
                $mes_eng = "September";
                $mes_eng_a = "Sep";
                break;
            case 10 : $mes_esp = "Octubre";
                $mes_esp_a = "Oct";
                $mes_eng = "October";
                $mes_eng_a = "Oct";
                break;
            case 11 : $mes_esp = "Noviembre";
                $mes_esp_a = "Nov";
                $mes_eng = "November";
                $mes_eng_a = "Nov";
                break;
            case 12 : $mes_esp = "Diciembre";
                $mes_esp_a = "Dic";
                $mes_eng = "December";
                $mes_eng_a = "Dec";
                break;
        }
        switch ($formato) {
            case 1 : $mes = $mes_esp;
                break; //Enero
            case 2 : $mes = $mes_esp_a;
                break; //Ene
            case 3 : $mes = $mes_eng;
                break; //January
            case 4 : $mes = $mes_eng_a;
                break; //Jan
        }
        return $mes;
    }

    public function getDisplayDate($fecha, $formato)
    {
        $fechaDisplay = "";
        $anio = substr($fecha, 0, 4);
        $mes = substr($fecha, 5, 2);
        $dia = substr($fecha, 8, 2);
        $nDia = date('w', mktime(0, 0, 0, $mes, $dia, $anio));

        switch ($formato) {
            case 1: $fechaDisplay = $this->getNameDay($nDia) . ", " . $dia . " de " . $this->getNameMonth($mes) . " del " . $anio;
                break;
            case 2: $fechaDisplay = $this->getNameDay($nDia) . ", " . $dia . " " . $this->getNameMonth($mes) . " " . $anio;
                break;
            case 3: $fechaDisplay = $dia . " de " . $this->getNameMonth($mes) . " del " . $anio;
                break;
            case 4: $fechaDisplay = $this->getNameMonth($mes) . ' ' . $dia . ", " . $anio;
                break;
        }
        return $fechaDisplay;
    }

    /**
     * 
     */
    public static function diferenciaEntreFechas($fechaPrincipal, $fechaSecundaria, $obtener = 'SEGUNDOS', $redondear = false)
    {
        $f0 = strtotime($fechaPrincipal);
        $f1 = strtotime($fechaSecundaria);

        if ($f0 && $f1) {
            $tmp = $f1;
            $f1 = $f0;
            $f0 = $tmp;
        }
        $resultado = ($f0 - $f1);

        switch ($obtener) {
            default: break;
            case 'MINUTOS' : $resultado = $resultado / 60;
                break;
            case 'HORAS' : $resultado = $resultado / 60 / 60;
                break;
            case 'DIAS' : $resultado = $resultado / 60 / 60 / 24;
                break;
            case 'SEMANAS' : $resultado = $resultado / 60 / 60 / 24 / 7;
                break;
        }

        if ($redondear)
            $resultado = round($resultado);

        return $resultado;
    }

    public static function getFechaDeRango($inicio, $fin, $formato = 'Y-m-d')
    {
        $fechas = array($inicio);
        while (end($fechas) < $fin) {
            $fechas[] = date('Y-m-d', strtotime(end($fechas) . ' +1 day'));
        }
        // Cambiamos de formato
        if ($formato != 'Y-m-d' && count($fechas) > 0) {
            foreach ($fechas as $i => $fecha) {
                $fechas[$i] = date($formato, strtotime($fecha));
            }
        }
        return $fechas;
    }

}
