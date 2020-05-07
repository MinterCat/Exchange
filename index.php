<?php
declare(strict_types=1);
require_once('../../config/minterapi/vendor/autoload.php');
use Minter\MinterAPI;
use Minter\SDK\MinterTx;
use Minter\SDK\MinterCoins\MinterMultiSendTx;

function TransactionSend($api,$address,$private_key,$chainId,$gasCoin,$text,$tx_array)
{
	$api = new MinterAPI($api);
	if ($chainId == 1) 
		{
			$tx = new MinterTx([
				'nonce' => $api->getNonce($address),
				'chainId' => MinterTx::MAINNET_CHAIN_ID,
				'gasPrice' => 1,
				'gasCoin' => $gasCoin,
				'type' => MinterMultiSendTx::TYPE,
				'data' => [
					'list' => $tx_array
				],
				'payload' => $text,
				'serviceData' => '',
				'signatureType' => MinterTx::SIGNATURE_SINGLE_TYPE
			]);
		} 
	else 
		{
			$tx = new MinterTx([
				'nonce' => $api->getNonce($address),
				'chainId' => MinterTx::TESTNET_CHAIN_ID,
				'gasPrice' => 1,
				'gasCoin' => $gasCoin,
				'type' => MinterMultiSendTx::TYPE,
				'data' => [
					'list' => $tx_array
				],
				'payload' => $text,
				'serviceData' => '',
				'signatureType' => MinterTx::SIGNATURE_SINGLE_TYPE
			]);
		}
	$transaction = $tx->sign($private_key);
	return $api->send($transaction)->result;
}
//-----------------------
$base = $_SERVER['DOCUMENT_ROOT'] . '/explorer/session.txt';
include($_SERVER['DOCUMENT_ROOT'] . '/explorer/online.php');
//-----------------------
$session_language = $_SESSION['session_language'];
$version = explode('public_html', $_SERVER['DOCUMENT_ROOT'])[1];
if ($version == 'testnet') {require_once($_SERVER['DOCUMENT_ROOT'] . 'config/config.php');}
else {require_once(explode('public_html', $_SERVER['DOCUMENT_ROOT'])[0] . 'config/config.php');}
require_once($_SERVER['DOCUMENT_ROOT'] . '/function.php');

$cript_mnemonic = $_SESSION['cript_mnemonic'];
if ($cript_mnemonic != '') {
$decript_text = openssl_decrypt($cript_mnemonic, $crypt_method, $crypt_key, $crypt_options, $crypt_iv);
$decript = json_decode($decript_text);

$address = $decript->address;
$private_key = $decript->private_key;

$nick = User::Address($address)->nick;

$check_language = User::Address($address)->language;

if ($check_language != '') {$Language = Language($check_language);}
elseif ($session_language != '') {$Language = Language($session_language);} 
else {$Language = Language('English');}

$GIFTCAT = CoinBalance($address, 'GIFTCAT');
$MINTERCAT = CoinBalance($address, 'MINTERCAT');
//-------------------------------
$url = ((!empty($_SERVER['HTTPS'])) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
}else{header('Location: '.$site.'exit.php'); exit;}
echo "
<!DOCTYPE html>
<html lang='en'>
<head>
  <meta charset='utf-8'>
  <title>MinterCat | $nick</title>
  <link rel='shortcut icon' href='".$site."static/img/icons/Cats.webp'>
  <link rel='stylesheet' href='".$site."static/css/styles.min.css'>
  <link rel='stylesheet' href='".$site."static/css/style_header.css'>
  <link rel='stylesheet' href='".$site."static/css/style_menu.css'>
  <link rel='stylesheet' href='".$site."static/css/pagination.css'>
  <link rel='stylesheet' href='".$site."static/css/lk.css'>
  <link rel='stylesheet' href='".$site."static/css/normalize.css'>
  <link rel='stylesheet' href='".$site."static/css/dragndrop_main.css'>
  <link rel='stylesheet' href='".$site."static/css/dragndrop_scale.css'>
  <script src='".$site."static/js/dragndrop/ba3a0add07.js' crossorigin='anonymous'></script>
  <script src='".$site."static/js/dragndrop/jquery-3.4.1.min.js'></script>
  <script src='".$site."static/js/dragndrop/jquery-ui.min.js'></script>
  <script src='".$site."static/js/dragndrop/popper.min.js'></script>
  <script src='".$site."static/js/dragndrop/tippy-bundle.iife.min.js'></script>
  <script src='".$site."static/js/dragndrop/jquery.ui.touch-punch.min.js'></script>
  <link rel='stylesheet' href='".$site."static/css/slider_style.css'>
  <script src='".$site."static/js/slider_jquery-1.12.4.js'></script>
  <script src='".$site."static/js/slider_jquery-ui.js'></script>
  <script src='".$site."static/js/slider_jquery.ui.touch-punch.min.js'></script>
  <link rel='stylesheet' href='".$site."static/css/social.css'>
  <meta name='viewport' content='width=device-width, initial-scale=1'>
</head>
<body>
  <div class='cat_header'>
	<div class='header'>
		<div class='logo_float'>
			<div class='logo_cat'>
				<a href='#'>
					<div class='logo__img'></div>
					<span class='logo__text'><div class='logo_text'>Minter</div>
					<span class='logo__text-dark'><div class='logo_text_2'>Cat Exchange</div></span></span>
				</a>
			</div>
			<div class='head_menu'>
";
$m = 2; include('../menu.php');
echo "$menu
			</div>
		</div>
	</div>
<center>
<br>
<h3>MINTERCAT -> GIFTCAT</h3>
<blockquote>
GIFTCAT: $GIFTCAT <br>
MINTERCAT: $MINTERCAT <br>
</blockquote>
<br>
<br>
";
$butt = false;
echo "<form method='POST'>
<select size='auto' multiple name='int'>
<option disabled>minimum 10 GIFTCAT</option>";
if ($MINTERCAT > 10) {echo "<option value='10'>10</option>";}
if ($MINTERCAT > 50) {echo "<option value='50'>50</option>";}
if ($MINTERCAT > 100) {echo "<option value='100'>100</option>";}
if ($MINTERCAT > 500) {echo "<option value='500'>500</option>";}
if ($MINTERCAT > 1000) {echo "<option value='1000'>1000</option>";}
if ($MINTERCAT > 5000) {echo "<option value='5000'>5000</option>";}
echo "
</select>
<br><br>
<input id='Exchange' name='Exchange' type='submit' value='Exchange'>
</form>";
//-------------------------------
echo "</div><div class='cat_form'></div><br><br></center>";
//include('../footer.php');

if (isset($_POST['Exchange']))
	{
		$int = $_POST['int'];
		
		if ($MINTERCAT >= $int)
			{
				$tx_array = array(array(
					'coin' => 'MINTERCAT',
					'to' => 'Mx836a597ef7e869058ecbcc124fae29cd3e2b4444',
					'value' => $int
				));
				$api = 'https://api.minter.one/';
				$transaction = TransactionSend($api,$address,$private_key,$chainId = 1,$gasCoin = 'MINTERCAT',$text = '',$tx_array);
				$code = $transaction->code;
				if ($code == 0)
					{
						$tx_array = array(array(
							'coin' => 'GIFTCAT',
							'to' => $address,
							'value' => $int
						));
						$transaction = TransactionSend($api,'Mx836a597ef7e869058ecbcc124fae29cd3e2b4444',$privat_key_mintercat,$chainId = 1,$gasCoin = 'MINTERCAT',$text = '',$tx_array);
						header('Location: '.$site.'test');
						exit;
					}
				else
					{
						echo 'transaction error';
					}
			}
	}