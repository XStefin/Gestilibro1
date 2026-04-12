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

    public function setUser(array $data): void
    {
        $nombre = $data['nombre'] ?? '';
        $apellido = $data['apellido'] ?? '';

        $this->userData = [
            'id_usuario' => $data['id_usuario'] ?? null,
            'correo' => $data['correo'] ?? null,
            'username' => $data['username'] ?? null,
            'rol' => $data['rol'] ?? null,
            'pin' => $data['pin'] ?? null,
            'active' => $data['active'] ?? 0,
            'nombreCompleto' => trim($nombre . ' ' . $apellido),
        ];

        $this->session->set($this->key, $this->userData);
    }

    public function getUser(): ?array
    {
        return $this->userData;
    }

    public function isLoggedIn(): bool
    {
        return !empty($this->userData) && !empty($this->userData['id_usuario']);
    }

    public function logout(): void
    {
        $this->userData = null;
        $this->session->remove($this->key);
        self::$instance = null;
    }
}