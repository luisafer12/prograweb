<?php  header('Content-type: text/html; charset=utf-8'); ?>
<!DOCTYPE html>
<html lang="es" dir="ltr">

<HEAD>
	<meta charset="UTF-8">
	<TITLE>Actualización de Empleado</TITLE>
	<LINK REL="stylesheet" TYPE="text/css" HREF="estilo.css">
</HEAD>

<BODY>

<?PHP
		// Conectar con el servidor de base de datos
		require_once('conDB.php');
		// Conectar con el servidor de base de datos
		$con = get_mysqli();
		if (!$con) die();

		//no hay Ssn
		if (isset($_REQUEST['ssn'])) { $ssn = $_REQUEST['ssn']; }
		if (!isset($ssn))
			die ("<P><B>No se recibio un No de Seguro Social</B></P>");

		$errores["fname"] = "";
		$errores["lname"] = "";
		$errores["ssn"] = "";
		$errores["dno"] = "";

		if (isset($_REQUEST['grabar'])) { 
			$grabar = $_REQUEST['grabar']; 
		}
		
		$fname = isset($_REQUEST['fname']) ? $_REQUEST['fname'] : null;
		$minit = isset($_REQUEST['minit']) ? $_REQUEST['minit'] : null;
		$lname = isset($_REQUEST['lname']) ? $_REQUEST['lname'] : null;
		$ssn = isset($_REQUEST['ssn']) ? $_REQUEST['ssn'] : null;
		$bdate = isset($_REQUEST['bdate']) ? $_REQUEST['bdate'] : null;
		$dno = isset($_REQUEST['dno']) ? $_REQUEST['dno'] : null;
		$address = isset($_REQUEST['address']) ? $_REQUEST['address'] : null;
		$sex = isset($_REQUEST['sex']) ? $_REQUEST['sex'] : null;
		$salary = isset($_REQUEST['salary']) ? $_REQUEST['salary'] : null;
		$super_ssn = isset($_REQUEST['super_ssn']) ? $_REQUEST['super_ssn'] : null;


	$error = false;
	
	
	if (isset($grabar))	{
		// Nombre
		
		  if (trim($fname) == "") {
			 $errores["fname"] = "!Debe introducir el nombre!";
			 $error = true;
		  }
		  else
			 $errores["fname"] = "";
		  // Apellido
		  if (trim($lname) == "") {
			 $errores["lname"] = "!Debe introducir el apellido!";
			 $error = true;
		  }
		  else
			 $errores["fname"] = "";

		  // SSN
		  if (trim($ssn) == "") {
			 $errores["ssn"] = "!Debe introducir el no de seguridad social!";
			 $error = true;
		  }
		  else
			 $errores["ssn"] = "";
		  // Departamento
		  if (trim($dno) == "") {
			 $errores["dno"] = "!Debe introducir el no de departamento!";
			 $error = true;
		  }
		  else
			 $errores["dno"] = "";

		  // Sipervisor
		  if (empty($super_ssn)) {
			 $super_ssn = null;
		  }
		  	
		}
		
		if (isset($grabar) && !$error) {
			$params = array();
			
			print ("<H1>Actualización de Empleado</H1>\n");

			print ("<P>Datos introducidos:</P>\n");
			print ("<UL>\n");
			print ("   <LI>No. Seguro Social: $ssn\n");
			print ("   <LI>Nombre: $fname\n");
			print ("   <LI>Inicial: $minit\n");
			print ("   <LI>Apellido: $lname\n");
			print ("   <LI>Fec. Nacimiento: $bdate\n");
			print ("   <LI>Dirección: $address\n");
			print ("   <LI>Sexo: $sex\n");
			print ("   <LI>Salario: $salary\n"); 
			print ("   <LI>Supervisor: $super_ssn\n");
			print ("   <LI>No. Departamento: $dno\n");
			print ("</UL>\n");
			
			//$params[] = [$fname, $minit, $lname, $bdate, $address, $sex, $salary, $super_ssn, $dno, $ssn];
		    $cSql = "update EMPLOYEE set Fname = ?, ";
			$cSql .= " Minit = ?, ";
			$cSql .= " Lname = ?, ";
			$cSql .= " Bdate = ?, ";
			$cSql .= " Address = ?, ";
			$cSql .= " Sex = ?, ";
			$cSql .= " Salary = ?, ";
			$cSql .= " Super_ssn = ?, ";
			$cSql .= " Dno = ?" ;
			$cSql .= " where Ssn = ?";
			
			echo "<br>$cSql<br>";
			
			try{
				//place code here that could potentially throw an exception
				$resultado = prepared_query($con, $cSql, [$fname, $minit, $lname, $bdate, $address, $sex, $salary, $super_ssn, $dno, $ssn]  );
				
			}
			catch(Exception $e) {
				//We will catch ANY exception that the try block will throw
				print ("<P>Falló la actualización, Error: " . $e->getMessage()."\n</P>"); 
			}


			if ($con->errno) {
			   print ("Falló la actualización, Error: " . $con->error); 
			   //print ("<P>[ <A HREF=\"delemp.php\">Hacer otra consulta</A> ]</P>\n");
			   //die ("");
			} else {
				print ("<P><B>actualización de datos con éxito</B></P>\n");
			}


			// Cerrar conexión
			$con->close();
			
			
		}
		else
		{
			if (!isset($grabar))
			{
				$cSql = "select * from EMPLOYEE where Ssn = ?";
				$consulta = prepared_query($con, $cSql, [$ssn]);
				$resultado = $consulta->get_result();

				if ($con->errno) {
				   print ("Falló la consulta, Error: " . $con->error); 
				   //print ("<P>[ <A HREF=\"delemp.php\">Hacer otra consulta</A> ]</P>\n");
				   die ("");
				}
			
				$renglon = $resultado->fetch_assoc();

				$fname = $renglon['Fname'];
				$minit = $renglon['Minit'];
				$lname = $renglon['Lname'];
				$bdate = $renglon['Bdate'];
				$address = $renglon['Address'];
				$sex = $renglon['Sex'];
				$salary = $renglon['Salary'];
				$super_ssn = $renglon['Super_ssn'];
				$dno = $renglon['Dno'];
			}
?>

<H1>Actualización de Empleado</H1>

<FORM CLASS="borde" ACTION="execUpdemp.php" METHOD="POST">
<P><LABEL>Seguro Social:</LABEL><B><?php echo "$ssn" ?></B>
<INPUT TYPE="HIDDEN" NAME="ssn" VALUE = "<?php echo "$ssn" ?>"> 
<?PHP
		if ($errores["ssn"] != "")
		  print ("<BR><SPAN CLASS='error'>" . $errores["ssn"] . "</SPAN>");
?>
</P>

<P><LABEL>Nombre:</LABEL>
<INPUT TYPE="TEXT" SIZE="40" NAME="fname"  VALUE = "<?php echo "$fname" ?>"> 
<?PHP
		if ($errores["fname"] != "")
		  print ("<BR><SPAN CLASS='error'>" . $errores["fname"] . "</SPAN>");
?>
</P>
<P><LABEL>Inicial:</LABEL>
<INPUT TYPE="TEXT" SIZE="5" NAME="minit"  VALUE = "<?php echo "$minit" ?>"> 
</P>

<P><LABEL>Apellido:</LABEL>
<INPUT TYPE="TEXT" SIZE="40" NAME="lname" VALUE = "<?php echo "$lname" ?>"> 
<?PHP
		if ($errores["lname"] != "")
		  print ("<BR><SPAN CLASS='error'>" . $errores["lname"] . "</SPAN>");
?>
</P>

<P><LABEL>Fec. Nacimiento:</LABEL>
<INPUT TYPE="TEXT" SIZE="20" NAME="bdate"  VALUE = "<?php echo "$bdate" ?>"></P>

<P><LABEL>Dirección:</LABEL>
<INPUT TYPE="TEXT" SIZE="40" NAME="address" VALUE = "<?php echo "$address" ?>"></P>

<P><LABEL>Genero:</LABEL>
<INPUT TYPE="RADIO" NAME="sex" VALUE="M" <?php if ($sex == "M") echo "CHECKED"; ?>>Hombre
<INPUT TYPE="RADIO" NAME="sex" VALUE="F" <?php if ($sex == "F") echo "CHECKED"; ?>>Mujer</P>

<P><LABEL>Salario:</LABEL>
<INPUT TYPE="TEXT" SIZE="40" NAME="salary" VALUE = "<?php echo "$salary" ?>"> </P>

<P><LABEL>Supervisor:</LABEL>
<INPUT TYPE="TEXT" SIZE="20" NAME="super_ssn" VALUE = "<?php echo "$super_ssn" ?>"> </P>

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

<?PHP
		if ($errores["dno"] != "")
		  print ("<BR><SPAN CLASS='error'>" . $errores["dno"] . "</SPAN>");
?>
</P>

<P><INPUT TYPE="SUBMIT" NAME="grabar" VALUE="Grabar"></P>

</FORM>
<?php
	}
	
  ?>
</BODY>
</HTML>
