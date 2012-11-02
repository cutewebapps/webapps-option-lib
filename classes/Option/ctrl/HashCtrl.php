<?php

class Option_HashCtrl extends App_AbstractCtrl
{
    public function indexAction()
    {
    }

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
}