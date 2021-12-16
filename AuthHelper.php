<?php

class AuthHelper{
    
    public function __construct() {
        session_start();
    }

    public function verifyLogin(){
        if (!isset($_SESSION["email"])){
            header("Location: ".BASE_URL."login");
        }
    }

    public function esAdmin(){
        return $_SESSION["rol"] == 'admin';
    }
}
