<?php

require_once("class/class_curl/facilcurl.php"); #IMPORTANDO CLASE DE CURL
require_once("class/color_cli/class_color.php");

//ini_set("default_socket_timeout", 6000);
error_reporting(0);  // para no mostrar los errores cuando un puerto no exista

/* #paginas donde se pueden ver proxys 
https://proxylist.geonode.com/api/proxy-list?limit=50&page=2&sort_by=lastChecked&sort_type=desc&country=MX
https://geonode.com/free-proxy-list
https://www.socks-proxy.net/
http://free-proxy.cz/en/
https://www.proxynova.com/proxy-server-list
https://www.proxyscan.io/
https://hidemy.name/es/proxy-list/?start=192#list
*/


// Son 350 puertos comunes que se usan en servidores proxy
//$puertosProxy= [81,82,83,84,87,808,999,1000,1080,1081,1256,2003,2020,2021,2120,2580,3000,3081,3122,3127,3128,3129,3131,3133,3140,3142,3150,3161,3163,3166,3256,3328,3333,3629,3888,4000,4145,4153,4216,4645,5678,6588,6666,7878,8000,8005,8060,8081,8082,8083,8085,8088,8089,8090,8118,8123,8181,8213,8380,8382,8385,8585,8686,8811,8828,8880,8888,8889,8899,9000,9001,9090,9300,9991,9992,9999,10000,10102,11335,12345,13231,13909,14571,16993,18321,20183,21213,21231,23492,23500,26260,29670,30072,30093,30142,30531,30640,30865,31111,31113,31228,31372,31398,31475,31511,31596,31707,31785,32108,32222,32231,32329,32412,32721,32767,32842,32849,33017,33038,33054,33060,33091,33208,33326,33428,33549,33630,33761,33786,34082,34184,34307,34359,34388,34575,34641,34730,34808,35090,35101,35138,35350,35608,35614,35844,35938,35945,35975,36314,36493,36506,36731,36984,37060,37083,37331,37409,37438,37444,37475,37717,37879,38051,38157,38178,38246,38351,38433,38472,38627,38646,38656,38779,38888,39143,39168,39369,39593,39611,39746,39810,39818,39880,40045,40049,40115,40219,40365,40527,40569,40768,40894,41258,41344,41466,41583,41715,41820,42113,42134,42492,42501,42535,42549,42590,42614,42914,43032,43036,43327,43395,43399,43520,43567,43772,43797,43844,43853,43891,43980,44047,44059,44393,44759,44761,44976,45099,45160,45178,45225,45282,45396,45521,45571,45578,45597,45692,45729,45822,46185,46317,46365,46547,46646,46669,46675,46789,46944,46977,47009,47045,47211,47247,47277,47326,47424,47437,47507,47532,48324,48458,48586,48678,48929,49044,49094,49309,49602,49787,50000,50007,50128,50177,50538,50625,50759,51008,51056,51166,51487,51489,51657,51680,51729,51915,52018,52076,52820,52827,53100,53281,53438,53731,53758,53916,54018,54256,54321,54555,54567,54621,55207,55357,55443,55716,55855,56218,56220,56297,56351,56816,56975,57114,57396,57783,57797,57903,58302,58389,58573,58687,58893,58901,58928,59021,59330,59458,59888,59933,60020,60103,60517,60604,60779,60852,60981,61148,61410,62694,63000,63123,63141,63238,64312,65238,80801,80811,80,8080,443];



// SI TIENES INTERNET MALO PUEDES USAR ESTE ARRAY
$puertosProxy= [80,8080,8081,8090,8888,8123,9999,443,81,5678,4153,4145,10801,1080,1088,3128,9991,3129,31337,12345];



/**
* Inserta en la base de datos el proxy detectado
*	$ip		=> IP que se guardara
*	$port	=> puerto que se guardara 
*
*/

