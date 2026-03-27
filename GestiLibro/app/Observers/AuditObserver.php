<?php
// app/Observers/AuditObserver.php
namespace App\Observers;

use CodeIgniter\Database\Config;

class AuditObserver implements ObserverInterface
{
    public function handle(string $evento, array $payload): void
    {   
        $db = \Config\Database::connect();
        $db->table('auditoria')->insert([
            'entidad'     => $payload['model'],
            'entidad_id'  => $payload['id'] ?? null,
            'accion'      => $evento,
            'detalle'     => json_encode($payload['data'] ?? []),
            'user_id'     => session()->get('user')['id_usuario'] ?? null,
            'fecha'       => date('Y-m-d H:i:s'),
        ]);
    }

    public static function onUserLoggedIn($user,$logout=null)
    {
        $accion = 'login';
        // Si usas tabla auditoría:
        if($logout){
            $accion = 'logout';
        }
        
        $db = \Config\Database::connect();
        $db->table('auditoria')->insert([
            'accion' => $accion,
            'entidad' => 'Login',
            'user_id' => $user['id_usuario'],
            'fecha' => date('Y-m-d H:i:s'),
            'detalle' => json_encode($user),
        ]);
    }
}
