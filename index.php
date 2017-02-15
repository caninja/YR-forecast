<?PHP
$Betaling = "Værvarsel fra Yr levert av Meteorologisk institutt og NRK";
# !! YR vil at du henter XML filen kun 1 gang per 10min, gjøres via cron
# */10 * * * * wget http://www.yr.no -O /en/plass/yr.xml >/dev/null 2>&1
########################################################################
function yr($vare = '')
{
	# Laster inn XML fra URL.
	$url = "http://www.yr.no/sted/Norge/Oppland/Lillehammer/Lillehammer/varsel.xml" ; 
	$xml = simplexml_load_file($url) or die("en hel feil skjedde.");
  
	# XML analysering til variabler.
	$sted = $xml->location[0]->name;
	$dato = $xml->forecast->tabular->time[$vare]['from'];
	$periode = $xml->forecast->tabular->time[$vare]['period'];
	$symbol = $xml->forecast->tabular->time[$vare]->symbol['name'];
	$symbolverdi = $xml->forecast->tabular->time[$vare]->symbol['var'];
	$temperature = $xml->forecast->tabular->time[$vare]->temperature['value'];
	$windSpeedName = $xml->forecast->tabular->time[$vare]->windSpeed['name'];
	$windSpeedMps = $xml->forecast->tabular->time[$vare]->windSpeed['mps'];
	$windSpeedDirection = $xml->forecast->tabular->time[$vare]->windDirection['name'];
	$precipitation = $xml->forecast->tabular->time[$vare]->precipitation['value'];
	
	# Fikse på et felt for å hente ut en ren verdi til slutt.
	$dato = $dato2 = substr($dato, 0, 10);

	# Liten fiks for varierende tid.
	if ($periode == 0)/*_*/{$periode = "00:00 - 06:00";} 
	elseif ($periode == 1) {$periode = "06:00 - 12:00";}
	elseif ($periode == 2) {$periode = "12:00 - 18:00";} 
	/****************/else {$periode = "18:00 - 00:00";}

	# HTML
	echo "<div class=\"divTableRow\">";
	echo "<div class=\"divTableCell\"><b>$dato</b></div>";
	echo "<div class=\"divTableCell\"><b>$periode</b></div>";
	echo "<div class=\"divTableCell\"><img src=\"https://symbol.yr.no/grafikk/sym/b38/$symbolverdi.png\" /></div>"; //Bruker YR sine symboler.
	echo "<div class=\"divTableCell\">$precipitation mm nedbør</div>";
	echo "<div class=\"divTableCell\">$temperature °</div>";
	echo "<div class=\"divTableCell\">$windSpeedName fra $windSpeedDirection</div>";
	echo "<div class=\"divTableCell\">$windSpeedMps m/s</div>";
	echo "</div>";
}
########################################################################
?>

<html><head><title><?PHP echo $Betaling; ?></title>
<!-- --------------------------------------------------------------- -->
<link rel="stylesheet" type="text/css" href="css.css">
</head><body>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
<input type="text" name="post" /> Antall objekter.
<input type="submit" name="submit" value="Hent!" /></form>
<?PHP
#$obj = "7" //uten boks og knapp.
$obj = $_POST['post']; if ($obj == 0){$obj = "5";} // sett 5 til standard.
elseif ($obj > 36){$obj = "36";} // stopp på 36. ?> 
<h1><a href="http://www.yr.no/sted/Norge/Oppland/Lillehammer/Lillehammer/varsel.xml">Lillehammer</a></h1>
<div class="divTable" style="border: 1px solid #000;" >
<div class="divTableBody">
<div class="divTableRow">
<div class="divTableCell">Dato</div>
<div class="divTableCell">Tid</div>
<div class="divTableCell">Symbol</div>
<div class="divTableCell">nedbør</div>
<div class="divTableCell">Temp</div>
<div class="divTableCell">Vind</div>
<div class="divTableCell">vindstyrke</div>
</div>
<?PHP $tall = "$obj"; for ($i = 0 ; $i < $tall; $i++){ $func = 'yr'; $func($i); } ?>
<!-- Fredrik ----------------------------------------------- YR.no -->
</div></div></body></html>
