<?php
use PHPUnit\Framework\TestCase;
use App\Models\User;

class RememberMeTest extends TestCase
{
    private $userId;

    protected function setUp(): void
    {
        // Crée un utilisateur factice en base
        $salt = bin2hex(random_bytes(16));
        $hashedPassword = \App\Utility\Hash::generate('testpassword', $salt);

        $this->userId = User::createUser([
            'username' => 'rememberTest',
            'email' => 'remember@test.com',
            'password' => $hashedPassword,
            'salt' => $salt
        ]);
    }

    public function testRememberMeStoresToken()
    {
        $token = bin2hex(random_bytes(32));
        $expires = time() + 3600;

        // Stocke le token en base
        User::storeRememberToken($this->userId, $token, $expires);

        // Va chercher l’utilisateur avec ce token
        $user = User::getByRememberToken($token);

        $this->assertNotEmpty($user);
        $this->assertEquals($this->userId, $user['id']);
    }

    public function testRememberMeTokenExpires()
    {
        $token = bin2hex(random_bytes(32));
        $past = time() - 3600;

        // Stocke un token déjà expiré
        User::storeRememberToken($this->userId, $token, $past);

        $user = User::getByRememberToken($token);

        $this->assertFalse($user); // ou assertEmpty selon ta méthode
    }

    protected function tearDown(): void
    {
        $db = \App\Models\User::getDB();
        $db->prepare("DELETE FROM users WHERE id = ?")->execute([$this->userId]);
    }
}
