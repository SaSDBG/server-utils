<?php

namespace SaS\Security;

/**
 *
 * @author drak3
 */
interface AuthenticatorInterface {
    public function isValidUser($user, $pass);
    public function userHasRole($user, $role);
}

?>
