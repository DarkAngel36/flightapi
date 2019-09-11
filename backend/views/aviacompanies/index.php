<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel common\models\AviacompaniesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title                   = 'Aviacompanies';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="aviacompanies-index">

	<h1><?= Html::encode($this->title) ?></h1>
	<?php Pjax::begin(); ?>
	<?php // echo $this->render('_search', ['model' => $searchModel]); ?>

	<p>
		<?= Html::a('Create Aviacompanies', ['create'], ['class' => 'btn btn-success']) ?>
	</p>
	
	<?= GridView::widget([
		'dataProvider' => $dataProvider,
		'filterModel'  => $searchModel,
		'columns'      => [
			['class' => 'yii\grid\SerialColumn'],
			
			'id',
			'code',
			'name',
			//            'name_translations:ntext',
			//            'status',
			//'created_at',
			//'updated_at',
			
			['class' => 'yii\grid\ActionColumn'],
		],
	]); ?>
	<?php Pjax::end(); ?>
</div>
