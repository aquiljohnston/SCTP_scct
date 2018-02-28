<?php
/**
 * Created by PhpStorm.
 * User: tzhang
 * Date: 2018/1/25
 * Time: 16:42
 */

/* @var $this yii\web\View */

$this->title = 'Create Task Entry';
$this->params['breadcrumbs'][] = ['label' => 'Task Entry', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

//check if task exist and display error if no task are avaliable. 
if($allTask != null)
{
	$body = $this->render('task_entry_form', [
				'model' => $model,
				'allTask' => $allTask,
				'chartOfAccountType' => $chartOfAccountType,
				'timeCardID' => $timeCardID,
				'SundayDate' => $SundayDate,
				'SaturdayDate' => $SaturdayDate
			]);
	Yii::trace($body);
}	
else
{
	$body = 'No task available for this project';
}
?>
<div class="time-entry-create">

     <?= $body
		?>

</div>