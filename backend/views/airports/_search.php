<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\AirportssSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="airports-search">
	
	<?php $form = ActiveForm::begin([
		'action'  => ['index'],
		'method'  => 'get',
		'options' => [
			'data-pjax' => 1,
		],
	]); ?>
	
	<?= $form->field($model, 'id') ?>
	
	<?= $form->field($model, 'name') ?>
	
	<?= $form->field($model, 'time_zone') ?>
	
	<?= $form->field($model, 'name_translations') ?>
	
	<?= $form->field($model, 'country_code') ?>
	
	<?php // echo $form->field($model, 'city_code') ?>
	
	<?php // echo $form->field($model, 'code') ?>
	
	<?php // echo $form->field($model, 'flightable') ?>
	
	<?php // echo $form->field($model, 'coordinates') ?>
	
	<?php // echo $form->field($model, 'status') ?>
	
	<?php // echo $form->field($model, 'created_at') ?>
	
	<?php // echo $form->field($model, 'updated_at') ?>

	<div class="form-group">
		<?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
		<?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
	</div>
	
	<?php ActiveForm::end(); ?>

</div>
