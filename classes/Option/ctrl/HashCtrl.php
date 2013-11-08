<?php

class Option_HashCtrl extends App_AbstractCtrl
{

    public function setAction()
    {
        if ( $this->_getParam( 'key' ) != '' &&
             $this->_getParam( 'value' ) != '' ) {
            
            $this->view->result = Option_Hash::setIfPublic (
                $this->_getParam( 'key' ),
                $this->_getParam( 'value' )
                );
        }
    }

    public function indexAction()
    {
        $arrFields = is_object( App_Application::getInstance()->getConfig()->options ) ?
			App_Application::getInstance()->getConfig()->options->toArray() : array();

        if ( $this->_isPost() ) {
            foreach ( $arrFields as $strField ) {
                if ( $this->_hasParam( $strField )) {
                    Option_Hash::set( $strField, $this->_getParam( $strField ) );
                }
            }
        }
        
        foreach ( $arrFields as $strField ) {
            $this->view->$strField  = Option_Hash::get( $strField );
        }
    }
}