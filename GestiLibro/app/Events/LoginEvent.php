<?php

namespace App\Events;

class LoginEvent
{
    public string $username;
    public array $userData;
    public string $timestamp;

    public function __construct(string $username, array $userData)
    {
        $this->username = $username;
        $this->userData = $userData;
        $this->timestamp = date('Y-m-d H:i:s');
    }
}
