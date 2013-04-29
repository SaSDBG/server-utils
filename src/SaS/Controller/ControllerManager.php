<?php

namespace SaS\Controller;

use SaS\Validation\Validator;
use SaS\Security\SecurityRequirementChecker;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

/**
 * Description of ControlerManager
 *
 * @author drak3
 */
class ControllerManager {
    
    protected $controllers = [];
    
    /**
     *
     * @var \SaS\Validation\Validator;
     */
    protected $validator;
    
    /**
     * SaS\Security\SecurityRequirementChecker
     */
    protected $securityChecker;
    
    public function __construct(Validator $v, SecurityRequirementChecker $s) {
        $this->validator = $v;
        $this->securityChecker = $s;
    }
    
    
    
    public function getControllers() {
        return $this->controllers;
    }

    public function setControllers($controllers) {
        $this->controllers = $controllers;
    }

    public function addControler(ControllerInterface $c) {
        $this->controllers[] = $c;
    }
    
    public function registerControllers(Application $app) {
        foreach($this->controllers as $c) {
            $this->registerController($c, $app);
        }
    }
    
    protected function registerController(ControllerInterface $c, Application $app) {
        $app->match($c->getRoute(), $c->getActionCallback())
            ->method($c->getMethod())
            ->before(function(Request $r) {
                return $this->handleRequest($r, $c, $app);
            });
    }
    
    public function handleRequest(Request $r, ControllerInterface $c, Application $app) {
        $data = array_merge($r->query->all(), $r->request->all());
        
        $validated = $this->validator->validate($data, $c->getRequestConstraints());
        
        if($validated instanceof \SaS\Validation\ValidationFailure) {
            return $this->buildRequestErrorResponse($validated->getErrors());
        } else {
            $data = $validated->get();
        }
        
        $c->setRequestData($data);
        
        $token = $r->get('token');
        $username = $r->get('user', '');
        $pass = $r->get('pass', '');
        
        if(!$this->securityChecker->isStatisfiedBy($c->getSecurityRequirements(), $token, $username, $pass)) {
            return $this->buildSecurityErrorResponse($c->getSecurityError());
        }
        
        return null; //null means success
    }
    
    protected function buildRequestErrorResponse(array $errors) {
        return ResponseBuilder::buildErrorResponse($errors, 400);
    }
    
    protected function buildSecurityErrorResponse(array $error) {
        return ResponseBuilder::buildErrorResponse([$error], 403);
    }
    
    
}

?>
