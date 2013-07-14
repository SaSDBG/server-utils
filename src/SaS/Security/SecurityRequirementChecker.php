<?php

namespace SaS\Security;
use SaS\Token\TokenRegistryInterface;

/**
 * Description of SecurityRequirements
 *
 * @author drak3
 */
class SecurityRequirementChecker {
    
    protected $tc;
    
    protected $auth;
    
    public function __construct(TokenRegistryInterface  $tc, AuthenticatorInterface $auth) {
        $this->tc = $tc;
        $this->auth = $auth;
    }
    
    public function isStatisfiedBy(array $req, $token, $username='', $pass='') {
        return true;
        foreach ($req as $requirement) {
            if($this->tc->isToken($requirement['token_name'], $token) ) {
                if($requirement['role'] === '' )  {
                    return true;
                }
                if( $this->isStatisfiedByUser($requirement['role'], $username, $pass)) {
                    return true;
                }
            } 
        }
        return false;
    }
    
    protected function isStatisfiedByUser($role, $username, $pass) {
        if($username === '' || $pass === '') {
            return false;
        }
        
        if(!$this->auth->isValidUser($username, $pass)) {
            return false;
        }
        
        if(!$this->auth->userHasRole($username, $role)) {
            return false;
        }
        
        return true;
    }
}

?>
