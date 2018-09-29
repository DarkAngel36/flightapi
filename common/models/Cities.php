<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "cities".
 *
 * @property int $id
 * @property string $code
 * @property string $name
 * @property string $coordinates
 * @property string $time_zone
 * @property string $name_translations
 * @property string $country_code
 * @property int $status
 * @property int $created_at
 * @property int $updated_at
 */
class Cities extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'cities';
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['code'], 'required'],
            [['coordinates', 'name_translations'], 'string'],
            [['status', 'created_at', 'updated_at'], 'integer'],
            [['code', 'name', 'time_zone'], 'string', 'max' => 255],
            [['country_code'], 'string', 'max' => 5],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'code' => Yii::t('app', 'Code'),
            'name' => Yii::t('app', 'Name'),
            'coordinates' => Yii::t('app', 'Coordinates'),
            'time_zone' => Yii::t('app', 'Time Zone'),
            'name_translations' => Yii::t('app', 'Name Translations'),
            'country_code' => Yii::t('app', 'Country Code'),
            'status' => Yii::t('app', 'Status'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    public function getAirports()
    {
        return $this->hasMany(Airports::className(), ['city_code' => 'code']);
    }

    public static function getCitiesWithAirports($q)
    {
        $cache = Yii::$app->cache;
        if($cache->exists('CitiesWithAirports1')) {
            $results = $cache->get('CitiesWithAirports');
        } else {
            /*$sql = 'SELECT * FROM (
SELECT cities.code AS `code`, CONCAT(cities.name, ", ", countries.name, ": All airports", " [" , cities.code ,"]") AS `name`
FROM cities
INNER JOIN countries ON countries.code = cities.country_code
INNER JOIN airports ON airports.city_code = cities.code
WHERE airports.name LIKE "%Airport%"
GROUP BY cities.name, cities.country_code, countries.name, cities.code
UNION ALL
SELECT airports.code AS `code`, CONCAT(cities.name, ", ", countries.name, ": ", airports.name, " [" , airports.code ,"]") AS `name`
FROM cities
INNER JOIN airports ON airports.city_code = cities.code
INNER JOIN countries ON countries.code = cities.country_code
WHERE airports.name LIKE "%Airport%"
GROUP BY airports.code, cities.name, cities.country_code, countries.name, airports.name
) AS qry
ORDER BY `name`';*/
            $sql = 'SELECT `code` as id, `name` as `text` FROM all_airports WHERE `name` LIKE "%'.$q.'%"';

            $connection = Yii::$app->getDb();

            $command = $connection->createCommand($sql);

            $results = $command->queryAll();
            $cache->set('CitiesWithAirports', $results);
        }

        return $results;
    }
}
