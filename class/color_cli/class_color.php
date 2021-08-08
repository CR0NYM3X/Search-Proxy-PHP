<?php

	class Color_texto
	{
		private $color=array("negro"=>30,
							 "rojo"=>31,
 							 "verde"=>32,
							 "naranja"=>33,
							 "azul"=>34,
							 "morado"=>35,
							 "azul_light"=>36,
							 "blanco"=>37,
							 "normal"=>38,
							 "negro1"=>90,
							 "rojo1"=>91,
 							 "verde1"=>92,
							 "naranja1"=>93,
							 "azul1"=>94,
							 "morado1"=>95,
							 "azul_light1"=>96,
							 "blanco1"=>97,
							 "normal1"=>98);

		private $fondo=array("negro"=>40,
							 "rojo"=>41,
 							 "verde"=>42,
							 "naranja"=>43,
							 "azul"=>44,
							 "morado"=>45,
							 "azul2"=>46,
							 "blanco"=>47,
							 "normal"=>48,
							 "negro1"=>100,
							 "rojo1"=>101,
 							 "verde1"=>102,
							 "naranja1"=>103,
							 "azul1"=>104,
							 "morado1"=>105,
							 "azul_light1"=>106,
							 "blanco1"=>107,
							 "normal1"=>108);


		public function txtcolor($string,$estilo=null,$txtcolor=null,$fondocolor=null)
		{
			if ($string)			
			{

			
			if($estilo>=0 && $estilo<=9)
			{
				$color_string ="\033[".$estilo.";";
			}else{
				die("  El numero  $estilo no existe por favor Consulte \$texto->Estilos()\n  Para ver los numeros para estilos\n");
			}
			
	
			if (isset($this->color[$txtcolor])) 
			{
				$color_string.=$this->color[$txtcolor].";";
			}else{
				die("  El color  $txtcolor no existe por favor Consulte \$texto->Colores()\n  Para ver los colores\n");
			}
			


			if (isset($this->fondo[$fondocolor]))  
			{
				//$color_string.=$this->fondo[$fondocolor]."m";
				$color_string.=$this->fondo[$fondocolor]."m";
			}else{
				die("  El fondo  $fondocolor no existe por favor Consulte \$texto->Fondos()\n  Para ver los Fondos\n");
			}
			


			return $color_string .=  $string ."\033[0m";

			}else
			{
				echo " Porfavor ingrese el texto";
			}

		}


		// Caracteristicas de los colores, Fondos, Estilos
		public function Colores()
		{
		
			echo "Tipos de colores para Texto:\n\n";
			foreach ($this->color as $key => $value) {
			echo " [#] ".$key."\n";	
			}
			echo "---> Los colores que terminen en 1 es porque son mas fuertes\n";

		}



		public function Fondos()
		{
			echo "Tipos de Fondos:\n\n";
			foreach ($this->fondo as $key => $value) {
			echo " [#] ".$key."\n";	
			}
			echo "---> Los colores que terminen en 1 es porque son mas fuertes\n";

		}



		public function Estilos()
		{
			echo "Tipos de Estilos para el texto:\n\n";
			echo  " [#]  0  texto por defaul de la terminal\n";
			echo  " [#]  1  \033[1mHace el texto grueso\033[0m\n";
			echo  " [#]  2  \033[2mAclara  el color del texto\033[0m\n";
			echo  " [#]  4  \033[4mSubralla el texto\033[0m\n";
			echo  " [#]  8  Hace invicible todo el texto -->\"\033[8m Texto invicible\033[0m\"\n";
			echo  " [#]  9  \033[9mAtrabieza una linea en todo el texto\033[0m\n";


		}
		public function ayuda()
		{

			echo " Metodos para utilizar y ejemplos\n\n";
			echo "   \$texto->txtcolor(\"Texto\",Numero estilo,\"color de texto\",\"color de fondo\");\n\n";
			echo "                          Ejemplo\n";
			echo "   \$texto->txtcolor(\"Hola Mundo\",1,\"negro1\",\"verde1\")\n\n";


			echo "   \$texto->colores(); para ver los nombres de los colores Existentes\n"; 
			echo "   \$texto->estilos(); para ver los numeros de los estilos Existentes\n";   
			echo "   \$texto->fondos();  para ver los nombres de los fondos Existentes\n"; 
		}

 
	}
// $texto= new Color_texto();


//             $texto->txtcolor(texto,estilo,color_texto,color_fondo);
//$texto->txtcolor("Este es el mensaje en el cual modificare\n",1,"blanco","negro1");
//$texto->colores();  // para ver los colores Existentes
//$texto->estilos();  // para ver los estilos Existentes
//$texto->fondos();  // para ver los fondos Existentes
//$texto->ayuda(); // ayudaditass



?>