<?php
	namespace Controllers;	

	use Symfony\Component\HttpFoundation\Request;
	use Symfony\Component\HttpFoundation\Response;
	use Silex\Application;
	use Silex\Api\ControllerProviderInterface;
	use Models\UserQuery;
	use Models\User;
	use Models\Validations;

	class ApiController implements ControllerProviderInterface{

		public function connect(Application $app){
			//Ref para a instancia da aplicação
			$api = $app['controllers_factory'];

			//Requisições get
			$api->get('/users', 'Controllers\ApiController::getAllUsers');
			$api->get('/users/{id}', 'Controllers\ApiController::getUserById');
			
			//Requisições post
			$api->post('/users', 'Controllers\ApiController::insertUser');
			
			//Requisições put
			$api->put('/users/{id}', 'Controllers\ApiController::updateUser');
			
			//Requisições delete
			$api->delete('/users/{id}', 'Controllers\ApiController::deleteUser');

			return $api;
		}

		//Retorna todos os usuários cadastrados
		public function getAllUsers(Application $api){
			$rows = UserQuery::create()
                 ->join('User.Profile')
                 ->select(
                    array('id','name', 'email', 'dateCreation', 'dateLastLogin')
                )
                 ->withColumn('Profile.name', 'profile')
                 ->find();

        	return $rows->toJson();
		}

		//Retorna usuário cadastrado a partir do id passado na URL
		public function getUserById(Application $api, $id){
			$row = UserQuery::create()->findPK($id);
	        if($row === null){
	            $return = ['status'=>'Error', 'statusMessage'=>'Nao existe usuario com esse ID!'];
		        return $api->json($return);
	        }
	        return $row->toJson();
		}

		//Insere um novo usuário no banco
		public function insertUser(Application $api, Request $request){
			$data = $request->request->all();
			$result = $api['validations']->validateNewData($data);

			//Se não houver erros, insere o novo usuário no banco
			if($result == null){
		    	$user = new User;
		        $user->setName($data['name']);
		        $user->setEmail($data['email']);
		        $user->setPassword($data['password']);
		        $user->setDatecreation(date("d-m-Y H:i:s"));
		        $user->setProfileId($data['profile']);
		        $user->save();

		        $return = ['status'=>'Sucess', 'statusMessage'=>'Created'];
		        return $api->json($return);
	    	//Se houver, retorna os erros para o usuário
    		} else {
				$return = ['status'=>'Error', 'statusMessage'=>"Erros nos parametros: ".$result];
		        return $api->json($return);
    		}
		}

		//Atualiza um usuário do banco
		public function updateUser(Application $api, Request $request, $id){
			$data = json_decode($request->getContent(), true);
	    	
			$result = $api['validations']->validateNewData($data);

			//Se não houver erros, insere o novo usuário no banco
			if($result == null){
	        	$update = UserQuery::create()->findPK($id);
	        	if($update == null){
	        		$return = ['status'=>'Error', 'statusMessage'=>'Nao existe usuario com esse ID!'];
		        	return $api->json($return);
	        	}
	        	$update->setNew(false);
	        	$update->setName($data['name']);
	        	$update->setEmail($data['email']);
	        	$update->setPassword($data['password']);
	        	$update->setProfileId($data['profile']);
	        	$update->save();

	        	$return = ['status'=>'Sucess', 'statusMessage'=>'Atualizado'];
		        return $api->json($return);
	        //Se houver, retorna os erros para o usuário
    		} else {
				$return = ['status'=>'Error', 'statusMessage'=>"Erros nos parametros: ".$result];
		        return $api->json($return);
    		}
		}

		//Deleta um usuário do banco
		public function deleteUser(Application $api, $id){
			$delete = UserQuery::create()->findPK($id);
			if($delete == null){
				$return = ['status'=>'Error', 'statusMessage'=>"Nao existe usuario com esse ID!"];
		    	return $api->json($return);
			}
        	$delete->delete();

        	$return = ['status'=>'Sucess', 'statusMessage'=>"Usuario deletado com sucesso!"];
		    return $api->json($return);
		}

	} 
	
?>