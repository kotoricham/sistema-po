<!-- **********************************************************************************************************************************************************
MAIN CONTENT
*********************************************************************************************************************************************************** -->
<!--main content start-->


              <div class="col-xs-12 text-center">
                <br>
                <br>
              </div>
              <div class="col-xs-12 text-center">
                <img src="/trabalho-simplex/assets/img/icon-s.jpg" width="200px">
                <h3>Soluções para problemas de Pesquisa Operacional</h3>
                <style type="text/css">
                  h3 {
                    color: #82c22c;
    margin-bottom: 40px;
    font: 45px Quicksand;
    text-align: center;
    margin-left: 90px;
                  }
                </style>
              </div>
              
            <div class="clearfix"></div>

            <div class="row">
              <div class="col-md-12 col-sm-12 col-xs-12">
                 
              
                <div class="jumbotron">
                  <?php  echo form_open("$controller/$funcao",array('class' => 'form-horizontal form-label-left', 'method' => 'post'));?>
                      <div class="form-group col-xs-12">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12 " for="first-name">Número de variáveis<span class="required">*</span>
                        </label>
                        <div class="col-md-6 col-sm-6 col-xs-12 ">
                          <input type="number" id="variaveis" name="variaveis" required=""  value="" class="form-control col-md-7 col-xs-12" >
                        </div>
                      </div>
                      <div class="form-group col-xs-12">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12 " for="first-name">Número de restrições<span class="required">*</span>
                        </label>
                        <div class="col-md-6 col-sm-6 col-xs-12 ">
                          <input type="number" id="restricoes" name="restricoes" required=""  value="" class="form-control col-md-7 col-xs-12" >
                          <br>
                          <br>
                          
                        </div>
                      </div>
                      <div class="text-center">
                        <button class="btn btn-primary" style="background-color: #81c12c; border-color: #81c12c;" type="submit">INICIAR</button>
                      </div>
                      
                      <?php echo form_close(); ?>
                  
                </div>
              </div>
            </div>
          

<!-- /MAIN CONTENT -->
<!--main content end-->
         
        <!-- /page content -->