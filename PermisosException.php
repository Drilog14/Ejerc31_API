<?php

 class PermisosException extends Exception {
    
 	var $strTipo;
 	
    public function __construct($code = 0, $message) {
    	$this->strTipo = 'PermisosException';
        parent::__construct($message, $code);
    }

    public function __toString() {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }

     public function get_mensaje() {
       return 'Error '.$this->code.' ['.$this->strTipo.']: '.$this->message;
    }
}
?>