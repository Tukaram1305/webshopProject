
<!--LOGOWANIE-->
<?php
session_start();
require_once "connect.php";
require_once "log_in_out.php";
?>

 <!DOCTYPE HTML5>

<html>
<head>
<title>Hardware Store</title>
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
<div id="basketNote">
	<br><br>. . .
</div>

<div class="parent" id="top">
	<div id="h_bg"><img src="img/bgmain2.jpg"/></div>
	<div id="h_title">Paweł Hardware Store</div>
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
			Witaj $_SESSION[name] $_SESSION[surname] ($_SESSION[email]) <!-- ID: $_SESSION[id]-->
			<form action="index.php" method="post">
				<input type="hidden" value="1" name="logout"/>
				<button class="aInline aAccent" type="submit" value="Wyloguj">Wyloguj<i class="fa fa-remove" style="color:red;"></i></button>
			</form>
			<a class="aInline aAccent" href="userAcc.php">
				<div id="uAcc">Moje konto<i class="fa fa-address-card" style="font-size:14px"></i></div>
			</a>
			<a class="aInline" href="basket.php">
				<div id="basket">
					<i class="fa fa-shopping-basket" style="font-size:24px; color:white;"></i>
					<span id="basket_items_bg"></span>
					<div id="hoverbasket"></div>
				</div>
			</a>
		</span>
		END;
		}
		else
		{
		// NIEZALOGOWANY
		echo<<<END
		<span class="barSpanElem">
			<form action="index.php" method="post">
			<span> <i class="fa fa-drivers-license-o" style="font-size:24px;color:white;margin-right:5px;"></i> Login: <input class="aInline aAccent" type="text" name="login"></span>
			<span>  Hasło: <input class="aInline aAccent" type="password" name="haslo"></span>
			<button class="aInline aAccent" type="submit"><i class="fa fa-check-square-o" style="font-size:14px"></i>Zaloguj</button>
			<span class="inpInline"><a href="register.php"><button class="aInline aAccent" type="button"><i class="fa fa-edit" style="font-size:14px"></i>Rejestracja</button></a></span>
			</form>
		</span>
		END;
		}
			?>
		</span>
	</div>

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
require_once "connect.php";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
   die("Connection failed: " . $conn->connect_error);
}
else {
	// Zbieranie produktow wg kryterium [1- mcu , 3- moduly, 2- elektronika]
	$prdCat = 1;
	if (isset($_POST['prdType'])) { $prdCat=$_POST['prdType']; }

    $question = sprintf("SELECT products.*, product_type.type FROM products,product_type WHERE products.type=product_type.id AND products.type=%d",$prdCat);
	$result = mysqli_query($conn,$question);
    $num = $result->num_rows;
    for ($i=0; $i<$num; $i++)
        {
            $row = mysqli_fetch_assoc($result);
			echo<<<END
			<div class="roomContainer">
			<table class="myTable"  align="center" border="1" bordercolor="#d5d5d5"  cellpadding="4" cellspacing="0">
			<!-- ROW 1 -->
			<tr>
			<td  width="200" align="center"">$row[name]</td>
			<td  class="scalableImgBig" align="center"> <img  class="scalableImgBig" src="$row[image]"> </td>
			<td  align="center">$row[description]</td>
			END;
			
			if (isset($_SESSION['zalogowany']) && $_SESSION['zalogowany']==true)
			{
				echo<<<END
				<td align="center" width="120px">
					<input onclick="addToBasket($_SESSION[id], $row[id]); delayRefresh();" class="grapIcon" type="image" id="bas" alt="BAS"src="icons/basket.png">
					<div>Do koszyka</div>
				</td>
				<td align="center" width="120px">
					<form action="placeOrder.php" method="POST" style="display:block;">
					<input class="grapIcon" value="$row[id]" type="image" id="buy" alt="BUY"src="icons/buy.png"><br>
					<input type="hidden" name="itemNum" id="hidden_$row[id]" value="1">
					<input type="hidden" name="itemID" value=$row[id]>
					<div>Zamów</div>
					</form>
				</td>
				</tr>
				END;
			}
			else
			{
				// NIEZALOGOWANY NIE MOZE DODAWAC I KUPOWAC
			}
			echo<<<END
			<!-- ROW 2 -->
			<tr>
				<th>Nr katalogowy</th>
				<th>Dostepność</th>
				<th>Cena</th>
			END;
			if (isset($_SESSION['zalogowany']) && $_SESSION['zalogowany']==true)
			{
				echo '<th colspan="2">Ilość</th>';
			}
			echo<<<END
			</tr>
			<!-- ROW 3 -->
			<tr>
			<td> #$row[id] </td>
			<td align="center"><font color="#2f5701"><strong>$row[stock]</strong></font> sztuk</td>
			<td align="center" class="priceTag">$row[price] zł
			<i class="fa fa-tags" style="font-size:28px"></i>
			</td>
			END;
			if (isset($_SESSION['zalogowany']) && $_SESSION['zalogowany']==true)
			{
				echo<<<END
				<td colspan="2">
				<input onchange="incItem($row[id])" class="inpNum1" id="$row[id]" type="number" min="1" max="$row[stock]" step="1" value="1">
				</td>
				END;
			}
			echo<<<END
			</tr>
			</table>
			</div>
			END;
        }
}

?>
</div> <!--content-->

<script>

document.getElementById("basket").addEventListener("mouseover",function(){showPopupBasket()})
document.getElementById("basket").addEventListener("mouseout",function(){hidePopupBasket()})
basketPopup = document.getElementById("hoverbasket")

// chwile poczekaj az HTTP REQ zwroci zapytanie
function delayRefresh()
{
	setTimeout(function(){
	updateBasketSetResult(<?php echo $_SESSION['id']; ?>)
}, 100);
}

</script>

<?php 
if (isset($_SESSION['zalogowany']) && $_SESSION['zalogowany']==true)
{
	echo '<script>';
	echo 'var uid='.$_SESSION['id'].';';
	echo 'updateBasketItemsNum(uid);';
	echo 'updateBasketSetResult('.$_SESSION['id'].')';
	echo '</script>';
	
}
?>

<footer>
	<?php echo "Markowiak &copy ".date("Y-m-d");?>
</footer>
</body>

</html>

