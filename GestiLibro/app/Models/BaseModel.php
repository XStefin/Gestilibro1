<?php
// app/Models/BaseModel.php
namespace App\Models;

use CodeIgniter\Model;
use Config\Services;

class BaseModel extends Model
{
    /**
     * Lista de observadores (clases con namespace) que se ejecutarán.
     * Puedes sobrescribir en modelos concretos para observadores por modelo.
     * Ej: protected $observers = ['App\Observers\AuditObserver'];
     */
    protected $observers = [];

    // Hooks de CodeIgniter que se ejecutarán automáticamente si los nombres existen
    protected $afterInsert  = ['notifyAfterInsert'];
    protected $afterUpdate  = ['notifyAfterUpdate'];
    protected $afterDelete  = ['notifyAfterDelete'];

    // Devuelve la lista final de observadores (global + modelo)
    protected function getObservers(): array
    {
        // Obtener observadores globales desde Services (opcional)
        $global = [];
        if (function_exists('service')) {
            $registry = Services::modelsObserverRegistry(false);
            if ($registry && is_array($registry)) {
                $global = $registry;
            }
        }

        // Combina y evita duplicados
        return array_values(array_unique(array_merge($global, $this->observers)));
    }

    // Notificador genérico
    protected function notify(string $evento, array $data): void
    {
        $observers = $this->getObservers();

        foreach ($observers as $observerClass) {
            if (!class_exists($observerClass)) continue;
            $obs = new $observerClass();
            if (method_exists($obs, 'handle')) {
                try {
                    $obs->handle($evento, $data);
                } catch (\Throwable $e) {
                    // loguea si quieres
                    log_message('error', "Observer {$observerClass} falló: {$e->getMessage()}");
                }
            }
        }
    }

    // Callbacks que CodeIgniter ejecuta y que a su vez notifican
    protected function notifyAfterInsert(array $data)
    {
        $id = $data['id'] ?? null;
        $payload = [
            'model'   => static::class,
            'id'      => $id,
            'data'    => $data['data'] ?? $data,
            'user_id' => session()->get('id') ?? null,
        ];
        $this->notify('insert', $payload);
        return $data;
    }

    protected function notifyAfterUpdate(array $data)
    {
        // $data suele incluir 'id' y 'data' según CI
        $id = $data['id'] ?? null;
        $payload = [
            'model'   => static::class,
            'id'      => is_array($id) ? ($id[0] ?? null) : $id,
            'data'    => $data['data'] ?? $data,
            'user_id' => session()->get('id') ?? null,
        ];
        $this->notify('update', $payload);
        return $data;
    }

    protected function notifyAfterDelete(array $data)
    {
        $id = $data['id'] ?? null;
        $payload = [
            'model'   => static::class,
            'id'      => is_array($id) ? ($id[0] ?? null) : $id,
            'data'    => $data,
            'user_id' => session()->get('id') ?? null,
        ];
        $this->notify('delete', $payload);
        return $data;
    }
}
