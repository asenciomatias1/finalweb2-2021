<?php

class ApiActividadController {
    private $clienteModel, $actividadModel, $apiView, $data;
    
    public function __construct() {
        $this->clienteModel = new ClienteModel();
        $this->actividadModel = new ActividadModel();
        $this->apiView = new ApiView();
        $this->data = file_get_contents("php://input");
    }

    public function getData(){
        return json_decode($this->data);
    }

    public function getActividadClientePeriodo($params = null){
        $idCliente = $params[":ID"];
        $fechaOrigen = $params[":FechaOrigen"];
        $fechaDestino = $params[":FechaDestino"];
        $cliente = $this->clienteModel->getClientePorID($idCliente);

        if ($cliente){
            $actividades = $this->actividadModel->getActividadesPeriodo($fechaOrigen, $fechaDestino);
            return $this->view->response($actividades, 200);
        }else {
            return $this->view->response("El cliente con el id=$idCliente no existe", 404);
        }
    }
}
