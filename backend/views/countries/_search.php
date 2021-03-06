<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\CountriessSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="countries-search">
	
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
	
	<?= $form->field($model, 'currency') ?>
	
	<?= $form->field($model, 'name_translations') ?>
	
	<?php // echo $form->field($model, 'status') ?>
	
	<?php // echo $form->field($model, 'created_at') ?>
	
	<?php // echo $form->field($model, 'updated_at') ?>

	<div class="form-group">
		<?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
		<?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
	</div>
	
	<?php ActiveForm::end(); ?>

</div>
