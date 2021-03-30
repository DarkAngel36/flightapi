<?php
/**
 * Created by PhpStorm.
 * User: Андрей
 * Date: 25.09.2018
 * Time: 15:12
 */

namespace common\components\API;


use common\models\Airports;
use common\models\Aviacompanies;
use common\models\Planes;
use phpDocumentor\Reflection\Types\Object_;
use yii\helpers\ArrayHelper;
use common\models\Cities;
use linslin\yii2\curl;

class amadeus {
	const API_URL        = 'https://api.sandbox.amadeus.com/v1.2/flights/';
	const OAUTH_URL      = 'https://test.api.amadeus.com/v1/security/oauth2/token';
	const STATE_APPROVED = 'approved';
	private $apiKey         = '3zqfReJjAPr7n6WRyK69iOffO3z43Ix7';
	private $apiSecret      = 'mGGkssUwcxGE2UT7';
	private $minDuration    = 9999999;
	private $minDurationStr = '';
	private $minDurationSec = 9999;
	private $minPrice       = 9999999;
	private $access_token   = null;
	private $token_type     = null;
	
	public function __construct() {
	}
	
	public function getToken() {
		$curl = new curl\Curl();
		
		$response = $curl->setOption(
			CURLOPT_POSTFIELDS,
			http_build_query([
					'grant_type'    => 'client_credentials',
					'client_id'     => $this->apiKey,
					'client_secret' => $this->apiSecret,
				]
			))
			->setHeader('Content-Type', 'application/x-www-form-urlencoded')
			->post(self::OAUTH_URL);
		
		if($curl->responseCode == 200) {
			$responseData = json_decode($response, true);
			if ($responseData && !empty($responseData['state'])) {
				if($responseData['state'] == self::STATE_APPROVED) {
					$this->access_token = $responseData['access_token'] ?? null;
					$this->token_type = $responseData['token_type'] ?? null;
				}
			}
		} else {
			return ['error' => 'Error response code: ' . $curl->responseCode];
		}
		
		return [
			'access_token' => $this->access_token,
			'token_type' => $this->token_type,
		];
	}
	
	private function getData($url, $data) {
		$this->getToken();
		$curl       = new curl\Curl();
		$requestUrl = /*self::API_URL . $url*/
			'https://test.api.amadeus.com/v2/shopping/flight-offers';
		$fields     = http_build_query($data);
		//		print_r("{$requestUrl}?{$fields}");
		$response = $curl->setHeader('Authorization', "{$this->token_type} {$this->access_token}")
			->setOption(
				CURLOPT_POSTFIELDS,
				http_build_query($data)
			)
			//			->setHeader('X-HTTP-Method-Override', 'GET')
			->get("{$requestUrl}?{$fields}");
		//			->post($requestUrl);
		
		if ($curl->responseCode !== 200) {
			echo 'Error: ' . $curl->responseCode;
		}
		
		//		echo "<pre>";
		//		print_r(json_decode($response, true));
		//		echo "</pre>";
		return json_decode($response, true);
	}
	
	private function getDataOld($url, $data) {
		//        $data['apikey'] = $this->apiKey;
		$urlSend = self::API_URL . $url . '?' . 'apikey=' . $this->apiKey . '&' . http_build_query($data);
		//        die($urlSend);
		$context = stream_context_create([
				'ssl' => [
					//					'method' => 'GET',
					//					'header' => 'Content-Type: application/json',
					//					'content' => $encryptedEncodedData,
					'verify_peer'      => false,
					'verify_peer_name' => false,
				],
			]
		);
		
		$retJson = file_get_contents($urlSend, false, $context);
		
		try {
			$returnData = json_decode($retJson, true);
			$status     = 'success';
			$message    = 'ok';
		}
		catch (Exception $e) {
			$status     = 'error';
			$message    = $e->getMessage();
			$returnData = [];
		}
		
		return ['status' => $status, 'message' => $message, 'data' => $returnData];
	}
	
	public function getCheap($data) {
		$url = 'inspiration-search';
		
		return $this->getDataOld($url, $data);
	}
	
	public function sortPrice($a, $b) {
		if ($a['price'] == $b['price']) {
			return 0;
		}
		
		return ($a['price'] < $b['price']) ? -1 : 1;
	}
	
	public function sortDuration($a, $b) {
		if ($a['durationSec'] == $b['durationSec']) {
			return 0;
		}
		
		return ($a['durationSec'] < $b['durationSec']) ? -1 : 1;
	}
	
	public function sortBest($a, $b) {
		$a = 0.3 * $this->minDurationSec / $a['durationSec'] + $this->minPrice / $a['price'];
		$b = 0.3 * $this->minDurationSec / $b['durationSec'] + $this->minPrice / $b['price'];
		
		if ($a == $b) {
			return 0;
		}
		
		return ($a < $b) ? 1 : -1;
	}
	
