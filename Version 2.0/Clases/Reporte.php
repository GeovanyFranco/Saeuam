  <?php require_once('../Connections/basededatos.php'); ?>
  <?php
//initialize the session
if (!isset($_SESSION)) {
  session_start();
}

// ** Logout the current user. **
$logoutAction = $_SERVER['PHP_SELF']."?doLogout=true";
if ((isset($_SERVER['QUERY_STRING'])) && ($_SERVER['QUERY_STRING'] != "")){
  $logoutAction .="&". htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_GET['doLogout'])) &&($_GET['doLogout']=="true")){
  //to fully log out a visitor we need to clear the session varialbles
  $_SESSION['MM_Username'] = NULL;
  $_SESSION['MM_UserGroup'] = NULL;
  $_SESSION['PrevUrl'] = NULL;
  unset($_SESSION['MM_Username']);
  unset($_SESSION['MM_UserGroup']);
  unset($_SESSION['PrevUrl']);
	
  $logoutGoTo = "../paginas/Sesion.php";// cambiar aca dependiendo donde nos ubicamos
  if ($logoutGoTo) {
    header("Location: $logoutGoTo");
    exit;
  }
}
?>
  <?php
if (!isset($_SESSION)) {
  session_start();
}
$MM_authorizedUsers = "";
$MM_donotCheckaccess = "true";

// *** Restrict Access To Page: Grant or deny access to this page
function isAuthorized($strUsers, $strGroups, $UserName, $UserGroup) { 
  // For security, start by assuming the visitor is NOT authorized. 
  $isValid = False; 

  // When a visitor has logged into this site, the Session variable MM_Username set equal to their username. 
  // Therefore, we know that a user is NOT logged in if that Session variable is blank. 
  if (!empty($UserName)) { 
    // Besides being logged in, you may restrict access to only certain users based on an ID established when they login. 
    // Parse the strings into arrays. 
    $arrUsers = Explode(",", $strUsers); 
    $arrGroups = Explode(",", $strGroups); 
    if (in_array($UserName, $arrUsers)) { 
      $isValid = true; 
    } 
    // Or, you may restrict access to only certain users based on their username. 
    if (in_array($UserGroup, $arrGroups)) { 
      $isValid = true; 
    } 
    if (($strUsers == "") && true) { 
      $isValid = true; 
    } 
  } 
  return $isValid; 
}

$MM_restrictGoTo = "../paginas/fail.html";
if (!((isset($_SESSION['MM_Username'])) && (isAuthorized("",$MM_authorizedUsers, $_SESSION['MM_Username'], $_SESSION['MM_UserGroup'])))) {   
  $MM_qsChar = "?";
  $MM_referrer = $_SERVER['PHP_SELF'];
  if (strpos($MM_restrictGoTo, "?")) $MM_qsChar = "&";
  if (isset($_SERVER['QUERY_STRING']) && strlen($_SERVER['QUERY_STRING']) > 0) 
  $MM_referrer .= "?" . $_SERVER['QUERY_STRING'];
  $MM_restrictGoTo = $MM_restrictGoTo. $MM_qsChar . "accesscheck=" . urlencode($MM_referrer);
  header("Location: ". $MM_restrictGoTo); 
  exit;
}
?>
  <?php
if (!function_exists("GetSQLValueString")) {
function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") 
{
  if (PHP_VERSION < 6) {
    $theValue = get_magic_quotes_gpc() ? stripslashes($theValue) : $theValue;
  }

  $theValue = function_exists("mysql_real_escape_string") ? mysql_real_escape_string($theValue) : mysql_escape_string($theValue);

  switch ($theType) {
    case "text":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;    
    case "long":
    case "int":
      $theValue = ($theValue != "") ? intval($theValue) : "NULL";
      break;
    case "double":
      $theValue = ($theValue != "") ? doubleval($theValue) : "NULL";
      break;
    case "date":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;
    case "defined":
      $theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue;
      break;
  }
  return $theValue;
}
}

mysql_select_db($database_basededatos, $basededatos);
$query_ClasesID = "SELECT clases.idClases, clases.Nombre_clase, Departamento.Nombre, profesores.profesores_nombres, profesores.Profesores_apellidos, aulas.Codigo_aula, niveles.NombreNivel, clases.Activo, turno.turno FROM clases, Departamento, profesores, aulas, niveles, turno WHERE Departamento.IDDepartamento=clases.IDDepartamento AND clases.idProfesores = profesores.idProfesores AND clases.idAulas =aulas.idAulas AND clases.IDNivel=niveles.idNivel AND turno.idTurno = clases.idTurno";
$ClasesID = mysql_query($query_ClasesID, $basededatos) or die(mysql_error());
$row_ClasesID = mysql_fetch_assoc($ClasesID);
$totalRows_ClasesID = mysql_num_rows($ClasesID);
$fecha= date('d_m_Y');
header ("content-type: application/vnd.ms-excel");
header ("content-Disposition: attachment; filename=Reporte_Clases_$fecha.xlsx");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Clases Registradas</title>

<link rel="stylesheet" href="../Css/Styles.css" type="text/css">
<link href="../Css/bootstrap.min.css" rel="stylesheet">
<script src="http://code.jquery.com/jquery-lastest.js"></script>
<script src="../header.js"></script>
<meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximun-scale=1.0, minimum-scale=1.0">
<link rel="icon" type="image/png" href="../Imagenes/mifavicon.png" />
</head>



<body>

<header>
<div class= "contenedor">
<div class="logo">
<center><img src="../Imagenes/sca-administrator_solo_por_illustracion.jpg" alt="" width="157" height="135"/></center>
</div >
	<nav>
<p>
	<a href="../paginas/Home.php">Inicio</a>
    <a href="ClaseRegistro.php">Registrar Clase</a>
 	<a href="Claseincomplet.php">Clases Incompletas</a>
    <a href="Buscar.php">Buscador</a>
<p>
	</nav>
</div>

</header>

 
 <div class="container">
 <div class="table-responsive">

<p>&nbsp;</p>
<table class="table" width="80%">
  <thead>
  <tr>
    <td>idClases</td>
    <td>Nombre de clase</td>
    <td>Departamento</td>
    <td>Nombre de Profesor </td>
    <td>Codigo_aula</td>
    <td>NombreNivel</td>
    <td>Activo</td>
    <td>turno</td>
  </tr>
  </thead>
  <?php do { ?>
  <tbody>  <tr>
      <td><?php echo $row_ClasesID['idClases']; ?></td>
      <td><?php echo $row_ClasesID['Nombre_clase']; ?></td>
      <td><?php echo $row_ClasesID['Nombre']; ?></td>
      <td><?php echo $row_ClasesID['profesores_nombres']; ?> <?php echo $row_ClasesID['Profesores_apellidos']; ?> </td>
      <td><?php echo $row_ClasesID['Codigo_aula']; ?></td>
      <td><?php echo $row_ClasesID['NombreNivel']; ?></td>
      <td><?php echo $row_ClasesID['Activo']; ?></td>
      <td><?php echo $row_ClasesID['turno']; ?></td>
    </tr>
    <?php } while ($row_ClasesID = mysql_fetch_assoc($ClasesID)); ?>
</tbody></table>

</div>
<div id="footer">
  <p> Copyright Softeasy unt&copy; - 2015</p>
  </div>
</div>
</body>
<p>&nbsp;</p>
<?php
mysql_free_result($ClasesID);
?>
