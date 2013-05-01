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
    
    abstract public function getRequestConstraints();
    abstract public function getSecurityRequirements();
    abstract public function getSecurityError();
    abstract public function action(Application $app, Request $r);
    
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
        if($this->data === null) {
            throw new \Exception('Cannot access Request Data before request is validated');
        }
        return $this->data;
    }
      
    protected function security() {
        return new \SaS\Security\SecurityRequirementBuilder();
    }
        
    public function getActionCallback() {
        return [$this, 'action'];
    }
}

?>
