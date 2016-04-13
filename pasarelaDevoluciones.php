<?php
require 'authorize.php';
$authorize = new authorize();

$datosDevolucion = array(
    "x_login" => "8mQ4RcRP2E8K", //ID ENTREGADO POR AUTHORIZE
    "x_tran_key" => "9Fb8Mr7wL5xB7y6q", //tran_key ENTREGADO POR AUTHORIZE
    "x_trans_id" => "2255025590",
    "x_exp_date" => "01" . "17", //MES Y AÑO, De preferencia dividirlo en 2 input text para facilitar el proceso al usuario
    "x_card_num" => "4111" . "1111" . "1111" . "1111", //De preferencia dividirlo en 4 input text para facilitar el proceso al usuario
    "x_amount" => "100", //CANTIDAD PAGADA
    "authorize_modo" => "0" //0=Prueba,1=Productivo
);
//DEVOLICION DE DINERO
$resultadoDevolucion = $authorize->devolucionTarjeta($datosDevolucion);
if ($resultadoDevolucion[estado_tran] == 1) {
    echo "Devolucion exitosa con id de transaccion $resultadoDevolucion[x_trans_id]";
} else {
    echo "Error de devolucion $resultadoDevolucion[text_tran] ";
}

