<?php

namespace SaS\Security;

/**
 * Throws exceptions when called, use when no User/Role-System is used
 *
 * @author drak3
 */
class NullAuthenticator implements AuthenticatorInterface {
    
    public function isValidUser($user, $pass) {
        throw new \LogicException("Called isValidUser on NullAuthenticator");
    }

    public function userHasRole($user, $role) {
        throw new \LogicException("Called userHasRole on NullAuthenticator");
    }

}

?>
