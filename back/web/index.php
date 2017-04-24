<?php

	// web/index.php
	require_once __DIR__.'/../vendor/autoload.php';
    require_once __DIR__.'/../generated-conf/config.php';
	
	use Symfony\Component\HttpFoundation\Request;
	use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\Debug\ErrorHandler;
    use Symfony\Component\Debug\ExceptionHandler;
    use Propel\Runtime\Propel;
    use Controllers\ApiController;
    use Models\Validations;
    use Silex\Application;
    use Models\UserQuery;

	$app = new Application();
	$app['debug'] = true;

    $app->before(
        function (Request $request){
            if(0 === strpos($request->headers->get('Content-Type'), 'application/json')){
                $data = json_decode($request->getContent(), true);
                $request->request->replace(is_array($data) ? $data : array());
            } 
        } 
    );

    ErrorHandler::register();
    ExceptionHandler::register();

    //Registrando a classe Validations na aplicação
    $app['validations'] = function() use ($app){
            return new Validations($app);
    };

    $app['UserQuery'] = function(){
        return new UserQuery();
    };

    //Registra o serviço Validator para validação dos dados recebidos via requisição
    $app->register(new Silex\Provider\ValidatorServiceProvider());

	//Registra o serviço Session para login na aplicação
    $app->register(new Silex\Provider\SessionServiceProvider());

    //Monta a rota para a API, que retornará os dados em JSON	
	$app->mount('/api', new Controllers\ApiController());
    
    //Monta a rota para opções do usuário
    $app->mount('/user', new Controllers\UserController());

    //Monta a rota para opções do Syslog
	$app->mount('/syslog', new Controllers\SyslogController());

    //Página home
    $app->get('/', function() use ($app){
        return $app->redirect('index.php/api/users');
    });

	$app->run();
?>