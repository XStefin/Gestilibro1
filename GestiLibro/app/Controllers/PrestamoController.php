<?php
namespace App\Controllers;
use CodeIgniter\RESTful\ResourceController;
use App\Models\PrestamoModel;
use App\Models\LibroModel;
use App\Models\UsuarioModel;
class PrestamoController extends ResourceController
{
    protected $modelName = 'App\Models\PrestamoModel';
    protected $format = 'json';

    public function index()
    {
        return $this->respond($this->model->findAll());
    }

    public function show($id = null)
    {
        return $this->respond($this->model->find($id));
    }

    public function create()
    {
        $data = $this->request->getJSON(true);

        $libroModel = new LibroModel();

        $libroPrestado = $this->model
            ->where('id_usuario', $data['id_usuario'])
            ->where('id_libro', $data['id_libro'])
            ->where('estado', 'prestado')
            ->first();

        $libro = $libroModel->find($data['id_libro']);
        
        if ($libroPrestado) {
            return $this->fail('El usuario ya tiene este libro prestado');
        }

        if (!$libro) {
            return $this->failNotFound('Libro no encontrado');
        }

        if ($libro['cantidad'] <= 0) {
            return $this->fail('No hay stock disponible');
        }

        $libroModel->set('cantidad', 'cantidad - 1', false)
            ->where('id_libro', $data['id_libro'])
            ->update();
        $this->model->insert($data);

        return $this->respondCreated([
            'message' => 'Préstamo creado correctamente'
        ]);
    }

    public function update($id = null)
    {
        $data = $this->request->getJSON(true);
        $this->model->update($id, $data);

        return $this->respond(['message' => 'Préstamo actualizado']);
    }

    public function delete($id = null)
    {
        $this->model->delete($id);

        $PrestamoModel = new PrestamoModel();
        $Prestamo = $PrestamoModel->where('id_libro', $id)->first();
        if ($Prestamo) {
            return $this->fail([
                'nombre' => 'Existe un préstamo asociado a este libro, no se puede eliminar'
            ]);
        }
        return $this->respondDeleted(['message' => 'Préstamo eliminado']);
    }
}