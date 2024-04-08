<!--REJESTRACJA-->
<?php
require_once "connect.php";
session_start();
$_SESSION['blad']="ok";
if (isset($_POST['name']))
{
	$user_name = $_POST['name'];
	$user_surname = $_POST['surname'];
	$user_login = $_POST['login'];
	$user_pass = $_POST['pass'];
	$user_gender = (int)$_POST['gender'];
	$user_phone = (int)$_POST['phone'];
	$user_email = $_POST['email'];
	$user_city = $_POST['city'];
	$user_zip = $_POST['zip'];
	$user_street = $_POST['street'];
	$user_house_no = $_POST['house_no'];

	$conn = new mysqli($servername, $username, $password, $dbname);
	if ($conn->connect_error) {
   	die("Connection failed: " . $conn->connect_error);}
	else{
		$sql = sprintf("SELECT * FROM customers WHERE customers.login='%s'",$user_login);
		$res = mysqli_query($conn,$sql);
		$num_logins = $res->num_rows;
		// User o tym loginie juz istnieje
		if ($num_logins>0)
			{
				$_SESSION['blad']="log";
				header("refresh:3,url=register.php");
			}
		// zaloz nowe konto
		else
			{
				// DODAJ ADRES I ZNAJDZ ID
				$sql = sprintf("INSERT INTO addr_city VALUES (NULL,'%s','%s','%s','%s')",$user_city,$user_street,$user_house_no,$user_zip);
				if (!mysqli_query($conn,$sql))
				{
					echo "<h1>COŚ POSZŁO NIE TAK!</h1>";
				}
				$sql = sprintf("SELECT * FROM addr_city WHERE city='%s' AND street='%s' AND house_no='%s'",$user_city,$user_street,$user_house_no);
				$res = mysqli_query($conn,$sql);
				$row = mysqli_fetch_assoc($res);
				$curr_ci_id = (int)$row['id'];

				// DODAJ UZYTKOWNIKA, przypisz plec i miasto
				$sql = sprintf("INSERT INTO customers VALUES (NULL,'%s','%s','%s','%s',%d,%d,'%s',%d)",$user_name,$user_surname,$user_login,$user_pass,$user_gender,$user_phone,$user_email,$curr_ci_id);
				if (!mysqli_query($conn,$sql))
				{
					echo "<h1>COŚ POSZŁO NIE PODCZAS WPROWADZANIA UŻYTKOWNIKA!</h1>";
				}
				// Resejtracja dokonana
				// Zaloguj i przejdz do konta
				$login = $user_login;
				$haslo = $user_pass;
				
				$login = htmlentities($login,ENT_QUOTES, "UTF-8");
				$haslo = htmlentities($haslo,ENT_QUOTES, "UTF-8");

				if ($rezultat = @$conn->query(
				sprintf("SELECT * FROM customers WHERE login='%s' AND password='%s'",
				mysqli_real_escape_string($conn,$login),
				mysqli_real_escape_string($conn,$haslo))));
				{
					$ilu_userow = $rezultat->num_rows;
					if ($ilu_userow>0)
					{
						$_SESSION['zalogowany'] = true;
						$wiersz = $rezultat->fetch_assoc();
						$_SESSION['id'] = $wiersz['id'];
						$_SESSION['name'] = $wiersz['name'];
						$_SESSION['surname'] = $wiersz['surname'];
						$_SESSION['email'] = $wiersz['email'];
						
						unset($_SESSION['blad']);
						$rezultat->close();

						header('Location:userAcc.php');
						exit();
					}
				}
}}}

?>

 <!DOCTYPE HTML5>

<html>
<head>
<title>Moje konto</title>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1">

<link rel="stylesheet" type="text/css" href="styl.css?ts=<?=time()?>" />
<link rel="stylesheet" type="text/css" href="controls.css?ts=<?=time()?>" />
<script src="script.js?ts=<?=time()?>">	</script>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Red+Hat+Mono:wght@300&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">


<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

</head>

<body>
<header>
<div class="parent" id="top">
	<div id="h_bg"><img src="img/bgmain2.jpg"/></div>
	<div id="h_title">Paweł Hardware Store</div>
</div>

</header>

