<?php
namespace App\Controllers;
use CodeIgniter\RESTful\ResourceController;

class UsuarioController extends ResourceController
{
    protected $modelName = 'App\Models\UsuarioModel';
    protected $format = 'json';

    public function index()
    {
        return $this->respond($this->model->findAll());
    }

    public function show($id = null)
    {
        $data = $this->model->find($id);

        if (!$data) {
            return $this->failNotFound('Usuario no encontrado');
        }

        return $this->respond($data);
    }

    public function create()
    {
        $data = $this->request->getJSON(true);

        // Validaciones
        if ($this->model->where('correo', $data['correo'])->first()) {
            return $this->fail([
                'correo' => 'Este correo ya está registrado'
            ]);
        }

        if ($this->model->where('username', $data['username'])->first()) {
            return $this->fail([
                'username' => 'Este nombre de usuario ya está en uso'
            ]);
        }

        // Hash contraseña
        $data['contrasena'] = password_hash($data['contrasena'], PASSWORD_BCRYPT);

        // Guardar usuario
        $this->model->insert($data);
        $data['esRegistro'] = $data['esRegistro'] ? true : false; // Asegurar que la clave exista
        // 🔥 Detectar si es registro
        if (!empty($data['esRegistro']) && $data['esRegistro'] === true) {

            $email = \Config\Services::email();

            $email->setTo($data['correo']);
            $email->setSubject('Registro exitoso');
            $email->setMessage("
            Hola {$data['username']},
            Tu cuenta ha sido creada correctamente.
            Ya puedes iniciar sesión.")
            ;

            if (!$email->send()) {
                log_message('error', $email->printDebugger(['headers']));
            }
        }

        return $this->respondCreated([
            'message' => 'Usuario creado correctamente'
        ]);
    }

    public function update($id = null)
    {
        $data = $this->request->getJSON(true);
        $passwordHash = password_hash($data['contrasena'], PASSWORD_BCRYPT);
        $data['contrasena'] = $passwordHash;
        if (!$this->model->find($id)) {
            return $this->failNotFound('Usuario no encontrado');
        }

        $this->model->update($id, $data);

        return $this->respond(['message' => 'Usuario actualizado']);
    }

    public function delete($id = null)
    {
        if (!$this->model->find($id)) {
            return $this->failNotFound('Usuario no encontrado');
        }

        $this->model->delete($id);

        return $this->respondDeleted(['message' => 'Usuario eliminado']);
    }
}
