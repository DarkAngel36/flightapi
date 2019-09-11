<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Countries */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="countries-form">
	
	<?php $form = ActiveForm::begin(); ?>
	
	<?= $form->field($model, 'code')->textInput(['maxlength' => true]) ?>
	
	<?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
	
	<?= $form->field($model, 'currency')->textInput(['maxlength' => true]) ?>
	
	<?= $form->field($model, 'name_translations')->textarea(['rows' => 6]) ?>
	
	<?= $form->field($model, 'status')->textInput() ?>
	
	<?= $form->field($model, 'created_at')->textInput() ?>
	
	<?= $form->field($model, 'updated_at')->textInput() ?>

	<div class="form-group">
		<?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
	</div>
	
	<?php ActiveForm::end(); ?>

</div>
