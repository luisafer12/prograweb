<HTML>
<HEAD>
   <TITLE>Actualización de Empeados</TITLE>
   <LINK REL="stylesheet" TYPE="text/css" HREF="estilo.css">
</HEAD>

<BODY>

<H1>Actualización de Empleados</H1>

<?PHP
   // Conectar con el servidor de base de datos
	require_once('conDB.php');
   // Conectar con el servidor de base de datos
   $con = get_mysqli();
   if (!$con) die();

   if (isset($_REQUEST['eliminar'])) { $eliminar = $_REQUEST['eliminar']; }
   if (isset($_REQUEST['consultar'])) { $consultar = $_REQUEST['consultar']; }
   if (isset($_REQUEST['borrar'])) { $borrar = $_REQUEST['borrar']; }
   if (isset($_REQUEST['fname'])) { $fname = $_REQUEST['fname']; }
   if (isset($_REQUEST['lname'])) { $lname = $_REQUEST['lname']; }
   if (isset($_REQUEST['ssn'])) { $ssn = $_REQUEST['ssn']; }
   if (isset($_REQUEST['dno'])) { $dno = $_REQUEST['dno']; }
   //Si no hay consulta
   if (!isset($consultar)) {
 ?>
     <P><B>Búsqueda de empleado a actualizar.</P></B>

	<FORM CLASS="borde" ACTION="updemp.php" METHOD="POST">
	<P><LABEL>Nombre:</LABEL>
	<INPUT TYPE="TEXT" SIZE="40" NAME="fname"></P>
	<P><LABEL>Apellido:</LABEL>
	<INPUT TYPE="TEXT" SIZE="40" NAME="lname"></P>
	<P><LABEL>Seguro Social:</LABEL>
	<INPUT TYPE="TEXT" SIZE="20" NAME="ssn" ></P>
	<P><LABEL>Departamento:</LABEL>
	<SELECT NAME="dno">
	<?php
		if (!isset($dno)) {
		  print ("<OPTION VALUE=>--- Seleccione Uno ---</OPTION>>\n");
	   }

	   $rsDepto = $con->query("select * from department");   
	   
	   $opt = "";
	   if ($rsDepto->num_rows > 0) { 
		 while($ren = $rsDepto->fetch_assoc()) { 
	   
		   $opt = "<OPTION VALUE='".$ren["Dnumber"]."'";
		   if (isset($dno)) {
			   if ($dno == $ren["Dnumber"]) { 
				  $opt = $opt." SELECTED";
			   }
			}
		   $opt = $opt.">".$ren["Dname"]."</OPTION>\n";
		   
		   print($opt);
		 }
	   }
	   mysqli_free_result($rsDepto);

	?>
	</SELECT>
	</P>

	<P><INPUT TYPE="SUBMIT" NAME="consultar" VALUE="Consultar">
	<INPUT TYPE="RESET" VALUE="Limpiar"></P>

	</FORM>
 <?php
	}
	else
	{
		//Realizar consulta
		$cSql = "select e.Fname, e.Lname, e.Ssn, e.Bdate, e.Address, d.Dname ";
		$cSql .= " from EMPLOYEE as e inner join DEPARTMENT as d on e.Dno = d.Dnumber ";

		$params = array();
		
		// Enviar consulta
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
		$cSql .= $cfiltro . " order by Lname, Fname";
		
		echo "<br>$cSql";
		print_r($params);

		$consulta = prepared_query($con, $cSql, $params);
		$resultado = $consulta->get_result();
		
		if ($con->errno) {
		   print ("Falló la consulta, Error: " . $con->error); 
		   //print ("<P>[ <A HREF=\"delemp.php\">Hacer otra consulta</A> ]</P>\n");
		   die ("");
		}

		print ("<P>[ <A HREF=\"updemp.php\">Hacer otra búsqueda</A> ]</P>\n");

		// Mostrar resultados de la consulta
		if ($resultado->num_rows > 0) {
			print "<P><B>Haga click en la liga del epleado que desea modificar.</B></P><BR>\n";
			//print ("<FORM CLASS=\"borde\" ACTION=\"updemp.php\" METHOD=\"POST\">\n");
			print ("<TABLE>\n");
			print ("<TR>\n");
			print ("<TH>Nombre</TH>\n");
			print ("<TH>Apellido</TH>\n");
			print ("<TH>No Seguro</TH>\n");
			print ("<TH>Fecha Nac</TH>\n");
			print ("<TH>Dirección</TH>\n");
			print ("<TH>Departamento</TH>\n");
			print ("<TH>Editar</TH>\n");
			print ("</TR>\n");

			while($renglon = $resultado->fetch_assoc()) {
				print ("<TR>\n");
				print ("<TD>" . $renglon['Fname'] . "</TD>\n");
				print ("<TD>" . $renglon['Lname'] . "</TD>\n");
				print ("<TD>" . $renglon['Ssn'] . "</TD>\n");
				print ("<TD>" . $renglon['Bdate'] . "</TD>\n");
				print ("<TD>" . $renglon['Address'] . "</TD>\n");
				print ("<TD>" . $renglon['Dname'] . "</TD>\n");
				print ("<TD>[ <A HREF=\"execUpdemp.php?ssn=" . $renglon['Ssn'] . "\" TARGET=\"_blank\">Editar</A> ]</TD>\n");
				print ("</TR>\n");
			}

			print ("</TABLE>\n");

			print ("<BR>\n");
		}
		else
			print ("<P><B>No hay registros en el rango de la consulta</B></P>\n");

		print ("<BR>\n");
		print ("<P>[ <A HREF=\"updemp.php\">Hacer otra búsqueda</A> ]</P>\n");
		//Cerrar conexion
		
		$con->close();
	}

	//por si las flyes
	//$con->close(); 
?>
</BODY>
</HTML>
