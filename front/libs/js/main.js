var app = angular.module("akerApp", ['ngRoute']);

app.config(function($routeProvider){
	$routeProvider
	.when('/home', {
		templateUrl: 'views/home.html',
		controller: 'loginController'
	})
	.when('/listAllUsers', {
		templateUrl: 'views/listUsers.html',
		controller: 'listUsersController'
	})
	.when('/createUser', {
		templateUrl: 'views/createUser.html',
		controller: 'createUserController'
	})
	.when('/updateUser', {
		templateUrl: 'views/updateUser.html',
		controller: 'updateUserController'
	})
	.when('/deleteUser', {
		templateUrl: 'views/deleteUser.html',
		controller: 'deleteController'
	})
	.when('/account', {
		templateUrl: 'views/account/user.html',
		controller: 'userController'
	})
	.otherwise({
		redirectTo: '/home'
	});
});


//Função que retorna os ítens possíveis para seleção no formulário
function itens(){
	return [
		{"cargo":1, "value":"Estagiario PHP"},
		{"cargo":2, "value":"Desenvolvedor PHP Junior"},
		{"cargo":3, "value":"Desenvolvedor PHP Senior"}
	]
}

app.controller('loginController', function($scope, $http, $window){
	$scope.enviar = function(){
		
		var url = 'http://localhost/TreinamentoAker/web/index.php/user/login';
		var credentials = '{"user":"'+$scope.user.user+'", "password":"'+$scope.user.pass+'"}';

		$http.defaults.headers.post['Content-Type'] = 'application/json';
		$http.post(url,credentials)
			.success(function(data, status, headers, config) {
            //alert(data);
        		$window.location.href = 'http://localhost/TreinamentoAker/web/#/account';
      		})
      		.error(function(data, status, headers, config) {
        	// called asynchronously if an error occurs
        	// or server returns response with an error status.
            var win = window.open('', 'printwindow');
            win.document.write(data);
            //win.print();
      			//alert(data);
      		});
	}
});

app.controller('userController', function($scope, $http, $window){
	var data; 
	var url = 'http://localhost/TreinamentoAker/web/index.php/user/loged';

	//Função que faz uma requisição post para o silex, com o comando a ser executado no linux pelo PHP
    function linuxCommand(command){
		var url = 'http://localhost/TreinamentoAker/web/index.php/syslog/command';
    	var data = '{"command":"'+command+'"}';
    	var retorno;
    	$http.post(url,data)
			.success(function(data, status, headers, config) {
        		//Atualiza o campo syslogstatus com a resposta da requisição(status do syslog)
        		$scope.syslogstatus = data;
      			//alert(data);
      		})
      		.error(function(data, status, headers, config) {
        	// called asynchronously if an error occurs
        	// or server returns response with an error status.
      			$scope.syslogstatus = data;
      			//alert(data);
      			//return data;
      			//retorno = data;
      		});
	}

	//Função para testar se já tem alguma sessão ativa no momento
	$scope.testSession = function(){
		linuxCommand('sudo /etc/init.d/syslog-ng status');
		$http.get(url)
		.success(function(data, status, headers, config) {
			//Se estiver logado preenche os dados na tela com o json recebido
			$scope.user = data;
	    })
	    .error(function(data, status, headers, config) {
	      	//Caso o usuário não esteja logado redirecionamos para a página inicial
	      	$window.location.href = 'http://localhost/TreinamentoAker/web/#/home';
	    });
	}

	$scope.syslogConfig = function(){
		var url = 'http://localhost/TreinamentoAker/web/index.php/syslog/lastconfig';
		$http.get(url)
			.success(function(data, status, headers, config) {
        		//Atualiza o campo syslogstatus com a resposta da requisição(status do syslog)
        		//$scope.syslogstatus = data;
      			//alert(data);
      			$scope.database = data.database;
      			$scope.collection = data.collection;
      		})
      		.error(function(data, status, headers, config) {
        	// called asynchronously if an error occurs
        	// or server returns response with an error status.
      			//$scope.syslogstatus = data;
      			//alert(data);
      			//return data;
      			//retorno = data;
      		});


		//$scope.database = 'testeDB';
		//$scope.collection = 'testeCollection';
	}	

	//Função que finaliza a sessão de um usuário
    $scope.sair = function(){
    	var url = 'http://localhost/TreinamentoAker/web/index.php/user/logout';
    	$http.get(url)
    		.success(function(data, status, headers, config) {
        		$window.location.href = 'http://localhost/TreinamentoAker/web/#/home';
      		})
      		.error(function(data, status, headers, config) {
        	// called asynchronously if an error occurs
        	// or server returns response with an error status.
      			alert(data);
      		});
    }

    $scope.initsyslog = function(){
    	linuxCommand('sudo /etc/init.d/syslog-ng start')
    }

    $scope.stopsyslog = function(){
    	linuxCommand('sudo /etc/init.d/syslog-ng stop');
    }

    $scope.sendConfig = function(){
    	var mongoConfig = '{"database":"'+$scope.database+'", "collection":"'+$scope.collection+'"}';
    	//alert(mongoConfig);
    	var url = 'http://localhost/TreinamentoAker/web/index.php/syslog/config';
    	$http.defaults.headers.post['Content-Type'] = 'application/json';
    	$http.post(url, mongoConfig)
    		.success(function(data, status, headers, config) {
        		alert(data);
      		})
      		.error(function(data, status, headers, config) {
        	// called asynchronously if an error occurs
        	// or server returns response with an error status.
      			var win = window.open('', 'printwindow');
            win.document.write(data);
      		});
    }
});

