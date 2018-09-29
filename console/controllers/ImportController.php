<?php
/**
 * Created by PhpStorm.
 * User: Андрей
 * Date: 25.09.2018
 * Time: 14:24
 */

namespace console\controllers;

use common\models\Airports;
use common\models\Aviacompanies;
use common\models\Cities;
use common\models\Countries;
use common\models\Planes;
use yii\console\Controller;

class ImportController extends Controller
{
    public function actionIndex()
    {
        $this->actionAirports();
        $this->actionAviacompanies();
        $this->actionCountries();
        $this->actionCities();
        $this->actionPlanes();
        $this->actionAllAirports();
    }

    public function actionAirports()
    {
        Airports::deleteAll();
        $json = file_get_contents('http://api.travelpayouts.com/data/airports.json');
        $airports = json_decode($json, true);
        foreach ($airports as $item) {
            $item['name_translations'] = json_encode($item['name_translations']);
            $item['coordinates'] = json_encode($item['coordinates']);
            $model = new Airports($item);
            $model->save();
        }
    }

    public function actionAviacompanies()
    {
        Aviacompanies::deleteAll();
        $json = file_get_contents('http://api.travelpayouts.com/data/airlines.json');
        $airports = json_decode($json, true);

        foreach ($airports as $item) {
    	    if(!isset($item['code'])) {print_r($item); die();}
	    if(!isset($item['name_translations']['en'])) $item['name_translations']['en'] = $item['name'];
            $item['name'] = $item['name_translations']['en'];
            $item['name_translations'] = json_encode($item['name_translations']);
//            print_r($item);die();
            $model = new Aviacompanies();
            $model->code = $item['code'];
            $model->name = $item['name'];
            $model->name_translations = $item['name_translations'];

            if(!$model->save()) print_r($model->errors);
        }
    }

    public function actionCountries()
    {
        Countries::deleteAll();
        $json = file_get_contents('http://api.travelpayouts.com/data/countries.json');
        $airports = json_decode($json, true);
        foreach ($airports as $item) {
            $item['name_translations'] = json_encode($item['name_translations']);

            $model = new Countries($item);
            $model->save();
        }
    }

    public function actionCities()
    {
        Cities::deleteAll();
        $json = file_get_contents('http://api.travelpayouts.com/data/cities.json');
        $airports = json_decode($json, true);
        foreach ($airports as $item) {
            $item['name_translations'] = json_encode($item['name_translations']);
            $item['coordinates'] = json_encode($item['coordinates']);
            $model = new Cities($item);
            $model->save();
        }
    }

    public function actionPlanes()
    {
        Planes::deleteAll();
        $json = file_get_contents('http://api.travelpayouts.com/data/planes.json');
        $airports = json_decode($json, true);

        foreach ($airports as $item) {
            $model = new Planes($item);
            $model->save();
        }
    }

    public function actionAllAirports()
    {
        $connection = \Yii::$app->getDb();
        $connection->createCommand('truncate all_airports;')->execute();
        $sql = 'INSERT INTO all_airports (`code`, `name`) SELECT * FROM (
SELECT cities.code AS `code`, CONCAT(cities.name, ", ", countries.name, ": ", "[" , cities.code ,"]") AS `name`
FROM cities
INNER JOIN countries ON countries.code = cities.country_code
INNER JOIN airports ON airports.city_code = cities.code
WHERE airports.name LIKE "%Airport%"
GROUP BY cities.name, cities.country_code, countries.name, cities.code
UNION ALL
SELECT airports.code AS `code`, CONCAT(cities.name, ", ", countries.name, ": ", airports.name, "[" , airports.code ,"]") AS `name`
FROM cities
INNER JOIN airports ON airports.city_code = cities.code
INNER JOIN countries ON countries.code = cities.country_code
WHERE airports.name LIKE "%Airport%"
GROUP BY airports.code, cities.name, cities.country_code, countries.name, airports.name
) AS qry
ORDER BY `name`';

        $connection->createCommand($sql)->execute();
    }
}