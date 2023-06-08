<HTML LANG="es">

<HEAD>
   <TITLE>Consulta de Empleados</TITLE>
   <LINK REL="stylesheet" TYPE="text/css" HREF="estilo.css">
</HEAD>

<BODY>

<H1>Consulta de Empleados</H1>

<?PHP
	require_once('conDB.php');
   // Conectar con el servidor de base de datos
   $con = get_mysqli();
   if (!$con) die();

   // Establecer el número de filas por página y la fila inicial
      $num = 5; // número de filas por página

      $comienzo = 0;
      if (isset($_REQUEST['comienzo']) ) { 
        $comienzo = $_REQUEST['comienzo']; 
         
      }
      
      //if (!isset($comienzo)) $comienzo = 0;

   // Calcular el número total de filas de la tabla
        $sql = "select e.Fname, e.Lname, e.Ssn, e.Bdate, e.Address, d.Dname ";
		$sql .= "from employee as e inner join department as d on e.Dno = d.Dnumber order by Lname, Fname";
		
		$consulta = $con->query($sql);
	    $nfilas = $consulta->num_rows;
        if ($nfilas < 0) {
		   print ("");
		   print ("<P>[ <A HREF='consulta_paginación.php'>Hacer otra consulta</A> ]</P>\n");
		   die ("");
		}

      if ($nfilas > 0)
      {

      // Mostrar números inicial y final de las filas a mostrar
         print ("<P>Mostrando empleados " . ($comienzo + 1) . " a ");
         if (($comienzo + $num) < $nfilas)
            print ($comienzo + $num);
         else
            print ($nfilas);
         print (" de un total de $nfilas.\n");

      // Mostrar botones anterior y siguiente
         $estapagina = $_SERVER['PHP_SELF'];
         if ($nfilas > $num)
         {
            if ($comienzo > 0)
               print ("[ <A HREF='$estapagina?comienzo=" . ($comienzo - $num) . "'>Anterior</A> | ");
            else
               print ("[ Anterior | ");
            if ($nfilas > ($comienzo + $num))
               print ("<A HREF='$estapagina?comienzo=" . ($comienzo + $num) . "'>Siguiente</A> ]\n");
            else
               print ("Siguiente ]\n");
         }
         print ("</P>\n");

      }

   // Enviar consulta

	  
        $sql = "select e.Fname, e.Lname, e.Ssn, e.Bdate, e.Address, d.Dname ";
		$sql .= "from employee as e inner join department as d on e.Dno = d.Dnumber order by Lname, Fname limit ?, ?";
		$consulta = $con->prepare($sql);
        $consulta->bind_param("ii", $comienzo, $num);
		$consulta->execute();
		$resultado = $consulta->get_result();
		
        if ($resultado->num_rows <=0) 
		{
		   print ("No hay registros que mostrar.");
		   print ("<P>[ <A HREF='consulta_paginacion.php'>Hacer otra consulta</A> ]</P>\n");
		   die ("");
		}

	  
	  
        // Mostrar resultados de la consulta




        if ($resultado->num_rows > 0) {
  		  print ("<TABLE>\n");
		  print ("<TR>\n");
		  print ("<TH>Nombre</TH>\n");
		  print ("<TH>Apellido</TH>\n");
		  print ("<TH>No Seguro</TH>\n");
		  print ("<TH>Fecha Nac</TH>\n");
		  print ("<TH>Dirección</TH>\n");
		  print ("<TH>Departamento</TH>\n");
		  print ("</TR>\n");
          while($renglon = $resultado->fetch_assoc()) {
            
            print ("<TR>\n");
            print ("<TD>" . $renglon['Fname'] . "</TD>\n");
            print ("<TD>" . $renglon['Lname'] . "</TD>\n");
            print ("<TD>" . $renglon['Ssn'] . "</TD>\n");
            print ("<TD>" . $renglon['Bdate'] . "</TD>\n");
            print ("<TD>" . $renglon['Address'] . "</TD>\n");
            print ("<TD>" . $renglon['Dname'] . "</TD>\n");
            print ("</TR>\n");
         }

         print ("</TABLE>\n");

         print ("<BR>\n");
        }
        else
          print ("<P>No hay registros en el rango de la consulta</P>");

    // Cerrar conexión
    mysqli_free_result($resultado);
    $con->close();         

?>

</BODY>
</HTML>
