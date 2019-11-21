<?php

$matriz = [];

$obj = [];

$v = $_POST['variaveis'];
$r = $_POST['restricoes'];

$tipo = $_POST['tipoprob'];

if($tipo == 'maximizar'){
    $obj[0] = 1;
    for($i=1; $i<=$v; $i++){
        $obj[$i] = ($_POST['varz'.$i])*(-1);
    }
} else {
    $obj[0] = -1;
    for($i=1; $i<=$v; $i++){
        $obj[$i] = ($_POST['varz'.$i]);
    }
}

for($i=1+$v; $i<=$r+$v; $i++){
    $obj[$i] = 0;
}

$obj[$v+$r+1] = 0;

$matriz[0] = $obj;



for($i=1; $i<=$r; $i++){
    $matriz += array();
}

for($j=1; $j<=$r; $j++){
    $matriz[$j][0] = 0;
    for($i=1; $i<=$v+$r; $i++){
        if($i<=$v){
            $matriz[$j][$i] = $_POST['varr'.$j.$i];
        }else{ 
            if($i-$v == $j){
                $matriz[$j][$i] = 1;
            } else {
                $matriz[$j][$i] = 0;
            }
        }
    }
    $matriz[$j][$v+$r+1] = $_POST['varr'.$j];;
}


$iter = 0;

echo "<div align='center'>";
echo "<div align='center' style='margin-top: 40px'>";
echo "<h1>Simplex</h1><br/>";
echo "<h2>Resolução</h2>";
do{
    $col;
    $val = 0;
    //acha a variavel que entra
    for($i=1; $i<=$v+$r+1; $i++){
        if($matriz[0][$i] < $val){
            $val = $matriz[0][$i];
            $col = $i; 
        }
    }

    $div = 10000;
    $lin;

    //acha a linha q sai
    for($j=1; $j<=$r; $j++){
        if(($matriz[$j][$col] != 0) && (($matriz[$j][$r+$v+1])/($matriz[$j][$col])) < $div && (($matriz[$j][$r+$v+1])/($matriz[$j][$col])) >= 0){
            $div = (($matriz[$j][$r+$v+1])/($matriz[$j][$col]));
            $lin = $j;
        }
    }
    
    $iter++;
    echo "<br/><br/>Iteração ".$iter.": ";
    echo "<div>";
    echo "<table border='1' align=center style='text-align:center;'>";
    echo "<tr style='border: none;'>";
        echo "<td style='border: none;'>Z</td>";
        for($i=1;$i<=$v;$i++)
        {
            echo "<td style='border: none;'>X".$i."</td>";
        }
        for($i=1+$v;$i<=$v+$r;$i++)
        {
            echo "<td style='border: none;'>xF".($i-$v)."</td>";
        }
        echo "<td style='border: none;'>b</td>";
        echo "</tr>";
    for($j=0; $j<=$r; $j++){
        if($j == $lin){
            echo "<tr bgcolor=pink>";
        } else {
            echo "<tr>";
        }
        for($i=0; $i<=$v+$r+1; $i++){
            if($i == $col && $j == $lin){
                echo "<td width=50 bgcolor=blue>";
            } else if($i == $col){
                echo "<td width=50 bgcolor=yellow>";
            } else {
                echo "<td width=50>";
            }
            echo round($matriz[$j][$i],3)."</td>";
        }
        echo "</tr>";
    }
    echo "</table>";
    echo "</div>";

   

    //divide a linha pelo pivo
    $pivo = $matriz[$lin][$col];
    $vet = [];
    for($i = 0; $i<=$r+$v+1; $i++){
        $vet[$i] = ($matriz[$lin][$i])/$pivo;
    }

    //achando nova linha
    for($j = 0; $j <= $r; $j++){
        $elepivo = ($matriz[$j][$col])*(-1);
        $newvet = [];
        for($k = 0; $k<=$r+$v+1; $k++){
            $newvet[$k] = ($vet[$k])*$elepivo;
        }
        if($j == $lin){
            for($i = 0; $i <= $r+$v+1; $i++){
                $matriz[$j][$i] = $vet[$i];
            }
        } else {
            for($i = 0; $i <= $r+$v+1; $i++){
                $matriz[$j][$i] += $newvet[$i];
            }
        }
    }
    
    
    $menor = 0;
    for($i = 1; $i<=$r+$v+1; $i++){
        if($matriz[0][$i]<$menor){
            $menor = $matriz[0][$i];
        }
    }
}while($menor<0);

echo "<div style='margin-bottom: 100px'>";
    echo "<h2 style='margin-top: 40px'>Tabela Final:</h2>";
    echo "<table border='1' align=center style='text-align:center;'>";
    echo "<tr border=0>";
        echo "<td style='border: none;'>Z</td>";
        for($i=1;$i<=$v;$i++)
        {
            echo "<td style='border: none;'>X".$i."</td>";
        }
        for($i=1+$v;$i<=$v+$r;$i++)
        {
            echo "<td style='border: none;'>xF".($i-$v)."</td>";
        }
        echo "<td style='border: none;'>b</td>";
        echo "</tr>";
    for($j=0; $j<=$r; $j++){
        echo "<tr>";
        for($i=0; $i<=$v+$r+1; $i++){
            if($i==$v+$r+1 && $j==0){
                echo "<td width=50 bgcolor=lightgreen>";
                $z = $matriz[$j][$i];
            } else {
                echo "<td width=50>";
            }
            echo round($matriz[$j][$i],3)."</td>";
        }
        echo "</tr>";
    }
    echo "</table>";
    echo "<p>Solução ótima: Z = ".$z."</p>";
echo "</div>";
echo "</div>";

?>

