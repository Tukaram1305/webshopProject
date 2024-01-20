<!--LOGOWANIE-->
<?php
session_start();
require_once "connect.php";
// zabezpieczenie
if (!isset($_SESSION['zalogowany']) && $_SESSION['zalogowany']!=true)
{
	header('Location:index.php');
	exit();
}

//WYLOGOWANIE
if (isset($_POST['logout']))
{
	session_unset();
	unset($_POST['logout']);
	header('Location:index.php');
}

?>

 <!DOCTYPE HTML5>

<html>
<head>
<title>P & M Hardware Store</title>
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
	<div id="h_title">Paweł & Mateusz Hardware Store</div>
</div>

</header>

<div id="INFOBAR_COL">
	<div class="INFOBAR">
		<span>
			<?php
		if (isset($_SESSION['zalogowany']) && $_SESSION['zalogowany']==true)
		{
			// ZALOGOWANY
			echo<<<END
			<span class="barSpanElem">
			<i class="fa fa-key" style="font-size:18px;color:white"></i>
				Witaj $_SESSION[name] $_SESSION[surname], email: $_SESSION[email], Karta zamówienia:
			</span>
			END;
		}
			?>
		</span>
	</div>


</div>

<div id="content">

<?php
require_once "connect.php";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
   die("Connection failed: " . $conn->connect_error);
}
else {
	// Zloz i zapisz bezposrednie zamowienie
	if (isset($_POST['ITEMID']))
	{
		// DODAJ ZAMOWIENIE ~poprawic
		$post = $_POST['post'];
		$shipCost = 0;
		$shipMeth = "...";
		if ($post=='1') {$shipCost = 8.99; $shipMeth="Paczkomaty 24h";}
		if ($post=='2') {$shipCost = 12.45; $shipMeth="Kurier";}
		if ($post=='3') {$shipCost = 18.91; $shipMeth="Poczta - paczka polecona";}
	
		$payMeth = $_POST['pay'];
		$price = $_POST['sumPrice'];

		$sql = sprintf("INSERT INTO orders VALUES (NULL,%d,NOW(),%.2F,'%s','%s',%d,'%s')",$_SESSION['id'],(float)$price+$shipCost,$payMeth,$shipMeth, time(),'Opłacone');
		mysqli_query($conn,$sql);
		
		// ostatnie zamowienie (id)
		$sql = "SELECT id FROM orders ORDER BY id DESC LIMIT 1";
		$res = mysqli_query($conn,$sql);
		$row = mysqli_fetch_assoc($res);
		$highest = $row["id"];
	
		$question = sprintf("INSERT INTO items_per_order VALUES(%d,%d,%d)",$highest,$_POST['ITEMID'],$_POST['ITEMQUANT']);
		if (!mysqli_query($conn,$question)) {echo "Coś poszło nie tak podczas przypisywania przedmiotów!";}
		// aktualizuj stock
		$question = sprintf("UPDATE products SET products.stock=products.stock-%d WHERE products.id=%d",$_POST['ITEMQUANT'],$_POST['ITEMID']);
		if (!mysqli_query($conn,$question)) {echo "Coś poszło nie tak podczas aktualizowania stanu magazynowego!";}
	}
	else{
		// Zloz i zapisz zamowienie z koszyka
	// DODAJ ZAMOWIENIE ~poprawic
	$post = $_POST['post'];
	$shipCost = 0;
	$shipMeth = "...";
	if ($post=='1') {$shipCost = 8.99; $shipMeth="Paczkomaty 24h";}
	if ($post=='2') {$shipCost = 12.45; $shipMeth="Kurier";}
	if ($post=='3') {$shipCost = 18.91; $shipMeth="Poczta - paczka polecona";}

	$payMeth = $_POST['pay'];
	$price = $_POST['sumPrice'];

	$sql = sprintf("INSERT INTO orders VALUES (NULL,%d,NOW(),%.2F,'%s','%s',%d,'%s')",$_SESSION['id'],(float)$price+$shipCost,$payMeth,$shipMeth,time(),'Opłacone');
	mysqli_query($conn,$sql);

	// ostatnie zamowienie (id)
	$sql = "SELECT id FROM orders ORDER BY id DESC LIMIT 1";
	$res = mysqli_query($conn,$sql);
	$row = mysqli_fetch_assoc($res);
	$highest = intval($row["id"]);

	// ZBIERZ PRZEDMIOTY
	$str = <<<EOD
	SELECT customers.id AS cus_id,products.* 
	,basket.*
	,ROUND(SUM(basket.quantity*products.price),2) AS Suma
	,(SELECT ROUND(SUM(basket.quantity*products.price),2) 
	FROM  basket 
	JOIN customers 
	ON (customers.id=basket.customer_id) 
	JOIN products 
	ON (products.id=basket.product_id) 
	WHERE customers.id=%d) as Overall 
	FROM basket 
	JOIN customers 
	ON (customers.id=basket.customer_id) 
	JOIN products 
	ON (products.id=basket.product_id) 
	WHERE customers.id=%d 
	GROUP BY products.name 
	ORDER BY products.name DESC
	EOD;
    $question = sprintf($str,$_SESSION['id'],$_SESSION['id']);
	$result = mysqli_query($conn,$question);
    $num = $result->num_rows;

	// przypisz itemy do zamowienia i odejmij od stock
	if ($num>0){
    for ($i=0; $i<$num; $i++)
        {
			// przypisz przedmioty
            $row = mysqli_fetch_assoc($result);
			$question = sprintf("INSERT INTO items_per_order VALUES(%d,%d,%d)",$highest,$row['id'],$row['quantity']);
			mysqli_query($conn,$question);
			// aktualizuj stock
			$question = sprintf("UPDATE products SET products.stock=products.stock-%d WHERE products.id=%d",$row['quantity'],$row['id']);
			mysqli_query($conn,$question);
        }
	} // num > 0
	else
	{
		echo "<h2>Coś poszło nie tak!</h2>";
	}

	//Wyczysc koszyk (mode=dellAll)
	require_once "basketHandle.php";
	}
}
echo<<<END
<div class="roomContainer">
<h3>
$_SESSION[name] $_SESSION[surname],<br>
Twoje zamówienie zostało przyjete do realizacji!<br>
Szczegóły możesz zobaczyć na karcie swojego profilu.<br><br>
Dostawa: $shipMeth, cena: $price zł cena dostawy: $shipCost zł<br>
Za chwilę nastąpi przekierowanie na stronę sklepu...<br>
<div id="reCounter">---</div>
</h3>
</div>
END;

header("refresh:3,url='index.php'");
?>
<script>
var counter=document.getElementById("reCounter")
counter.innerHTML = "3"
var ncount = 3
setInterval(function(){
	ncount-=1
counter.innerHTML = ncount
},1000)

</script>

</div> <!--content-->

<footer>
	<?php echo "Markowiak & Sołtysiak &copy ".date("Y-m-d");?>
</footer>
</body>

</html>

