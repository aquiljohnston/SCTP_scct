<?php

use yii\helpers\Html;
use kartik\form\ActiveForm;
use kartik\sortinput\SortableInput;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $project app\models\project */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Add Users to' . ' ' . $project->ProjectName;
$this->params['breadcrumbs'][] = ['label' => 'Project', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $project->ProjectID, 'url' => ['view', 'id' => $project->ProjectID]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="project-add-user">
    <h1 class="title"><?= Html::encode($this->title) ?></h1>

	<p>
		<?= Html::a('Back', ['view' , 'id' => $project->ProjectID], ['class' => 'btn btn-primary']) ?>
	</p>
	
	 <?php $form = ActiveForm::begin([
				'type' => ActiveForm::TYPE_VERTICAL,
				'formConfig' => ['showLabels' => false,'deviceSize' => ActiveForm::SIZE_SMALL],
                'options' => ['id' => 'projectAdduserform']
			]); ?>
		<label id="projectFilter">
			<?= $form->field($model, 'filter')->textInput(['value' => $projectFilterParams, 'id' => 'projectFilter'])->label('Search'); ?>
		</label>
		<input type="hidden" value=<?php echo $project->ProjectID;?> name="projectID" id="projectID">
	<?php ActiveForm::end(); ?>
		<?php Pjax::begin(['id' => 'projectSortableView', 'timeout' => false]) ?>
		<?php $form = ActiveForm::begin([
			'type' => ActiveForm::TYPE_VERTICAL,
			'formConfig' => ['showLabels' => false,'deviceSize' => ActiveForm::SIZE_SMALL],
			'options' => ['id' => 'projectSortableInputForm']
		]); ?>
		<div class="row">
			<div class="col-sm-6">
				<label style="font-size:20px">Unassigned Users</label>
				<div class="projectAddUserForms">
					<?= $form->field($model, 'UnassignedUsers')->widget(SortableInput::classname(),[
						//'name'=>'Unassigned Users',
						'items' => $unassignedData,
						'hideInput' => true,
						'sortableOptions' => [
							'connected'=>true,
							'options' => ['style'=> 'min-height: 20pt']
						],
						'options' => [
							'class'=>'form-control',
							'readonly'=>true,
						]
					]); ?>
				</div>
			</div>
			<div class="col-sm-6">
				<label style="font-size:20px">Assigned Users</label>
				<div class="projectAddUserForms">
					<?= $form->field($model, 'AssignedUsers')->widget(SortableInput::classname(),[
						'name'=>'Assigned Users',
						'items' => $assignedData,
						'hideInput' => true,
						'sortableOptions' => [
							'itemOptions'=>['class'=>'alert alert-warning'],
							'connected'=>true,
							'options' => ['style'=> 'min-height: 20pt']

						],
						'options' => [
							'class'=>'form-control',
							'readonly'=>true
						]
					]); ?>
				</div>
			</div>
		</div>
		<input type="hidden" value=<?php echo $project->ProjectID;?> name="projectID" id="projectID">
	<?php ActiveForm::end(); ?>
	<?php Pjax::end() ?>
	<div class="form-group">
		<?= Html::Button( 'Submit', ['class' => 'btn btn-success','id' => 'projectAddUserSubmitBtn']) ?>
		<?= Html::resetButton('Reset', ['class' => 'btn btn-default','id' => 'projectAddUserResetBtn']) ?>
	</div>
</div>
