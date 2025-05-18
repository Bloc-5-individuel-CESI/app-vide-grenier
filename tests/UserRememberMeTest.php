<?php

use PHPUnit\Framework\TestCase;
use App\Models\User;

class UserRememberMeTest extends TestCase
{
    private static $userId;
    private static $token;
    private static $expires;

    public static function setUpBeforeClass(): void
    {
        // Crée un utilisateur fictif
        $username = 'testuser_' . uniqid();
        $email = $username . '@example.com';
        $password = password_hash('password123', PASSWORD_DEFAULT);
        $salt = bin2hex(random_bytes(8));

        self::$userId = User::createUser([
            'username' => $username,
            'email' => $email,
            'password' => $password,
            'salt' => $salt,
        ]);
    }

    public function testStoreRememberToken()
    {
        self::$token = bin2hex(random_bytes(16));
        self::$expires = time() + 3600; // expire dans 1h

        User::storeRememberToken(self::$userId, self::$token, self::$expires);

        $user = User::getByRememberToken(self::$token);

        $this->assertIsArray($user);
        $this->assertEquals(self::$userId, $user['id']);
    }

    public function testClearRememberToken()
    {
        User::clearRememberToken(self::$userId);

        $user = User::getByRememberToken(self::$token);
        $this->assertFalse($user); // PDO::fetch retourne false si aucune ligne trouvée
    }
}
