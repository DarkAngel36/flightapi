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
	private $apiKey    = '3zqfReJjAPr7n6WRyK69iOffO3z43Ix7';
	private $apiSecret = 'mGGkssUwcxGE2UT7';
	private $minDuration    = 9999;
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
		$curl = new curl\Curl();
		$requestUrl = /*self::API_URL . $url*/ 'https://test.api.amadeus.com/v1/shopping/flight-destinations';
		$fields = http_build_query($data);
		print_r("{$requestUrl}?{$fields}");
		$response = $curl->setHeader('Authorization', "{$this->token_type} {$this->access_token}")
//			->setOption(
//				CURLOPT_POSTFIELDS,
//				http_build_query($data)
//			)
			->get("{$requestUrl}?{$fields}");
//			->post($requestUrl);
		
		if($curl->responseCode !== 200) echo 'Error: ' . $curl->responseCode;
		
		echo "<pre>";
		print_r(json_decode($response, true));
		echo "</pre>";
		return json_decode($response, true);
	}
	
	private function getDataOld($url, $data) {
		//        $data['apikey'] = $this->apiKey;
		$urlSend = self::API_URL . $url . '?' . 'apikey=' . $this->apiKey . '&' . http_build_query($data);
		//        die($urlSend);
		$retJson = file_get_contents($urlSend);
		
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

    public function sortBest($a, $b)
    {
        $a = 0.3 * $this->minDurationSec / $a['durationSec'] + $this->minPrice / $a['price'];
        $b = 0.3 * $this->minDurationSec / $b['durationSec'] + $this->minPrice / $b['price'];

        if ($a == $b) {
            return 0;
        }
        return ($a < $b) ? 1 : -1;
    }

    public function getDirect($data)
    {
        error_reporting(E_ALL);
        ini_set('display_errors', 1);
        $url = 'low-fare-search';
        $data = $this->getData($url, $data);
print_r($data);die();
        $this->minDuration = 9999;
        $this->minPrice = 9999999;

        $result = [];
        $flights = [];
        $existAviacompanies = [];
        $existAirports = [];
        if($data['status'] == 'success') {
            foreach($data['data']['results'] as $item) {
                foreach($item['itineraries'] as $itinerarie) {
                    $duration = $itinerarie['outbound']['duration'];
                    $this->minDuration = min($this->minDuration, $duration);
                    $this->minPrice = min($this->minPrice, round($item['fare']['total_price'] * 0.75, 2));
                    list($mdh, $mds) = explode(':', $duration);
                    $this->minDurationSec = min($this->minDurationSec, $mdh * 60 + $mds);
                    $flights[] = [
                        'duration' => $duration,
                        'durationSec' => $mdh*60+$mds,
                        'price' => round($item['fare']['total_price'] * 0.75, 2),
                        'fare' => $item['fare'],
                        'flights' => $itinerarie/*['outbound']['flights']*/,
                    ];
                    $existAviacompanies[] = $itinerarie['outbound']['flights'][0]['operating_airline'];
                    $existAirports[] = $itinerarie['outbound']['flights'][0]['origin']['airport'];
                }
            }
        }
        $fastest = $flights;
        $cheapest = $flights;
        $best = $flights;
        usort($fastest, [$this, 'sortDuration']);
        usort($cheapest, [$this, 'sortPrice']);
        usort($best, [$this, 'sortBest']);
        list($mdh, $mds) = explode(':', $this->minDuration);

        $planes = Planes::find()->select('code, name')->asArray()->all();

//        ArrayHelper::

        $planes[] = ['code' => '32A', 'name' => 'A320 (sharklets)'];
        $planes[] = ['code' => '32B', 'name' => 'A321 (sharklets)'];
        $planes[] = ['code' => '32C', 'name' => 'A318 (sharklets)'];
        $planes[] = ['code' => '32D', 'name' => 'A319 (sharklets)'];
        $planes[] = ['code' => 'SU9', 'name' => 'Sukhoi SuperJet 100'];

        $result = [
            'minPrice' => $this->minPrice,
            'minDuration' => $this->minDuration,
            'minDurationSec' => $mdh*60+$mds,
            'flights' => $flights,
            'fastest' => $fastest,
            'cheapest' => $cheapest,
            'best' => $best,
            'airports' => ArrayHelper::index( Airports::find()->select('code, name, city_code')->asArray()->all(), 'code'),
            'aviacompanies' => ArrayHelper::index( Aviacompanies::find()->select('code, name')->asArray()->all(), 'code'),
            'cities' => ArrayHelper::index( Cities::find()->select('code, name')->asArray()->all(), 'code'),
            'planes' => ArrayHelper::index( $planes, 'code'),
            'existAviacompanies' => array_values( array_unique( $existAviacompanies ) ),
            'existAirports' => array_values( array_unique( $existAirports) ),
        ];

        return $result;
    }
}