<?php
// app/Observers/ObserverInterface.php
namespace App\Observers;

interface ObserverInterface
{
    /**
     * @param string $evento  ('insert', 'update', 'delete', etc.)
     * @param array  $payload datos útiles: ['model' => $modelClass, 'id' => $id, 'data' => $data, 'user_id' => ...]
     */
    public function handle(string $evento, array $payload): void;
}
