<?php

$restricoes = $_POST['restricoes'];
$variaveis = $_POST['variaveis'];

?>

<html>
    <div align="center" style="margin-top: 40px">
        <h1>Simplex</h1>
        <form action='resolucao' method="post" align="center">
            <p style="margin-top: 40px">Objetivo da Função: <select name='tipoprob'><option value='maximizar'>Maximizar</option><option value='minimizar'>Minimizar</option></select>
            <p style="margin-top: 40px">Função Objetivo: Z = <?php for($i=1; $i<=$variaveis; $i++){ echo "<input type='text' id='varz$i' name='varz$i' style='width:3%' />X$i"; if($i<$variaveis) echo " + "; } ?></p>
            <table style="width:100%; margin-top:40px">
                <?php for($j=1; $j<=$restricoes; $j++){ ?>
                <tr align="center">
                <td>Restrição <?php echo "$j: "; for($i=1; $i<=$variaveis; $i++){ echo "<input type='text' id='varr$j$i' name='varr$j$i' style='width:6%' />X$i"; if($i<$variaveis) echo " + "; } echo " <select><option value='menor$j'>≤</option><option value='igual$j'>=</option></select> <input type='number' id='varr$j' name='varr$j' style='width:5%' />" ?></td>
                </tr>
                <?php } ?>
            </table>
            <p><?php for($t=1; $t<=$variaveis; $t++){ echo 'X'.$t; if($t<$variaveis) echo ', '; } echo ' ≥ 0'; ?></p>
            <input type="hidden" value="<?php echo $variaveis; ?>" id='variaveis' name='variaveis' />
            <input type="hidden" value="<?php echo $restricoes; ?>" id='restricoes' name='restricoes' />
            <button type="submit" style="margin-top: 40px">Resolver</button>
        </form>
    </div>
</html>

