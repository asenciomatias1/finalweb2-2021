<?php

class ApiTarjetasController {
    private $clienteModel, $tarjetaModel, $apiView, $data;
    
    public function __construct() {
        $this->clienteModel = new ClienteModel();
        $this->tarjetaModel = new TarjetaModel();
        $this->apiView = new ApiView();
        $this->data = file_get_contents("php://input");
    }

    public function getData(){
        return json_decode($this->data);
    }

    public function getTarjetasCliente($params = null){
        $idCliente = $params["ID"];
        $cliente = $this->clienteModel->getClientePorID($idCliente);

        if ($cliente){
            $tarjetas = $this->tarjetaModel->getTarjetasCliente($idCliente);
            return $this->view->response($tarjetas, 200);
        }else {
            return $this->view->response("El cliente con el id=$idCliente no existe", 404);
        }
    }

    public function deleteTarjeta($params = null){
        $idTarjeta = $params["ID"];
        $tarjeta = $this->tarjetaModel->getTarjeta($idTarjeta);

        try {
            if ($tarjeta){
                $this->tarjetaModel->deleteTarjeta($idTarjeta);
                return $this->view->response("La tarjeta con el id=$idTarjeta se borro exitosamente", 200);
            }else {
                return $this->view->response("La tarjeta con el id=$idTarjeta no existe", 404);
            }
        } catch (\Throwable $th) {
            return $this->view->response("No se pudo borrar la tarjeta", 501);
        }
    }
}
