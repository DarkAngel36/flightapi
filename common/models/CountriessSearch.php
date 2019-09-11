<?php

namespace common\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Countries;

/**
 * CountriessSearch represents the model behind the search form of `\common\models\Countries`.
 */
class CountriessSearch extends Countries {
	/**
	 * {@inheritdoc}
	 */
	public function rules() {
		return [
			[['id', 'status', 'created_at', 'updated_at'], 'integer'],
			[['code', 'name', 'currency', 'name_translations'], 'safe'],
		];
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function scenarios() {
		// bypass scenarios() implementation in the parent class
		return Model::scenarios();
	}
	
	/**
	 * Creates data provider instance with search query applied
	 *
	 * @param array $params
	 *
	 * @return ActiveDataProvider
	 */
	public function search($params) {
		$query = Countries::find();
		
		// add conditions that should always apply here
		
		$dataProvider = new ActiveDataProvider([
			'query' => $query,
		]);
		
		$this->load($params);
		
		if (!$this->validate()) {
			// uncomment the following line if you do not want to return any records when validation fails
			// $query->where('0=1');
			return $dataProvider;
		}
		
		// grid filtering conditions
		$query->andFilterWhere([
			'id'         => $this->id,
			'status'     => $this->status,
			'created_at' => $this->created_at,
			'updated_at' => $this->updated_at,
		]);
		
		$query->andFilterWhere(['like', 'code', $this->code])
			->andFilterWhere(['like', 'name', $this->name])
			->andFilterWhere(['like', 'currency', $this->currency])
			->andFilterWhere(['like', 'name_translations', $this->name_translations]);
		
		return $dataProvider;
	}
}
