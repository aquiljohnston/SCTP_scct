 <?php

use yii\helpers\Html;
use kartik\form\ActiveForm;
use kartik\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $project app\models\project */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Add Users to' . ' ' . $project->ProjectName;
$this->params['breadcrumbs'][] = ['label' => 'Project', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $project->ProjectID, 'url' => ['view', 'id' => $project->ProjectID]];
$this->params['breadcrumbs'][] = $this->title;
?>

<style type="text/css">
div.inline { float:left; }
.clearBoth { clear:both; }
</style>

<div class="project-add-user" style="margin-top: 2%;">
    <h1 class="title"><?= Html::encode($this->title) ?></h1>

	<p>
		<?= Html::a('Back', ['view' , 'id' => $project->ProjectID], ['class' => 'btn btn-primary']) ?>
	</p>

	<div class="row">
         <?php $form = ActiveForm::begin([
                'type' => ActiveForm::TYPE_VERTICAL,
                'formConfig' => ['showLabels' => false,'deviceSize' => ActiveForm::SIZE_SMALL],
                'options' => ['id' => 'projectForm']
            ]); ?>
     
            <div id="unassignedFilter" class="col-sm-2" style = "">
                 <?= $form->field($model, 'uaFilter')->textInput(['value' => $unassignedFilterParams, 'id' => 'projectFilter', 'style' => 'width:auto'])->label('Search'); ?>  
            </div>      
            
            <div class="col-sm-1">
                <img id="projectSearchCleanFilterButton" src="/logo/filter_clear_black.png" alt="">
            </div> 

            <div class="col-sm-3" >
                  <span id="unassignedTagCloud" style="display: none;" class="tagCloud"></span>
            </div>

            <div class="col-sm-2" id="assignedFilter" style = "">
                <?= $form->field($model, 'aFilter')->textInput(['value' => $assignedFilterParams, 'class'=>'projectFilterAssigned', 'id' => 'projectFilterAssigned', 'style' =>'width:auto'])->label('Search'); ?>       
            </div>

            <div class="col-sm-1">
                 <img class="assignedSearchCleanFilterButton" src="/logo/filter_clear_black.png" alt="">
            </div>

			<div class="col-sm-3" >
				<span id="assignedTagCloud" style="display: none;" class="tagCloud"></span>
            </div>
            <br>
    <?php ActiveForm::end(); ?>
	</div>

	<div class="row">
		<?php Pjax::begin(['id' => 'projectGridView', 'timeout' => false]) ?>
			<div class="col-sm-6">
				<?= GridView::widget([
					'id' => 'unAssignedGV',
					'dataProvider' => $dataProviderUnassigned,
					'export' => false,
					'pjax' => false,
					'floatHeader' => true,
					'summary' => '',
					'floatOverflowContainer' => true,
					'columns' => [
						[
							'label' => 'Name',
							'attribute' => 'content',
						],
						[
							'header' => 'Assign User',
							'class' => 'kartik\grid\CheckboxColumn',
							'contentOptions' => [],
							'checkboxOptions' => function ($model, $key, $index, $column) {
								return ['userID' => $key,'disabled' => false,'class' => 'moveToAssigned'];
							}
						]
					]
				]); ?>

				<div>
					<?php echo "<b>Showing " . ($unassignedPagination->getOffset() + 1) . " to " .($unassignedPagination->getOffset() + $unassignedPagination->getPageSize()) . " of " . $unassignedPagination->totalCount . " entries</b>" ?>
				</div>
			</div>
			<input type="hidden" value=<?php echo $project->ProjectID;?> name="projectID" id="projectID">
		<?php Pjax::end() ?>
		<?php Pjax::begin(['id' => 'projectGridViewAssigned', 'timeout' => false]) ?>
			<div class="col-sm-6">
				<?= GridView::widget([
					'id' => 'assignedGV',
					'dataProvider' => $dataProviderAssigned,
					'export' => false,
					'pjax' => false,
					'floatHeader' => true,
					'summary' => '',
					'columns' => [
						[
							'label' => 'Name',
							'attribute' => 'content',
						],
						[
							'header' => 'Unassign User',
							'class' => 'kartik\grid\CheckboxColumn',
							'contentOptions' => [],
							'checkboxOptions' => function ($model, $key, $index, $column) {
								return ['userID' => $key,'disabled' => false,'class' => 'moveToUnAssigned'];
							}
						]
					],
					'floatOverflowContainer' => true,
				]); ?>
				<br>
				<div>
					<?php echo "<b>"."Showing " . ($assignedPagination->getOffset() + 1) . " to " .($assignedPagination->getOffset() + $assignedPagination->getPageSize()) . " of " . $assignedPagination->totalCount . " entries</b>" ?>
				</div>
			</div>
			<input type="hidden" value=<?php echo $project->ProjectID;?> name="projectID" id="projectID">
		<?php Pjax::end() ?>
	</div>
	<div class="form-group">
		<?= Html::Button( 'Submit', ['class' => 'btn btn-success','id' => 'projectAddUserSubmitBtn']) ?>
		<?= Html::resetButton('Reset', ['class' => 'btn btn-default','id' => 'projectAddUserResetBtn']) ?>
	</div>
</div>
