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
class ZradAid_Controller_Plugin_LangSelector extends Zend_Controller_Plugin_Abstract
{

    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        if($request->getControllerName() == 'error'){
            $controller = 'index';
        }else{
            $controller = $request->getControllerName();
        }
        
        if($request->getActionName() == 'error'){
            $action = 'home';
        }else{
            $action = $request->getActionName();
        }
        
        $request = $request->getModuleName() . '/' . $controller . '/' . $action;                
        $viewRenderer = Zend_Controller_Action_HelperBroker::getExistingHelper('ViewRenderer');
        $viewRenderer->initView();
        $view = $viewRenderer->view;
        
        $front = Zend_Controller_Front::getInstance();
        $requestURL = $front->getBaseUrl() . '/' . $request;        
        $view->requestURL = $requestURL;        
    }

}
