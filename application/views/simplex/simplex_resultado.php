<!-- **********************************************************************************************************************************************************
MAIN CONTENT
*********************************************************************************************************************************************************** -->

<!--main content start-->
            
            <div class="col-xs-12 text-center">
                <br>
                <br>
              </div>
            
              <nav>
        <img src="img/logo-white.png" alt="">
        <h1 class="brand"><a href="#">Equacione - Soluções em Pesquisa Operacional - 

SOLUÇÃO SIMPLEX</a></h1>
  </nav>

  <style type="text/css">
    nav{
  position: fixed;
  top: 0;
  left: 0;
  overflow: hidden;
  display: block;
  width: 100%;
  height: 70px;
  background:#81c12c;
  padding: 0 50px;
  box-sizing: border-box;
}

nav h1{
  margin: 0;
  padding: 15px 20px;


}

nav img{
  float: left;

}

nav h1 a{
  font-size: 30px;
  color: #fff;
  text-decoration: none;

}

nav ul{
  margin: 0;
  padding: 0;
  float: right;
}

nav ul li{
  font-size: 24px;
  list-style: none;
  display: inline-block;
  padding: 20px 30px;
  transition: .5s;
}

nav ul li a{
  color: #fff;
  text-decoration: none;
}

nav ul li:hover{
  background: #69db60;
}
  </style>
           
            <div class="clearfix"></div>

            <div class="row" style="margin-top: 10%;">
              <div class="col-md-12 col-sm-12 col-xs-12 text-center">
                 
                  <div class="jumbotron">
                    
                    <?php if (!isset($errors)): ?>
                    <?php if ($qtdeVar == 2 && isset($script)): ?>
                    <?php echo $script; ?>
                    <div id="div_simplex">
                    <hr>
                    <h2>Forma gráfica</h2>
                    <div class="col-xs-12" style="background-color: white" >
                      
                      <span  id="simplex"  ></span>
                    </div>
                    <div class="col-xs-12">
                    <h3>Ponto Ótimo: x1 = <?php echo $otimo[0]?>, x2 = <?php echo $otimo[1]?></h3>
                    __________________________________________________________________________________________________________
                        
                    <h2>Legenda Gráfico</h2>
                    <table class="table table-bordered">
                      <thead>
                        <tr>
                          <th bgcolor='white'>Cor</th>
                          <th bgcolor='white'>Restrição</th>      
                        </tr> 
                      </thead>
                      <tbody>
                        <?php  echo $legenda?>
                      </tbody>
                    </table>
                    </div>
                     </div>
                    <div class="col-xs-12">

                      <div class="text-center " align="">
                        <br>
                        <button id="btn-grafico"  class="btn btn-primary text-center" style="background-color: #81c12c; border-color: #81c12c;" onclick="roda(); $('#btn-grafico').hide();">Visualizar resultado na forma gráfica</button>
                      </div>
                      <hr>
                    </div>
                    
                   __________________________________________________________________________________________________________
                        
                    
                   
                    <?php endif ?>
                    <h2>Forma Tabular</h2>
                    <div class="col-xs-12">
                    <div class="col-xs-2 text-center"><h4>Legenda</h4> <table class="table">
                      <thead>
                        <tr>
                          <th>Coluna Pivô</th>
                          <th>Linha Pivô</th> 
                           
                        </tr>
                      </thead>
                      <tbody> <tr> <td bgcolor="#81c12c"></td> <td bgcolor="YELLOW"></td>
                          </tr></tbody>
                    </table></div>
                    </div>
                    <?php for ($i=0; $i <= $qtde_iter; $i++) {

                          ?>

                      <br>
                      <h3>Iteração <?php echo $i;?></h3>
                      <table class="table table-striped table-bordered" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                              <th bgcolor="WHITE" class="text-center">Lin.</th>
                              <th bgcolor="WHITE" class="text-center"><?php echo "VB";?></th>
                        <?php for ($a=0; $a <= $tamanho; $a++) { 
                          ?>
                              <?php if ($a == $tamanho): ?>
                              <th bgcolor="WHITE" class="text-center"><?php echo "Lado Direito";?></th>
                              <?php elseif(isset($pos_art) && in_array($a, $pos_art)): ?>
                              <th  bgcolor="WHITE" class="text-center"><?php echo "x̄"; echo $a+1;?> </th>     
                              <?php else: ?>
                              <th  bgcolor="WHITE" class="text-center"><?php echo "x"; echo $a+1;?> </th>  
                              <?php endif ?>
                              

                          <?php
                        } ?>
                            </tr>
                        </thead>
                            <tbody>

                            <?php


                              for ($j=0; $j < count($matriz_iter[$i]); $j++) { 
                                    
                                      echo "<tr>";

                                      if($j==0){
                                        echo "<td>{$j}</td>";
                                        echo "<td>{$vb[$i][$j]}</td>";
                                      }
                                      else{
                                        echo "<td>{$j}</td>";
                                        echo "<td>x{$vb[$i][$j]}</td>";
                                      }
                                      

                                      for ($p=0; $p <= $tamanho; $p++) { 
                                        
                                        ?>
                                        <?php if ($p == $tamanho): ?>
                                        <td <?php if($i<$qtde_iter && $j == $lin_pivos[$i]+1) echo 'bgcolor="YELLOW"';        elseif($i<$qtde_iter && $p == $col_pivos[$i]) 
                                                          echo 'bgcolor = "#81c12c"  style="color: WHITE"';
                                            ?> >
                                                    <?php echo $matriz_iter[$i][$j]['ld'][0]; 
                                                        if((in_array(2, $igualdade) || in_array(3, $igualdade)) && 
                                                                  isset($matriz_iter[$i][$j]['ld']['m']) && 
                                                                  $matriz_iter[$i][$j]['ld']['m']>0) 
                                                              echo " +". $matriz_iter[$i][$j]['ld']['m']."M"; 

                                                        elseif((in_array(2, $igualdade) || in_array(3, $igualdade)) && 
                                                                            isset($matriz_iter[$i][$j]['ld']['m']) && 
                                                                            $matriz_iter[$i][$j]['ld']['m']!= 0  && 
                                                                            $matriz_iter[$i][$j]['ld']['m']<0) 
                                                                        echo " ". $matriz_iter[$i][$j]['ld']['m']."M";
                                                    ?> 

                                            </td>

                                        <?php else: ?>
                                          <td <?php if($i<$qtde_iter && $j == $lin_pivos[$i] +1) echo 'bgcolor="YELLOW"';        elseif($i<$qtde_iter && $p == $col_pivos[$i]) 
                                                          echo 'bgcolor = "#81c12c" style="color: WHITE"';
                                              ?> 
                                            >
                                            <?php 
                                                  echo $matriz_iter[$i][$j][$p][0]; 
                                                  if((in_array(2, $igualdade) || in_array(3, $igualdade)) && 
                                                      isset($matriz_iter[$i][$j]['ld']['m']) && 
                                                      $matriz_iter[$i][$j][$p]['m']!= 0  && 
                                                      $matriz_iter[$i][$j][$p]['m']>0) 
                                                        echo " +". $matriz_iter[$i][$j][$p]['m']."M"; 
                                                  elseif((in_array(2, $igualdade) || in_array(3, $igualdade)) && 
                                                          isset($matriz_iter[$i][$j]['ld']['m']) && 
                                                          $matriz_iter[$i][$j][$p]['m']!= 0  && 
                                                          $matriz_iter[$i][$j][$p]['m']<0) 
                                                              echo " ". $matriz_iter[$i][$j][$p]['m']."M";
                                            ?> 
                                          </td>
    
                                        <?php endif ?>
                                        


                        <?php
                                      }
                                     echo "</tr>"; 
                              }
                        ?>


                          </tbody>
                      </table>
                      <br>
                      <hr>
                      <?php

                    } ?>

                    <h3><b>Solução Ótima</b></h3>
                     <br>
                     <p>Como todos todos os coeficientes da linha (0) são positivos, Z não tem mais capacidade de melhorar, logo, a solução atual é otima descrita por:</p>
                     <p>x*=(
                       <?php 
                          for ($i=0; $i < $tamanho; $i++) {
                            $aux = $i+1;
                            if ($i==$tamanho-1) {
                              if(isset($pos_art) && in_array($i, $pos_art)){
                                echo "x̄{$aux}";
                              }
                              else{
                                echo "x{$aux}";  
                              }
                              
                            }

                            else{ 
                              if(isset($pos_art) && in_array($i, $pos_art)){
                                echo "x̄{$aux},";
                              }
                              else{
                                echo "x{$aux},";  
                              }
                              
                              } 
                          }
                        ?>) = (


                                <?php
                                    for ($i=0; $i < count($result_fim); $i++) { 
                                        if($i == count($result_fim)-1)
                                          echo "{$result_fim[$i]}";
                                        else
                                          echo "{$result_fim[$i]},";
                                     } 
                                 ?>
                              ) => Z* = <?php echo $z_otimo; ?>
                     </p>
                     <hr>
                     <h3><b>Soluções Inteiras</b></h3>
                     <br>
                     <?php if (isset($val_int_lotes)): ?>
                        <p>Por meio da solução de lotes se encontra como solução ótima: </p>
                        <p>
                        <?php $quantidade =count($val_int_lotes)-2;
                              for($i=0; $i<$quantidade; $i++){
                          ?>
                        x<?php $valor_x = $i+1;
                              
                              echo $valor_x. "* = ".$val_int_lotes[$i]. ", ";
                        ?>
                          

                          <?php
                              }

                              ?>
                              Z* = <?php echo $val_int_lotes['z']."."; ?>
                          </p>
                          <br>
                          <?php if (!empty($val_int_proximos)): ?>

                          <p>Por meio da solução por valores inteiros próximos a solução real se tem:</p>
                          <p>
                            <?php for($i=0; $i<$quantidade; $i++){
                          ?>
                        x<?php 
                              $valor_x = $i+1;
                              
                              echo $valor_x. "* = ".$val_int_proximos[$i]. ", ";
                        ?>
                         

                          <?php
                              }
                              ?>
                               Z* = <?php echo $val_int_proximos['z']."."; ?>
                          </p>
                          </p>
                          <p>obs: Vale destacar que a solução apresentada é uma possível solução inteira viável, não necessariamente a melhor solução inteira para o problema.</p>
                          <?php else: ?>
                            <p>Nenhuma das soluções inteiras próximas a solução real do problema satisfazem todas as restrições propostas, não sendo possível então se predizer uma solução inteira apartir desse metódo.</p>
                          <?php endif ?>
                          
                     <?php else: ?>
                      <p>A solução atual do problema já é uma solução inteira, sendo a melhor tanto para o problema inteiro quanto para o real.</p>
                     <?php endif ?>
                    <?php else: ?>
                      <p>Não há solução para esse problema simplex, uma vez que foram encontrados os seguintes erros:</p>
                      <?php foreach ($errors as $error): ?>

                        <p><?php echo $error['message'];?></p>
                      <?php endforeach ?>
                    <?php endif ?>

                    <div class="text-center">
                      <a href="<?php echo base_url()?>home/" class="btn btn-primary" style="background-color: #81c12c; border-color: #81c12c;">
                          Resolver outro problema Simplex
                      </a>  
                    </div>
                    


                  </div>
               
              </div>
            </div>
          

<!-- /MAIN CONTENT -->
<!--main content end-->

        <!-- /page content -->