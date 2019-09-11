<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Airports */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="airports-form">
	
	<?php $form = ActiveForm::begin(); ?>
	
	<?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
	
	<?= $form->field($model, 'time_zone')->textInput(['maxlength' => true]) ?>
	
	<?= $form->field($model, 'name_translations')->textarea(['rows' => 6]) ?>
	
	<?= $form->field($model, 'country_code')->textInput(['maxlength' => true]) ?>
	
	<?= $form->field($model, 'city_code')->textInput(['maxlength' => true]) ?>
	
	<?= $form->field($model, 'code')->textInput(['maxlength' => true]) ?>
	
	<?= $form->field($model, 'flightable')->textInput() ?>
	
	<?= $form->field($model, 'coordinates')->textarea(['rows' => 6]) ?>
	
	<?= $form->field($model, 'status')->textInput() ?>
	
	<?= $form->field($model, 'created_at')->textInput() ?>
	
	<?= $form->field($model, 'updated_at')->textInput() ?>

	<div class="form-group">
		<?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
	</div>
	
	<?php ActiveForm::end(); ?>

</div>
