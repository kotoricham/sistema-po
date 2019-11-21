<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Simplex extends CI_Controller {


	public function __construct()
	{
		parent::__construct();
	}
	public function index()
	{
		$dados = array(
			'variavel'=> $this->input->post('variaveis'),
			'restricao'=> $this->input->post('restricoes'),
			'funcao' =>'calcular',
	 		'controller' => 'simplex'
			);

		if (!empty($dados['variavel'])) {
			$sessao = array(
				'variavel'=> $this->input->post('variaveis'),
				'restricao'=> $this->input->post('restricoes')
			);
			$this->session->set_userdata($sessao);
		}
		elseif (empty($dados['variavel']) && !empty($this->session->userdata('variavel'))) {
			$dados['variavel'] = $this->session->userdata('variavel');
			$dados['restricao'] = $this->session->userdata('restricao');
		}
		

	 	$this->template->load('template/template', 'simplex/simplex_entrada', $dados);
		
		
	}

	public function calcular()
	{
		$z = $this->input->post('z');
		$restricao = $this->input->post('restricoes');//recebe a quantidade de restrições
		for ($i=0; $i < $restricao; $i++) { 
		$r[$i] = $this->input->post("r$i");//recebe as restricoes
		$ld[$i] = $this->input->post("ld$i");//recebe o lado direito
		$igualdade[$i] = $this->input->post("igualdade$i");//recebe as igualdades(menor-igual, igual, maior-igual) das restrições
		}

		$a = 0;

		$error = $this->verifica_error($r,$igualdade, $ld, $restricao);
		
		//exit;
		if (empty($error)) {

		$obj = $this->input->post("objetivo");//maximizar ou minimizar
		$tamanho = count($z);
		$tamanho_inicial = $tamanho;
		$z_ini = $z;
		for ($i=0; $i < $tamanho; $i++) { 
			$valores[$i] = $i;
		}
		$dados['tamanhoVarIni'] = $valores;//aqui ajuda na hora de identificar variaveis basicas e não basicas
		$tamanhofinal = $tamanho;
		
		if ($tamanho == 2) {
		
		$dados['restricoes'] = $r;
		$dados['lado_direito'] = $ld;
	
		}
		for ($i=0; $i < $restricao; $i++) {//tamanho depois do acrescimo das variaveis de folga e artificiais
			if ($igualdade[$i]==3) {
				$tamanhofinal = $tamanhofinal+2;
			}
			else{
				$tamanhofinal++;
			}
		}
		
		$retorno = $this->acrescenta_novas_variaveis($r, $restricao, $tamanho, $tamanhofinal, $igualdade);
		$r = $retorno['r'];
		$linArt = $retorno['linArt'];
		$contart = $retorno['contart'];
		$art = $retorno['art'];
		if(!empty($art)){
			$dados['pos_art'] = $art;
		}

		//print_r($contart);
		$tamanhoz = count($z);
		$z = $this->acrescenta_novas_variaveis_em_z($z, $tamanho, $tamanhofinal, $contart, $art);
		
		for ($i=0; $i < $tamanhoz; $i++) { 
			if ($obj == 1) { // maximizar
			$z[$i] = $z[$i] * -1; // vai deixando os valores do z negativos	
			}
			else{ //minimizar
				break;
			}
			
		}
		$iter = $this->preenche_matriz_iter_inicial($z, $r, $ld, $art, $contart, $linArt, $restricao);
		$contador = count($iter[0][0]);
		/*print_r($iter[0][0]);
		exit;*/
		if (in_array(2, $igualdade) || in_array(3, $igualdade)) {

			for ($i=1; $i < $restricao+1; $i++) { 
				for ($j=0; $j < $contador; $j++) { 

					if ($igualdade[$i-1]!=1) {
						if ($j == $contador-1) {//caso seja o lado direito
							$iter[0][0]['ld']['m'] -= $iter[0][$i]['ld'][0];
						}
						elseif ($iter[0][$i][$j]['m']==0) {
							$iter[0][0][$j]['m'] -= $iter[0][$i][$j][0];
						}
						else{
							$iter[0][0][$j]['m'] -= $iter[0][$i][$j]['m'];	
						}

					}

				}
			}
		}


		for ($i=0; $i < $restricao; $i++) { 
			$contador = count($r[$i]);
			for ($j=0; $j < $contador+1; $j++) {
			if ($j==count($r[$i])) {
			 	$iter[0][$i+1]['ld'][0] = $ld[$i][0];// coloca o lado direito 
			 	$iter[0][$i+1]['ld']['m'] = 0;//inicializando as variaveis de m
			 } 
			 else{
			 	$verifica = FALSE;
			 	for ($q=0; $q < $contart; $q++) { 

			 		if ($art[$q] == $j && $linArt[$q] == $i) {
			 			$verifica = TRUE;
			 			$iter[0][$i+1][$j][0] = 1;
						$iter[0][$i+1][$j]['m'] = 0;//inicializando as variaveis de m
						break;
			 		}
			 	}
			 	if (!$verifica) {
			 	$iter[0][$i+1][$j][0] = $r[$i][$j];
			 	$iter[0][$i+1][$j]['m'] = 0;//inicializando as variaveis de m	
			 	}
			 	
			 }
				
			}
		}//e aqui preenche a matriz de iteração
			
		
		//parei aqui, fazer a parte que o z muda


		$contIter = 0; //contador de iterações
		$checagem = TRUE;

		$tamanhoz = count($iter[0][0]);
		//Apartir do while a baixo começa as iterações de fato
		while ($checagem) {
			
			//calcula a coluna pivô
			$cp[$contIter] = $this->retorna_coluna_pivo($tamanhoz, $igualdade, $iter, $contIter);
			//calcula a linha pivô
			$lp[$contIter] = $this->retorna_linha_pivo($cp, $contIter, $iter, $restricao);
		
			$fator = $iter[$contIter][$lp[$contIter]+1][$cp[$contIter]][0];//acha o valor a qual a linha pivô será dividida
			//calcula a nova matriz de iteracao
			$iter = $this->retorna_resultado_iteracao($iter, $contIter, $cp, $igualdade, $lp, $fator);
			
			$checagem = $this->verifica_termino($iter, $contIter, $igualdade);//verifica se todos os x ficaram positivos
			//caso retorne false o while irá parar
			$contIter++;
			
		}
		$dados['vb'] = $this->retorna_posicao_variaveis_basicas($iter, $contIter, $obj, $lp, $cp, $valores, $tamanhofinal);
		$retorno = $this->retorna_resultado_final($dados['vb'], $contIter, $iter, $tamanhofinal);
		$dados['result_fim'] = $retorno['valores_otimos'];
		$dados['z_otimo'] = $retorno['z_otimo'];
		$dados['matriz_iter'] = $iter;
		$dados['qtde_iter'] = $contIter;
		$dados['lin_pivos']	= $lp;
		$dados['col_pivos'] = $cp;
		$dados['igualdade'] = $igualdade;
		$dados['tamanho'] = $tamanhofinal;
		$dados['objetivo'] = $obj;
		$dados['qtdeVar'] = $tamanho;
		$val_int_otimos = $this->retorna_inteiros_lotes($retorno['valores_otimos'], $z_ini, $tamanho_inicial, $retorno['z_otimo']);
		if($val_int_otimos['obs'] == FALSE){//caso a solução do problema relaxado não seja inteira, ele executa o resto
			$dados['val_int_lotes'] = $val_int_otimos;
			/*echo "Lotes: <br>";
			print_r($val_int_otimos);
			echo "<br>";*/
			$val_int_otimos = $this->retorna_inteiros_proximos($retorno['valores_otimos'], $z_ini, $tamanho_inicial, $obj, $r, $ld, $igualdade);
			$dados['val_int_proximos'] = $val_int_otimos;

		}

		if ($tamanho == 2) {
			$restricoes = $dados['restricoes'];
			$lado_direito = $dados['lado_direito'];
			$matriz_iter = $iter;
			$qtdeVar = $tamanho;
			$qtde_iter = $contIter;
			$tamanho = $tamanhofinal;
			$data = $this->gera_script($restricoes, $igualdade, $lado_direito, $matriz_iter, $qtdeVar, $qtde_iter, $tamanho, $retorno['valores_otimos']);//essa funcao alem de gerar os script, gera a legenda tambem	
			$dados['script'] = $data['script'];
			$dados['legenda'] = $data['legenda'];
			$dados['otimo'] = $data['otimo'];
			
		}
		//echo "z = ". $iter[$contIter][0]['ld'][0]. "<br>";
		}
		else{
			$dados['restricoes'] = $r;
			$dados['lado_direito'] = $ld;
			$dados['errors'] = $error;
			$dados['igualdade'] = $igualdade;
			
		}

		
		
		/*echo "Proximos: <br>";
		print_r($val_int_otimos);
		exit;*/
		/*$teste = 30.555;
		$s = explode('.', $teste);
		print_r(strlen($s[1]));
		exit;
		*/
		$this->template->load('template/template', 'simplex/simplex_resultado', $dados);

	}



private function gera_script($restricoes, $igualdade, $lado_direito, $matriz_iter, $qtdeVar, $qtde_iter, $tamanho, $otimo){
	//caso tenha duas variaveis faz esse bloco pra gerar o script que monta o grafico e a legenda do grafico 
			
			for ($a=0; $a < count($restricoes); $a++) { 
				if($restricoes[$a][0] !=0)
				$X1 = $lado_direito[$a][0]/$restricoes[$a][0];
	            if($restricoes[$a][1] !=0)
	            $X2 = $lado_direito[$a][0]/$restricoes[$a][1];
	            if($a == 0){
	           		if($restricoes[$a][0] ==0) $dominioX1 = -100000000000; // valor muito pequeno caso de uma divisao por 0 ele seta esse valo muito pequeno para que na proxima iteracao do for esse valor saia
	            	else $dominioX1 = $X1;

	            	if($restricoes[$a][1] ==0) $dominioX2 =-100000000000;
	            	else $dominioX2 = $X2;	
	            }
	            else{
	            	if($X1>$dominioX1){
	            		$dominioX1 = $X1;
	            	}
	            	if($X2>$dominioX2){
	            		$dominioX2 = $X2;
	            	}
	            }
								
			}
	        $script = "<script type='text/javascript'> 
	          $( window ).load(function() {//esconde a div relacionada a cursos

	          $('#div_simplex').hide();


	          });
	          function roda(){
	            $('#div_simplex').show();
	                    return functionPlot({
	                    target: '#simplex',
	                    yAxis: {label: 'x2',domain: [0, {$dominioX2}]},
	                    xAxis: {label: 'x1', domain: [0, {$dominioX1}]},
	                    data: [
	                    ";
	        $quantDois = 0; //quantidade dos arrais de duas variaveis
	       for ($a=0; $a < count($restricoes); $a++) { 
	         $qtdeValid = 0; //quantidade de variaveis maiores que 0
	         for ($b=0; $b < count($restricoes[$a]); $b++) { //aqui verificara o dominio tambem
	           if ($restricoes[$a][$b]!=0) {
	              $qtdeValid ++;
	            }
	            
	            
	         }
	         if ($qtdeValid == $qtdeVar) {
	          
	          $posDuas[$quantDois] = $a;
	          $igual[$quantDois] = $igualdade[$a];
	          $ld = $lado_direito[$a][0] / $restricoes[$a][1]; // lado direto dividido por x2
	          $x1 = -1 * ($restricoes[$a][0]/$restricoes[$a][1]);
	            //cor da linha gerada de maneira randomica
	            $letters = '0123456789ABCDEF';
	            $color[$quantDois] = '#';
	            for($i = 0; $i < 6; $i++) {
	            $index = rand(0,15);
	            $color[$quantDois] .= $letters[$index];
	            }
	            //fim da geracao da cor

	           $script .= "{fn: '{$x1}x+$ld', color: '{$color[$quantDois]}'},";
	           $quantDois++;
	         }
	       }
	      $script .= "{
	                  points: [
	                      [$otimo[0], $otimo[1]]
	                      ],
	                  fnType: 'points',
	                  color: 'black',
	                  graphType: 'scatter'
	                  }
	                  ],
	                  annotations: [
	                  ";
	        $checkx1 = FALSE;
	        $checkx2 = FALSE;
	        for ($a=0; $a < count($restricoes); $a++) { 
	         $qtdeValid = 0; //quantidade de variaveis maiores que 0
	         for ($b=0; $b < count($restricoes[$a]); $b++) { 
	           if ($restricoes[$a][$b]!=0) {
	              $qtdeValid ++;
	              $x = $b;
	            }
	         }
	         
	         if ($qtdeValid == 1) {
	              if ($x == 0) {
	                $var = 'x';
	              }
	              else{
	                $var = 'y';
	                
	              }
	              if ($igualdade[$a]==1) {
	                $varI = '≤';//variavel de igualdade
	              }
	              elseif ($igualdade[$a]==2) {
	                $varI = '=';//variavel de igualdade
	              }
	              else{
	               $varI = '≥'; 
	              }
	              
	              $dir = $lado_direito[$a][0]/$restricoes[$a][$x];
	              if($otimo[$x] == $dir){
	                if($x ==0) $checkx1 = TRUE;
	                else $checkx2 = TRUE;
	              }
	              $pos = $x+1;
	              if($checkx1){
	                $var_otima = ', x1 ótimo';
	              }
	              elseif($checkx2){
	                $var_otima = ', x2 ótimo';
	              }
	              else{
	                $var_otima = '';
	              }
	              $script .="{
	                 $var: $dir,
	                text: 'x{$pos} {$varI} {$dir}{$var_otima}'
	                },";
	              }
	         }
	        if(!$checkx1){
	          $script .= "{
	                      x: $otimo[0],
	                      text: 'x1 ótimo'
	                    },";
	        }
	        if(!$checkx2){
	          $script .= "{
	                      y: $otimo[1],
	                      text: 'x2 ótimo'
	                    },";
	        }
	        $script .= "
	                    {
	                      x: 0,
	                      text: 'x2 ≥ 0'
	                    },
	                    {
	                      y: 0,
	                      text: 'x1 ≥ 0'
	                    }]
	                })
	          }

	          </script>

	                ";
		$legenda = $this->return_legenda($color, $quantDois, $restricoes, $igual, $lado_direito, $posDuas, $igual);

		$data['otimo'] = $otimo;
		$data['legenda'] = $legenda;
		$data['script'] = $script;
	    return $data;
}

