<?php

class authorize {

    //VALIDAMOS QUE LA TARJETA SEA REAL Y TENGA DINERO 
    //retorna array("estado_tran"=>,"text_tran"=>$texto_resultado_pago,"x_trans_id"=>$x_trans_id);
    function validamosTarjeta($datosPasarela) {
        //SI ES 0 SE USA LA PASARELA DE PRUEBA SI ES 1 SE USA LA PASARELA DE PRODUCTIVO
        if ($datosPasarela[authorize_modo] == 0) {
            $post_url = "https://test.authorize.net/gateway/transact.dll";
        }
        if ($datosPasarela[authorize_modo] == 1) {
            $post_url = "https://secure.authorize.net/gateway/transact.dll";
        }

        //TOMAMOS LOS DATOS A ENVIAR A AUTHORIZE
        $post_values = array(
            "x_login" => $datosPasarela[x_login],
            "x_tran_key" => $datosPasarela[x_tran_key],
            //
            "x_version" => "3.1",
            "x_delim_data" => "TRUE",
            "x_delim_char" => "|",
            "x_relay_response" => "FALSE",
            "x_type" => "AUTH_ONLY",
            "x_method" => "CC",
            //
            "x_card_num" => $datosPasarela[x_card_num],
            "x_exp_date" => $datosPasarela[x_exp_date],
            "x_amount" => $datosPasarela[x_amount],
            //
            "x_description" => "Tienda",
            "x_first_name" => $datosPasarela[x_first_name],
            "x_last_name" => $datosPasarela[x_last_name],
            "x_address" => $datosPasarela[x_address],
            "x_state" => $datosPasarela[x_state],
            "x_zip" => $datosPasarela[x_zip]
        );

        //CONVERTIMOS LOS DATOS EN UN ARREGLO
        $post_string = "";
        foreach ($post_values as $key => $value) {
            $post_string .= "$key=" . urlencode($value) . "&";
        }
        $post_string = rtrim($post_string, "& ");

        //INICIALIZAMOS EL CURL CON LA RIRECCION DEL GETWAY
        $request = curl_init($post_url);
        //COLOCAMO EN EL CURL LOS PARAMETROS A ENVIAR
        curl_setopt($request, CURLOPT_HEADER, 0); // set to 0 to eliminate header info from response
        curl_setopt($request, CURLOPT_RETURNTRANSFER, 1); // Returns response data instead of TRUE(1)
        curl_setopt($request, CURLOPT_POSTFIELDS, $post_string); // use HTTP POST to send form data
        curl_setopt($request, CURLOPT_SSL_VERIFYPEER, FALSE); // uncomment this line if you get no gateway response.
        //EJECUTAMOS EL CURL Y REGRESAMOS LA RESPUESTA EN $post_response
        $post_response = curl_exec($request); // execute curl post and store results in $post_response
        //CERRAMOS EL CURL
        curl_close($request); // close curl object
        //TOAMOS EL RESULTADO Y LO DIVIDIMOS EN PARTES
        $response_array = explode($post_values[x_delim_char], $post_response);
        //SI LA TARJETA FUE ACEPTA EL RESULTADO = 1 , 2 = Declined ,3 = Error ,4 = Retenida para revicios
        $estado_tran = $response_array[0];
        //TEXTO QUE DA EL RESULTADO EJEMPLO=This transaction has been approved.
        $texto_resultado_pago = $response_array[3];
        //EL CODIGO DE TRANSACCION
        $x_trans_id = $response_array[6];
        $respuestaValidar = array(
            "estado_tran" => $estado_tran, //1=Aceptada,2=Rechazada,3=Error,4 = Retenida para revicios
            "text_tran" => $texto_resultado_pago, //Id de esta tranzaccion a pasar a PRIOR_AUTH_CAPTURE
            "x_trans_id" => $x_trans_id
        );
        return $respuestaValidar;
    }
    //UNA VEZ HECHA LA VALIDACION DE LA TARJETA SE SACA EL DINERO DE ELLA
    function obtenerDineroTarjeta($datosObtenerDinero) {
        //SI ES 0 SE USA LA PASARELA DE PRUEBA SI ES 1 SE USA LA PASARELA DE PRODUCTIVO
        if ($datosObtenerDinero[authorize_modo] == 0) {
            $post_url = "https://test.authorize.net/gateway/transact.dll";
        }
        if ($datosObtenerDinero[authorize_modo] == 1) {
            $post_url = "https://secure.authorize.net/gateway/transact.dll";
        }
        //DATOS A ENVIAR
        $post_values = array(
            "x_login" => $datosObtenerDinero[x_login],
            "x_tran_key" => $datosObtenerDinero[x_tran_key],
            //
            "x_version" => "3.1",
            "x_delim_data" => "TRUE",
            "x_delim_char" => "|",
            "x_relay_response" => "FALSE",
            "x_type" => "PRIOR_AUTH_CAPTURE",
            "x_trans_id" => $datosObtenerDinero[x_trans_id]
        );
        //CONVERTIMOS LOS DATOS EN UN ARREGLO
        $post_string = "";
        foreach ($post_values as $key => $value) {
            $post_string .= "$key=" . urlencode($value) . "&";
        }
        $post_string = rtrim($post_string, "& ");
        //INICIALIZAMOS EL CURL CON LA RIRECCION DEL GETWAY
        $request = curl_init($post_url);
        //COLOCAMO EN EL CURL LOS PARAMETROS A ENVIAR
        curl_setopt($request, CURLOPT_HEADER, 0); // set to 0 to eliminate header info from response
        curl_setopt($request, CURLOPT_RETURNTRANSFER, 1); // Returns response data instead of TRUE(1)
        curl_setopt($request, CURLOPT_POSTFIELDS, $post_string); // use HTTP POST to send form data
        curl_setopt($request, CURLOPT_SSL_VERIFYPEER, FALSE); // uncomment this line if you get no gateway response.
        //EJECUTAMOS EL CURL Y REGRESAMOS LA RESPUESTA EN $post_response
        $post_response = curl_exec($request); // execute curl post and store results in $post_response
        //CERRAMOS EL CURL
        curl_close($request); // close curl object
        //TOAMOS EL RESULTADO Y LO DIVIDIMOS EN PARTES
        $response_array = explode($post_values[x_delim_char], $post_response);
        //SI LA TARJETA FUE ACEPTA EL RESULTADO = 1 , 2 = Declined ,3 = Error ,4 = Held for review
        $estado_tran = $response_array[0];
        //TEXTO QUE DA EL RESULTADO EJEMPLO=This transaction has been approved.
        $texto_resultado_pago = $response_array[3];
        //EL CODIGO DE TRANSACCION
        $x_trans_id = $response_array[6];
        $respuestaObtener = array(
            "estado_tran" => $estado_tran, //1=Aceptada,2=Rechazada,3=Error,4 = Retenida para revicios
            "text_tran" => $texto_resultado_pago, //Id de esta tranzaccion a pasar a PRIOR_AUTH_CAPTURE
            "x_trans_id" => $x_trans_id
        );
        return $respuestaObtener;
    }
    //SI HUBO UN ERROR EN LA OPERACION DE LA VENTA SE DEBE REGRESAR EL DINERO A LA TARJETA
    function regresarDineroTarjeta($datosRegresarDinero) {
        //SI ES 0 SE USA LA PASARELA DE PRUEBA SI ES 1 SE USA LA PASARELA DE PRODUCTIVO
        if ($datosRegresarDinero[authorize_modo] == 0) {
            $post_url = "https://test.authorize.net/gateway/transact.dll";
        }
        if ($datosRegresarDinero[authorize_modo] == 1) {
            $post_url = "https://secure.authorize.net/gateway/transact.dll";
        }
        //DATOS A ENVIAR
        $post_values = array(
            "x_login" => $datosRegresarDinero[x_login],
            "x_tran_key" => $datosRegresarDinero[x_tran_key],
            //
            "x_version" => "3.1",
            "x_delim_data" => "TRUE",
            "x_delim_char" => "|",
            "x_relay_response" => "FALSE",
            "x_type" => "void",
            "x_trans_id" => $datosRegresarDinero[x_trans_id]
        );
        //CONVERTIMOS LOS DATOS EN UN ARREGLO
        $post_string = "";
        foreach ($post_values as $key => $value) {
            $post_string .= "$key=" . urlencode($value) . "&";
        }
        $post_string = rtrim($post_string, "& ");
        //INICIALIZAMOS EL CURL CON LA RIRECCION DEL GETWAY
        $request = curl_init($post_url);
        //COLOCAMO EN EL CURL LOS PARAMETROS A ENVIAR
        curl_setopt($request, CURLOPT_HEADER, 0); // set to 0 to eliminate header info from response
        curl_setopt($request, CURLOPT_RETURNTRANSFER, 1); // Returns response data instead of TRUE(1)
        curl_setopt($request, CURLOPT_POSTFIELDS, $post_string); // use HTTP POST to send form data
        curl_setopt($request, CURLOPT_SSL_VERIFYPEER, FALSE); // uncomment this line if you get no gateway response.
        //EJECUTAMOS EL CURL Y REGRESAMOS LA RESPUESTA EN $post_response
        $post_response = curl_exec($request); // execute curl post and store results in $post_response
        //CERRAMOS EL CURL
        curl_close($request); // close curl object
        //TOAMOS EL RESULTADO Y LO DIVIDIMOS EN PARTES
        $response_array = explode($post_values[x_delim_char], $post_response);
        //SI LA TARJETA FUE ACEPTA EL RESULTADO = 1 , 2 = Declined ,3 = Error ,4 = Held for review
        $estado_tran = $response_array[0];
        //TEXTO QUE DA EL RESULTADO EJEMPLO=This transaction has been approved.
        $texto_resultado_pago = $response_array[3];
        //EL CODIGO DE TRANSACCION
        $x_trans_id = $response_array[6];
        $respuestaRegresarDinero = array(
            "estado_tran" => $estado_tran, //1=Aceptada,2=Rechazada,3=Error,4 = Retenida para revicios
            "text_tran" => $texto_resultado_pago, //Id de esta tranzaccion a pasar a PRIOR_AUTH_CAPTURE
            "x_trans_id" => $x_trans_id
        );
        return $respuestaRegresarDinero;
    }
}
