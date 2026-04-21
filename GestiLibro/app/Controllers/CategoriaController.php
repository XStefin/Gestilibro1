<?php
namespace App\Controllers;
use App\Models\CategoriaModel;
use App\Models\LibroModel;
use CodeIgniter\RESTful\ResourceController;

class CategoriaController extends ResourceController
{
    protected $modelName = 'App\Models\CategoriaModel';
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
        if ($this->model->where('nombre', $data['nombre'])->first()) {
            return $this->fail([
                'nombre' => 'Esta categoría ya está en uso'
            ]);
        }
        $this->model->insert($data);

        return $this->respondCreated($data);
    }

    public function update($id = null)
    {
        $data = $this->request->getJSON(true);
        if ($this->model->where('nombre', $data['nombre'])->first()) {
            return $this->fail([
                'nombre' => 'Esta categoría ya está en uso'
            ]);
        }
        $this->model->update($id, $data);

        return $this->respond(['message' => 'Categoría actualizada']);
    }

    public function delete($id = null)
    {
        $LibroModel = new LibroModel();
        $Libro = $LibroModel->where('id_categoria', $id)->first();
        if ($Libro) {
            return $this->fail([
                'nombre' => 'Existe un libro asociado a esta categoría, no se puede eliminar'
            ]);
        }
        $this->model->delete($id);

        return $this->respondDeleted(['message' => 'Categoría eliminada']);
    }
}