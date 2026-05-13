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
    $rawBody = $this->request->getBody();

    $data = json_decode($rawBody, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        return $this->response->setStatusCode(400)->setJSON([
            'error' => 'JSON inválido',
            'detalle' => json_last_error_msg(),
            'body_recibido' => $rawBody,
            'content_type' => $this->request->getHeaderLine('Content-Type')
        ]);
    }

    if (!isset($data['nombre']) || trim($data['nombre']) === '') {
        return $this->response->setStatusCode(400)->setJSON([
            'error' => 'El campo nombre es obligatorio'
        ]);
    }

    if ($this->model->where('nombre', $data['nombre'])->first()) {
        return $this->response->setStatusCode(409)->setJSON([
            'error' => 'Esta categoría ya está en uso'
        ]);
    }

    $this->model->insert([
        'nombre' => $data['nombre'],
        'descripcion' => $data['descripcion'] ?? null
    ]);

    return $this->respondCreated([
        'message' => 'Categoría creada correctamente',
        'data' => [
            'id_categoria' => $this->model->getInsertID(),
            'nombre' => $data['nombre'],
            'descripcion' => $data['descripcion'] ?? null
        ]
    ]);
}




    public function update($id = null)
    {
        $data = $this->request->getJSON(true);


        $categoria = $this->model->find($id);


        if (!$categoria) {
            return $this->failNotFound('Categoría no encontrada');
        }


        if (isset($data['nombre']) && trim($data['nombre']) !== '') {
            $categoriaExistente = $this->model
                ->where('nombre', $data['nombre'])
                ->where('id_categoria !=', $id)
                ->first();


            if ($categoriaExistente) {
                return $this->fail([
                    'error' => 'Esta categoría ya está en uso'
                ]);
            }
        }


        $this->model->update($id, $data);


        return $this->respond([
            'message' => 'Categoría actualizada'
        ]);
    }




    public function delete($id = null)
    {
        $LibroModel = new LibroModel();
        $Libro = $LibroModel->where('id_categoria', $id)->first();
        if ($Libro) {
            return $this->fail([
                'error' => 'Existe un libro asociado a esta categoría, no se puede eliminar'
            ]);
        }
        $this->model->delete($id);




        return $this->respondDeleted(['message' => 'Categoría eliminada']);
    }
}







