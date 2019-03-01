<?php

$this->title = 'Create Mileage Task';
$this->params['breadcrumbs'][] = ['label' => 'Mileage Task Entry', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$body = $this->render('_mileage_task_form', [
		'model' => $model,
		'sundayDate' => $sundayDate,
		'saturdayDate' => $saturdayDate,
		'mileageCardProjectID' => $mileageCardProjectID,
		
	]);
?>
<div class="mileage-entry-create">

     <?= $body
		?>

</div>