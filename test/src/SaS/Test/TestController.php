<?php

namespace SaS\Test;

use SaS\Controller\AbstractController;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Silex\Application;

class TestController extends AbstractController {
    
    protected $route = '/accounts/{accountID}';
    protected $method = 'GET';
    
    public function getRequestConstraints() {
        return [
            'accountID' => [
                'int' => [100, 'accountID must be an int']
            ],
            'token' => [
                'required' => [101, 'token is required'],
                'token' => [102, 'token is incorrect'],
            ],
            'user' => [
                'required' => [103, 'user is required'],
                'valid-chars' => [104, 'user must be valid'],
            ],
            'pass' => [
                'required' => [105, 'pass is required'],
                'sha1' => [106, 'pass is invalid']
            ],
        ];
    }
    
    public function getSecurityRequirements() {
        //$accountSpecToken = sprintf('TB_ACCOUNT_SPEC_%s', $this->getData()['accountID']);
        //$accountSpecRole = sprintf('ROLE_ACCOUNT_OWNER_%s', $this->getData()['accountID']);
                
        return $this->security()
                ->requires()
                    ->token('TB_ACCOUNT')->with()
                    ->role('ROLE_BANK_ADMIN')
                //->orRequires()
                //    ->token($accountSpecToken)->with()
                //    ->role($accountSpecRole)
              ->get();
    }
    
    public function getSecurityError() {
        return [107, 'Invalid token or credentials'];
    }
    
    public function action(Application $app, Request $r) {
        return new JsonResponse($this->getData());
    }
} 

?>
