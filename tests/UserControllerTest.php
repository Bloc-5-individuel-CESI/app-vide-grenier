<?php

use PHPUnit\Framework\TestCase;

class UserControllerTest extends TestCase
{
    protected function setUp(): void
    {
        $_SESSION = [];
        $_POST = [];
    }

    public function testRegisterAutoLogsInUser()
    {
        $_POST = [
            'submit' => '1',
            'email' => 'test@example.com',
            'username' => 'testuser',
            'password' => 'password123',
            'password-check' => 'password123'
        ];
    
        $controller = new class([]) extends \App\Controllers\User {
            public function __construct($route_params)
            {
                parent::__construct($route_params);
            }
    
            protected function register($data)
            {
                return 42; // id mocké
            }
    
            protected function login($data)
            {
                $_SESSION['user'] = [
                    'id' => 42,
                    'username' => $data['username']
                ];
                return true;
            }
        };
    
        // Eviter warning header()
        $this->expectOutputRegex('/.*/');
    
        $controller->registerAction();
    
        $this->assertArrayHasKey('user', $_SESSION);
        $this->assertEquals(42, $_SESSION['user']['id']);
        $this->assertEquals('testuser', $_SESSION['user']['username']);
    }    
    
}
