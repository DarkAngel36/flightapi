<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\CitiesSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="cities-search">
	
	<?php $form = ActiveForm::begin([
		'action'  => ['index'],
		'method'  => 'get',
		'options' => [
			'data-pjax' => 1,
		],
	]); ?>
	
	<?= $form->field($model, 'id') ?>
	
	<?= $form->field($model, 'code') ?>
	
	<?= $form->field($model, 'name') ?>
	
	<?= $form->field($model, 'coordinates') ?>
	
	<?= $form->field($model, 'time_zone') ?>
	
	<?php // echo $form->field($model, 'name_translations') ?>
	
	<?php // echo $form->field($model, 'country_code') ?>
	
	<?php // echo $form->field($model, 'status') ?>
	
	<?php // echo $form->field($model, 'created_at') ?>
	
	<?php // echo $form->field($model, 'updated_at') ?>

	<div class="form-group">
		<?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
		<?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
	</div>
	
	<?php ActiveForm::end(); ?>

</div>
