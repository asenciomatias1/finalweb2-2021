<?php

// CLIENTE(id: int; nombre: string, dni: string, telefono: string, direccion: string,
// ejecutivo: boolean)
// TARJETA(id: int; fecha_alta: datetime; nro_tarjeta: string, fecha_vencimiento:
// int, tipo_tarjeta: string, id_cliente: int)
// ACTIVIDAD(id: int; kms: int, fecha: datetime, tipo_operación: int, id_cliente:
// int)

class ClienteModel {
    private $db;

    public function __construct() {
        $this->db = new PDO('mysql:host=localhost;'.'dbname=db_pfy;charset=utf8', 'root', '');
    }

    public function addCliente($nombre, $dni, $telefono, $direccion, $ejecutivo){
        $query = $this->db->prepare("INSERT INTO cliente 
                                    (nombre, dni, telefono, direccion, ejecutivo)
                                    VALUES (?,?,?,?,?");
        $query->execute([$nombre, $dni, $telefono, $direccion, $ejecutivo]);
    }

    public function getClientePorDni($dni){
        $query = $this->db->prepare("SELECT * FROM cliente WHERE dni = ?");
        $query->execute([$dni]);
        return $query->fetch(PDO::FETCH_OBJ);
    }

    public function getClientePorID($id){
        $query = $this->db->prepare("SELECT * FROM cliente WHERE id = ?");
        $query->execute([$id]);
        return $query->fetch(PDO::FETCH_OBJ);
    }
}
