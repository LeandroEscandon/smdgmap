<?php

    $POST_JSON = null;
    $POST_str = "";
    $post_id = ""; 
    $post_latitud = 0;
    $post_longitud = 0;
    $post_tipo = 1;


    //Función de encabezado 
    function encabezado(){
        header("Content-Type: application/json; charset=UTF-8");
        echo "======================================"."\n";
        echo "SIIGOTT BACK END"."\n";
        echo "======================================"."\n";
    }

    //Función de nota al pie
    function pieDePagina(){
        echo "======================================"."\n";
        echo "FIN DE SIIGOTT BACK END"."\n";
        echo "======================================"."\n";
    }

    function exceptionNullObject($variable, $msg_error){
        if($variable == null){
            throw new Exception($msg_error."\n");
        }
    }

    function exceptionJsonNullProperty($variable, $propertyName){
        exceptionNullObject($variable, "Propiedad '".$propertyName."' no válida!"."\n");
    }

    //Función para capturar los datos POST y mostrarlos
    function capturarDatosPost(){
        global $POST_str, $POST_JSON, $post_id, $post_latitud, $post_longitud, $post_tipo;
    
        echo "======================================"."\n";
        echo "CAPTURA DE POST:"."\n";

        try {
            // Capturar string del POST request
            $POST_str = file_get_contents('php://input');

            // Mostrar Post recibido
            echo "POST RECIBIDO: "."\n".$POST_str."\n";

            exceptionNullObject($POST_str, "POST VACIO");

            // Intentar convertir POST_str en un Objeto JSON de PHP 
            echo "CONVIRTIENDO A OBJETO JSON..."."\n";
            $POST_JSON = json_decode($POST_str, false);
            echo "OBJETO CONVERTIDO CORRECTAMENTE!"."\n";

            $POST_JSON_str = json_encode($POST_JSON);
            echo "POST JSON STR: "."\n".$POST_JSON_str."\n";

            echo "DATOS DEL JSON RECIBIDO: "."\n";

            $post_id = $POST_JSON->id; 
            exceptionJsonNullProperty($post_id, "id");
            $post_latitud = $POST_JSON->latitud;
            exceptionJsonNullProperty($post_latitud, "latitud");
            $post_longitud = $POST_JSON->longitud;
            exceptionJsonNullProperty($post_longitud, "longitud");
            $post_tipo = 1;
    
            echo "id: ".$post_id."\n";
            echo "latitud: ".$post_latitud."\n";
            echo "longitud: ".$post_longitud."\n";
            echo "tipo: ".$post_tipo."\n";

            return true;
    
        } catch (\Throwable $th) {
            //echo 'ERROR DE EXCEPCION: '.$th."\n";
            echo $th."\n";
        }

        echo "FIN DE CAPTURA DE POST"."\n";
        echo "======================================"."\n";
        return false;

    }

    function actualizarMarcadores(){
        global $POST_JSON, $post_id, $post_latitud, $post_longitud, $post_tipo;
        echo "======================================"."\n";
        echo "ACTUALIZAR MARCADORES"."\n";
        //$fp = fopen("https://siigotttest.000webhostapp.com/assets/data/test/marcadores.txt", "r");

        $urlFile = "../assets/data/test/marcadores.txt"; // Host Web
        //$urlFile = "../assets/data/test/marcadorestest.txt"; // Host Web
        //$urlFile = "../siigott-front/src/assets/data/test/marcadores.txt"; // Back End Test Front End
        //$urlFile = "assets/data/test/marcadores.txt"; // Localhost

        echo "Abriendo archivo marcadores.txt..."."\n";
        $fp = fopen($urlFile, "r");
        $strFp = "";
        echo $fp;

        if($fp){
            echo "\n"."\n"."Archivo abierto"."\n";

            echo "Cargando contenido del archivo..."."\n";
            // Guardar contenido del archivo JSON en la variable strJSON
            while(!feof($fp))
                $strFp = $strFp.fread($fp, filesize($urlFile));
                //echo $strFp;
                //echo fread($fp, filesize($urlFile));

                fclose($fp);
            echo "Archivo cerrado"."\n";
                
            // Mostrar contenido del archivo
            echo "Contenido del archivo: "."\n";
            echo $strFp."\n";
            
            //Convertir string en JSON
            echo "Convirtiendo contenido en objeto JSON..."."\n";
            $jsonList = json_decode($strFp, false);

            echo "Conversión completa!"."\n";

            echo "Buscando item..."."\n";

            $encontrado = false;
            foreach ($jsonList as $jsonElement) {
                if($jsonElement->id == $post_id){
                    echo "Item encontrado"."\n";
                    $encontrado = true;
                    //$strJSON = json_encode($jsonElement);

                    echo "Actualizando datos..."."\n";
                    $jsonElement->latitud = $post_latitud;
                    $jsonElement->longitud = $post_longitud;
                    $jsonElement->tipo = $post_tipo;
                    echo "Datos actualizados correctamente!"."\n";
                    break;
                }
            } 

            
            if($encontrado == false){
                echo "Item no encontrado"."\n";
                // $jsonElementPost->id == $post_id;
                // $jsonElementPost->latitud = $post_latitud;
                // $jsonElementPost->longitud = $post_longitud;
                // $jsonElementPost->tipo = $post_tipo;

                echo "Agregando item..."."\n";
                array_push($jsonList, $POST_JSON);
                echo "Item agregado!"."\n";
            }

            $strListaJSON = json_encode($jsonList);

            echo "\n"."\n"."Objeto JSON: "."\n".$strListaJSON."\n"."\n";;
            
            // Guardar archivo
            echo "GUARDANDO ARCHIVO..."."\n";
            $fp = fopen($urlFile, "w");
            fwrite($fp, $strListaJSON);
            echo "ARCHIVO GUARDADO!"."\n";

        }
        else{
            echo "Error al intertar abrir el archivo!!!"."\n";
        }
        echo "FIN ACTUALIZAR MARCADORES"."\n";
        echo "======================================"."\n";
    }

    function permisosCORS(){

    }

    function test(){
        permisosCORS();
        if(capturarDatosPost()){
            actualizarMarcadores();
        }
        //leerMarcadores();
    }


    function main(){
        encabezado();
        test();
        pieDePagina();
    }

    main();


?>