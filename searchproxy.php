<?php


/** 
 * Este script busca proxy haciendo un barrido de ips publicas y consulatando los puertos mas comunes que utilizan  los servidores proxy
 * 
 * @package : SearchProxy;
 * @version : 1.0;
 * @author 	: ; CronyMex
 * @link 	: https://github.com/CR0NYM3X;
 * 
 */


/*
Color Descripcion
Verde	: Es un proxy
Rojo	: El puerto esta abierto pero no es un proxy
Morado	: El puerto contesta la peticion de google pero  no retorn el html de google, conclusion no es un proxy que se pueda usar

*/


require_once("conf.php");
date_default_timezone_set('America/cancun'); #AJUSTANDO ZONA HORARIA DE MI CIUDAD


/*
IP RESERVADAS: https://es.wikipedia.org/wiki/Anexo:Direcciones_IP_reservadas
* 0.0.0.0     – 0.255.255.255	  - [Software] Ip Red actual​ (solo válido como dirección de origen). 
* 10.0.0.0 	– 10.255.255.255  - Clase A -Utilizado para las comunicaciones locales dentro de una red privada.4
​* 100.64.0.0	– 100.127.255.255 - Espacio de direcciones compartido5​ para las comunicaciones entre un proveedor de servicios y sus suscriptores cuando se utiliza un NAT de nivel de 									operador.
* 127.0.0.1	- 127.255.255.25	  - Se utiliza para las direcciones de loopback.3​
* 169.254.0.0 – 169.254.255.255 - Clase B simple  - Se utiliza para las direcciones de enlace local6​l entre dos hosts en un solo enlace cuando de otra manera no se especifica una 									dirección IP, como 	normalmente se habría recuperado de un servidor DHCP.

* 172.16.0.0 	– 172.31.255.255  - Clase B - 	Utilizado para las comunicaciones locales dentro de una red privada.4​
* 192.0.0.0	– 192.0.0.255	  - IETF Protocol Assignments.3​
* 192.0.2.0	– 192.0.2.255	  - Asignada como TEST-NET-1, para documentación y ejemplos.7​
* 198.18.0.0	– 198.19.255.255  - 	Se utiliza para pruebas de referencia de comunicaciones entre dos subredes separadas.10​
* 192.88.99.0 – 192.88.99.255	  - Reservada.8​ Previamente usado para relay IPv6 a IPv4.9​ (incluido el bloque de direcciones IPv6 2002::/16).
* 198.51.100.0 –198.51.100.255  - Asignado como TEST-NET-2, para documentación y ejemplos.7​
* 192.168.0.0 – 192.168.255.255 - Clase C - Utilizado para las comunicaciones locales dentro de una red privada.4​
* 203.0.113.0 – 203.0.113.255   - Asignado como TEST-NET-3, para documentación y ejemplos.7​
* 224.0.0.0   – 255.255.255.255 - Usado para Multicast IP.11​ (previamente una red clase D). (Experimental) Reservada para usos futuros.12​ (anteriormente una red clase E). (Experimental) estas direcciones solo se pueden utilizar para fines de investigación o experimentación 
* 255.255.255.255				  - Broadcast
*/

# Total ips: 4,294,967,295

//149.19.224.30
#  0	.	0	.	0	.	0
$Oct1=149; $Oct2=19; $Oct3=224; $Oct4=28;


$ip= $Oct1.'.'.$Oct2.'.'.$Oct3.'.'.$Oct4;

$mascara=255;

$cntIPS=1;  $CalculoIP=pow(($mascara+1), 4);

$ConsultaIPS=[];
$cntConsultas=0;
$MostrarCantidad=3;

$i=100000;
echo date('m-d-Y h:i:s a', time())."\n"; 



