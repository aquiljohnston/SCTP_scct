<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\helpers\Url;
use yii\widgets\LinkPager;
use yii\widgets\Pjax;
use kartik\form\ActiveForm;
use yii\bootstrap\Modal;
use app\assets\UserAsset;

//register assets
UserAsset::register($this);

/* @var $this yii\web\View */
/* @var $searchModel app\models\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'User Management';
$this->params['breadcrumbs'][] = $this->title;
$pageSize = ["50" => "50", "100" => "100", "200" => "200"];
$column = [
    [
        'label' => 'Username',
        'attribute' => 'UserName',
        'headerOptions' => ['class' => 'text-center'],
        'contentOptions' => ['class' => 'text-center'],
    ],
    [
        'label' => 'First Name',
        'attribute' => 'UserFirstName',
        'headerOptions' => ['class' => 'text-center'],
        'contentOptions' => ['class' => 'text-center'],
    ],
    [
        'label' => 'Last Name',
        'attribute' => 'UserLastName',
        'headerOptions' => ['class' => 'text-center'],
        'contentOptions' => ['class' => 'text-center'],
    ],
    [
        'label' => 'Job Title',
        'attribute' => 'UserEmployeeType',
        'headerOptions' => ['class' => 'text-center'],
        'contentOptions' => ['class' => 'text-center'],
    ],
];
//build action column based on permissions
if($canDeactivate){
	$actionColumn  = [
		'class' => 'kartik\grid\ActionColumn',
		'template' => '{update} {delete}',
		'urlCreator' => function ($action, $model, $key, $index) {
			if ($action === 'update') {
				$url = '/user/update?username=' . $model["UserName"];
				return $url;
			}
			if ($action === 'delete') {
				$url = '/user/Deactivate?username=' . $model["UserName"];
				return $url;
			}
		},
		'buttons' => [
			'delete' => function ($url, $model, $key) {
				$url = '/user/deactivate?username=' . $model["UserName"];
				$options = [
					'title' => Yii::t('yii', 'Deactivate'),
					'aria-label' => Yii::t('yii', 'Deactivate'),
					'data-confirm' => Yii::t('yii', 'Are you sure you want to deactivate this user?'),
					'data-method' => 'Put',
					'data-pjax' => '0',
				];
				return Html::a('<span class="glyphicon glyphicon-trash"></span>', $url, $options);
			},
		]
	];
}else{
	$actionColumn  = [
		'class' => 'kartik\grid\ActionColumn',
		'template' => '{update}',
		'urlCreator' => function ($action, $model, $key, $index) {
			if ($action === 'update') {
				$url = '/user/update?username=' . $model["UserName"];
				return $url;
			}
		}
	];
}
//append action column
$column[] = $actionColumn;
?>
<div class="user-index">
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
    <div class="user_filter">
        <div id="userDropdownContainer">
            <?php $form = ActiveForm::begin([
                'type' => ActiveForm::TYPE_HORIZONTAL,
                'formConfig' => ['deviceSize' => ActiveForm::SIZE_SMALL],
                'method' => 'get',
                'options' => [
                    'id' => 'UserForm',
                ],
				'fieldConfig' => ['template' => "{label}<div>{input}</div>"],
                'action' => Url::to(['user/index'])
            ]); ?>

            <div class="row" style="margin-left: 0; margin-bottom: 1vh">
                <h3 class="title" style="float: left;"><?= Html::encode($this->title) ?></h3>
                <div id="userPageSizeLabel" class="col-sm-1 col-md-1 col-lg-1">
                    <?= $form->field($model, 'pageSize')->dropDownList($pageSize, ['value' => $model->pageSize, 'id' => 'userPageSize'])
						->label('Records Per Page', [
                            'class' => 'recordsPerPage'
                        ]); ?>
                </div>
            </div>
            <div class="row" style="margin-left: 0;">
                <div id="reactivateButtonUser" class="col-sm-1 col-md-1 col-lg-1" style="float:right;padding-right: 0;padding-left: 0;margin-left: 2%;">
                    <?php echo Html::button('Reactivate', ['class' => 'btn btn-primary reactivate_btn', 'id' => 'reactivateButton']); ?>
                </div>
                <div class="col-sm-1 col-md-1 col-lg-1" style="float:right;padding-left: 0;padding-right: 0;">
                    <?php echo Html::button('Add/Remove Users', ['class' => 'btn btn-success', 'id' => 'addUserButton']);?>
                </div>
                <div id="userButtons" class="col-sm-1 col-md-1 col-lg-1" style="float:right;padding-left: 0;padding-right: 0; margin-right: 2%;">
                    <?php
						if($canCreate){
							echo Html::a('Create User', ['create'], ['class' => 'btn btn-success', 'id' => 'createUserButton']);
						}
					?>
                </div>
				<div class="col-sm-1 col-md-1 col-lg-1" style="float:right;">
					<?php echo Html::img('@web/logo/filter_clear_black.png', ['id' => 'searchCleanFilterButton', 'style' => 'float: left']) ?>
				</div>
                <div id="userFilter" class="col-sm-2 col-md-2 col-lg-2" style="float:right;">
                    <?= $form->field($model, 'filter')->textInput(['placeholder'=>'Search', 'id' => 'userSearchFilter'])->label(false); ?>
                </div>
				<?php if($showProjectDropdown){ ?>
					<div id="userProjectDropdown" class="col-sm-2 col-md-2 col-lg-2" style="float:right; margin-right:2%;">
						<?=
							$form->field($model, 'projectID')->dropDownList($projectDropdown,
							['value' => $model->projectID, 'id'=>'userProjectFilterDD'])->label(false); 
						?>
					</div>
					<label class = 'control-label' style="float:right; margin-right: 1%">Project<label/>
				<?php } ?>
                <?php Pjax::begin(['id' => 'reactivateBtnPjax', 'timeout' => false]) ?>
                <?php Pjax::end() ?>
            </div>
            <input id="UserManagementPageNumber" type="hidden" name="UserManagementPageNumber" value="<?= $page ?>" />
            <?php ActiveForm::end(); ?>
        </div>
    </div>
    <div id="userGridViewContainer">
        <div id="userGV" class="userForm">
            <?php Pjax::begin(['id' => 'userGridview', 'timeout' => false]) ?>
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'export' => false,
                'pjax' => true,
                'summary' => '',
                'columns' => $column
            ]); ?>
            <div id="UserPagination" class="UserPagination">
                <?php
                echo LinkPager::widget([
                    'pagination' => $pages,
                ]);
                ?>
            </div>
            <div class="GridviewTotalNumber">
                <?php echo "Showing " . ($pages->offset + 1) . " to " . ($pages->offset + $pages->getPageSize()) . " of " . $pages->totalCount . " entries"; ?>
            </div>
            <?php Pjax::end() ?>
        </div>
    </div>
    <?php
    Modal::begin([
        'header' => '<h4>Reactivate Users</h4>',
        'id' => 'reactivateUserModal',
    ]);?>
    <div id='modalReactivateUser'>Loading...</div>
    <?php 
        Modal::end();
    ?>
    <!-- AddRemoveUserFromProject Modal -->
    <?php
    Modal::begin([
        'header' => '<h4>Select Project</h4>',
        'id' => 'AddRemoveUserFromProject',
    ]);?>
    <!-- Modal content-->
    <div id='modalAddRemoveUserFromProjectBody' class="modal-body">
        <?php 
            foreach($addUserProjects as $row) {
                echo '<p style="text-align: center; margin-right: 2%;">' 
                    . Html::a($row['ProjectName'] . '(' .$row['ProjectReferenceID'] . ')', ['project/add-user?id='.$row['ProjectID']], ['class' => 'btn btn-success', 'id' => 'createUserButton']) 
                    . '</p>';
            }
        ?>
    </div>
    <?php 
        Modal::end();
    ?>
</div>