<!-- BELKA GLOWNEGO INPUTU -->
<div id="INFOBAR_COL">
	<div class="INFOBAR">
	<span class="barSpanElem">
			<form action="index.php" method="post">
			<span> <i class="fa fa-drivers-license-o" style="font-size:24px;color:white;margin-right:5px;"></i> Login: <input class="aInline aAccent" type="text" name="login"></span>
			<span>  Hasło: <input class="aInline aAccent" type="password" name="haslo"></span>
			<button class="aInline aAccent" type="submit"><i class="fa fa-check-square-o" style="font-size:14px"></i>Zaloguj</button>
			<span class="inpInline"><a href="register.php"><button class="aInline aAccent" type="button"><i class="fa fa-edit" style="font-size:14px"></i>Rejestracja</button></a></span>
			</form>
		</span>
	</div>

<!-- BELKA PRODUKTOW -->
	<div class="INFOBAR" >
		<span class="barSpanElem">Produkty</span>
	<span class="barSpanElem">
				<form action="index.php" method="POST">
					<button class="mainMenu" name="prdType" type="submit" value="1">Mikrokontrolery</button>
				</form>
	</span>
	<span class="barSpanElem">
				<form action="index.php" method="POST">
					<button class="mainMenu" name="prdType" type="submit" value="3">Moduły</button>
				</form>
	</span>
	<span class="barSpanElem">
				<form action="index.php" method="POST">
					<button class="mainMenu" name="prdType" type="submit" value="2">Elektronika</button>
				</form>
	</span>
	</div>
</div> <!--INFOBAR GORNY-->

<div id="content">

<?php
if ($_SESSION['blad']=="ok")
{
require_once "connect.php";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
   die("Connection failed: " . $conn->connect_error);
}
else {
	print '<div class="roomContainer">';
	print '<table class="myTable_reg"  align="center" border="1" bordercolor="#d5d5d5"  cellpadding="4" cellspacing="0">';
	echo<<<END
	<form action="register.php" method="POST">
	<tr>
	<th colspan="2" style="text-align: left;">Dane osobowe</th>
	</tr>
	<tr>
	<td>Imię</td>
	<th><i class="fa fa-user icon"></i><input type="text" name="name" required></th>
	</tr>
	<tr>
	<td>Nazwisko</td>
	<th><i class="fa fa-indent"></i><input type="text" name="surname" required></th>
	</tr>
	<tr>
	<td>Login</td>
	<th><i class="fa fa-check-square-o"><input type="text" name="login" required></th>
	</tr>
	<tr>
	<td>Hasło</td>
	<th><i class="fa fa-key icon"></i><input type="password" name="pass" required></th>
	</tr>
	<tr>
	<td>Płeć</td>
	<th>
	<input type="radio" id="gender1" name="gender" required checked="1" value="1">
	<label for="gender1">Mężczyzna</label><br>
	<input type="radio" id="gender2" name="gender" required value="2">
	<label for="gender2">Kobieta</label><br>
	</th>
	</tr>
	<tr>
	<th colspan="2" style="text-align: left;">Dane kontaktowe</th>
	</tr>
	<tr>
	<td>Numer telefonu</td>
	<th><i class="fa fa-home"><input type="tel" name="phone" required></th>
	</tr>
	<tr>
	<td>Adres email</td>
	<th><i class="fa fa-envelope icon"></i><input type="email" name="email" required></th>
	</tr>
	<tr>
	<td>Miasto</td>
	<th><i class="fa fa-home"></i><input type="text" name="city" required></th>
	</tr>
	<tr>
	<td>Kod pocztowy</td>
	<th><i class="fa fa-home"></i><input type="text" name="zip" required></th>
	</tr>
	<tr>
	<td>Ulica</td>
	<th><i class="fa fa-home"></i><input type="text" name="street" required></th>
	</tr>
	<tr>
	<td>Nr domu/mieszkania</td>
	<th><i class="fa fa-home"></i><input type="text" name="house_no" required></th>
	</tr>
	<tr>
	<th colspan="2" style="text-align: center;"><i class="fa fa-user-plus"><input type="submit" value="Zarejestruj"></th>
	</tr>
	</form>
	</div>
	END;
}
}
else if ($_SESSION['blad']=="log")
{
	echo "<h1 style='color:white; text-align:center;'>Użytkownik o tym loginie juz istnieje!</h1><br>";
}
?>
</div> <!--content-->

<footer>
	<?php echo "Markowiak &copy ".date("Y-m-d");?>
</footer>
</body>

</html>

