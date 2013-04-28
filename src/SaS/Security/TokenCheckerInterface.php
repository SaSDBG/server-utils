<?php

namespace SaS\Security;

/**
 *
 * @author drak3
 */
interface TokenCheckerInterface {
    public function isToken($name, $token);
}

?>
