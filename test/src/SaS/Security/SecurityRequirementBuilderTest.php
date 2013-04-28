<?php

namespace SaS\Security;

class SecurityRequirementBuilderTest extends \PHPUnit_Framework_TestCase {


    public function testBuild() {
        $security  = new SecurityRequirementBuilder();
        $security->requires()
                    ->token('T_TOKEN')->with()
                    ->role('ROLE_IMPORTANT')
                 ->orRequires()
                    ->token('T_ANOTHER_TOKEN')->with()
                    ->role('ROLE_VERY_IMPORTANT');
        $requirement = $security->get();
        
        $expectedRequirement = [
            [
                'token_name' => 'T_TOKEN',
                'role' => 'ROLE_IMPORTANT'
            ],
            [
                'token_name' => 'T_ANOTHER_TOKEN',
                'role' => 'ROLE_VERY_IMPORTANT',
            ]
        ];
        
       $this->assertEquals($expectedRequirement, $requirement);
    }

}
