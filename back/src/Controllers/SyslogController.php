<?php
	namespace Controllers;

	use Symfony\Component\HttpFoundation\Request;
	use Symfony\Component\HttpFoundation\Response;
	use Silex\Application;
	use Silex\Api\ControllerProviderInterface;
	use Models\UserQuery;
	use Models\User;

	require_once __DIR__.'/../../vendor/autoload.php';

	class SyslogController implements ControllerProviderInterface{
		public function connect(Application $app){
			$appSys = $app['controllers_factory'];

			//Requisições get
			$appSys->get('/hello/{name}', 'Controllers\SyslogController::home');
			$appSys->get('lastconfig', 'Controllers\SyslogController::lastConfig');
			$appSys->get('logs', 'Controllers\SyslogController::getNumberLogs');

			//Requisições post
			$appSys->post('/command', 'Controllers\SyslogController::syslogCommand');
			$appSys->post('/config', 'Controllers\SyslogController::syslogConfig');

			return $appSys;
		}

		public function home(Application $appSys, $name){
			$teste = array('ola'=>$name);
			return $appSys->json($teste);
		}

		public function syslogCommand(Application $appSys, Request $request){
			$data = json_decode($request->getContent(), true);
        	exec($data['command']);
        	$command = exec('sudo /etc/init.d/syslog-ng status');
        	$status = explode(":", $command);

        	//return new Response($status[3], 200);
        	$return = array('status'=>'Sucess', 'statusMessage'=>$status[3]);
		    return $appSys->json($return);
		}

		public function syslogConfig(Application $appSys, Request $request){
			//Para o seviço do Syslog
        	exec('sudo /etc/init.d/syslog-ng stop');
        	$configs = json_decode($request->getContent(), true);
        	//Arquivo de configurações do syslog
        	$syslogConfig = fopen("/var/www/html/treinamentophp/back/syslog/syslog-ng.conf", "w");
        	$lastFileConfig = fopen("/var/www/html/treinamentophp/back/syslog/lastConfig.txt", "w");
            
        	$contentSyslogConfig = '@version: 3.5 
@include "/etc/syslog-ng/scl.conf"
@include "`scl-root`/system/tty10.conf"

# Syslog-ng configuration file, compatible with default Debian syslog
# installation.
# First, set some global options.
options { chain_hostnames(off); flush_lines(0); use_dns(no); use_fqdn(no);
  owner("root"); group("adm"); perm(0640); stats_freq(0);
  bad_hostname("^gconfd$");
};
        
source s_src {
    system();
    internal();
};

destination d_mongodb_user{
    mongodb(
    servers("localhost:27017")
        database("'.$configs['database'].'")
        collection("'.$configs['collection'].'")
        value-pairs(
            scope("selected-macros" "nv-pairs" "sdata")
        )        
        );
    };

########################
# Filters
########################
#filter f_debug { level(debug) and not facility(auth, authpriv, news, mail); };
#filter f_user { facility(user) and not filter(f_debug); };
#Mongo
log { source(s_src); destination(d_mongodb_user); };
###
# Include all config files in /etc/syslog-ng/conf.d/
###
@include "/etc/syslog-ng/conf.d/*.conf"
            ';

            $currentConfig = "database:".$configs['database']."
collection:".$configs['collection'];
            //Escreve as configuracoes do syslog no arquivo 
            fwrite($syslogConfig, $contentSyslogConfig);
            //Escreve as configuracoes usadas no syslog para verificação posterior 
            fwrite($lastFileConfig, $currentConfig);
            fclose($lastFileConfig);
            //Reinicia o syslog 
            $ok = fclose($syslogConfig);
            exec('sudo /etc/init.d/syslog-ng start');
            if($ok){
               	//return new Response("Ok", 200);
               	$return = array('status'=>'Sucess', 'statusMessage'=>'Ok');
		    	return $appSys->json($return);
            }
            //return new Response("Fail", 404);
            $return = array('status'=>'Error', 'statusMessage'=>'Falha nas configurações');
		    return $appSys->json($return);
		}

		public function lastConfig(Application $appSys){
			$file = fopen("/var/www/html/treinamentophp/back/syslog/lastConfig.txt", "r");
	        $lastConfig = array();
	        while(!feof($file)){
	            $line[] = fgets($file);
	        }
	        $database = explode(":", $line[0]);
	        $database = str_replace("\n", "", $database);
	        $collection = explode(":", $line[1]);
	        $collection = str_replace("\n", "", $collection);
	        fclose($file);
	        $returnJson["database"] = $database[1];
	        $returnJson["collection"] = $collection[1];

	        return $appSys->json($returnJson);
		}

		public function getNumberLogs(Application $appSys){
			$conn  = new \MongoDB\Driver\Manager("mongodb://localhost:27017");
			$query = new \MongoDB\Driver\Query(['HOST'=>'gabriel']);
			
			$file = fopen("/var/www/html/treinamentophp/back/syslog/lastConfig.txt", "r");
	        $lastConfig = array();
	        while(!feof($file)){
	            $line[] = fgets($file);
	        }
	        $database = explode(":", $line[0]);
	        $database = str_replace("\n", "", $database);
	        $collection = explode(":", $line[1]);
	        $collection = str_replace("\n", "", $collection);
	        fclose($file);
	        $sysConfigs["database"] = $database[1];
	        $sysConfigs["collection"] = $collection[1];

			$rows = $conn->executeQuery($sysConfigs["database"].'.'.$sysConfigs["collection"], $query);

			return count($rows->toArray());
		}

	}

?>