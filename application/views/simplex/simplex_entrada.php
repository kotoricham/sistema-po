<!-- **********************************************************************************************************************************************************
MAIN CONTENT
*********************************************************************************************************************************************************** -->

<!--main content start-->
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
            
            <div class="col-xs-12 text-center">
                <br>
                <br>
              </div>

               <style type="text/css">
                  h3 {
                    color: #82c22c;
    margin-bottom: 40px;
    font: 45px Quicksand;
    text-align: center;
    margin-left: 90px;
                  }
                </style>
           
            <div class="clearfix"></div>

            <div class="row" style="margin-top: 10%;">
              <div class="col-md-12 col-sm-12 col-xs-12 text-center">
                 
              
                  
                  <div class="jumbotron ">
                  <?php  echo form_open("$controller/$funcao",array('class' => 'form-horizontal form-label-left', 'method' => 'post'));?>
                  <select name="objetivo"  class="" required>
                    <option value="1" <?php if(!empty(set_value('objetivo')) && set_value('objetivo') == 1 ) echo "selected";?> >Maximizar</option>
                    <option value="0" <?php if(!empty(set_value('objetivo')) && set_value('objetivo') == 0 ) echo "selected";?> >Minimizar</option>
                  </select> 
                  

                    Z = 
                  
                  <?php for ($j=0; $j < $variavel; $j++) { 
                   ?>

                   <input class="" type="text" size="3px" name="z[]" value="<?php echo set_value('z[]'); ?>" required> x<?php echo $j+1; ?> 
                   <?php if ($j != $variavel-1): ?>
                     +
                   <?php endif ?>
                  <?php
                  } ?>  
                  <br>
                  Sujeito a:
                  
                  <?php for ($i=0; $i < $restricao; $i++) { 
                    ?>
                  <br>
                  <br>
                  Restrição <?php echo $i+1; ?>:
                    <?php
                    for ($j=0; $j < $variavel; $j++) { 
                  ?> 
                   <input type="text" size="3px" name="r<?php echo $i;?>[]" value="<?php echo set_value('r$i[]'); ?>" required> x<?php echo $j+1; ?> 

                  <?php
                  }
                  ?>
                  <select name="igualdade<?php echo $i;?>" required>
                    <option value="1" <?php if(set_value('igualdade$i') == 1 ) echo "selected" ?>> ≤ </option>
                    <option value="2" <?php if(set_value('igualdade$i') == 2 ) echo "selected" ?>> = </option>
                    <option value="3" <?php if(set_value('igualdade$i') == 3 ) echo "selected" ?>> ≥ </option>
                  </select><input type="text" size="3px" name="ld<?php echo $i?>[]">
                  <?php
                  } ?>
                  <div class=""><br><button class="btn btn-success" style="background-color: #81c12c; border-color: #81c12c;" type="submit"> Calcular</button></div>
                  <input type="hidden" name="restricoes" value="<?php echo $restricao?>">
                      
                      <?php echo form_close(); ?>
                  </div>
               
              </div>
            </div>
          

<!-- /MAIN CONTENT -->
<!--main content end-->

        <!-- /page content -->