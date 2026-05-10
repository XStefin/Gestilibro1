<?php


namespace App\Filters;


use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;


class AuthCheck implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $authHeader = $request->getHeaderLine('Authorization');


        if (empty($authHeader)) {
            return service('response')
                ->setStatusCode(401)
                ->setJSON([
                    'message' => 'No autorizado'
                ]);
        }


        return null;
    }


    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        //
    }
}

