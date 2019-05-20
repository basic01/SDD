<?php
    $conexion = mysqli_connect("localhost", "root", "", "dss"); 
    $query = "SELECT * FROM salariomin";
    $result = mysqli_query($conexion, $query);
    $n = mysqli_num_rows($result);

    $titulos = ["Periodo", "Frecuencias", "PS"];

    for ($i=0; $i < $n; $i++) { 
        $fila = mysqli_fetch_row($result);
        $datos[$i][0] = $fila[0];
        $datos[$i][1] = $fila[1];
    }

    //Calcular el PS
    $acumulador = 0;
    $datos[0][2] = "---";
    for ($i=1; $i < $n; $i++) { 
        $acumulador += $datos[$i-1][1];
        $PS = $acumulador/$i;
        $datos[$i][2] = round($PS,2);
    }

    //Calcular el PMS
    $acumuladorPMS = 0;
    $k = 3;
    $kAumentada = $k;
    $contk = 0;

    $titulos[3] = "PMS k=".$k;

    for ($i=0; $i < $k; $i++) { 
        $datos[$i][3] = "---";
    }

    for ($i=$k; $i <$n + 1; $i++) {

        while($contk < $kAumentada){
                $acumuladorPMS += $datos[$contk][1];
                $contk++;
            }

        $PMS = $acumuladorPMS / $k;
        $datos[$i][3] = round($PMS, 2);

        $kAumentada++;
        $contk = $kAumentada-$k;
        $acumuladorPMS = 0;
    }

    //Calcular el PMD
    $acumuladorPMD = 0;
    $J = 2;
    $jAumentada = $J;
    $contj = 0;

    $titulos[4] = "PMD j=".$J;

    for ($i=0; $i < $k + $J; $i++) { 
        $datos[$i][4] = "---";
    }

    for ($i=$k + $J; $i <$n + 1; $i++) {
        $datos[$i][4] = $i;
        $contj = $i-$J;
        for ($z=0; $z < $J; $z++) { 
            $acumuladorPMD += $datos[$contj][3];
            $contj++;
        }
        $PMD = $acumuladorPMD / $J;
        $datos[$i][4] = round($PMD, 2);
        $acumuladorPMD = 0;

    }

    //Calcular A,B y PMDA
    $A = 0;
    $B = 0;
    $m = 1;
    $PMDA = 0;
    $titulos[5] = "A";
    $titulos[6] = "B";
    $titulos[7] = "PMDA";
    for ($i=0; $i < $k + $J; $i++) { 
        $datos[$i][5] = "---";
        $datos[$i][6] = "---";
        $datos[$i][7] = "---";
    }

    for ($i=$k + $J; $i < $n+1; $i++) { 
        $A = (2*$datos[$i][3])-$datos[$i][4];
        $B = (2*($datos[$i][3]-$datos[$i][4]))/($n-1);
        $PMDA = ($A + $B) * $m;
        $datos[$i][5] = round($A, 2);
        $datos[$i][6] = round($B, 2);
        $datos[$i][7] = round($PMDA, 2);
    }


    // Calcular EABS
    for ($i=0; $i < 4; $i++) { 
        
        $eabs[0][$i] = "---";
        //EABS PS, PMS, PMD
        if($i < 3){
            $pos = 2;
        }
        //EABS PMDA
        if($i == 3){
            $pos = 4;
        }

        for ($j=1; $j < 1 + $n - 1; $j++) { 
            //Datos frecuencias = $datos[$j][1]
            //Datos promediosn = $datos[$j][$i+$pos]
            if($datos[$j][$i+$pos] == "---"){
                $eabs[$j][$i] = "---";
            }
            else{
                $resta = $datos[$j][1] - $datos[$j][$i+$pos];
                $eabs[$j][$i] =  round(abs($resta),2);
            }
        }
    }


 ?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="css/estilos.css">
	<title></title>
</head>
<body>
    <div class="contenedor">

        <h1>Sistema de Pronóstico</h1>

        <p>A continuación se despliegan varias tablas 
            reflejando las distintos métodos para obtener 
            el pronóstico de un dato en un periodo específico de tiempo:</p>

        <table class="table">
            <tbody>
        <?php

            print("<tr>");
                for ($j=0; $j < 8; $j++) { 
                    if($j>1){
                        print("<th>".$titulos[$j]. "</th>"); 
                    }
                    else{
                        if($j == 0){
                            print("<th>".$titulos[$j]. "</th>");
                        }
                        else if($j==1){ 
                            print("<th>".$titulos[$j]. "</th>");
                        }
                    }
                } 
            print("</tr>");


            //Periodo, Frecuencia, PS, PMS k = ....
            for ($i=0; $i < $n; $i++) {
                print("<tr>");
                for ($j=0; $j < 8; $j++) { 
                    if($j>1){
                        //PS, PMS k = ....
                        print("<td>".$datos[$i][$j]. "</td>");
                    }
                    else{
                        //PMS Periodo, Frecuencia
                        print("<td class='important'>".$datos[$i][$j]. "</td>");
                    }
                } 
                print("</tr>");
            }

        ?>
            </tbody>
        </table>

        <table class="table">
            <tbody>
        <?php
            print("<tr>");
            //count(current($eabs)) = length de columnas de arreglo eabs
                for ($j=0; $j < count(current($eabs)); $j++) { 
                    if($j==0){
                        print("<th class='header2'>EABS PS </th>");
                    }
                    if($j == 1){
                        print("<th class='header2'>EABS PMS k=$k </th>");
                    }
                    if($j == 2){
                        print("<th class='header2'>EABS PMD j=$J </th>");
                    }
                    if($j == 3){
                        print("<th class='header2'>EABS PMDA m=$m </th>");
                    }
                } 
            print("</tr>");


            //EABS PS y PMS k = ....
            for ($i=0; $i < $n; $i++) {
                print("<tr>");
                for ($j=0; $j < count(current($eabs)); $j++) { 
                    print("<td>".$eabs[$i][$j]."</td>");
                } 
                print("</tr>");
            }
        ?>
            </tbody>
        </table>




        </div>
            
    
</body>
</html>
   