	public static function convertTime($time) {
		$time    = str_replace('PT', '', $time);
		$days    = 0;
		$hours   = 0;
		$minutes = 0;
		
		$pos = strpos($time, 'D');
		if ($pos !== false) {
			$days = +substr($time, 0, $pos);
			$time = substr($time, $pos + 1, strlen($time) - $pos);
		}
		$pos = strpos($time, 'H');
		if ($pos !== false) {
			$hours = +substr($time, 0, $pos);
			$time  = substr($time, $pos + 1, strlen($time) - $pos);
		}
		$pos = strpos($time, 'M');
		if ($pos !== false) {
			$minutes = +substr($time, 0, $pos);
		}
		
		return $days * 24 * 60 + $hours * 60 + $minutes;
	}
	
	public function getDirect($data) {
		error_reporting(E_ALL);
		ini_set('display_errors', 1);
		$url = 'low-fare-search';
		
		$data = $this->getData($url, $data);
		
		$this->minDuration = 9999;
		$this->minPrice    = 9999999;
		
		$result             = [];
		$flights            = [];
		$existAviacompanies = [];
		$existAirports      = [];
		//        print_r($data);die();
		if (!empty($data['data'])) {
			foreach ($data['data'] as $item) {
				$operating = [];
				$tmp_flights = [];
				foreach ($item['itineraries'] as $itinerarie) {
					$departure = [];
					$arrival   = [];
					
					foreach ($itinerarie['segments'] as $counter => $segment) {
						if ($counter == 0) {
							$departure = $segment['departure'];
						}
						else if ($counter == (count($itinerarie['segments']) - 1)) {
							$arrival = $segment['arrival'];
						}
						if (empty($arrival)) {
							$arrival = $itinerarie['segments'][count($itinerarie['segments']) - 1]['arrival'];
						}
						if (isset($segment['operating'])) {
							$operating[] = $segment['operating']['carrierCode'];
						}
					}
					
					$duration          = self::convertTime($itinerarie['duration']);
					$this->minDuration = min($this->minDuration, $duration);
					
					$durationStr = str_replace([
						'PT',
						'D',
						'H',
						'M',
					], [
						'',
						' d ',
						' h ',
						' m',
					], $itinerarie['duration']);
					if ($duration == $this->minDuration) {
						$this->minDurationStr = $durationStr;
					}
					
					$this->minDurationSec = $this->minDuration * 60;
					
					$tmp_flights[] = [
						'duration'    => $durationStr,
						'durationSec' => +$duration * 60,
						
						'itineraries' => $item['itineraries'],
						
						'departure' => $departure,
						'arrival'   => $arrival,
						
					];
				}
				$this->minPrice = min($this->minPrice, round($item['price']['total'] * 0.75, 2));
				
				$flights[] = [
					'tickets'          => $tmp_flights,
					'priceTotal'       => round($item['price']['total'] * 0.75, 2),
					'price'            => $item['price'],
					'priceData'        => $item['price'],
					'pricingOptions'   => $item['pricingOptions'],
					'travelerPricings' => $item['travelerPricings'],
					'operating' => array_unique($operating),
					'duration'    => $durationStr,
					'durationSec' => +$duration * 60,
				];
			}
		}
		$fastest  = $flights;
		$cheapest = $flights;
		$best     = $flights;
		usort($fastest, [$this, 'sortDuration']);
		usort($cheapest, [$this, 'sortPrice']);
		//		usort($best, [$this, 'sortBest']);
		//		list($mdh, $mds) = explode(':', $this->minDuration);
		
		$planes = Planes::find()->select('code, name')->asArray()->all();
		
		//        ArrayHelper::
		
		$planes[] = ['code' => '32A', 'name' => 'A320 (sharklets)'];
		$planes[] = ['code' => '32B', 'name' => 'A321 (sharklets)'];
		$planes[] = ['code' => '32C', 'name' => 'A318 (sharklets)'];
		$planes[] = ['code' => '32D', 'name' => 'A319 (sharklets)'];
		$planes[] = ['code' => 'SU9', 'name' => 'Sukhoi SuperJet 100'];
		
		$result = [
			'minPrice'           => $this->minPrice,
			'minDuration'        => $this->minDurationStr,
			'minDurationSec'     => $this->minDuration * 60,
			'flights'            => $flights,
			'fastest'            => $fastest,
			'cheapest'           => $cheapest,
			'best'               => $best,
			'airports'           => ArrayHelper::index(Airports::find()->select('code, name, city_code')->asArray()->all(), 'code'),
			'aviacompanies'      => ArrayHelper::index(Aviacompanies::find()->select('code, name')->asArray()->all(), 'code'),
			'cities'             => ArrayHelper::index(Cities::find()->select('code, name')->asArray()->all(), 'code'),
			'planes'             => ArrayHelper::index($planes, 'code'),
			'existAviacompanies' => array_values(array_unique($existAviacompanies)),
			'existAirports'      => array_values(array_unique($existAirports)),
			'dictionaries'       => $data['dictionaries'] ?? [],
			'meta'               => $data['meta'] ?? [],
			'errors'             => $data['errors'] ?? [],
		];
		
		return $result;
	}
}