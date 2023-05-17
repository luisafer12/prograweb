<HTML>

<HEAD>
   <TITLE>Eliminación de Empeados</TITLE>
   <LINK REL="stylesheet" TYPE="text/css" HREF="estilo.css">
</HEAD>

<BODY>

<H1>Eliminación de Empleados</H1>

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
   if (!isset($consultar) && !isset($eliminar))
   {
 ?>
    <P><B>Búsqueda de empleado a eliminar.</P></B>
	<FORM CLASS="borde" ACTION="delemp.php" METHOD="POST">
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
		if (isset($eliminar))
		{
			if (!isset($borrar)) {  $borrar = array(); }
			
			print ("<P>[ <A HREF=\"delemp.php\">Hacer otra consulta</A> ]</P>\n");
			
			$nborrados = 0;
			
			for ($i=0; $i< count ($borrar); $i++)
			{
				// Obtener datos del i-esimo empleado
				$cSql = "select Fname, Lname, Ssn from EMPLOYEE where Ssn = ?";
				
				$consulta = prepared_query($con, $cSql, [$borrar[$i]]);
				$resultado = $consulta->get_result();
				$renglon = $resultado->fetch_assoc();
				
				// Mostrar datos del empleado a eliminar
				print ("<P><B>Eliminando Empleado:</B>\n");
				print ("<UL>\n");
				print ("   <LI>Nombre: " . $renglon['Fname']);
				print ("   <LI>Apellido: " . $renglon['Lname']);
				print ("   <LI>No. Seguro Soc.: " . $renglon['Ssn']);
				print ("</UL></P>\n");
				
				mysqli_free_result($resultado);

				if ($con->errno) {
				   print ("Falló la consulta, Error: " . $con->error); 
				   //print ("<P>[ <A HREF=\"delemp.php\">Hacer otra consulta</A> ]</P>\n");
				   //die ("");
				}
				
				// borrar el i-esimo empleado seleccionado
				$cSql = "delete from EMPLOYEE where Ssn = ?";
				try{
					//place code here that could potentially throw an exception
					$resultado = prepared_query($con, $cSql, [$borrar[$i]]);
					$nborrados ++;
				}
				catch(Exception $e) {
					//We will catch ANY exception that the try block will throw
					print ("Falló la eliminación, Error: " . $e->getMessage()); 
				}
				
			
				if ($con->errno) {
				   print ("Falló la consulta, Error: " . $con->error); 
				   //print ("<P>[ <A HREF=\"delemp.php\">Hacer otra consulta</A> ]</P>\n");
				   //die ("");
				}
				//mysqli_free_result($resultado);


			}
			print ("<P>Número total de Empleados eliminados: " . $nborrados . "</P>\n");

			// Cerrar conexión
			$con->close();

			print ("<P>[ <A HREF=\"delemp.php\">Hacer otra consulta</A> ]</P>\n");
		
		}
		else
			if (isset($consultar))
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

				print ("<P>[ <A HREF=\"delemp.php\">Hacer otra consulta</A> ]</P>\n");

			  // Mostrar resultados de la consulta
				if ($resultado->num_rows > 0) {
					print "<P><B>Seleccione los registros a eliminar marcando la casilla correspondiente en la columna eliminar.</B></P><BR>\n";
					print ("<FORM CLASS=\"borde\" ACTION=\"delemp.php\" METHOD=\"POST\">\n");
					print ("<TABLE>\n");
					print ("<TR>\n");
					print ("<TH>Nombre</TH>\n");
					print ("<TH>Apellido</TH>\n");
					print ("<TH>No Seguro</TH>\n");
					print ("<TH>Fecha Nac</TH>\n");
					print ("<TH>Dirección</TH>\n");
					print ("<TH>Departamento</TH>\n");
					print ("<TH>Eliminar</TH>\n");
					print ("</TR>\n");

					while($renglon = $resultado->fetch_assoc()) {
						print ("<TR>\n");
						print ("<TD>" . $renglon['Fname'] . "</TD>\n");
						print ("<TD>" . $renglon['Lname'] . "</TD>\n");
						print ("<TD>" . $renglon['Ssn'] . "</TD>\n");
						print ("<TD>" . $renglon['Bdate'] . "</TD>\n");
						print ("<TD>" . $renglon['Address'] . "</TD>\n");
						print ("<TD>" . $renglon['Dname'] . "</TD>\n");
						print ("<TD><INPUT TYPE='CHECKBOX' NAME='borrar[]' VALUE='" . $renglon['Ssn'] . "'></TD>\n");
						print ("</TR>\n");
					}

					print ("</TABLE>\n");

					print ("<BR>\n");
					print ("<P><INPUT TYPE=\"SUBMIT\" NAME=\"eliminar\" VALUE=\"Eliminar\"></P>\n");
					print ("</FORM>\n") ;
				}
				else
					print ("<P><B>No hay registros en el rango de la consulta</B></P>\n");

				print ("<BR>\n");
				print ("<P>[ <A HREF=\"delemp.php\">Hacer otra consulta</A> ]</P>\n");
				//Cerrar conexion
			    mysqli_free_result($resultado);
			    $con->close();
			}

?>
</BODY>
</HTML>
