<?php
/**
 * Created by PhpStorm.
 * User: tzhang
 * Date: 2018/1/25
 * Time: 16:42
 */

/* @var $this yii\web\View */

$this->title = 'Create Mileage Task';
$this->params['breadcrumbs'][] = ['label' => 'Mileage Task Entry', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

//check if task exist and display error if no task are avaliable. 

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