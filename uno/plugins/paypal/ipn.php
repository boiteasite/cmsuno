 <?php 
// http://www.johnboy.com/blog/http-11-paypal-ipn-example-php-code
// http://www.lafermeduweb.net/billet/tutorial-integrer-paypal-a-son-site-web-en-php-partie-2-276.html#ipn
// https://developer.paypal.com/webapps/developer/docs/classic/ipn/ht_ipn/
// ex : 
// mc_gross=19.95&protection_eligibility=Eligible&address_status=confirmed&payer_id=LPLWNMTBWMFAY&tax=0.00&address_street=1+Main+St&payment_date=20%3A12%3A59+Jan+13%2C+2009+PST&payment_status=Completed&charset=windows-1252&address_zip=95131&first_name=Test&mc_fee=0.88&address_country_code=US&address_name=Test+User&notify_version=2.6&custom=&payer_status=verified&address_country=United+States&address_city=San+Jose&quantity=1&verify_sign=AtkOfCXbDm2hu0ZELryHFjY-Vb7PAUvS6nMXgysbElEn9v-1XcmSoGtf&payer_email=gpmac_1231902590_per%40paypal.com&txn_id=61E67681CH3238416&payment_type=instant&last_name=User&address_state=CA&receiver_email=gpmac_1231902686_biz%40paypal.com&payment_fee=0.88&receiver_id=S8XGHLYDW9T3S&txn_type=express_checkout&item_name=&mc_currency=USD&item_number=&residence_country=US&test_ipn=1&handling_amount=0.00&transaction_subject=&payment_gross=19.95&shipping=0.00
//
$q = file_get_contents(dirname(__FILE__).'/../../data/paypal.json');
$a = json_decode($q,true);
if($a)
	{
	$hostPaypal = (($a['mod']=='test')?'www.sandbox.paypal.com':'www.paypal.com'); // test / prod
	$urlPaypal = (($a['mod']=='test')?'https://www.sandbox.paypal.com':'https://www.paypal.com'); // test / prod
	$req = 'cmd=_notify-validate'; // read the post from PayPal system and add 'cmd'
	$kv=array("time" => time(), "treated" => 0);
	foreach($_POST as $k=>$v)
		{
		$kv[$k] = $v;
		$v = urlencode(stripslashes($v));
		$req .= "&$k=$v";
		}
	$ipn = json_encode($kv);
	//post back to PayPal system to validate
	$header = "POST /cgi-bin/webscr HTTP/1.1\r\n";
	$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
	$header .= "Content-Length: " . strlen($req) . "\r\n";
	$header .= "Host: ".$hostPaypal."\r\n"; // sans http - sans /cgi-bin/webscr
	$header .= "Connection: close\r\n\r\n";
	if(function_exists('openssl_open')) {$fp=fsockopen('ssl://'.$hostPaypal,443,$errno,$errstr,30); $type="_SSL";}
	else {$fp=fsockopen($hostPaypal,80,$errno,$errstr,30); $type="_HTTP";}
	if(!$fp)
		{ //error connecting to paypal
		file_put_contents(dirname(__FILE__).'/../../data/sdata/paypal/tmp/errorConnecting'.$_POST['txn_id'].'.json', $ipn);
		}
	else
		{ //successful connection
		$written=fwrite($fp,$header.$req);
		if ($written)
			{
			$res=stream_get_contents($fp);
			fclose($fp);
			if(strpos($res, "VERIFIED")!==false)
				{ //insert order into database
				if($_POST['payment_status']=="Completed")
					{
					// vérifier que txn_id n'a pas été précédemment traité
					if(VerifIXNID($_POST['txn_id'])==0)
						{ // vérifier que receiver_email est votre adresse email PayPal principale
						if($a['mail']==$_POST['receiver_email'])
							{ // OK
							file_put_contents(dirname(__FILE__).'/../../data/sdata/paypal/'.$_POST['txn_id'].'.json', $ipn); // OK
							}
						else
							{ // Mauvaise adresse email paypal
							file_put_contents(dirname(__FILE__).'/../../data/sdata/paypal/tmp/errorMailPaypal'.$_POST['txn_id'].'.json', $ipn);
							}
						}
					else
						{ // ID de transaction déjà utilisé
						file_put_contents(dirname(__FILE__).'/../../data/sdata/paypal/tmp/errorRepetition'.$_POST['txn_id'].'.json', $ipn);
						}
					}
				else
					{ // Statut de paiement: Echec
					file_put_contents(dirname(__FILE__).'/../../data/sdata/paypal/tmp/errorNotCompleted'.$_POST['txn_id'].'.json', $ipn);
					}
				}
			else if(strpos($res, "INVALID")!==false)
				{ //insert into DB in a table for bad payments for you to process later
				file_put_contents(dirname(__FILE__).'/../../data/sdata/paypal/tmp/errorINVALID'.$_POST['txn_id'].'.json', $ipn);
				}
			}
		}
	}
//
function VerifIXNID($txn_id)
	{ // fonction pour verifier si la depense est deja effectue (1) ou pas (0)
	$a=array();
	if ($h=opendir(dirname(__FILE__).'/../../data/sdata/paypal/'))
		{
		while (($file=readdir($h))!==false)
			{
			if($file==$txn_id.'.json') {closedir($h); return 1;}
			}
		closedir($h);
		}
	return 0;
	}
?>
