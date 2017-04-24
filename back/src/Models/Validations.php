<?php

	namespace Models;
	
	use Symfony\Component\Validator\Constraints as Assert;
	use Silex\Application;

	class Validations{
		private $valApp;

		function __construct(Application $valApp){
			$this->valApp = $valApp;
		}

		//Valida os dados recebidos para tentativa de login
		function validateLogin($data){
			//echo 'entrou no validateLogin';
			$constraints = new Assert\Collection(
				array(
					'user' => array(new Assert\NotBlank(), new Assert\Email()),
					'password' => array(new Assert\NotBlank(), new Assert\Length(array('min' => 8)), new Assert\Length(array('max' => 16)))
				)
			);			

			$errors = $this->valApp['validator']->validate($data, $constraints);

			//Caso haja, retorna os erros para o usuário
			if(count($errors) > 0){
				return (string) $errors;
			//Se não houver, returna null
			} else {
				return null;
			}
		}

		//Valida os dados recebidos para a criação de um novo usuário
		function validateNewData($data){
			$constraints = new Assert\Collection(
				array(
					'name' => array(new Assert\NotBlank(), new Assert\Length(array('min' => 5)), new Assert\Length(array('max' => 50)),),
					'email' => array(new Assert\NotBlank(), new Assert\Email()),
					'password' => array(new Assert\NotBlank(), new Assert\Length(array('min' => 8)), new Assert\Length(array('max' => 16)),),
					'profile' => array(
									new Assert\NotBlank(), 
									new Assert\Type('integer'), 
									new Assert\Range(array('min'=> 1, 'max' => 3,)),
								  ),
				)

			);
			$errors = $this->valApp['validator']->validate($data, $constraints);

			//Caso haja, retorna os erros para o usuário
			if(count($errors) > 0){
				return $errors;
			//Se não houver, retorna null
			} else {
				return null;
			}
		}
	}

?>