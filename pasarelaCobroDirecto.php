<?php

require 'authorize.php';
$authorize = new authorize();

$datosPasarela = array(
    "x_login" => "8mQ4RcRP2E8K", //ID ENTREGADO POR AUTHORIZE
    "x_tran_key" => "9Fb8Mr7wL5xB7y6q", //tran_key ENTREGADO POR AUTHORIZE
    "x_card_num" => "4111" . "1111" . "1111" . "1111", //De preferencia dividirlo en 4 input text para facilitar el proceso al usuario
    "x_exp_date" => "01" . "17", //MES Y AÑO, De preferencia dividirlo en 2 input text para facilitar el proceso al usuario
    "x_card_code" => "111", //CCV o CCV2
    "x_amount" => "100", //CANTIDAD A PAGAR
    "x_first_name" => "Eugenio", //Nombre como esta en la tarjeta
    "x_last_name" => "Chaparro", //Apellido como esta en la tarjeta
    "x_address" => "Atlacomulco de Fabela Calle 12 de Octubre Col. los Labadero", //Direccion
    "x_state" => "Estado de Mexico", //Estado
    "x_zip" => "50450", //Codigo Postal
    "authorize_modo" => "0" //0=Prueba,1=Productivo
);
//VALIDAMOS QUE LA TARJETA TENGA DINERO
$resultadoCobroDirecto = $authorize->cobroDirectoTarjeta($datosPasarela);
//SI estado_tran ES 1 OBTENEMOS EL DINERO DE LA TARJETA
if ($resultadoCobroDirecto[estado_tran] == 1) {
    echo "El resultado del cobro directo fue exitoso con el x_trans_id= $resultadoCobroDirecto[x_trans_id]</br>";
}
//SI EL ESTADO ES DIFERENTE A 1 MOSTRAMOS EL TEXTO CON EL ERROR
else {
    echo $resultadoCobroDirecto[text_tran];
}
