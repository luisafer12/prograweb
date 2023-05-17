<HTML LANG="es">

<HEAD>
   <TITLE>Consulta de Empeados</TITLE>
   <LINK REL="stylesheet" TYPE="text/css" HREF="estilo.css">

</HEAD>

<BODY>

<H1>Consulta de Empleados</H1>

<?PHP
	require_once('conDB.php');
   // Conectar con el servidor de base de datos
   $con = get_mysqli();
   if (!$con) die();



   if (isset($_REQUEST['consultar'])) {
   $consultar = $_REQUEST['consultar'];
   $fname = $_REQUEST['fname'];
   $lname = $_REQUEST['lname'];
   $ssn = $_REQUEST['ssn'];
   $dno = $_REQUEST['dno'];
   }
   
   
   if (isset($consultar))
   {
		$params = array();
		
        // Enviar consulta
        $sql = "select e.Fname, e.Lname, e.Ssn, e.Bdate, e.Address, d.Dname ";
		$sql .= "from employee as e inner join department as d on e.Dno = d.Dnumber ";
		$cfiltro = "";
		if ($fname != "")
		{
			$cfiltro .= " where Fname LIKE ?";
			$params[] = "%".$fname."%";
		} 
		if ($lname != "")
		{
			if ($cfiltro == "")
				$cfiltro .= " where ";
			else
				$cfiltro .= " and ";
			$cfiltro .= "Lname LIKE ?";
			$params[] = "%".$lname."%";
		}
		if ($ssn != "")
		{
			if ($cfiltro == "")
				$cfiltro .= " where ";
			else
				$cfiltro .= " and ";
			$cfiltro .= "Ssn LIKE ?";
			$params[] = "%".$ssn."%";

		}
		if ($dno != "")
		{
			if ($cfiltro == "")
				$cfiltro .= " where ";
			else
				$cfiltro .= " and ";
			$cfiltro .= "Dno = ?" ;
			$params[] = $dno;

		}	
		$sql .= $cfiltro . " order by Lname, Fname";
        
		echo "<br>$sql";
        print_r($params);

		$consulta = prepared_query($con, $sql, $params);
		$resultado = $consulta->get_result();
		
        if ($resultado->num_rows <=0) 
		{
		   print ("No hay registros que mostrar.");
		   print ("<P>[ <A HREF='consulta_simple.php'>Hacer otra consulta</A> ]</P>\n");
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
         print ("<P><B>No hay registros en el rango de la consulta</B></P>");

      print ("<BR>\n");
      print ("<P>[ <A HREF='consulta_simple.php'>Hacer otra consulta</A> ]</P>\n");

    // Cerrar conexión
    mysqli_free_result($resultado);
    $con->close();         

   }
   else
   { 
      //Poner el formulario
   ?>

	<FORM CLASS="borde" ACTION="consulta_simple.php" METHOD="POST">

	<P><LABEL>Nombre:</LABEL>
	<INPUT TYPE="TEXT" SIZE="40" NAME="fname"></P>
	<P><LABEL>Apellido:</LABEL>
	<INPUT TYPE="TEXT" SIZE="40" NAME="lname"></P>
	<P><LABEL>Seguro Social:</LABEL>
	<INPUT TYPE="TEXT" SIZE="20" NAME="ssn" ></P>
	<P><LABEL>Departamento:</LABEL>
	<SELECT NAME="dno">
	<OPTION VALUE="">--- Seleccione Uno ---</OPTION> 
	<?php
	   $rsDepto = $con->query("select * from department");   
       if ($rsDepto->num_rows > 0) {
         while($ren = $rsDepto->fetch_assoc()) {
		  echo "<OPTION VALUE=\"".$ren["Dnumber"]."\">".$ren["Dname"]."</OPTION>";
         }
	   }
       mysqli_free_result($rsDepto);
       $con->close();

	?>
	</SELECT></P>

	<P><INPUT TYPE="SUBMIT" NAME="consultar" VALUE="Consultar">
	<INPUT TYPE="RESET" VALUE="Limpiar"></P>

	</FORM>
<?php	  
   }
?>


<?php     
    
             
 ?>
</BODY>
</HTML>
