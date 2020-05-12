<?php
include('estimate.php');
include('whitelist.php');

// $db_giftcat = new GIFTCAT();
class GIFTCAT extends SQLite3
{
    function __construct()
    {
        $this->open('giftcat.sqlite');
    }
}

$DOCUMENT_ROOT = $_SERVER['DOCUMENT_ROOT'];
require_once(explode('public_html', $DOCUMENT_ROOT)[0] . 'config/config.php');
require_once($DOCUMENT_ROOT . '/function.php');

$push='';
$address = 'Mx836a597ef7e869058ecbcc124fae29cd3e2b4444';
$sumbip = 0; $stabledollar = 0;

$price_json = json_decode(file_get_contents('https://api.coingecko.com/api/v3/coins/bip'));
$price = $price_json->market_data->current_price->usd;
$price_change_percentage_24h = '('. number_format($price_json->market_data->price_change_percentage_24h,1, '.', '') . '%)';

echo '<div style="margin-left: 10px; margin-top: 10px;"><p> 1 BIP = <a href="https://www.coingecko.com/en/coins/bip" target="_blank" style="text-decoration: none; color: black;">$' . $price . ' '. $price_change_percentage_24h .'</a>';
$url = 'https://explorer-api.minter.network/api/v1/addresses/' . $address;
$data = file_get_contents($url);
$json = json_decode($data)->data->balances;

$daily_json = file_get_contents('https://www.cbr-xml-daily.ru/daily_json.js');
$daily_json = json_decode($daily_json);
$ROUBLE = $daily_json->Valute->USD->Value;

echo '<br>$1 = ' . $ROUBLE . ' RUB</a></p></div>';

$url = 'https://explorer-api.minter.network/api/v1/addresses/' . $address . '/delegations';
$delegations = json_decode(file_get_contents($url))->data;

echo "
<meta charset='utf-8'>
<meta name='viewport' content='width=device-width, initial-scale=1.0'>
<meta http-equiv='X-UA-Compatible' content='ie=edge'>
<link rel='shortcut icon' href='".$site."static/img/icons/Cats.webp'>
<link rel='stylesheet' href='".$site."static/css/styles.min.css'>
<link rel='stylesheet' href='".$site."static/css/style_header.css'>
<link rel='stylesheet' href='".$site."static/css/style_menu.css'>
<link rel='stylesheet' href='".$site."static/css/pagination.css'>
<link rel='stylesheet' href='".$site."static/css/lk.css'>
<link rel='stylesheet' href='".$site."static/css/social.css'>
<script type='text/javascript' src='https://mintercat.com/static/js/jquery-3.4.1.min.js'></script>
<link rel='stylesheet' href='".$site."static/css/normalize.css'>
<div class='cat_content_none' float: left;'>
<div class='explorer_content' style='width: 98%; text-align: left; margin: 0;'>
<div class='explorer_block' style='width: 100%; float: none;'>
<div class='explorer_block_header'><center><a href='https://explorer.minter.network/address/Mx836a597ef7e869058ecbcc124fae29cd3e2b4444' target='_blank' style='text-decoration: none; color: black;'>PORTFOLIO</a></center></div>
<div class='explorer_block_content' style='overflow: auto;'>
";
$data = [];
foreach ($delegations as $value => $coins) {
$coin = $coins->coin;
$value = $coins->value;
$bip_value = $coins->bip_value;

if (array_key_exists($coin, $data)) {
        $data[$value] += $value;
    }
    else {
        $data[$coin] = $value;
    }

}

foreach ($json as $value => $coins) {
	$coin = $coins->coin;
	if (array_search($coin, $whitelist))
		{
			if ($coin == 'BIT')
				{
					echo '<div class="block">
							<span class="hover">⚛️</span>
							<span class="hidden">StableCoin</span>';
					echo $coin . ' - ';
					echo $amount = number_format($coins->amount,6, '.', '');
					echo ' | $'.$amount.'</div>';
					$stabledollar += $amount;
					$push .= "['".$coin."', ".$amount."],";
				}
			elseif ($coin == 'ROUBLE')
				{
					echo '<div class="block">
							<span class="hover">⚛️</span>
							<span class="hidden">StableCoin by Scryaga</span>';
					echo $coin . ' - ';
					echo $amount = number_format($coins->amount,6, '.', '');
					echo ' | $'. $amount / $ROUBLE .'</div>';
					$stabledollar += $amount / $ROUBLE;
					$push .= "['".$coin."', ".$amount / $ROUBLE."],";
				}
			elseif ($coin == 'BIGMAC')
				{
					echo '<div class="block">
							<span class="hover">⚛️</span>
							<span class="hidden">StableCoin by imho.group</span>';
					echo '<a href="https://imho.group" target="_blank" style="text-decoration: none; color: black;">' . $coin . '</a> - ';
					$amount = number_format($coins->amount,6, '.', '');
					
					$BIGMAC_price = json_decode(file_get_contents('https://imho.group/api/bigmac/price.json'))->price;
					$BIGMAC = $amount * $BIGMAC_price;
					echo ' 1 BIGMAC = $ '. $BIGMAC_price .' | $'. $BIGMAC .'</div>';
					$stabledollar += $BIGMAC;
					$push .= "['".$coin."', ".$BIGMAC."],";
				}
			else
				{
					echo '✅';
					echo $coin . ' - ';
					echo $amount = number_format($coins->amount,6, '.', '');
					$estimate = estimate($coin);
					$sum = $estimate*$amount;
					echo ' | Estimate: ' . $estimate .' BIP | ' . $sum .' BIP<br>';
					$sumbip += $sum;
					$push .= "['".$coin."', ".$sum * $price."],";
				}
		}
	else
		{
			if ($coin == 'BIP')
				{
					echo '✅';
					echo $coin;
					echo ' - ';
					echo $amount = number_format($coins->amount,6, '.', '');
					echo '<br>';
					$sumbip += $amount;
					$push .= "['".$coin."', ".$amount * $price."],";
				}
			else
				{
					echo '❌';
					echo $coin;
					echo ' - ';
					echo $amount = number_format($coins->amount,6, '.', '');
					if ($coin == 'GIFTCAT') {$GIFTCAT_amount = $amount;}
					echo '<br>';
				}
		}
}
$Uncertain = 0;
$url = 'https://explorer-api.minter.network/api/v1/addresses/' . $address . '/delegations';
$data = file_get_contents($url);
$json = json_decode($data)->meta->additional->total_delegated_bip_value;
$sumbalance = $sumbip;
$sumbip += $json;
$dollar = ($sumbip * $price) + $stabledollar;
$freefloat = pow(10,9) - $GIFTCAT_amount;
$priceDollar = $dollar/((pow(10,9)*2)-pow(10,9)-$freefloat)*100*500;
$GIFTCAT = number_format($priceDollar,6, '.', '');

