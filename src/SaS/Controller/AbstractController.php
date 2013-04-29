<?php

namespace SaS\Controller;
use Symfony\Component\HttpFoundation\Request;
use Silex\Application;

/**
 * Description of AbstractControler
 *
 * @author drak3
 */
abstract class AbstractController implements ControllerInterface {
    
    //to be implemented by controllers
    
    protected $route;
    protected $method;
    
    public function getRequestConstraints();
    public function getSecurityRequirements();
    public function getSecurityError();
    
    protected $action;
    
    
    private $data;
    
    public function getRoute() {
        return $this->route;
    }
    
    public function getMethod() {
        return $this->method;
    }
    
    public function setRequestData(array $data) {
        $this->data = $data;
    }
    
    protected function getData() {
        if($this->data === NULL) {
            throw new Exception('Cannot access Request Data before request is validated');
        }
        return $this->data;
    }
      
    protected function security() {
        return new \SaS\Security\SecurityRequirementBuilder();
    }
    
    protected function constraintBuilder() {
        //todo: implement
    }
    
    public function getActionCallback() {
        return $this->action;
    }
}

?>
