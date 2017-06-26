(function() {
    'use strict';

    var url = 'http://localhost/php-crud-project/back/web/index.php/';

    angular.module('syslog')
        .controller('SyslogCtrl', SyslogCtrl);

    SyslogCtrl.$inject = ['$scope', '$state', '$http'];

    function SyslogCtrl($scope, $state, $http) {
        var syslog  = this;
        syslog.initSyslog = initSyslog;
        syslog.stopSyslog = stopSyslog;
        syslog.sendMongoConfig = sendMongoConfig;
        syslog.getLastConfig = getLastConfig;
        syslog.getStatus = getStatus;
        syslog.getCountLogs = getCountLogs;

        //Função que faz uma requisição post para o silex, com o comando a ser executado no linux pelo PHP
        function linuxCommand(command){
            var data = {
                "command":command
            }

            $http
            .post(url+'syslog/command',data)
            .success(function(data, status, headers, config) {
                //Atualiza o campo syslogstatus com a resposta da requisição(status do syslog)
                $scope.syslogStatus = data.statusMessage;
            })
            .error(function(data, status, headers, config) {
                //$scope.syslogStatus = data;
            });
        }

        function getStatus(){
            linuxCommand('sudo /etc/init.d/syslog-ng status');
            getLastConfig();
        }

        function initSyslog(){
            linuxCommand('sudo /etc/init.d/syslog-ng start');
        }

        function stopSyslog(){
            linuxCommand('sudo /etc/init.d/syslog-ng stop');
            linuxCommand('sudo pkill syslog-ng');
        }
       
        function sendMongoConfig(){
            var mongoConfig = {
                "database":$scope.database,
                "collection":$scope.collection
            }

            $http
            .post(url+'syslog/config', mongoConfig)
            .success(function(data, status, headers, config) {
                alert(data.statusMessage);
            })
            .error(function(data, status, headers, config) {
                var win = window.open('', 'printwindow');
                win.document.write(data);
            });
        }

        function getLastConfig(){
            $http
            .get(url+'syslog/lastconfig')
            .success(function(data) {
                $scope.database = data.database;
                $scope.collection = data.collection;
            })
            .error(function(data) {
                alert(data.statusMessage);
            });
        }

        function getCountLogs(){
            $http
            .get(url+'syslog/logs')
            .success(function(data) {
                $scope.syslog.count = data;
            })
            .error(function(data) {
                alert(data);
            });
        }
    }
})();