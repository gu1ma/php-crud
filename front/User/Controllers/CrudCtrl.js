(function() {
    'use strict';

    var url = 'http://localhost/php-crud-project/back/web/index.php/';

    angular
        .module('user')
        .controller('CrudCtrl', CrudCtrl);

    CrudCtrl.$inject = ['$scope', '$state', '$http', '$window'];

    function CrudCtrl($scope, $state, $http, $window) {
        var crud  = this;
        crud.newUser = newUser;
        crud.getAllUsers = getAllUsers;
        crud.edit = editUser;
        crud.delete = deleteUser;
        $scope.items = [
            {"profile":1, "value":"Estagiario PHP"},
            {"profile":2, "value":"Desenvolvedor PHP Junior"},
            {"profile":3, "value":"Desenvolvedor PHP Senior"}
        ]

        //Função para criar um novo usuário
        function newUser(){
            var data = {
                "name":crud.name,
                "email":crud.email,
                "password":crud.password,
                "profile":crud.profiles.profile
            };

            $http
            .post(url+'api/users',data)
            .success(function(data) {
                if(data.status == 'Sucess'){
                    alert('Cadastrado com sucesso!');
                    $window.location.reload();
                } else if(data.status == 'Error'){
                    alert('Houve algum erro ao cadastrar');
                } 
            }).
            error(function(data) {
                alert('Houve um erro ao tentar criar novo usuário!');
            });
        }

        //Função para capturar os dados dos usuários cadastrados no banco
        function getAllUsers(){
            //Requisição get para capturarmos todos os usuários cadastrados
            $http
            .get(url+'api/users')
            .success(function(data) {
                crud.users = data.Users;
            })
            .error(function(data) {
                alert('Error!');
            });
        }

        function editUser(id){
            var data = {
                "name": $scope.user.name, 
                "email": $scope.user.email,
                "password": $scope.user.password,
                "profile": crud.profiles.profile
            }
            $http
            .put(url+'api/users/'+id, data)
            .success(function(data) {
                if(data.status == 'Sucess'){
                    alert('Cadastro atualizado com sucesso!');
                    $window.location.reload();
                } else if(data.status == 'Error'){
                    alert(data.statusMessage);
                }
                
            }).
            error(function(data, status, headers, config) {
                alert('Houve um erro ao tentar editar usuário!');
            });
            
        }

        function deleteUser(id){ 
            $http.delete(url+'api/users/'+id)
            .success(function(data) {
                if(data.status == 'Sucess'){
                    alert('Usuário deletado com sucesso!');
                    $window.location.reload();
                } else if(data.status == 'Error'){
                    alert(data.statusMessage);
                }              
            })
            .error(function(data) { 
                alert('Houve um erro ao tentar deletar usuário!');              
            });
        }
	}       
})();