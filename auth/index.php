<?php
    use Psr\Http\Message\ResponseInterface as Response;
    use Psr\Http\Message\ServerRequestInterface as Request;
    use Selective\BasePath\BasePathMiddleware;
    use Slim\Factory\AppFactory;
    
    require_once __DIR__ . '/vendor/autoload.php';
    
    $app = AppFactory::create();
    
    // Add Slim routing middleware
    $app->addRoutingMiddleware();
    
    // Set the base path to run the app in a subdirectory.
    // This path is used in urlFor().
    $app->add(new BasePathMiddleware($app));
    
    $app->addErrorMiddleware(true, true, true);
    
    // Define app routes
    $app->get('/', function (Request $request, Response $response) {

    });

    $app->get('/test', function($request, $response, $args){

        $response->write(json_encode(get_user('dsfsd@gmail-com'), JSON_PRETTY_PRINT));

        return $response;
    });

    $app->post('/auth', function($request, $response, $args){
        $reqPost = $request->getParsedBody();
        $correo=$reqPost['correo'];
        $pass=$reqPost['pass'];
        
        return $response;
    });

    $app->run();
?>