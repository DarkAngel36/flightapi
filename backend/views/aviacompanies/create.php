<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Aviacompanies */

$this->title                   = 'Create Aviacompanies';
$this->params['breadcrumbs'][] = ['label' => 'Aviacompanies', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="aviacompanies-create">

	<h1><?= Html::encode($this->title) ?></h1>
	
	<?= $this->render('_form', [
		'model' => $model,
	]) ?>

</div>
