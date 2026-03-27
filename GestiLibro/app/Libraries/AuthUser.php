<?php
namespace App\Libraries;

use CodeIgniter\Session\SessionInterface;

class AuthUser
{
    private static ?AuthUser $instance = null;
    private ?array $userData = null;
    private SessionInterface $session;
    private string $key = 'auth_user';

    private function __construct(SessionInterface $session)
    {
        $this->session = $session;
        $this->userData = $this->session->get($this->key) ?: null;
    }

    public static function getInstance(?SessionInterface $session = null): AuthUser
    {
        $session = $session ?? \Config\Services::session();
        if (self::$instance === null) {
            self::$instance = new self($session);
        }
        return self::$instance;
    }

    public function setUser(array $data)
    {
        $this->userData = [
            'id_usuario' => $data['id_usuario'] ?? null,
            'correo' => $data['correo'] ?? null,
            'id_rol' => $data['id_rol'] ?? [],
            'nombreCompleto' => $data['nombre'].' '.$data['apellido'] ?? null,
        ];
        $this->session->set($this->key, $this->userData);
    }

    public function getUser(): ?array
    {
        return $this->userData;
    }

    public function isLoggedIn(): bool
    {
        return !empty($this->userData) && !empty($this->userData['id']);
    }

    public function logout()
    {
        $this->userData = null;
        $this->session->remove($this->key);
        self::$instance = null;
    }
}
