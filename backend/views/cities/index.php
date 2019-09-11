<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel common\models\CitiesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title                   = 'Cities';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="cities-index">

	<h1><?= Html::encode($this->title) ?></h1>
	<?php Pjax::begin(); ?>
	<?php // echo $this->render('_search', ['model' => $searchModel]); ?>

	<p>
		<?= Html::a('Create Cities', ['create'], ['class' => 'btn btn-success']) ?>
	</p>
	
	<?= GridView::widget([
		'dataProvider' => $dataProvider,
		'filterModel'  => $searchModel,
		'columns'      => [
			['class' => 'yii\grid\SerialColumn'],
			
			//            'id',
			'code',
			'name',
			//            'coordinates:json',
			'time_zone',
			//'name_translations:ntext',
			'country_code',
			//'status',
			//'created_at',
			//'updated_at',
			
			['class' => 'yii\grid\ActionColumn'],
		],
	]); ?>
	<?php Pjax::end(); ?>
</div>
