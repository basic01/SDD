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
    for ($j=1; $j < $n-1; $j++) {
        $acumuladorPMS = 0;
        $k = $j;
        $pos = $j + 2;
        $kAumentada = $j;
        $contk = 0;

        $titulos[$pos] = "PMS k=".$k;

        for ($i=0; $i < $k; $i++) { 
            $datos[$i][$pos] = "---";
        }

        for ($i=$k; $i <$n + 1; $i++) {

        while($contk < $kAumentada){
                $acumuladorPMS += $datos[$contk][1];
                $contk++;
            }

            $PMS = $acumuladorPMS / $k;
            $datos[$i][$pos] = round($PMS, 2);

            $kAumentada++;
            $contk = $kAumentada-$k;
            $acumuladorPMS = 0;
        }
    }

    //Calcular EABS
    for ($i=0; $i < 1 + $n - 2; $i++) { 
        
        $eabs[0][$i] = "---";

        for ($j=1; $j < 1 + $n - 1; $j++) { 
            //Datos frecuencias = $datos[$j][1]
            //Datos promediosn = $datos[$j][$i+2];

            if($datos[$j][$i+2] == "---"){
                $eabs[$j][$i] = "---";
            }
            else{
                $resta = $datos[$j][1] - $datos[$j][$i+2];
                $eabs[$j][$i] =  abs($resta);
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
                for ($j=0; $j < (2 + $n-1); $j++) { 
                    if($j>1){
                        print("<th>".$titulos[$j]. "</th>"); 
                    }
                    else{
                        if($j == 0){
                            print("<th class='periodo'>".$titulos[$j]. "</th>");
                        }
                        else if($j==1){ 
                            print("<th class='frecuencia'>".$titulos[$j]. "</th>");
                        }
                    }
                } 
            print("</tr>");


            //PMS k = ....
            for ($i=0; $i < $n; $i++) {
                print("<tr>");
                for ($j=0; $j < (2 + $n-1); $j++) { 
                    if($j>1){
                        print("<td>".$datos[$i][$j]. "</td>");
                    }
                    else{
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
                    if($j !== 0){
                        print("<th class='header2'>EABS k=$j </th>");
                    }
                    if($j==0){
                        print("<th class='header2'>EABS PS </th>");
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
   
