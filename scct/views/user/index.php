<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\helpers\Url;
use yii\widgets\LinkPager;
use yii\widgets\Pjax;
use kartik\form\ActiveForm;
use yii\bootstrap\Modal;

/* @var $this yii\web\View */
/* @var $searchModel app\models\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'User Management';
$this->params['breadcrumbs'][] = $this->title;
$pageSize = ["50" => "50", "100" => "100", "200" => "200"];
$column = [

    //'UserID',
    [
        'label' => 'Username',
        'attribute' => 'UserName',
    ],
    [
        'label' => 'First Name',
        'attribute' => 'UserFirstName',
    ],
    [
        'label' => 'Last Name',
        'attribute' => 'UserLastName',
    ],
    [
        'label' => 'Role Type',
        'attribute' => 'UserAppRoleType',
    ],
    // 'UserEmployeeType',
    // 'UserPhone',
    // 'UserCompanyName',
    // 'UserCompanyPhone',
    // 'UserAppRoleType',
    // 'UserComments',
    // 'UserKey',
    // 'UserActiveFlag',
    // 'UserCreatedDate',
    // 'UserModifiedDate',
    // 'UserCreatedBy',
    // 'UserModifiedBy',
    // 'UserCreateDTLTOffset',
    // 'UserModifiedDTLTOffset',
    // 'UserInactiveDTLTOffset',

    ['class' => 'kartik\grid\ActionColumn',
        'urlCreator' => function ($action, $model, $key, $index) {
            if ($action === 'view') {
                $url = '/user/view?id=' . $model["UserID"];
                return $url;
            }
            if ($action === 'update') {
                $url = '/user/update?id=' . $model["UserID"];
                return $url;
            }
            if ($action === 'delete') {
                $url = '/user/Deactivate?id=' . $model["UserID"];
                return $url;
            }
        },
        'buttons' => [
            'delete' => function ($url, $model, $key) {
                $url = '/user/deactivate?id=' . $model["UserID"];
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
    ],
];
?>
<div class="user-index">
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
	<div class="user_filter">
		<div id="userDropdownContainer">
			<?php $form = ActiveForm::begin([
				'type' => ActiveForm::TYPE_HORIZONTAL,
				'formConfig' => ['labelSpan' => 7, 'deviceSize' => ActiveForm::SIZE_SMALL],
				'method' => 'get',
				'options' => [
					'id' => 'UserForm',
				],
				'action' => Url::to(['user/index'])
			]); ?>

            <div class="row" style="margin-left: 0;">
                <h3 class="title" style="float: left;"><?= Html::encode($this->title) ?></h3>
                <label id="userPageSizeLabel" class="col-sm-4 col-md-4 col-lg-4">
                    <?= $form->field($model, 'pagesize')->dropDownList($pageSize, ['value' => $userPageSizeParams, 'id' => 'userPageSize'])->label(""); ?>
                </label>
            </div>
            <div class="row" style="margin-left: 0;">
                <div id="reactivateButtonUser" class="col-sm-1 col-md-1 col-lg-1" style="float:right;padding-right: 0;padding-left: 0;margin-left: 2%;">
                    <?php echo Html::button('Reactivate', ['class' => 'btn btn-primary reactivate_btn', 'id' => 'reactivateButton']); ?>
                </div>
                <div id="userButtons" class="col-sm-1 col-md-1 col-lg-1" style="float:right;padding-left: 0;padding-right: 0;">
                    <?= Html::a('Create User', ['create'], ['class' => 'btn btn-success', 'id' => 'createUserButton']) ?>
                </div>
                <label id="userFilter" class="col-sm-7 col-md-7 col-lg-7" style="float:right;">
                    <?= $form->field($model, 'filter')->textInput(['placeholder'=>'Search'])->label(''); ?>
                </label>
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
                'bootstrap' => false,
                'export' => false,
                'pjax' => false,
                'summary' => '',
                'columns' => $column
            ]); ?>
            <div id="UserPagination">
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
</div>
