<?php
	namespace Controllers;

	use Symfony\Component\HttpFoundation\Request;
	use Symfony\Component\HttpFoundation\Response;
	use Silex\Application;
	use Silex\Api\ControllerProviderInterface;
	use Models\UserQuery;
	use Models\User;
	use Models\Validations;

	class UserController implements ControllerProviderInterface{

		public function connect(Application $app){
			$userApp = $app['controllers_factory'];

			//Requisições get
			$userApp->get('/logged', 'Controllers\UserController::isLogged');
			$userApp->get('/logout', 'Controllers\UserController::logout');

			//Requisições post
			$userApp->post('/login', 'Controllers\UserController::login');

			return $userApp;
		}

		public function login(Request $request, Application $userApp){
			//$data = json_decode($request->getContent(), true);
			$data = $request->request->all();

			$result = $userApp['validations']->validateLogin($data);

			//Se a validação não retornar nenhum erro, testamos as credenciais
			if($result == null){

				$query = $userApp['UserQuery']->filterByEmail($data['user'])->find();
	            
		        $credentials = $query->toArray();

		        if(empty($credentials) || is_null($credentials)){
		        	//return new Response("Usuário inexistente", 400);
		        	$return = ['status'=>'Error', 'statusMessage'=>'Usuario inexistente'];
		        	//return $userApp->json($return);
		        	return json_encode($return);
		        }

		        if($data['user'] === $credentials[0]['Email'] && $data['password'] === $credentials[0]['Password']){
		            $update = $userApp['UserQuery']->findPK($credentials[0]['Id']);
		            $update->setNew(false);
		            $update->setDatelastlogin(date("d-m-Y H:i:s"));
		            $update->save();
		            
		            $userApp['session']->set('user', 
		                array(
		                    'id'=>$credentials[0]['Id'],
		                    'name'=>$credentials[0]['Name'],
		                    'email'=>$credentials[0]['Email'],
		                )
		            );
		            //return new Response('Login autorizado', 200);
		            $return = ['status'=>'Sucess', 'statusMessage'=>'Login autorizado'];
		        	//return $userApp->json($return);
		        	return json_encode($return);
		        } else {
		        	//return new Response("Usuário ou senha incorretos", 400);
		        	$return = ['status'=>'Error', 'statusMessage'=>'Usuario ou senha incorretos'];
		        	//return $userApp->json($return);
		        	return json_encode($return);
		        }

			//Se retornar algum erro mostramos o(s) erro(s) para o usuário
			} else {
				$return = ['status'=>'Error', 'statusMessage'=>"Erros nos parametros: ".$result];
		        //return $userApp->json($return);
		        return json_encode($return);
			}
	        
		}

		//Método para verificar se o usuário já iniciou uma sessão
		public function isLogged(Application $userApp){
			if(null === $user = $userApp['session']->get('user')){
            	$return = array('status'=>'Error', 'statusMessage'=>'Not logged');
		        return $userApp->json($return);
	        }
	        $return = array('status'=>'Sucess', 'statusMessage'=>'Logged');
		    return $userApp->json($return);
		}

		public function logout(Application $userApp){
			$userApp['session']->clear();
        	$return = array('status'=>'Sucess', 'statusMessage'=>'Logout');
		    return $userApp->json($return);
		}

	}
?>