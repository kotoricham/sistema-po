<?php

/*
 * Sistema Helper é um arquivo auxiliar do CodeIgniter criado para otimizar
 * algumas funções de grande utilização e que ou não são contempladas
 * no framework ou possuem algumas especificidades, tais como: 
 * redirecionamentos, datas, conversões de valores, etc. 
 *
 * Desenvolvido por: Marcelo Almeida - Analista de Sistemas
 * Data: 20/12/2016
 */

/* LOCATION
 * 
 * A função location tem o objetivo de redirecionar as páginas do sistema
 * Método pode ser utilizando PHP ou JavaScript, de acordo o servidor
 * php para utilizar o PHP
 * js para utilizar o JavaScript
 *   */

function location($pagina = "", $metodo = 'php') {

    $url = base_url() . "" . $pagina;

    //Redireciona utilizando PHP
    if ($metodo == "php") {

        header("Location: $url");
    }
    //Redireciona utilizando javascript
    else if ($metodo == "js") {

        echo '<script language="javascript">
                    location.href="' . $url . '"
                  </script>';
    }
}


/*
SET LOG 
 * Função para inserir na tabela tbl_log as ações executadas pelo usuário.
 *  */
function set_log($usuario = null, $id_usuario = null, $acao = null) {

    $CI = &get_instance();

    if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {

        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {

        $ip = $CI->session->userdata('ip_address');
    }

    $array = array('ip' => $ip, 'acao' => $acao, 'usuario' => $usuario, 'id_usuario' => $id_usuario);

    $CI->db->insert('tbl_log', $array);
}

/*

 * FUNÇÃO SET MSG 
    Funçaõ para receber a mensagem que será exibida na próxima tela pelo
 * flash data
 *  */
function set_msg($id = null, $msg = null, $tipo = null) {
    $CI = &get_instance();  // CI vai receber  a instancia do codeigniter que está sendo executado  //

    if ($tipo == "erro") {

        $CI->session->set_flashdata($id, '<div class="alert alert-danger"><p>' . $msg . '</p></div>');
    } else if ($tipo == "sucesso") {

        $CI->session->set_flashdata($id, '<div class="alert alert-success"><p>' . $msg . '	</p></div>');
    }
    else if ($tipo == "atencao") {

        $CI->session->set_flashdata($id, '<div class="alert alert-success"><p>' . $msg . '	</p></div>');
    }
    else if ($tipo == "info") {

        $CI->session->set_flashdata($id, '<div class="alert alert-success"><p>' . $msg . '	</p></div>');
    }
}

// verifica se existe uma mensagem  para ser exibida na tela atual
function get_msg($id, $printar = true) {
   
    // CI vai receber  a instancia do codeigniter que está sendo executado  //
    $CI = &get_instance();  
    
    if ($CI->session->flashdata($id)){
        
        if ($printar){
            echo $CI->session->flashdata($id);
            
        }else{
            return $CI->session->flashdata($id);
        }
    }
    return FALSE;
}


//VERIFICA CRYPT
/*
    A função hash_crypt tem como objetivo comparar um valor passado como parametro
 *se corresponde a criptografia utilizada.
 *  */
function hash_crypt($string = "",$hash = ""){
    
    //Faz a criptografia através da função crypt
    $senhaHash = crypt($string, $hash);

    if(strcmp($senhaHash, $hash) === 0 ){

        return TRUE;
    }else{

        return FALSE;
    }
    
}


 /* A FUNÇÃO getData retorna a data/hora atual no formato passado por parâmetro */

function getData($fomato = "d/m/Y H:i:s") {
    
    date_default_timezone_set('America/Sao_Paulo');
    
    $now = time();
    $human = unix_to_human($now);
    return nice_date($human, $fomato);
    
}

function mensagem($classe = NULL, $mensagem = NULL) {
        if ($classe != NULL && $mensagem != NULL) {
            return "<div class='alert $classe alert-dismissable'>
                          <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>
                          $mensagem
                        </div> ";
        }
        return FALSE;
    }