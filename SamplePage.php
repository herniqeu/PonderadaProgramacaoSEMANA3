<?php 
include "../inc/dbinfo.inc";

/* Connect to MySQL and select the database. */
$connection = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD);
if (mysqli_connect_errno()) echo "Failed to connect to MySQL: " . mysqli_connect_error();
$database = mysqli_select_db($connection, DB_DATABASE);

/* Ensure that the EMPLOYEES and PRODUCTS tables exist. */
VerifyEmployeesTable($connection, DB_DATABASE);
VerifyProductsTable($connection, DB_DATABASE);

/* If input fields for employees are populated, add a row to the EMPLOYEES table. */
$employee_name = htmlentities($_POST['EMPLOYEE_NAME']);
$employee_address = htmlentities($_POST['EMPLOYEE_ADDRESS']);

if (strlen($employee_name) || strlen($employee_address)) {
    AddEmployee($connection, $employee_name, $employee_address);
}

/* If input fields for products are populated, add a row to the PRODUCTS table. */
$plantao_name = htmlentities($_POST['PLANTAO']);
$plantao_descricao = htmlentities($_POST['DESCRICAO']);
$plantao_valor = floatval($_POST['VALOR']);
$plantao_duracao = intval($_POST['DURACAO']);

if (!empty($plantao_name) || !empty($plantao_descricao) || !empty($plantao_valor) || !empty($plantao_duracao)) {
    AdicionarPlantao($connection, $plantao_name, $plantao_descricao, $plantao_valor, $plantao_duracao);
}

?>

<html>
<body>
<h1>Sample page</h1>

<!-- Input form for employees -->
<form action="<?PHP echo $_SERVER['SCRIPT_NAME'] ?>" method="POST">
  <h2>Add Employee</h2>
  <table>
    <tr>
      <td>Name</td>
      <td>Address</td>
    </tr>
    <tr>
      <td>
        <input type="text" name="EMPLOYEE_NAME" maxlength="45" size="30" />
      </td>
      <td>
        <input type="text" name="EMPLOYEE_ADDRESS" maxlength="90" size="60" />
      </td>
      <td>
        <input type="submit" value="Add Employee" />
      </td>
    </tr>
  </table>
</form>

<!-- Input form for products -->
<form action="<?PHP echo $_SERVER['SCRIPT_NAME'] ?>" method="POST">
  <h2>Adicionar Plantao</h2>
  <table>
    <tr>
      <td>Plantao</td>
      <td>Descricao</td>
      <td>Valor</td>
      <td>Duracao</td>
    </tr>
    <tr>
      <td>
        <input type="text" name="PLANTAO" maxlength="50" size="30" />
      </td>
      <td>
        <input type="text" name="DESCRICAO" maxlength="255" size="60" />
      </td>
      <td>
        <input type="text" name="VALOR" maxlength="10" size="10" />
      </td>
      <td>
        <input type="text" name="DURACAO" maxlength="10" size="10" />
      </td>
      <td>
        <input type="submit" value="Add Product" />
      </td>
    </tr>
  </table>
</form>

<!-- Display table data for employees -->
<h2>Employees</h2>
<table cellpadding="2" cellspacing="2">
  <tr>
    <td>ID</td>
    <td>Name</td>
    <td>Address</td>
  </tr>

<?php
$result = mysqli_query($connection, "SELECT * FROM EMPLOYEES");

while($query_data = mysqli_fetch_row($result)) {
  echo "<tr>";
  echo "<td>",$query_data[0], "</td>",
       "<td>",$query_data[1], "</td>",
       "<td>",$query_data[2], "</td>";
  echo "</tr>";
}
?>
</table>

<!-- Display table data for products -->
<h2>Products</h2>
<table cellpadding="2" cellspacing="2">
  <tr>
    <td>id</td>
    <td>Nome</td>
    <td>Descrição</td>
    <td>Preço</td>
    <td>Duração</td>
  </tr>

<?php
$result = mysqli_query($connection, "SELECT * FROM PRODUCTS");

while($query_data = mysqli_fetch_row($result)) {
  echo "<tr>";
  echo "<td>",$query_data[0], "</td>",
       "<td>",$query_data[1], "</td>",
       "<td>",$query_data[2], "</td>",
       "<td>",$query_data[3], "</td>",
       "<td>",$query_data[4], "</td>";
  echo "</tr>";
}
?>
</table>

<!-- Clean up. -->
<?php
mysqli_free_result($result);
mysqli_close($connection);
?>

</body>
</html>

<?php

/* Add an employee to the table. */
function AddEmployee($connection, $name, $address) {
   $n = mysqli_real_escape_string($connection, $name);
   $a = mysqli_real_escape_string($connection, $address);

   $query = "INSERT INTO EMPLOYEES (NAME, ADDRESS) VALUES ('$n', '$a');";

   if(!mysqli_query($connection, $query)) echo("<p>Error adding employee data.</p>");
}

/* Add a product to the table. */
function AdicionarPlantao($connection, $name, $description, $price, $stock) {
   $n = mysqli_real_escape_string($connection, $name);
   $d = mysqli_real_escape_string($connection, $description);
   $p = floatval($price);
   $s = intval($stock);

   $query = "INSERT INTO PRODUCTS (NAME, DESCRIPTION, PRICE, STOCK) VALUES ('$n', '$d', '$p', '$s');";

   if(!mysqli_query($connection, $query)) echo("<p>Error adding product data.</p>");
}

/* Check whether the table exists and, if not, create it. */
function VerifyEmployeesTable($connection, $dbName) {
  if(!TableExists("EMPLOYEES", $connection, $dbName))
  {
     $query = "CREATE TABLE EMPLOYEES (
         ID int(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
         NAME VARCHAR(45),
         ADDRESS VARCHAR(90)
       )";

     if(!mysqli_query($connection, $query)) echo("<p>Error creating EMPLOYEES table.</p>");
  }
}

/* Check whether the table exists and, if not, create it. */
function VerifyProductsTable($connection, $dbName) {
  if(!TableExists("PRODUCTS", $connection, $dbName))
  {
     $query = "CREATE TABLE PRODUCTS (
         ID int(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
         NAME VARCHAR(50),
         DESCRIPTION TEXT,
         PRICE DECIMAL(10,2),
         STOCK INT
       )";

     if(!mysqli_query($connection, $query)) echo("<p>Error creating PRODUCTS table.</p>");
  }
}

/* Check for the existence of a table. */
function TableExists($tableName, $connection, $dbName) {
  $t = mysqli_real_escape_string($connection, $tableName);
  $d = mysqli_real_escape_string($connection, $dbName);

  $checktable = mysqli_query($connection,
      "SELECT TABLE_NAME FROM information_schema.TABLES WHERE TABLE_NAME = '$t' AND TABLE_SCHEMA = '$d'");

  if(mysqli_num_rows($checktable) > 0) return true;

  return false;
}
?>