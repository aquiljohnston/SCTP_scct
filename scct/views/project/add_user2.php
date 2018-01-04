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

<div class="project-add-user" style="margin-top: 2%;">
    <h1 class="title"><?= Html::encode($this->title) ?></h1>

	<p>
		<?= Html::a('Back', ['view' , 'id' => $project->ProjectID], ['class' => 'btn btn-primary']) ?>
	</p>

	<div class="row">
	</div>

		<div class="row">
     
		<div id="unassignedTable">
        <div id="unassignedTableGrid">
   			<div class="col-sm-6">
            <?= GridView::widget([
                'id' 						=> 'unAssignedGV',
                'dataProvider' 				=> $dataProviderUnassigned,
                'export' 					=> false,
                'pjax' 						=> false,
                'floatHeader' 				=> true,
                'summary' 					=> '',
                'floatOverflowContainer' 	=> true,
                'columns' => [

                    [
                        'label' 	=> 'Name',
                        'attribute' => 'content',
                    ],
                    [
                        'header' 			=> 'Assign User',
                        'class' 			=> 'kartik\grid\CheckboxColumn',
                        'contentOptions' 	=> [],
                        'checkboxOptions' 	=> function ($model, $key, $index, $column) {

                            return ['userID' => $key,'disabled' => false,'class' => 'moveToAssigned'];
                        }
                    ]
                ]

            ]  
            
              ); ?>
          </div>
        </div>
    </div>

    	   <div id="assignedTable">
        <div id="assignedTableGrid">
   			<div class="col-sm-6">
            <?= GridView::widget([
                'id' 				=> 'assignedGV',
                'dataProvider' 		=> $dataProviderAssigned,
                'export' 			=> false,
                'pjax' 				=> false,
                'floatHeader' 		=> true,
                'summary' 			=> '',
                'columns' 			=> [

                    [
                        'label' 	=> 'Name',
                        'attribute' => 'content',
                    ],
                    [
                        'header' 	=> 'Unassign User',
                        'class' 	=> 'kartik\grid\CheckboxColumn',
                        'contentOptions' => [],
                        'checkboxOptions' => function ($model, $key, $index, $column) {

                            return ['userID' => $key,'disabled' => false,'class' => 'moveToUnAssigned'];
                        }
                    ]
                ],
                'floatOverflowContainer' => true,
            ]); ?>
          </div>
        </div>
    </div>




		</div>
		<input type="hidden" value=<?php echo $project->ProjectID;?> name="projectID" id="projectID">
 
	<div class="form-group">
		<?= Html::Button( 'Submit', ['class' => 'btn btn-success','id' => 'projectAddUserSubmitBtn']) ?>
		<?= Html::resetButton('Reset', ['class' => 'btn btn-default','id' => 'projectAddUserResetBtn']) ?>
	</div>
</div>