app.controller('listUsersController', function($scope, $http){
	//Requisição get para capturarmos todos os usuários cadastrados
	$http
		.get('http://localhost/TreinamentoAker/web/index.php/api/users')
		.success(function(data, status, headers, config) {
            // this callback will be called asynchronously
            // when the response is available
            $scope.users = data.Users;
        //alert(data.Users.Id);
      }).
      error(function(data, status, headers, config) {
          // called asynchronously if an error occurs
          // or server returns response with an error status.
        $scope.msg = 'Houve algum erro ao cadastrar!!!';
      });
});

app.controller('createUserController', function($scope, $http){
	//Itens para  o select em html
	$scope.itens = itens();

    //Essa função será lançada quando o usuário apertar o botão enviar
	$scope.enviar = function(){		
		//Endereço para a requisição 
		var url = 'http://localhost/TreinamentoAker/web/index.php/api/users';
		//Dados em JSON 
		var data = '{"name":"'+$scope.user.nome+'","email":"'+$scope.user.email+'" ,"password":"'+$scope.user.senha+'", "profile":'+$scope.user.cargos.cargo+'}';
		//alert(data);
		//Requisição POST
		$http.defaults.headers.post['Content-Type'] = 'application/json';
		$http.post(url,data)
			.success(function(data, status, headers, config) {
        		// this callback will be called asynchronously
        		// when the response is available
        		$scope.msg = 'Cadastrado com sucesso!!!';
      		}).
         	error(function(data, status, headers, config) {
        	// called asynchronously if an error occurs
        	// or server returns response with an error status.
            var win = window.open('', 'printwindow');
            win.document.write(data);
      			$scope.msg = 'Houve algum erro ao cadastrar!!!';
      		});
	}
});

app.controller('updateUserController', function($scope, $http){
	//Itens para  o select em html
	$scope.itens = itens();

	$scope.enviar = function(){
		//Endereço para a requisição 
		var url = 'http://localhost/TreinamentoAker/web/index.php/api/users/'+$scope.user.id;
    //Dados em JSON 
		var data = '{"name":"'+$scope.user.nome+'","email":"'+$scope.user.email+'", "password":"'+$scope.user.senha+'", "profile":'+$scope.user.cargos.cargo+'}';
		//alert(url+'\n'+data);
    //alert('Click');
		//Requisição PUT para atualizar os dados, cabecalho informando o tipo de dados a ser enviado
		//alert(data);
		$http.defaults.headers.put['Content-Type'] = 'application/json';
		$http.put(url, data)
		.success(function(data, status, headers, config) {
        		// this callback will be called asynchronously
        		// when the response is available
        		$scope.msg = 'Cadastro atualizado com sucesso!!!';
      		}).
      		error(function(data, status, headers, config) {
        	// called asynchronously if an error occurs
        	// or server returns response with an error status.
      			$scope.msg = 'Houve algum erro ao atualizar!!!';
            //alert(data);
      		});
	}

});

app.controller('deleteController', function($scope, $http){
	$scope.enviar = function(){
		var url = 'http://localhost/TreinamentoAker/web/index.php/api/users/'+$scope.user.id;
		//$scope.msg = url;
		$http.delete(url)
			.success(function(data, status, headers, config) {
        		// this callback will be called asynchronously
        		// when the response is available
        		$scope.msg = 'Usuário deletado com sucesso!!!';
      		}).
      		error(function(data, status, headers, config) {
        	// called asynchronously if an error occurs
        	// or server returns response with an error status.
      			$scope.msg = 'Houve algum erro ao deletar usuário!!!';
            //alert(data);
      		});
	}

});

