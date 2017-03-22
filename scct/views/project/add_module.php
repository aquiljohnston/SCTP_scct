<?php

use yii\helpers\Html;
use kartik\form\ActiveForm;
use kartik\sortinput\SortableInput;

/* @var $this yii\web\View */
/* @var $project app\models\project */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Add Modules to' . ' ' . $project->ProjectName;
$this->params['breadcrumbs'][] = ['label' => 'Project', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $project->ProjectID, 'url' => ['view', 'id' => $project->ProjectID]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="project-add-module">

    <h1 class="title"><?= Html::encode($this->title) ?></h1>

	<p>
		<?= Html::a('Back', ['view' , 'id' => $project->ProjectID], ['class' => 'btn btn-primary']) ?>
	</p>
	
	 <?php $form = ActiveForm::begin([
				'type' => ActiveForm::TYPE_VERTICAL,
				'formConfig' => ['showLabels' => false,'deviceSize' => ActiveForm::SIZE_SMALL],
                'options' => ['id' => 'projectAddModuleform']
			]); ?>
		<div class="row">
		<div class="col-sm-6">
		<label style="font-size:20px">Inactive Modules</label>
			<div class="projectAddModuleForms">
				<?= $form->field($model, 'InactiveModules')->widget(SortableInput::classname(),[
					//'name'=>'Inactive Modules',
					'items' => $inactiveData,
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
		<label style="font-size:20px">Active Modules</label>
			<div class="projectAddModuleForms">
				<?= $form->field($model, 'ActiveModules')->widget(SortableInput::classname(),[
					'name'=>'Active Modules',
					'items' => $activeData,
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
	
	<div class="form-group" id="">
        <!-- Taken from commit a09e6b8d7fed3035d888ade56ffd0e1a623f4c00 on PGE-Web -->
        <?= Html::submitButton( 'Submit', ['class' => 'btn btn-success','id' => 'projectAddModuleSubmitBtn']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default','id' => 'projectAddModuleResetBtn']) ?>
    </div>
    <?php ActiveForm::end(); ?>
	
</div>
