<?php

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
				'timeReasonDropdown' => $timeReasonDropdown,
				'ptoData' => $ptoData,
				'SundayDate' => $SundayDate,
				'SaturdayDate' => $SaturdayDate,
				'hoursOverviewDataProvider' => $hoursOverviewDataProvider,
			]);
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