function insertSQL($ip,$port)
{

	$conexion= mysqli_connect("127.0.0.1","root",""); // ip - usuario - contraseña
	mysqli_select_db($conexion,"proxys");  // nombre base de datos
	mysqli_set_charset($conexion,"utf8");


	$sql= "INSERT INTO ips (ip,port) VALUES ( '".$ip."','".$port."')"; 


	// En caso de un Error al guardar, se mostrara en el archivo llamado Error.txt 
	if (!(mysqli_query($conexion, $sql))) {

		$Data = fopen("error.txt", "a");
		fwrite($Data, "ERROR SQL   ".date('m-d-Y h:i:s a', time())." | $sql ". PHP_EOL);
		fclose($Data);

	}
	mysqli_close($conexion);

}



/**
* hace la verificacion de un puerto
*	$ip		=> IP 
*	$puerto	=> puerto 
*	$time 	=>	Tiempo limite para escanear IP:PORT 
*/

function isOpenPort($ip,$puerto,$time=0.5)
{

	$texto= new Color_texto();
	if( ($fp =fsockopen("tcp://".$ip, $puerto, $err, $errn, $time)) )  // Verifica IP:PORT  que exista
	{
	
		// Si existe se hace una consulta a google con IP:PORT
		$mm= new facilcurl();
		//$mm->tiempos(3,false,120); // tiempo de tolerancia de espera para la pagina y si se activa el true el numero sera en milisegundos
		$mm->proxy($ip,$puerto);  
		$mm->curl("www.google.com"); 
		


		if ( ($pag= $mm->exe_curl()) ) //  verificar si IP:PORT se conecto
		{
			echo $texto->txtcolor("|".$puerto,1,"verde","negro");

			insertSQL($ip,$puerto); // Inserta el proxy IP:PORT a la base de datos
		}
		else
		{
			echo $texto->txtcolor("|".$puerto,1,"rojo","negro");
		} 

	}
	else
	{

		//echo $texto->txtcolor( $puerto." Close\n",1,"rojo","negro");
	}


}



/**
* 	Abre procesos para que consulte si una IP y distintos puertos si estan abiertos
*	$ip 		=> IP que quieres que consulte
*	$puertos 	=> Se manda un Array() con numeros de puertos como { $puertosProxy }
*
*/

function hilos($ip,$puertos)
{

	foreach ($puertos as $dpuerto) {
	  $pid = pcntl_fork();


	  if ($pid == -1) {
	    // si hay un error con el proceso entra aqui
	    //exit("Error forking...\n");
	  }else if($pid){

	  	//	Proceso Padre
	  	$pids[$pid] = $pid;
	  	

	  }else{

	  	// Aqui son los procesos hijos, aqui se le dice a cada hijo que hacer, en este caso manda llamar la funcion { isOpenPort() }
	    isOpenPort($ip,$dpuerto);
	    exit(); //cuando termines cierrate

	  }

	}

}





/**
*	Esta funcion divide ( Cantidad de puertos / hilos que se quieren abrir ) los puertos se ingresan a un array() y se mandan consultar por cantidad 
*	$ip 		=> IP que quieres que consulte
*	$cntHilos	=> Cantidad de Procesos que quieres que se abran
*/

function manejoPuerto($ip,$cntHilos=20)
{


	global $puertosProxy; # Lista de puertos
	$juntandoPuertos=[]; 
	$limiteHilo  =  $cntHilos;
	$cntCiclo=0;


	foreach ($puertosProxy as  $puerto) {
		$cntCiclo++;
		$juntandoPuertos[].= $puerto;

		if ( $limiteHilo == $cntCiclo) {

			//print_r($juntandoPuertos);
			
			hilos($ip,$juntandoPuertos);  

			while(pcntl_waitpid(0, $status) != -1);
			$juntandoPuertos=[];
			$limiteHilo += $cntHilos;

		}

	}

	// si la cantidad de puertos es mas grande que los hilos que se van a usar es necesario usar estas lineas
	//while(pcntl_waitpid(0, $status) != -1);
	//hilos($ip,$juntandoPuertos);

}



# simple ejemplos de funciones
//manejoPuerto("149.19.224.30");

//isOpenPort("149.19.224.30",80);
//isOpenPort("149.19.224.29",3128,$time=0.5);


//while(pcntl_waitpid(0, $status) != -1);










?>