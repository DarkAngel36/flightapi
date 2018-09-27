<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "countries".
 *
 * @property int $id
 * @property string $code
 * @property string $name
 * @property string $currency
 * @property string $name_translations
 * @property int $status
 * @property int $created_at
 * @property int $updated_at
 */
class Countries extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'countries';
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
            [['name_translations'], 'string'],
            [['status', 'created_at', 'updated_at'], 'integer'],
            [['code', 'currency'], 'string', 'max' => 5],
            [['name'], 'string', 'max' => 255],
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
            'currency' => Yii::t('app', 'Currency'),
            'name_translations' => Yii::t('app', 'Name Translations'),
            'status' => Yii::t('app', 'Status'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }
}
