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

class amadeus
{
    private $apiKey = 'Qqp9cVlvG0ywG2JncjcpcK1XIiMrGpdA';
    const API_URL = 'https://api.sandbox.amadeus.com/v1.2/flights/';
    private $minDuration = 9999;
    private $minDurationSec = 9999;
    private $minPrice = 9999999;

    public function __construct()
    {
    }

    private function getData($url, $data)
    {
//        $data['apikey'] = $this->apiKey;
        $urlSend = self::API_URL . $url . '?' . 'apikey='.$this->apiKey .'&'. http_build_query($data);
//        die($urlSend);
        $retJson = file_get_contents($urlSend);

        try{
            $returnData = json_decode($retJson, true);
            $status = 'success';
            $message = 'ok';
        } catch (Exception $e) {
            $status = 'error';
            $message = $e->getMessage();
            $returnData = [];
        }

        return ['status' => $status, 'message' => $message, 'data' => $returnData];
    }

    public function getCheap($data)
    {
        $url = 'inspiration-search';
        return $this->getData($url, $data);
    }

    public function sortPrice($a, $b)
    {
        if ($a['price'] == $b['price']) {
            return 0;
        }
        return ($a['price'] < $b['price']) ? -1 : 1;
    }

    public function sortDuration($a, $b)
    {
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

        $this->minDuration = 9999;
        $this->minPrice = 9999999;

        $result = [];
        $flights = [];
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
        ];

        return $result;
    }
}