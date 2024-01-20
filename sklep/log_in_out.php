<?php
//WYLOGOWANIE
if (isset($_POST['logout']))
{
	session_unset();
	header('Location:index.php');
}

// POLACZENIE
$polaczenie = @new mysqli($servername, $username, $password, $dbname);

if ($polaczenie->connect_errno!=0)
{
	echo "ERROR: ".$polaczenie->connect_errno. "Opis: ".$polaczenie->connect_error;
}

// ZALOGOWAL SIE
else if (isset($_POST['login']) || isset($_POST['haslo']))
{ 
$login = $_POST['login'];
$haslo = $_POST['haslo'];
 
$login = htmlentities($login,ENT_QUOTES, "UTF-8");
$haslo = htmlentities($haslo,ENT_QUOTES, "UTF-8");

if ($rezultat = @$polaczenie->query(
sprintf("SELECT * FROM customers WHERE login='%s' AND password='%s'",
mysqli_real_escape_string($polaczenie,$login),
mysqli_real_escape_string($polaczenie,$haslo))));
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

		$_SESSION['login'] = $wiersz['login'];
		$_SESSION['password'] = $wiersz['password'];
		
		unset($_SESSION['blad']);
		$rezultat->close();

		if ($_SESSION['name']=="Superuser" && $_SESSION['login']=="admin" && $_SESSION['password']=="admin")
		{
			header('Location:management.php');
			exit();
		}
		else{
		header('Location:userAcc.php');
		exit();}
	}
	else
	{
		if ($_POST['login']=='' || $_POST['haslo']=='')
		{
			$_SESSION['blad'] = "Puste pola w formularzu logowania!";
			echo "<h2>".$_SESSION['blad']."</h2>";
			header('Refresh:2; url=index.php');
		}
		else
		{
			$_SESSION['blad'] = "Nie ma takiego uzytkownika!";
			echo "<h2>".$_SESSION['blad']."</h2>";
			header('Refresh:2; url=index.php');
		}

	}
}

$polaczenie->close();	
}
?>