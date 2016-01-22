<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $model app\models\time-card */

//$this->title = $model->TimeCardID;
$this->params['breadcrumbs'][] = ['label' => 'Time Cards', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="timecard-view">

    <h1><?= Html::encode($this->title) ?></h1>

   <p>
		<?= Html::a('Back', ['index'], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Update', ['update', 'id' => $model["TimeCardID"]], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model["TimeCardID"]], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'delete',
            ],
        ]) ?>
    </p>
    
	<!--Sunday TableView-->
	<h2 class="time_entry_header">Sunday</h2>
	<?= GridView::widget([
		'dataProvider' => $SundayProvider,
		'columns' => [
			//'TimeEntryID',
			//'TimeEntryUserId',
			'TimeEntryStartTime',
			'TimeEntryEndTime',
			'TimeEntryDate',
			//'TimeEntryTimeCardID',
			//'TimeEntryActivityID',
			'TimeEntryComment',
			'TimeEntryCreateDate',
			'TimeEntryCreateBy',
			'TimeEntryModifiedDate',
			'TimeEntryModifiedBy'
		]
	])?>
	
	<!--Monday TableView-->
	<h2 class="time_entry_header">Monday</h2>
	<?= GridView::widget([
		'dataProvider' => $MondayProvider,
		'columns' => [
			'TimeEntryStartTime',
			'TimeEntryEndTime',
			'TimeEntryDate',
			'TimeEntryComment',
			'TimeEntryCreateDate',
			'TimeEntryCreateBy',
			'TimeEntryModifiedDate',
			'TimeEntryModifiedBy'
		]
	])?>
	
	<!--Tuesday TableView-->
	<h2 class="time_entry_header">Tuesday</h2>
	<?= GridView::widget([
		'dataProvider' => $TuesdayProvider,
		'columns' => [
			'TimeEntryStartTime',
			'TimeEntryEndTime',
			'TimeEntryDate',
			'TimeEntryComment',
			'TimeEntryCreateDate',
			'TimeEntryCreateBy',
			'TimeEntryModifiedDate',
			'TimeEntryModifiedBy'
		]
	])?>
	
	<!--Wednesday TableView-->
	<h2 class="time_entry_header">Wednesday</h2>
	<?= GridView::widget([
		'dataProvider' => $WednesdayProvider,
		'columns' => [
			'TimeEntryStartTime',
			'TimeEntryEndTime',
			'TimeEntryDate',
			'TimeEntryComment',
			'TimeEntryCreateDate',
			'TimeEntryCreateBy',
			'TimeEntryModifiedDate',
			'TimeEntryModifiedBy'
		]
	])?>
	
	<!--Thursday TableView-->
	<h2 class="time_entry_header">Thursday</h2>
	<?= GridView::widget([
		'dataProvider' => $ThursdayProvider,
		'columns' => [
			'TimeEntryStartTime',
			'TimeEntryEndTime',
			'TimeEntryDate',
			'TimeEntryComment',
			'TimeEntryCreateDate',
			'TimeEntryCreateBy',
			'TimeEntryModifiedDate',
			'TimeEntryModifiedBy'
		]
	])?>
	
	<!--Friday TableView-->
	<h2 class="time_entry_header">Friday</h2>
	<?= GridView::widget([
		'dataProvider' => $FridayProvider,
		'columns' => [
			'TimeEntryStartTime',
			'TimeEntryEndTime',
			'TimeEntryDate',
			'TimeEntryComment',
			'TimeEntryCreateDate',
			'TimeEntryCreateBy',
			'TimeEntryModifiedDate',
			'TimeEntryModifiedBy'
		]
	])?>
	
	<!--Saturday TableView-->
	<h2 class="time_entry_header">Saturday</h2>
	<?= GridView::widget([
		'dataProvider' => $SaturdayProvider,
		'columns' => [
			'TimeEntryStartTime',
			'TimeEntryEndTime',
			'TimeEntryDate',
			'TimeEntryComment',
			'TimeEntryCreateDate',
			'TimeEntryCreateBy',
			'TimeEntryModifiedDate',
			'TimeEntryModifiedBy'
		]
	])?>

</div>
