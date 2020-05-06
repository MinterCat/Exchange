<?php
include('estimate.php');
include('whitelist.php');

$address = 'Mx836a597ef7e869058ecbcc124fae29cd3e2b4444';
$sumbip = 0;
$price = json_decode(file_get_contents('https://api.bip.dev/api/price'))->data->sell_price*0.0001;
echo '1 BIP = <a href="https://bip.dev/" target="_blank">$' . $price . '</a><br><br>';
$url = 'https://explorer-api.minter.network/api/v1/addresses/' . $address;
$data = file_get_contents($url);
$json = json_decode($data)->data->balances;
foreach ($json as $value => $coins) {
	$coin = $coins->coin;
	if (array_search($coin, $whitelist))
		{
			echo $coin . ' - ';
			echo $amount = number_format($coins->amount,6, '.', '');
			$estimate = estimate($coin);
			$sum = $estimate*$amount;
			echo ' | Estimate: ' . $estimate .' BIP | ' . $sum .' BIP<br>';
			$sumbip += $sum;
		}
	else
		{
			if ($coin == 'BIP')
				{
					echo $coin;
					echo ' - ';
					echo $amount = number_format($coins->amount,6, '.', '');
					echo '<br>';
					$sumbip += $amount;
				}
			else
				{
					echo '❗️';
					echo $coin;
					echo ' - ';
					echo $amount = number_format($coins->amount,6, '.', '');
					if ($coin == 'GIFTCAT') {$GIFTCAT = $amount;}
					echo '<br>';
				}
		}
}
echo "<br>Баланс BIP: $sumbip<br>Делегировано BIP: ";
$url = 'https://explorer-api.minter.network/api/v1/addresses/' . $address . '/delegations';
$data = file_get_contents($url);
echo $json = json_decode($data)->meta->additional->total_delegated_bip_value;
$sumbip += $json;
$dollar = $sumbip * $price;

echo '<br><br>';
$freefloat = pow(10,9) - $GIFTCAT;
echo "
GIFTCAT<br>
-----<br>
Эмиссия - ". pow(10,9)*2 . "<br>
Делегировано - ".pow(10,9)."<br>
В системе - $GIFTCAT<br>
Фрифлоат - $freefloat<br>
Резерв BIP - $sumbip<br>
Резерв в $ - $dollar<br>
<br>
";
$priceDollar = $dollar/((pow(10,9)*2)-pow(10,9)-$freefloat)*100*500;
echo "
Цена монеты в $ - ".number_format($priceDollar,6, '.', '')."<br>
Цена монеты в BIP - ".number_format($priceDollar/$price,6, '.', '')."<br>
";
