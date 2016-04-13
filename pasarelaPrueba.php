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
$resultadoValidacion = $authorize->validamosTarjeta($datosPasarela);

//SI estado_tran ES 1 OBTENEMOS EL DINERO DE LA TARJETA
if ($resultadoValidacion[estado_tran] == 1) {
    echo "El resultado de la validacion exitoso con el x_trans_id= $resultadoValidacion[x_trans_id]</br>";
    $datosTerminarTrans = array(
        "x_login" => $datosPasarela[x_login], //ID ENTREGADO POR AUTHORIZE
        "x_tran_key" => $datosPasarela[x_tran_key], //tran_key ENTREGADO POR AUTHORIZE
        "x_trans_id" => $resultadoValidacion[x_trans_id],
        "authorize_modo" => $datosPasarela[authorize_modo]
    );
    //SUPONGAMOS LOS PROCESOS DE VENTA FUERON EXITOSOS (OSEA QUE NO SE COLAPSARON LOS DATOS)
    $procesoVentaExitosa = true;
    //SI EL PROCESO DE VENTA ES EXITOSO SE SACA EL DINERO DE LA TARJETA
    if ($procesoVentaExitosa == false) {
        $resultadoObtenerDinero = $authorize->obtenerDineroTarjeta($datosTerminarTrans);
        if ($resultadoObtenerDinero[estado_tran] == 1) {
            echo "El proceso de obtencion de dinero fue exitoso con el x_trans_id=$resultadoObtenerDinero[x_trans_id] </br>";
        } else {
            echo "Error al obtener dinero= $resultadoObtenerDinero[text_tran] </br>";
        }
    }
    //SI EL PROCESO DE VENTA NO FUE EXITOSO SE CANCELA EL PROCESO DE VENTA
    else {
        $resultadoRegresarDinero = $authorize->regresarDineroTarjeta($datosTerminarTrans);
        if ($resultadoRegresarDinero[estado_tran] == 1) {
            echo "El proceso de regresar el dinero fue exitoso con el x_trans_id=$resultadoRegresarDinero[x_trans_id] </br>";
        }
    }
}
//SI EL ESTADO ES DIFERENTE A 1 MOSTRAMOS EL TEXTO CON EL ERROR
else {
    echo $resultadoValidacion[text_tran];
}

