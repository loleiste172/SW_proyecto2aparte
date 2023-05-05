<?php
    use Psr\Http\Message\ResponseInterface as Response;
    use Psr\Http\Message\ServerRequestInterface as Request;
    use Slim\Factory\AppFactory;
   
    require __DIR__ . '/vendor/autoload.php';
    error_reporting(0);
    date_default_timezone_set('America/Mazatlan'); //PHP cree que la hora de la ciudad de mexico es es una hora adelantada :v jajaja salu2
    $app = AppFactory::create();
    $app->setBasePath("/proy_SW/authv2");
    function get_user($user) {
        $url = 'https://pr06-3e340-default-rtdb.firebaseio.com/usuarios_sistema/'.$user.'.json';
        //https://pr06-3e340-default-rtdb.firebaseio.com/
        //https://pr06-3e340-default-rtdb.firebaseio.com/usuarios_sistema
        $ch =  curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($ch);   
        curl_close($ch);
        
        // Se convierte a Object o NULL
        return json_decode($response, true);
    }

    $app->get('/test', function($request, $response, $args){

        //$response->write(json_encode(get_user('dsfsd@gmail-com'), JSON_PRETTY_PRINT));
        $now = new DateTime('+2 minutes');
        //string test = DateTime.Now.ToString("d-MM-yyyy HH:mm:ss");
        $response->write($now->format('d-m-Y H:i:s'));
        // $response->write(date_default_timezone_get());
        $newresp= $response->withstatus(400);

        return $newresp;
    });

    $app->post('/auth', function($request, $response, $args){
        $reqPost = $request->getParsedBody();
        $correo=$reqPost['correo'];
        $pass=$reqPost['pass'];
        $resp=array(
            'token' => '',
            'message' => 'error desconocido'
        );

        if(!(isset($correo) && isset($pass))){
            $resp['message']='Peticion invalida';
            $response->write(json_encode($resp, JSON_PRETTY_PRINT));
            $newresp= $response->withstatus(400);
            return $newresp;
        }

        $user_id=str_replace('.', '-', $correo);
        $usrdata=get_user($user_id);
        if(is_null($usrdata) || md5($pass)!=$usrdata['pass']){
            
            $resp['message']='El correo o la contraseña con incorrectos.';
            $response->write(json_encode($resp, JSON_PRETTY_PRINT));
            $newresp= $response->withstatus(401);
            return $newresp;
        }
        /**
            nombre_usuario = username en firebase
            fecha_fin = vigencia de 3 dias o x tiempo
            app_destino = ventas o almacen
            app_origen = id app? (numeros aleatorios creados en la app)

            ejemplo:
            test-1683443836-ventas-231
        */
        $now = new DateTime('+2 minutes');

        $username=$usrdata['nombre'];
        $fecha_fin=strtotime($now->format('d-m-Y H:i:s'));
        $app_dest=$usrdata['aplicacion'];
        $app_origen=rand(1,999);
        $resp['message']="Usuario validado exitosamente";
        $resp['token']=$username."-".$fecha_fin."-".$app_dest."-".$app_origen;
        $response->write(json_encode($resp, JSON_PRETTY_PRINT));
        
        return $response;
    });

    $app->run();
?>