<?php
namespace App\Tests;

require_once __DIR__.'/../../../vendor/autoload.php';
require_once __DIR__.'/../../../generated-conf/config.php';

use Controllers\UserController;
use GuzzleHttp\Client;
use Models\UserQuery;
use Models\Validations;
use Propel\Runtime\Collection\ObjectCollection;
use Silex\Application;
use Silex\Provider\ValidatorServiceProvider;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Models\Base\User;

/**
* 
* Classe para os testes da UserController
*
**/

class UserControllerTest extends \PHPUnit_Framework_TestCase {
	private $mockUser;
	private $mockRequest;
	private $mockApp;
	private $mockBag;
	private $mockValidations;
	private $collectionMock;
	private $mockUserBase;

	public function setUp(){
		$this->mockApp = $this->getMockBuilder(Application::class)->setMethods(null)->getMock();
		$this->mockValidations = $this->getMockBuilder(Validations::class)->setConstructorArgs([$this->mockApp])->getMock();
		$this->mockApp['validations'] = $this->mockValidations;
		$this->mockUser = $this->getMockBuilder(UserController::class)->setMethods(['connect'])->getMock();
		$this->mockRequest = $this->getMockBuilder(Request::class)->getMock();
		$this->mockBag = $this->getMockBuilder(ParameterBag::class)->setMethods(['all'])->getMock();
		$this->mockSession = $this->getMockBuilder(Session::class)->getMock();
		$this->mockApp['session'] = $this->mockSession;
		$this->collectionMock = $this->getMockBuilder(ObjectCollection::class)->getMock();
		$this->mockUserQuery = $this->getMockBuilder(UserQuery::class)->getMock();
		$this->mockApp['UserQuery'] = $this->mockUserQuery;
		$this->mockUserBase = $this->getMockBuilder(User::class)->setMethods(null)->getMock();
	}

	//Teste para senha incorreta 
	public function testLoginInvalidPassword() {
		$allData = ['user'=>'pablo@hotmail.com', 'password'=>'pablo1234'];
		$expected = '{"status":"Error","statusMessage":"Usuario ou senha incorretos"}';
		$credentials = [['Email'=>'pablo@hotmail.com', 'Password'=>'123', 'Id'=>'11']];

		$this->collectionMock = $this->getMockBuilder(ObjectCollection::class)->getMock();

		$this->mockRequest->request = $this->mockBag;

		$this->mockBag->expects($this->once())->method('all')->will($this->returnValue($allData));
		$this->mockValidations->expects($this->once())->method('validateLogin');
		$this->mockUserQuery->expects($this->once())
			->method('filterByEmail')
			->with($allData['user'])
			->will($this->returnValue($this->mockUserQuery));
		$this->mockUserQuery->expects($this->once())
			->method('find')
			->will($this->returnValue($this->collectionMock));

		$this->collectionMock->expects($this->once())->method('toArray')->will($this->returnValue($credentials));
		
		$actual = $this->mockUser->login($this->mockRequest, $this->mockApp);

		$this->assertEquals($expected, $actual);
	}

	//Teste para usuario inexistente
	public function testLoginInexistentUser(){
		$allData = ['user'=>'asdf@hotmail.com', 'password'=>'senhaqualquer'];
		$expected = '{"status":"Error","statusMessage":"Usuario inexistente"}';
		$credentials = null;

		$this->mockRequest->request = $this->mockBag;
		$this->collectionMock = $this->getMockBuilder(ObjectCollection::class)->getMock();
		$this->mockBag->expects($this->once())->method('all')->will($this->returnValue($allData));
		$this->mockValidations->expects($this->once())->method('validateLogin')->will($this->returnValue(null));
		$this->mockUserQuery->expects($this->once())
			->method('filterByEmail')
			->with($allData['user'])
			->will($this->returnValue($this->mockUserQuery));

		$this->mockUserQuery->expects($this->once())
			->method('find')
			->will($this->returnValue($this->collectionMock));

		$this->collectionMock->expects($this->once())->method('toArray')->will($this->returnValue($credentials));
		
		$actual = $this->mockUser->login($this->mockRequest, $this->mockApp);

		$this->assertEquals($expected, $actual);

	}

	//Teste para credenciais corretas
	public function testLoginCorrectCredentials(){
		$allData = ['user'=>'pablo@hotmail.com', 'password'=>'pablo123'];
		$expected = '{"status":"Sucess","statusMessage":"Login autorizado"}';
		$credentials = [['Email'=>'pablo@hotmail.com', 'Password'=>'pablo123', 'Id'=>'11', 'Name'=>'pablo']];

		$selectData = [['id'=>'11', 'name'=>'pablo', 'email'=>'pablo@hotmail.com']];

		$this->mockRequest->request = $this->mockBag;
		$this->collectionMock = $this->getMockBuilder(ObjectCollection::class)->getMock();
		$this->mockBag->expects($this->once())->method('all')->will($this->returnValue($allData));
		$this->mockValidations->expects($this->once())->method('validateLogin')->will($this->returnValue(null));
		$this->mockUserQuery->expects($this->once())
			->method('filterByEmail')
			->with($allData['user'])
			->will($this->returnValue($this->mockUserQuery));

		$this->mockUserQuery->expects($this->once())
			->method('find')
			->will($this->returnValue($this->collectionMock));

		$this->collectionMock->expects($this->once())->method('toArray')->will($this->returnValue($credentials));
		
		$this->mockUserQuery->expects($this->once())
			 ->method('findPK')
			 ->with($credentials[0]['Id'])
			 ->will($this->returnValue($this->mockUserQuery));
		
		$actual = $this->mockUser->login($this->mockRequest, $this->mockApp);

		$this->assertEquals($expected, $actual);
	}

	//Quando a senha contem menos de oito caracteres e o usuario nao e um email valido (Validacao)
	/*public function testLoginDataValidationIncorrect(){
		$data = ['user'=>'pablohotmail.com', 'password'=>'pa3'];

		$validationResult = $this->mockValidations->validateLogin($data);

		$validatonResult = (string) $validationResult;

		$expected = "Array[user]:\n    This value is not a valid email address. (code bd79c0ab-ddba-46cc-a703-a7a4b08de310)\nArray[password]:\n    This value is too short. It should have 8 characters or more. (code 9ff3fdc4-b214-49db-8718-39c315e33d45)\n";

		$this->assertEquals($expected, $validationResult);
	}

	//Quando os campos estao em branco
	public function testLoginDataValidationBlackFields(){
		$data = ['user'=>'', 'password'=>''];

		$validationResult = $this->mockValidations->validateLogin($data);

		$validatonResult = (string) $validationResult;

		$expected = "Array[user]:\n    This value should not be blank. (code c1051bb4-d103-4f74-8988-acbcafc7fdc3)\nArray[password]:\n    This value should not be blank. (code c1051bb4-d103-4f74-8988-acbcafc7fdc3)\n";

		$this->assertEquals($expected, $validationResult);
	}*/

}