private function return_legenda($color, $quantDois, $restricoes, $igual, $lado_direito, $posDuas){
	$legenda = '';
	for ($i=0; $i < $quantDois; $i++) { 
                        $legenda .= "<tr>";
                        $legenda .= "<td bgcolor = '$color[$i]'> </td>";
                        $qtdeVar = count($restricoes[$i]);
                        $text = '';
                        for ($j=0; $j < $qtdeVar; $j++) { 
                          $posx = $j+1;

                          
                            if ($j < $qtdeVar-1) {
                              $text .= "{$restricoes[$posDuas[$i]][$j]}x{$posx} + ";
                            }
                            
                            elseif ($j == $qtdeVar-1) {
                              if ($igual[$i] == 1)
                                  $sinal = '≤'; 
                              elseif($igual[$i] == 2)
                                $sinal = '=';
                              else
                                $sinal = '≥';
                              $text .= "{$restricoes[$posDuas[$i]][$j]}x{$posx} {$sinal} ";
                            }
                     
                          
                        }
                        $text .= "{$lado_direito[$posDuas[$i]][0]}";
                        $legenda .= "<td bgcolor='white'> $text </td>";
                        $legenda .=  "</tr>
                        ";

                      }
              return $legenda;
}

// a funcao a baixo verifica caso enviar um caso impossivel, exemplo x1>=3 e 									
//x1<=2, não tem como o valor ser menor igual a dois e maior 													
//igual a 3
private function verifica_error($r,$igualdade, $ld, $restricao){ 
	$error = NULL;
	$a = 0;
	for ($i=0; $i < $restricao; $i++) {
			$contador = count($r[$i]);
			$contValid = 0; 
			$check = FALSE;
			for ($j=0; $j < $contador; $j++) { 
				if ($r[$i][$j]!=0) {
					$contValid ++;
				}
				if ($contValid == 1 && $r[$i][$j] == 1) {
					$posi = $i;
					$posj = $j;
					$check = TRUE;
				}
			}
			if ($contValid == 1 && $check) {
					$posI[$a] = $posi;
					$posJ[$a] = $posj;
					$a++;
				}
		}
		if (isset($posI)) {
		$a = 0;

		for ($i=0; $i < count($posI)-1; $i++) { 
			for ($j=$i+1; $j < count($posI); $j++) { 
				if ($posJ[$i] == $posJ[$j]) {
					$aVerifiI1[$a] = $posI[$i]; //primeira pos i pra se verificar no caso $posI[$i]
					$aVerifiI2[$a] = $posI[$j]; //segunda pos i pra se verificar no caso $posI[$j]
					$aVerifiJ[$a]  = $posJ[$j];
					$a++;
				}
			}
		}
		if (isset($aVerifiI2)) {
			$a = 0;
			for ($i=0; $i < count($aVerifiI1); $i++) { 
				$posI1 = $aVerifiI1[$i];
				$posI2 = $aVerifiI2[$i];
				$posJ = $aVerifiJ[$i];
				
				if($igualdade[$posI1] == 2 || $igualdade[$posI2] == 2){
					if($ld[$posI1][0]!=$ld[$posI2][0]){
							$error[$a]['posI1'] = $posI1;
							$error[$a]['posI2'] = $posI2;
							$error[$a]['posJ'] = $posI2;
							$pos = $posI2+1;
							$error[$a]['message'] = "Uma restrição contem como igualdade o sinal de '=' porém os valores das restrições se diferem ({$ld[$posI1][0]} ≠ {$ld[$posI2][0]}), fazendo x{$pos} assumir valores diferentes e tornando impossíveis as restrições impostas.";
							$a++;
					}

				}
				elseif ($igualdade[$posI1] == 1) {//menor igual
					if ($igualdade[$posI2] == 3) {//maior igual
						if ($ld[$posI2][0] > $ld[$posI1][0]) {
							$error[$a]['posI1'] = $posI1;
							$error[$a]['posI2'] = $posI2;
							$error[$a]['posJ'] = $posI2;
							$pos = $posI2+1;

							$error[$a]['message'] = "As restrições impostas fazem com que x{$pos} assuma valores ≥{$ld[$posI2][0]} e valores ≤{$ld[$posI1][0]}, algo que não é possivel uma vez que um intervalo infringe o outro.";
							$a++;
						}

					}

				}
				
				
				
				else{
					if ($igualdade[$posI2] == 1) {
						if ($ld[$posI1][0] > $ld[$posI2][0]) {
							$error[$a]['posI1'] = $posI1;
							$error[$a]['posI2'] = $posI2;
							$error[$a]['posJ'] = $posI2;
							$pos = $posI2+1;
							$error[$a]['message'] = "As restrições impostas fazem com que x{$posJ} assuma valores ≥{$ld[$posI1][0]} e valores ≤{$ld[$posI2][0]}, algo que não é possivel uma vez que um intervalo infringe o outro.";
							$a++;
						}

					}
				}
				

			}
		}
		
		}
		
	return $error;	
}
private function preenche_matriz_iter_inicial($z, $r, $ld, $art, $contart , $linArt, $restricao){

		for ($i=0; $i < count($z)+1; $i++) {
			if ($i==count($z)) {
			 	$iter[0][0]['ld'][0] = 0;// coloca o lado direito
			 	$iter[0][0]['ld']['m'] = 0; //inicializando as variaveis de m
			 } 
			 else{
			 	$verifica = FALSE;
			 	for ($j=0; $j < $contart; $j++) { 
			 		if ($art[$j] == $i) {
			 			$verifica = TRUE;
			 			$iter[0][0][$i][0] = 0;
						$iter[0][0][$i]['m'] = 1;//inicializando as variaveis de m
						break;
			 		}
			 	}
			 	if (!$verifica) {
			 		$iter[0][0][$i][0] = $z[$i];
				$iter[0][0][$i]['m'] = 0;//inicializando as variaveis de m
			 	}
				
			 }
			
		}//aqui
		
		for ($i=0; $i < $restricao; $i++) { 
			$contador = count($r[$i]);	
			for ($j=0; $j < $contador+1; $j++) {
			if ($j==count($r[$i])) {
			 	$iter[0][$i+1]['ld'][0] = $ld[$i][0];// coloca o lado direito 
			 	$iter[0][$i+1]['ld']['m'] = 0;//inicializando as variaveis de m
			 } 
			 else{
			 	$verifica = FALSE;
			 	for ($q=0; $q < $contart; $q++) { 

			 		if ($art[$q] == $j && $linArt[$q] == $i) {
			 			$verifica = TRUE;
			 			$iter[0][$i+1][$j][0] = 0;
						$iter[0][$i+1][$j]['m'] = 1;//inicializando as variaveis de m
						break;
			 		}
			 	}
			 	if (!$verifica) {
			 	$iter[0][$i+1][$j][0] = $r[$i][$j];
			 	$iter[0][$i+1][$j]['m'] = 0;//inicializando as variaveis de m	
			 	}
			 	
			 }
				
			}
		}//e aqui preenche a matriz de iteração
		return $iter;
}
private function retorna_coluna_pivo($tamanhoz, $igualdade, $iter, $contIter){
			$checkZ = FALSE;
			$menor = 0;
			for ($i=0; $i < $tamanhoz-1; $i++) {
				if (in_array(2, $igualdade) || in_array(3, $igualdade)) {
					if ($iter[$contIter][0][$i]['m'] < $menor) {			
						$menor = $iter[$contIter][0][$i]['m'];
						$cp = $i;//tem mais tendencia a crescer, aqui pega a coluna pivô
						$checkZ = TRUE;

					}			
				
				
				}	else{

						if ($iter[$contIter][0][$i][0] < $menor) {
							$menor = $iter[$contIter][0][$i][0];
							$cp = $i;//tem mais tendencia a crescer, aqui pega a coluna pivô
						}

					}

			}
			if ((in_array(2, $igualdade) || in_array(3, $igualdade)) && !$checkZ ) { // caso percorreu todo o m e não achou um menor que 0, ou seja, negativo
				for ($i=0; $i < $tamanhoz-1; $i++) {
					if ($iter[$contIter][0][$i][0] < $menor && $iter[$contIter][0][$i]['m']==0) {
							$menor = $iter[$contIter][0][$i][0];
							$cp = $i;//tem mais tendencia a crescer, aqui pega a coluna pivô
						}
				}

			}
			return $cp;
}

