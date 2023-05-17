<HTML>

<HEAD>
   <TITLE>Registro de Empleado</TITLE>
   <LINK REL="stylesheet" TYPE="text/css" HREF="estilo.css">
</HEAD>

<BODY>

<?PHP
	require_once('conDB.php');
   // Conectar con el servidor de base de datos
   $con = get_mysqli();
   if (!$con) die();

   $errores["fname"] = "";
   $errores["lname"] = "";
   $errores["ssn"] = "";
   $errores["dno"] = "";
   
   /* $fname = "";
   $minit = "";
   $lname = "";
   $ssn = "";
   $bdate = "";
   $address = "";
   $sex = "";
   $salary = "";
   $super_ssn = "";
   $dno = "";
   $grabar = "";
*/


   $error = false;
   if (isset($_REQUEST['grabar'])) { $grabar = $_REQUEST['grabar']; }
   if (isset($_REQUEST['fname'])) { $fname = $_REQUEST['fname']; }
   if (isset($_REQUEST['minit'])) { $minit = $_REQUEST['minit']; }
   if (isset($_REQUEST['lname'])) { $lname = $_REQUEST['lname']; }
   if (isset($_REQUEST['ssn'])) { $ssn = $_REQUEST['ssn']; }
   if (isset($_REQUEST['bdate'])) { $bdate = $_REQUEST['bdate']; }
   if (isset($_REQUEST['address'])) { $address = $_REQUEST['address']; }
   if (isset($_REQUEST['sex'])) { $sex = $_REQUEST['sex']; }
   if (isset($_REQUEST['salary'])) { $salary = $_REQUEST['salary']; }
   if (isset($_REQUEST['super_ssn'])) { $super_ssn = $_REQUEST['super_ssn']; }
   if (isset($_REQUEST['dno'])) { $dno = $_REQUEST['dno']; }
      
   if (isset($_REQUEST['grabar'])) {
      // Nombre
      if (trim($fname) == "") {
         $errores["fname"] = "¡Debe introducir el nombre!";
         $error = true;
      }
      else
         $errores["fname"] = "";
      // Apellido
      if (trim($lname) == "") {
         $errores["lname"] = "¡Debe introducir el apellido!";
         $error = true;
      }
      else
         $errores["fname"] = "";

      // SSN
      if (trim($ssn) == "") {
         $errores["ssn"] = "¡Debe introducir el no de seguridad social!";
         $error = true;
      }
      else
         $errores["ssn"] = "";
      
	  // Departamento
      if (trim($dno) == "") {
         $errores["dno"] = "¡Debe introducir el no de departamento!";
         $error = true;
      }
      else
         $errores["dno"] = "";
   }

   if (isset($_REQUEST['grabar']) && $error == false) {
      print ("<H1>Registro de Empleado. Resultados del formulario</H1>\n");

      print ("<P>Estos son los datos introducidos:</P>\n");
      print ("<UL>\n");
      print ("   <LI>Nombre: $fname\n");
      print ("   <LI>Inicial: $minit\n");
      print ("   <LI>Apellido: $lname\n");
      print ("   <LI>No. Seguro Social: $ssn\n");
      print ("   <LI>Fec. Nacimiento: $bdate\n");
      print ("   <LI>Dirección: $address\n");
      print ("   <LI>Sexo: $sex\n");
      print ("   <LI>Salario: $salary\n"); 
      print ("   <LI>Supervisor: $super_ssn\n");
      print ("   <LI>No. Departamento: $dno\n");
      print ("</UL>\n");
	  $params = array();
		$cols = "insert into EMPLOYEE (Fname, Minit, Lname, Ssn, Bdate, Address, Sex, Salary,";
		$vals = "values (?, ?, ?, ?, ?, ?, ?, ?,";
		
		$params[] = $fname;
		$params[] = $minit;
		$params[] = $lname;
		$params[] = $ssn;
		$params[] = $bdate;
		$params[] = $address;
		$params[] = $sex;
		$params[] = $salary;
		if (!empty($super_ssn)) {
		  $cols = $cols." Super_ssn,"; 
		  $vals = $vals."?, ";
		  $params[] = $super_ssn; 
		}
		$params[] = $dno;
		$cols = $cols." Dno) ";
		$vals = $vals." ?)";
		
		echo "<br>".$cols.$vals;
        
		$consulta = prepared_query($con, $cols.$vals, $params);
		$resultado = $consulta->get_result();

        if ($con->errno) {
               printf("Error al insertar registro: %s<br />", $con->error);
        }
		else {
			print ("<P><B>Registro Realizado con Éxito</B></P>\n");
		}
      print ("<BR><P>[ <A HREF='regemp.php'>Nuevo reistro</A> ]</P>\n");
   }
   else
   {
?>

<H1>Registro de Empleado</H1>

<FORM CLASS="borde" ACTION="regemp.php" METHOD="POST">

<P><LABEL>Nombre:</LABEL>
<INPUT TYPE="TEXT" SIZE="40" NAME="fname"
<?PHP
   if (isset($grabar))
      print (" VALUE='$fname'>\n");
   else
      print (">\n");
   if ($errores["fname"] != "")
      print ("<BR><SPAN CLASS='error'>" . $errores["fname"] . "</SPAN>");
?>
</P>
<P><LABEL>Inicial:</LABEL>
<INPUT TYPE="TEXT" SIZE="5" NAME="minit"></P>

<P><LABEL>Apellido:</LABEL>
<INPUT TYPE="TEXT" SIZE="40" NAME="lname"
<?PHP
   if (isset($grabar))
      print (" VALUE='$lname'>\n");
   else
      print (">\n");
   if ($errores["lname"] != "")
      print ("<BR><SPAN CLASS='error'>" . $errores["lname"] . "</SPAN>");
?>
</P>

<P><LABEL>Seguro Social:</LABEL>
<INPUT TYPE="TEXT" SIZE="20" NAME="ssn" 
<?PHP
   if (isset($grabar))
      print (" VALUE='$ssn'>\n");
   else
      print (">\n");
   if ($errores["ssn"] != "")
      print ("<BR><SPAN CLASS='error'>" . $errores["ssn"] . "</SPAN>");
?>
</P>

<P><LABEL>Fec. Nacimiento:</LABEL>
<INPUT TYPE="TEXT" SIZE="20" NAME="bdate"></P>
<P><LABEL>Dirección:</LABEL>
<INPUT TYPE="TEXT" SIZE="40" NAME="address"></P>

<P><LABEL>Genero:</LABEL>
<INPUT TYPE="RADIO" NAME="sex" VALUE="H">Hombre
<INPUT TYPE="RADIO" NAME="sex" VALUE="M">Mujer</P>

<P><LABEL>Salario:</LABEL>
<INPUT TYPE="TEXT" SIZE="40" NAME="salary"></P>

<P><LABEL>Supervisor:</LABEL>
<INPUT TYPE="TEXT" SIZE="20" NAME="super_ssn"></P>

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

   if ($errores["dno"] != "")
      print ("<BR><SPAN CLASS='error'>" . $errores["dno"] . "</SPAN>");
  
  
?>
</SELECT>
</P>

<P><INPUT TYPE="SUBMIT" NAME="grabar" VALUE="Grabar">
<INPUT TYPE="RESET" VALUE="Limpiar"></P>

</FORM>
<?php
   }
   $con->close();
  ?>
</BODY>
</HTML>
