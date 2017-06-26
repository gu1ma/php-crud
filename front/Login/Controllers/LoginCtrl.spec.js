describe('Teste do modulo login da aplicacao',function(){
	var scope, http, state, controller, url = 'http://localhost/php-crud-project/back/web/index.php/';
	var Users;

	beforeEach(angular.mock.module('login'));

	beforeEach(inject(function($rootScope, $controller, $injector){
		scope = $rootScope.$new();
		$state = $injector.get('$state');
		$httpBackend = $injector.get('$httpBackend');
		controller = $controller('LoginCtrl as login', {
			$scope: scope
		});
	}));

	it('Verifica se o controller esta definido', function(){
		expect(controller).toBeDefined();
	});

	it('Verifica se o estado se mantem caso as credenciais sejam incorretas', function(){
		scope.login.user = 'naoexiste';
		scope.login.password = 'naoexiste';
		scope.login.send();
		expect(!$state.go);
	});

	it('Verifica se o estado muda caso as credenciais estejam corretas', function(){
		scope.login.user = 'pablo';
		scope.login.password = 'pablo123';
		scope.login.send();
		expect($state.go);
	});

});
