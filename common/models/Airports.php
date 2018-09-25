<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "airports".
 *
 * @property int $id
 * @property string $name
 * @property string $time_zone
 * @property string $name_translations
 * @property string $country_code
 * @property string $city_code
 * @property string $code
 * @property int $flightable
 * @property string $coordinates
 * @property int $status
 * @property int $created_at
 * @property int $updated_at
 */
class Airports extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'airports';
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
            [['name'], 'required'],
            [['name_translations', 'coordinates'], 'string'],
            [['flightable', 'status', 'created_at', 'updated_at'], 'integer'],
            [['name', 'time_zone'], 'string', 'max' => 255],
            [['country_code', 'city_code', 'code'], 'string', 'max' => 3],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Name'),
            'time_zone' => Yii::t('app', 'Time Zone'),
            'name_translations' => Yii::t('app', 'Name Translations'),
            'country_code' => Yii::t('app', 'Country Code'),
            'city_code' => Yii::t('app', 'City Code'),
            'code' => Yii::t('app', 'Code'),
            'flightable' => Yii::t('app', 'Flightable'),
            'coordinates' => Yii::t('app', 'Coordinates'),
            'status' => Yii::t('app', 'Status'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }
}
