(function() {
    'use strict';

    var url = 'http://localhost/php-crud-project/back/web/index.php/';

    angular.module('login')
        .controller('LoginCtrl', LoginCtrl);

    LoginCtrl.$inject = ['$state', '$http'];

    function LoginCtrl($state, $http) {
        var login  = this;
        login.send = send;

        function send() {
        	var credentials = {
        		'user': login.user,
        		'password': login.password
        	}
        	$http
                .post(url+'user/login',credentials)
				.success(function(data) {
	        		if(data.status == 'Sucess'){
                        $state.go('user');
                    } else {
                        alert(data.statusMessage);
                    }
	      		})
	      		.error(function(data) {
		            alert('Houve um erro ao tentar logar!');
	      		});
        }
    }
})();
