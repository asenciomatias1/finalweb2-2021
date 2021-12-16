<?php

// CLIENTE(id: int; nombre: string, dni: string, telefono: string, direccion: string,
// ejecutivo: boolean)
// TARJETA(id: int; fecha_alta: datetime; nro_tarjeta: string, fecha_vencimiento:
// int, tipo_tarjeta: string, id_cliente: int)
// ACTIVIDAD(id: int; kms: int, fecha: datetime, tipo_operación: int, id_cliente:
// int)

class ActividadModel {
    private $db;

    public function __construct() {
        $this->db = new PDO('mysql:host=localhost;'.'dbname=db_pfy;charset=utf8', 'root', '');
    }

    public function addActividad($kms, $fecha, $tipo_operacion, $id_cliente){
        $query = $this->db->prepare("INSERT INTO actividad
                                    (kms, fecha, tipo_operacion, id_cliente)
                                    VALUES (?,?,?,?)");
        $query->execute([$kms, $fecha, $tipo_operacion, $id_cliente]);
    }

    public function getKilometrosSumados($id_cliente){
        $query = $this->db->prepare("SELECT COUNT(kms) AS kms_sumados 
            FROM actividad WHERE id_cliente = ? AND tipo_operacion = 2");
        $query->execute([$id_cliente]);
        return $query->fetch(PDO::FETCH_OBJ);
    }

    public function getKilometrosCanjeados($id_cliente){
        $query = $this->db->prepare("SELECT COUNT(kms) AS kms_sumados 
            FROM actividad WHERE id_cliente = ? AND tipo_operacion = 1");
        $query->execute([$id_cliente]);
        return $query->fetch(PDO::FETCH_OBJ);
    }

    public function getActividadesCliente($id_cliente){
        $query = $this->db->prepare("SELECT * FROM actividad WHERE id_cliente = ?");
        $query->execute([$id_cliente]);
        return $query->fetchAll(PDO::FETCH_OBJ);
    }
}
