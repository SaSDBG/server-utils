<?php

namespace SaS\Controller;

use SaS\Validation\Validator;
use SaS\Security\SecurityRequirementChecker;

use Silex\Application;
use Silex\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Psr\Log\LoggerInterface;


/**
 * Description of ControlerManager
 *
 * @author drak3
 */
class ControllerManager implements ControllerProviderInterface {
    
    protected $controllers = [];
    
    /**
     *
     * @var \SaS\Validation\Validator;
     */
    protected $validator;
    
    /**
     * @var SaS\Security\SecurityRequirementChecker
     */
    protected $securityChecker;
    
    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;
    
    public function __construct(Validator $v, SecurityRequirementChecker $s, LoggerInterface $logger) {
        $this->validator = $v;
        $this->securityChecker = $s;
        $this->logger = $logger;
    }
    
    
    
    public function getControllers() {
        return $this->controllers;
    }

    public function setControllers($controllers) {
        $this->controllers = $controllers;
    }

    public function addController(ControllerInterface $c) {
        $this->controllers[] = $c;
    }
    
    public function connect(Application $app) {
        $controllers = $app['controllers_factory'];
        foreach($this->controllers as $c) {
            $this->registerController($c, $app, $controllers);
        }
        return $controllers;
    }
    
    protected function registerController(ControllerInterface $c, Application $app, \Silex\ControllerCollection $collection) {
        $collection->match($c->getRoute(), $c->getActionCallback())
            ->method($c->getMethod())
            ->before(function(Request $r) use ($c, $app) {
                return $this->handleRequest($r, $c, $app);
            });
    }
    
    public function handleRequest(Request $r, ControllerInterface $c, Application $app) {
        $context = [
            'clientIP' => $r->getClientIp(),
        ];
        
        $this->logger->debug('[ControllerManager] Handling Request', $context);
        
        $data = array_merge($r->query->all(), $r->request->all(), $r->attributes->get('_route_params', []));
        
        $validated = $this->validator->validate($data, $c->getRequestConstraints());
        
        if($validated instanceof \SaS\Validation\ValidationFailure) {
            $this->logger->error('[ControllerManager] Received Invalid Request', $context);
            return $this->buildRequestErrorResponse($validated->getErrors());
        } else {
            $data = $validated->get();
        }
        
        $c->setRequestData($data);
        
        $token = $r->get('token');
        $username = $r->get('user', '');
        $pass = $r->get('pass', '');
        
        if(!$this->securityChecker->isStatisfiedBy($c->getSecurityRequirements(), $token, $username, $pass)) {
            $context['token'] = $token;
            $context['user'] = $username;
            $this->logger->error('[ControllerManager] Request does not statisfy security Requirements', $context);
            return $this->buildSecurityErrorResponse($c->getSecurityError());
        }
        
        $this->logger->debug('[ControllerManager] Forwareded request to controller', $context);
        
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
