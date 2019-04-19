 <?php

use yii\helpers\Html;
use kartik\form\ActiveForm;
use kartik\grid\GridView;
use yii\widgets\Pjax;
use yii\helpers\Url;
use app\assets\ProjectAsset;

//register assets
ProjectAsset::register($this);

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
	<?php if($isAdmin) {
				echo Html::a('Back Project', ['view' , 'id' => $project->ProjectID], ['class' => 'btn btn-primary']); 
			} 
			echo Html::a('Back User Mgmt', ['/user'],['class' => 'btn btn-primary', 'style' => 'margin-left: .5%']);
		?>
	</p>
	

	<div class="row">
        <?php $form = ActiveForm::begin([
                'type' => ActiveForm::TYPE_VERTICAL,
                'formConfig' => ['showLabels' => false,'deviceSize' => ActiveForm::SIZE_SMALL],
                'options' => ['id' => 'projectUserForm'],
				'action' => Url::to(['project/add-user?id=' . $project->ProjectID])
            ]); ?>
     
            <div class="col-sm-2">
                <?= $form->field($model, 'uaFilter')->textInput(['value' => $unassignedFilterParams, 'id' => 'projectUserUnassignedFilter', 'style' => 'width:auto'])->label('Search'); ?>  
            </div>      
            <div class="col-sm-1">
                <img id="projectUserUnassignedFilterClear" class="projectUserClearFilterButton" src="/logo/filter_clear_black.png" alt="">
            </div> 
            <div class="col-sm-3" >
                <span id="unassignedTagCloud" style="display: none;" class="tagCloud"></span>
            </div>

            <div class="col-sm-2">
                <?= $form->field($model, 'aFilter')->textInput(['value' => $assignedFilterParams, 'id' => 'projectUserAssignedFilter', 'style' =>'width:auto'])->label('Search'); ?>       
            </div>
            <div class="col-sm-1">
                <img id="projectUserAssignedFilterClear" class="projectUserClearFilterButton" src="/logo/filter_clear_black.png" alt="">
            </div>
			<div class="col-sm-3" >
				<span id="assignedTagCloud" style="display: none;" class="tagCloud"></span>
            </div>
			
            <br>
		<?php ActiveForm::end(); ?>
	</div>

	<div class="row">
		<?php Pjax::begin(['id' => 'unassignedProjectUserGridView', 'timeout' => false]) ?>
			<div class="col-sm-6">
				<?= GridView::widget([
					'id' => 'unassignedProjectUserGV',
					'dataProvider' => $dataProviderUnassigned,
					'export' => false,
					'pjax' => false,
					'floatHeader' => true,
					'summary' => '',
					'floatOverflowContainer' => true,
					'columns' => [
						[
							'label' => 'Unassigned Users',
							'attribute' => 'content',
						],
						[
							'class' => 'kartik\grid\CheckboxColumn',
							'checkboxOptions' => function ($model, $key, $index, $column) {
								return [
									'userID' => $key,
									'disabled' => false,
									'class' => 'unAssignedUser'
								];
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
		<?php Pjax::begin(['id' => 'assignedProjectUserGridView', 'timeout' => false]) ?>
			<div class="col-sm-6">
				<?= GridView::widget([
					'id' => 'assignedProjectUserGV',
					'dataProvider' => $dataProviderAssigned,
					'export' => false,
					'pjax' => false,
					'floatHeader' => true,
					'summary' => '',
					'floatOverflowContainer' => true,
					'columns' => [
						[
							'label' => 'Assigned Users',
							'attribute' => 'content',
						],
						[
							'class' => 'kartik\grid\CheckboxColumn',
							'checkboxOptions' => function ($model, $key, $index, $column) {
								return [
									'userID' => $key,
									'disabled' => false,
									'class' => 'assignedUser'
								];
							}
						]
					],
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
		<?= Html::Button( 'Submit', ['class' => 'btn btn-success','id' => 'projectAddUserSubmitBtn', 'disabled' => 'disabled']) ?>
		<?= Html::resetButton('Reset', ['class' => 'btn btn-default','id' => 'projectAddUserResetBtn', 'disabled' => 'disabled']) ?>
	</div>
</div>