// BUCLE ENTRA POR DEFECTO
while (true) 
{
	



	// OCTETO #4
	while ( ($Oct4 !=  $mascara) )
	{	
		

		// IP RESERVADA  0.0.0.0     – 0.255.255.255
		if ( ($Oct1 == 0) ) {
			$Oct1++; $cntIPS++;
			//break 1;
		}
		else
		{
		$Oct4++; $cntIPS++;

		}


		echo "\n".$ip= $Oct1.'.'.$Oct2.'.'.$Oct3.'.'.$Oct4;

		  manejoPuerto($ip); // aqui manda a consultar la ip y sus posibles puertos de proxys
		  while(pcntl_waitpid(0, $status) != -1);


	}







	// OCTETO #3
	while ( ($Oct3 !=  $mascara) and ($Oct4 >= $mascara) )
	{	
		$Oct3++; $Oct4=0;  $cntIPS++;
	

		//  		192.0.0.0/8 - 192.0.2.0/8 - 192.88.99.0/8 -  198.51.100.0/8 - 203.0.113.0/8 
		if ( (($Oct1 == 192) and ($Oct2 == 0) and ($Oct3 == 0))		or (($Oct1 == 192) and ($Oct2 == 0) and ($Oct3 == 2)) or (($Oct1 == 192) and ($Oct2 == 88) and ($Oct3 == 99)) or
			 (($Oct1 == 198) and ($Oct2 == 51) and ($Oct3 == 100))	or (($Oct1 == 203) and ($Oct2 == 0) and ($Oct3 == 113)) ) 
		{
 			$Oct3++;	
		}

		echo "\n".$ip= $Oct1.'.'.$Oct2.'.'.$Oct3.'.'.$Oct4;
		
		manejoPuerto($ip);
		 while(pcntl_waitpid(0, $status) != -1);


	}



	// OCTETO #2
	while ( ($Oct2 !=  $mascara) and ($Oct3 >= $mascara)  and ($Oct4 >= $mascara) )
	{
		
		$Oct2++; $Oct3=0; $Oct4=0;  $cntIPS++;

		
		//  169.254.0.0/16  - 192.168.0.0/16
		if ( (($Oct1 == 169) and ($Oct2 == 254)) or (($Oct1 == 192) and ($Oct2 == 168)) )   
		{
			$Oct2++;
		}


		//  100.64.0.0	– 100.127.255.255  |   198.18.0.0	– 198.19.255.255 | 172.16.0.0 	– 172.31.255.255
		if ( ($Oct1 == 100) and ($Oct2 == 64) ) 
		{
			$Oct2=128;
		}
		elseif ( ($Oct1 == 198) and ($Oct2 == 18) ) 
		{
			$Oct2=20;
		}
		elseif ( ($Oct1 == 172) and ($Oct2 == 16) ) 
		{
			$Oct2=32;
		}

		echo "\n".$ip= $Oct1.'.'.$Oct2.'.'.$Oct3.'.'.$Oct4;
	

		manejoPuerto($ip);
		 while(pcntl_waitpid(0, $status) != -1);

	

	}



	// OCTETO #1
	while ( (($Oct1 !=  $mascara) and ($Oct2 >= $mascara) and ($Oct3 >= $mascara)  and ($Oct4 >= $mascara)  )  )
	{
	
		
		$Oct1++; $Oct2=0; $Oct3=0; $Oct4=0;  $cntIPS++;

	

		if( ($Oct1 == 10) or ($Oct1 == 127) or ($Oct1 == 224) )  // 10.0.0.0/24 - 127.0.0.1/24 - 224.0.0.0/24
		{
			$Oct1++;
		}	


			echo "\n".$ip= $Oct1.'.'.$Oct2.'.'.$Oct3.'.'.$Oct4;

			
			manejoPuerto($ip);
			 while(pcntl_waitpid(0, $status) != -1);


	}


		



	# EN CASO SE HAGA UN BUCLE SE CIERRE 
	if ( ($Oct3 == $mascara) and ($Oct4 == $mascara) and ($Oct2 == $mascara) and ($Oct1 == $mascara) ) 
	{

		echo "\n\n".date('m-d-Y h:i:s a', time()); 
		echo  "\n"." IP Contadas : ".$cntIPS." y IP Calculadas ".$CalculoIP;
		echo "\n Cantidad de consultas internet : ".$cntConsultas;
		break;

	}


}


empty($ConsultaIPS);






?>