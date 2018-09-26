<?php
/**
 * Created by PhpStorm.
 * User: Андрей
 * Date: 25.09.2018
 * Time: 15:12
 */

namespace common\components\API;


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
        $a = $this->minDurationSec / $a['durationSec'] + $this->minPrice / $a['price'];
        $b = $this->minDurationSec / $b['durationSec'] + $this->minPrice / $b['price'];

        if ($a == $b) {
            return 0;
        }
        return ($a < $b) ? -1 : 1;
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
                    $this->minPrice = min($this->minPrice, $item['fare']['total_price']);
                    list($mdh, $mds) = explode(':', $duration);
                    $this->minDurationSec = min($this->minDurationSec, $mdh * 60 + $mds);
                    $flights[] = [
                        'duration' => $duration,
                        'durationSec' => $mdh*60+$mds,
                        'price' => round($item['fare']['total_price'] * 0.75, 2),
                        'fare' => $item['fare'],
                        'flights' => $itinerarie['outbound']['flights']
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

        $result = [
            'minPrice' => $this->minPrice,
            'minDuration' => $this->minDuration,
            'minDurationSec' => $mdh*60+$mds,
            'flights' => $flights,
            'fastest' => $fastest,
            'cheapest' => $cheapest,
            'best' => $best,
        ];

        return $result;
    }
}