<?php
require_once "ClienteModel.php";
require_once "ActividadModel.php";
require_once "TarjetaModel.php";

// CLIENTE(id: int; nombre: string, dni: string, telefono: string, direccion: string,
// ejecutivo: boolean)

class ClienteController {
    private $view, $clienteModel, $actividadModel, $tarjetaModel, $authHelper, $cardHelper;

    public function __construct() {
        $this->view = new View();
        $this->clienteModel = new ClienteModel();
        $this->actividadModel = new ActividadModel();
        $this->tarjetaModel = new TarjetaModel();
        $this->authHelper = new AuthHelper();
        $this->cardHelper = new CardHelper();
    }

    private function parametrosValidos($array){
        if (isset($array)){
            foreach ($array as $var) {
                // Quiero $var === 0
                if ($var !== 0 && empty($var)){
                    return false;
                }
            }

            return true;
        }

        return false;
    }

    public function addCliente(){
        $this->authHelper->verifyLogin();

        if ($this->authHelper->esAdmin()){
            $existeCliente = $this->clienteModel->getClientePorDni($_POST["dni"]);

            if (!$existeCliente){
                $datosCliente = [$_POST["nombre"], $_POST["dni"], 
                    $_POST["telefono"], $_POST["direccion"], $_POST["ejecutivo"]];

                if ($this->parametrosValidos($datosCliente)){
                    $this->clienteModel->addCliente($_POST["nombre"], $_POST["dni"], 
                        $_POST["telefono"], $_POST["direccion"], $_POST["ejecutivo"]);

                    // Busco el cliente recientemente agregado para usar su ID
                    $clienteAgregado = $this->clienteModel->getClientePorDni($_POST["dni"]);

                    // Agrego una actividad de suma de puntos con 200kms
                    $this->actividadModel->addActividad(200, $_POST["fechaActual"], "suma", $clienteAgregado->id);
                    
                    if ($clienteAgregado->ejecutivo){
                        // Datos de la tarjeta a agregar
                        $tarjeta = $this->cardHelper->getBussinesCard();
                        $this->tarjetaModel->addTarjeta($tarjeta->alta, $tarjeta->nro, 
                            $tarjeta->vencimiento, $tarjeta->tipo, $clienteAgregado->id);
                    }

                }else {
                    $this->view->showMensaje("Los datos ingresados son incorrectos");
                }
            }else {
                $this->view->showMensaje("Ya existe un cliente con este numero de DNI");
            }
        }else {
            $this->view->showMensaje("No tenes permiso de realizar esta accion");
        }
    }

    public function showResumenCliente(){
        $dniCliente = $_POST["dni"];
        $cliente = $this->clienteModel->getClientePorDni($dniCliente);

        if ($cliente){
            $nombreCliente = $cliente->nombre;
            $kmsSumados = $this->actividadModel->getKilometrosSumados($cliente->id)->kms_sumados;
            $kmsCanjeados = $this->actividadModel->getKilometrosCanjeados($cliente->id)->kms_canjeados;
            $kmsAcumulados = $kmsSumados - $kmsCanjeados;
            $actividades = $this->actividadModel->getActividadesCliente($cliente->id);
            $tarjetas = $this->tarjetaModel->getTarjetasCliente($cliente->id);

            $this->view->showResumenCliente($nombreCliente, $kmsAcumulados, $actividades, $tarjetas);
        }else {
            $this->view->showMensaje("No existe un cliente con ese DNI");
        }
    }

    public function transferenciaRapida(){
        $this->authHelper->verifyLogin();
        $clienteOriginario = $this->clienteModel->getClientePorDni($_POST["dniOriginario"]);
        $montoKms = $_POST["montoKms"];
        $clienteDestinatario = $this->clienteModel->getClientePorDni($_POST["dniDestinatario"]);

        if ($clienteDestinatario){
            $kmsSumados = $this->actividadModel->getKilometrosSumados($clienteOriginario->id)->kms_sumados;
            $kmsCanjeados = $this->actividadModel->getKilometrosCanjeados($clienteOriginario->id)->kms_canjeados;
            $kmsAcumuladosOriginario = $kmsSumados - $kmsCanjeados;

            if ($kmsAcumuladosOriginario >= $montoKms){
                $this->actividadModel->addActividad($montoKms, $_POST["fecha"], 2, $clienteDestinatario->id);
            }else {
                $this->view->showMensaje("No tenes kilometros suficientes para realizar esta transferencia");
            }
        }else {
            $this->view->showMensaje("El cliente destinatario no existe");
        }
    }
}