$prce = '';
$db_giftcat = new GIFTCAT();
$result = $db_giftcat->query('SELECT * FROM (SELECT * FROM "table" ORDER BY id DESC LIMIT 100) t ORDER BY id');
while ($res = $result->fetchArray(1))
{$prce .= number_format($res['giftcat'],6, '.', '') . ', ';}
echo "
</div></div></div>
<div class='explorer_block' style='float: left;'>
<div class='explorer_block_header'><center>RESERVE</center></div>
<div class='explorer_block_content'>
<script src='https://www.google.com/jsapi'></script>
  <script>
   google.load('visualization', '1', {packages:['corechart']});
   google.setOnLoadCallback(drawChart);
   function drawChart() {
    var data = google.visualization.arrayToDataTable([
     ['COIN', 'Amount'],
     $push
	 ['Uncertain',    $Uncertain]
    ]);
    var options = {
     title: 'Coins',
     is3D: true,
     pieResidueSliceLabel: 'Uncertain'
    };
    var chart = new google.visualization.PieChart(document.getElementById('coins'));
     chart.draw(data, options);
   }
  </script>
  <div id='coins' style='width: 460px; height: 400px;'></div>
Баланс BIP: $sumbalance<br>Делегировано BIP: $json 
</div></div></div>
<div class='explorer_block' style='width: 350px; height: 300px; float: none;'>
<div class='explorer_block_header'><center>GIFTCAT</center></div>
<div class='explorer_block_content'>
Эмиссия - ". pow(10,9)*2 . "<br>
Делегировано - ".pow(10,9)."<br>
В системе - $GIFTCAT_amount<br>
Фрифлоат - $freefloat<br>
Резерв BIP - $sumbip<br>
Резерв в $ - $dollar<br>
<br>
Цена монеты в $ - $GIFTCAT<br>
Цена монеты в BIP - ".number_format($priceDollar/$price,6, '.', '')."<br>
</div></div></div>
<div class='explorer_block' style='width: 98%; float: none; height: auto;'>
<div class='explorer_block_header'><center>Dynamics of GIFTCAT price changes</center></div>
<div class='explorer_block_content' style='overflow: auto;'>
<script src='https://code.highcharts.com/highcharts.js'></script>
<script src='https://code.highcharts.com/modules/exporting.js'></script>
<script src='https://code.highcharts.com/modules/export-data.js'></script>
<script src='https://code.highcharts.com/modules/accessibility.js'></script>
<style>
#container {
  height: 450px;
}

.highcharts-data-table table {
	font-family: Verdana, sans-serif;
	border-collapse: collapse;
	border: 1px solid #EBEBEB;
	margin: 10px auto;
	text-align: center;
	width: 100%;
	max-width: 500px;
}
.highcharts-data-table caption {
  padding: 1em 0;
  font-size: 1.2em;
  color: #555;
}
.highcharts-data-table th {
	font-weight: 600;
  padding: 0.5em;
}
.highcharts-data-table td, .highcharts-data-table th, .highcharts-data-table caption {
  padding: 0.5em;
}
.highcharts-data-table thead tr, .highcharts-data-table tr:nth-child(even) {
  background: #f8f8f8;
}
.highcharts-data-table tr:hover {
  background: #f1f7ff;
}
</style>
<figure class='highcharts-figure'>
  <div id='container'></div>
</figure>
<script>
Highcharts.chart('container', {
  title: {
    text: 'Dynamics of GIFTCAT price changes'
  },

subtitle: {
    text: 'For the last 500 minutes'
  },

 

xAxis: {
    tickInterval: 1,
   
    accessibility: {
      rangeDescription: 'Range: 1 to 100'
    }
  },
  yAxis: {
    type: 'logarithmic',
    minorTickInterval: 0.1,
    accessibility: {
      rangeDescription: 'Range: 1 to 100'
    },

title: {
      text: 'Price dynamics'
    }

  },
  tooltip: {
    headerFormat: '<b>GIFTCAT</b><br>',

    pointFormat: '$ {point.y}'
  },
  series: [{
    name: 'GIFTCAT',
    data: [$prce],
    pointStart: 1
  }]
});
</script>
</div></div></div>
<div><div>
";