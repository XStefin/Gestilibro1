<?php
namespace App\Controllers;
use App\Models\PrestamoModel;
use CodeIgniter\RESTful\ResourceController;

class LibroController extends ResourceController
{
    protected $modelName = 'App\Models\LibroModel';
    protected $format = 'json';

    public function index()
    {
        return $this->respond($this->model->findAll());
    }

    public function show($id = null)
    {
        $data = $this->model->find($id);

        if (!$data) {
            return $this->failNotFound('Libro no encontrado');
        }

        return $this->respond($data);
    }

    public function create()
    {
        $data = $this->request->getJSON(true);

        $this->model->insert($data);

        return $this->respondCreated($data);
    }

    public function update($id = null)
    {
        $data = $this->request->getJSON(true);

        $this->model->update($id, $data);

        return $this->respond(['message' => 'Libro actualizado']);
    }

    public function delete($id = null)
    {
        $PrestamoModel = new PrestamoModel();
        $Prestamo = $PrestamoModel->where('id_libro', $id)->first();
        if ($Prestamo) {
            return $this->fail([
                'nombre' => 'Existe un préstamo asociado a este libro, no se puede eliminar'
            ]);
        }
        $this->model->delete($id);

        return $this->respondDeleted(['message' => 'Libro eliminado']);
    }
}