private function retorna_linha_pivo($cp, $contIter, $iter, $restricao){
	$primeira = 0;//primeira refere-se ao primeiro j, caso seja 0 tera que saltar logo a primeira posicao vai mudar
			for ($i=0; $i < $restricao; $i++) {
				$valor = $cp[$contIter];
				$vc = $iter[$contIter][$i+1][$valor][0];//valor da coluna pivô
			

				//$vc = $r[$j][$cp[$contIter]];
				//print_r($vc); 
				
				if ($vc!=0) {

					$lado_direito = $iter[$contIter][$i+1]['ld'][0];
			
			 		$resultado = $lado_direito / $vc;
			 		//print_r($resultado);
			 		
			 	
					if (($i==$primeira || (isset($menor) && $resultado< $menor)) && $resultado>0) {
							
			 			
			 			$menor = $resultado;
			 			$lp = $i;//acha a linha pivô
			 			
						
			 		 
			 		}
			 		else{
						$primeira++;
					}

				}   else{
						$primeira++;
					}
					
					 	 			  
			}
			
			return $lp;
}
private function retorna_resultado_iteracao($iter, $contIter, $cp, $igualdade, $lp, $fator){
		for ($i=0; $i < count($iter[$contIter][$lp[$contIter]+1]); $i++) { //esse for serve para calcular a linha que servira de pivô na iteração seguinte
				if ($i==count($iter[$contIter][$lp[$contIter]+1])-1) {
					$iter[$contIter+1][$lp[$contIter]+1]['ld'][0] = $iter[$contIter][$lp[$contIter]+1]['ld'][0] / $fator;
					break;
				}

				$iter[$contIter+1][$lp[$contIter]+1][$i][0] = $iter[$contIter][$lp[$contIter]+1][$i][0] / $fator;
		}

		for ($i=0; $i < count($iter[$contIter]); $i++) {
				if ($i!=$lp[$contIter]+1) {
					for ($j=0; $j < count($iter[$contIter][$i]); $j++) {
						if (in_array(2, $igualdade) || in_array(3, $igualdade)) {//caso tenha maior igual ou igual
							if ($i!=0) {
							$fatorM = $iter[$contIter][$i][$cp[$contIter]][0] * - 1;//fator que multiplica a linha pivô
							$conta = count($iter[$contIter][$i]);

								if ($j== $conta - 1) {
						
								$iter[$contIter+1][$i]['ld'][0] = round(($fatorM * $iter[$contIter+1][$lp[$contIter]+1]['ld'][0]) + $iter[$contIter][$i]['ld'][0], 15);//caso chegue no lado direito

								break;
								}
							//echo $j;
								
							$iter[$contIter+1][$i][$j][0] = round(($fatorM * $iter[$contIter+1][$lp[$contIter]+1][$j][0]) + $iter[$contIter][$i][$j][0], 15);

							
							} else{

								$fatorM = $iter[$contIter][$i][$cp[$contIter]][0] * - 1;
								$fatorMM = $iter[$contIter][$i][$cp[$contIter]]['m'] * - 1;
								$conta = count($iter[$contIter][$i]);

									if ($j== $conta - 1) {
							
									$iter[$contIter+1][$i]['ld'][0] = round(($fatorM * $iter[$contIter+1][$lp[$contIter]+1]['ld'][0]) + $iter[$contIter][$i]['ld'][0], 14);//caso chegue no lado direito
									$iter[$contIter+1][$i]['ld']['m'] = round(($fatorMM * $iter[$contIter+1][$lp[$contIter]+1]['ld'][0]) + $iter[$contIter][$i]['ld']['m'], 14);//caso chegue no lado direito
									break;
									}
								//echo $j;
								$iter[$contIter+1][$i][$j][0] = round(($fatorM * $iter[$contIter+1][$lp[$contIter]+1][$j][0]) + $iter[$contIter][$i][$j][0], 15);
								$iter[$contIter+1][$i][$j]['m'] = round(($fatorMM * $iter[$contIter+1][$lp[$contIter]+1][$j][0]) + $iter[$contIter][$i][$j]['m'], 15);

							  }


							} else{ //caso so tenha menor igual
								$fatorM = $iter[$contIter][$i][$cp[$contIter]][0] * - 1;//fator que multiplica a linha pivô
								$conta = count($iter[$contIter][$i]);
							
								if ($j== $conta - 1) {
							
								$iter[$contIter+1][$i]['ld'][0] = round(($fatorM * $iter[$contIter+1][$lp[$contIter]+1]['ld'][0]) + $iter[$contIter][$i]['ld'][0], 15);//caso chegue no lado direito
								break;
								}
								//echo $j;
								$iter[$contIter+1][$i][$j][0] = round(($fatorM * $iter[$contIter+1][$lp[$contIter]+1][$j][0]) + $iter[$contIter][$i][$j][0], 15);	
						  		}
					}	
				}
				
			}
			return $iter;
}
private function verifica_termino($iter, $contIter, $igualdade){
	$contadorResult = 0; // conta se todos os x são positivos
		for ($i=0; $i<count($iter[$contIter+1][0]); $i++) {
			if (in_array(2, $igualdade) || in_array(3, $igualdade)) {
				if ($i!=count($iter[$contIter+1][0])-1) {

					if ($iter[$contIter+1][0][$i]['m'] == 0 && $iter[$contIter+1][0][$i][0] >= 0) {//caso m seja 0, verifica o outro valor também
						$contadorResult++;//se o x do z for positivo ele acresce

					}
					elseif($iter[$contIter+1][0][$i]['m'] > 0){
						$contadorResult++;//se o x do z for positivo ele acresce
					}
				}

			}
			else{
				if ($i!=count($iter[$contIter+1][0])-1) {
					$z = $iter[$contIter+1][0][$i][0];
	
					if ($z>=0) {
						$contadorResult++;//se o x do z for positivo ele acresce
					}
				}
			

			}

		}
		
		
		if ($contadorResult==count($iter[$contIter+1][0])-1){
		return FALSE;	
		}
	return TRUE;
}
//a função a baixo acrescenta as variaveis de folga e/ou artificiais
private function acrescenta_novas_variaveis($r, $restricao, $tamanho, $tamanhofinal, $igualdade){
	$contart = 0;// contador de variaveis artificiais
	for ($i=0; $i < $restricao; $i++) {//acrescimo das variaveis de folga e artificiais
			 $checkMaior = FALSE;
			if ($i==0) {
			$posicao = $tamanho;	
			}


			for ($j = $tamanho; $j < $tamanhofinal; $j++) {
				
			 if ($igualdade[$i]==1) {//igualdade é relacionado a menor igual, igual ou maior igual, nesta ordem
			 	if ($j == $posicao) {
					$r[$i][$j]=1;
				}
				else{
					$r[$i][$j]=0;
				}
				
				}
			 elseif ($igualdade[$i]==2) {
			 	if ($j == $posicao) {
					$r[$i][$j]=1;
					$art[$contart]= $j;//art é a matriz de posições das variaveis artificiais
					$linArt[$contart] = $i;
					$contart++;
				}
				else{
					$r[$i][$j]=0;
				}
				
			 }

			 else {
			 	$checkMaior = TRUE;//checa se entrou aqui para adicionar dois elementos a posicao
			 	if ($j == $posicao) {
					$r[$i][$j] = -1;
					}

				elseif ($j == $posicao+1) {
					
					$r[$i][$j]=1;
					$art[$contart]= $j;//art é a matriz de posições das colunas das variaveis artificiais
					$linArt[$contart] = $i;
					$contart++;
				}
				else{


					$r[$i][$j]=0;
				}
				
			 	
			 }

			}

			if ($checkMaior) {
				$posicao +=2;
			}
			else{

			$posicao++;	
			}
		}

	if(!isset($art)){
			$art = NULL; $contart = NULL; $linArt = NULL;
		}
		
	$retorno = array( 
					  'r'		=> $r,
					  'art'	   	=> $art,
					  'contart' => $contart,
					  'linArt' 	=> $linArt 
					);
	return $retorno;

}
//a função a baixo acrescenta as variaveis de folga e/ou artificiais em z
private function acrescenta_novas_variaveis_em_z($z, $tamanho, $tamanhofinal, $contart, $art){
	
	$verificador = 0;
	for ($i=$tamanho; $i < $tamanhofinal; $i++) {
			
			for ($j=0; $j < $contart; $j++) { 
			if ($art[$j]==$i) {
				$z[$i] = 1;
				$verificador = 1;//verifica se entrou aqui	
				}	
			}
			if ($verificador==0) {
			
			$z[$i] = 0; //acrescimo das variaveis de folga em z	
			}
			$verificador=0;
			
		}
	return $z;
}
private function retorna_posicao_variaveis_basicas($matriz_iter, $qtde_iter, $objetivo, $lp, $cp, $tamanhoVarIni, $tamanho){
	for ($i=0; $i <= $qtde_iter; $i++) {
		for ($j=0; $j < count($matriz_iter[$i]); $j++) {
			 	if ($j == 0) {
	              if ($objetivo == 1) {
	               $vb[$i][$j] = "Z";
	              }
	              else{
	               $vb[$i][$j] = "-Z"; 
	              }
	              
	            }
	            else{
	            	if($i == 0){
	            		for ($u=0; $u < $tamanho; $u++) { 
	                
		                    if (round($matriz_iter[$i][$j][$u][0], 14) == 1) {
		                      $vb[$i][$j] =  $u+1;;
		                      if (!in_array($u, $tamanhoVarIni) || $i == $qtde_iter) {
		                      break;
		                      }
		                    }  
	              		}	
	            	}
	            	else{
	            		if($j == $lp[$i-1]+1){
		                    $vb[$i][$j] = $cp[$i-1]+1;
	            		}
	            		else{
	            			$vb[$i][$j] = $vb[$i-1][$j];
	            		}
	            	}
	              
	            }
		}

	}
	return $vb;

}
private function retorna_resultado_final($vb, $contIter, $iter, $tamanho){
	$resultado_final =  array();
	$check = FALSE;
	for ($i=0; $i < $tamanho; $i++) {
			for($j=0; $j<count($vb[$contIter]); $j++){
				if($i+1 == $vb[$contIter][$j]){
					array_push($resultado_final, $iter[$contIter][$j]['ld'][0]);
					$check = TRUE;
				}
			}
			if(!$check){
				array_push($resultado_final, 0);
			}
			else{
				$check = FALSE;
			} 
			
	}
	$z_otimo = $iter[$contIter][0]['ld'][0];
	$result = array(
					'valores_otimos'=> $resultado_final,
					'z_otimo' => $z_otimo
					);
	return $result;
}
private function retorna_inteiros_lotes($valores_otimos, $z, $tamanho, $z_otimo){
	$otimos = array();
	$otimos['obs'] = FALSE;//verifica se so tem inteiros

	$cont_int = 0;
	//verifica a quantidade de inteiros 
	for ($i=0; $i < $tamanho; $i++) { 
		$s = explode('.', $valores_otimos[$i]);
		if(!isset($s[1])){
			$otimos[$i] = $valores_otimos[$i];
			$cont_int++;
		}
		
	}

	if($cont_int == $tamanho){
		$otimos['obs'] = TRUE;
		return $otimos;
	}
	else{
		
		for ($i=0; $i < $tamanho; $i++) { 
		$s = explode('.', $valores_otimos[$i]);
		if(isset($s[1])){
			$tamanho_dp_virg = strlen($s[1]);//quantidade de casas depois da virgula
			if(!isset($maior)){
				$maior = $tamanho_dp_virg;
			}
			elseif(isset($maior) && $tamanho_dp_virg>$maior){
				$maior = $tamanho_dp_virg;
			}
		}
		
		
		}
		for ($i=0; $i < $tamanho; $i++) { 
			$otimos[$i] = pow (10 , $maior) * $valores_otimos[$i];
		}
		$newZ = pow (10 , $maior) * $z_otimo;
		$otimos['z'] = $newZ;
		return $otimos;

	}

}
//logica muito doida aqui, caso veja essa parte da implementação me pergunte como fiz
//tem a ver com binario, caso eu esqueça
private function retorna_inteiros_proximos($valores_otimos, $z, $tamanho, $objetivo,$r, $ld, $igualdade){
	$quantidade = pow (2, $tamanho);
	
	$binarios = array();
	$otimos = array();
	$otimos['obs'] = FALSE;//verifica se so tem inteiros
	$cont = 0;

	for($i = 0; $i<$tamanho; $i++){
		if(is_integer($valores_otimos[$i])){ 
			$cont++;
			array_push($otimos, $valores_otimos[$i]);
		}
	}
	
	if($cont == $tamanho){
		$otimos['obs'] = TRUE;
		return $otimos;
	}
	else{
	$otimos = NULL;

	for ($i=0; $i < $quantidade; $i++) { 
		$var = "%0{$tamanho}d";

		$bin = sprintf($var, decbin( $i ));
		//print_r()
		
		$splited_bin  = str_split($bin);

		array_push($binarios, $splited_bin);
	}
	for ($i=0; $i < $quantidade; $i++) {
		$zInteiro = 0;
		
		$val_arredondados = array();
		for ($j=0; $j < $tamanho; $j++) { 
			if($binarios[$i][$j]==0){
				$val_arredondado = floor($valores_otimos[$j]);
				array_push($val_arredondados, $val_arredondado);
				$zInteiro += $val_arredondado * $z[$j];
				for($a=0; $a<count($r); $a++){
					$val_restricao =  $r[$a][$j] * $val_arredondado;
					if($j == 0) {

						$val_restricoes[$a] = $val_restricao;
					}
					else {
						$val_restricoes[$a] += $val_restricao;
					}
				}		
			}
			else{

				$val_arredondado = ceil($valores_otimos[$j]);
				
				
				array_push($val_arredondados, $val_arredondado);
				$zInteiro += $val_arredondado * $z[$j];
				for($a=0; $a<count($r); $a++){
					$val_restricao =  $r[$a][$j] * $val_arredondado;
					if($j == 0) {

						$val_restricoes[$a] = $val_restricao;
					}
					else {
						$val_restricoes[$a] += $val_restricao;
					}
				}
			}
		}
		
		
		$restricoes_atendidas = 0;
		for ($j=0; $j < count($val_restricoes); $j++) { 
			
			if($igualdade[$j]==1 && $val_restricoes[$j]<=$ld[$j][0])	$restricoes_atendidas++;
			elseif($igualdade[$j]==2 && $val_restricoes[$j]==$ld[$j][0]) $restricoes_atendidas++;
			elseif($igualdade[$j]==3 && $val_restricoes[$j]>=$ld[$j][0]) $restricoes_atendidas++;
		}


		if($restricoes_atendidas == count($val_restricoes)){
			
			if(is_null($otimos)){
				$melhorZ = $zInteiro;
				$otimos = $val_arredondados;
				}
			
			elseif($objetivo == 0 && $zInteiro < $melhorZ){
				$melhorZ = $zInteiro;
				$otimos = NULL;
				$otimos = $val_arredondados;
			}
			elseif($objetivo == 1 && $zInteiro > $melhorZ){
				$melhorZ = $zInteiro;
				$otimos = NULL;
				$otimos = $val_arredondados;

			}
		}
		}
	}
	if(!empty($otimos)){
		if($objetivo == 0) $z = $melhorZ * -1;
		else $z = $melhorZ;
		$otimos['z'] = $z;	
	}
	
	return $otimos;

}


}


