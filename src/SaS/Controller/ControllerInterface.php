<?php

namespace SaS\Controller;
use Symfony\Component\HttpFoundation\Request;
use Silex\Application;

/**
 *
 * @author drak3
 */
interface ControllerInterface {
    /**
     * 
     */
    public function getRoute();
    public function getMethod();
    public function getRequestConstraints();
    
    public function setRequestData(array $data);
    
    public function getSecurityRequirements();
    public function getSecurityError();
        
    public function getActionCallback();
}

?>
