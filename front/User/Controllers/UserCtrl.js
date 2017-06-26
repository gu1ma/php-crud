(function() {
    'use strict';

    var url = 'http://localhost/php-crud-project/back/web/index.php/';

    angular
        .module('user')
        .controller('UserCtrl', UserCtrl);

    UserCtrl.$inject = ['$state', '$http'];

    function UserCtrl($state, $http) {
        var user  = this;
        user.logout = logout;
        user.isLogged = isLogged;

        function logout(){
          	$http
          	.get(url+'user/logout')
      			.success(function(data) {
                if(data.status == 'Sucess'){
          			   $state.go('login');
                }
        		})
        		.error(function(data) {
          		  alert('Houve um erro ao tentar efetuar logout!');
        		});
        }

        function isLogged(){
  	        $http
  	        	.get(url+'user/logged')
  				    .success(function(data) {
  					       //Se estiver logado não faz nada, deixa o usuário acessar a página
                   if(data.status == 'Sucess'){
                      //deixa ficar na pagina
                   } else if(data.status == 'Error'){
                      $state.go('login');
                   }
  			       })
  			       .error(function(data) {
    			      	alert('Houve um erro ao tentar verificar login!');
  			       }); 
		    }       
    }
})();