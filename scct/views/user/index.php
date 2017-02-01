<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use app\controllers\User;
use yii\widgets\LinkPager;
use yii\widgets\Pjax;
use kartik\form\ActiveForm;

/* @var $this yii\web\View */
/* @var $searchModel app\models\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'User Management';
$this->params['breadcrumbs'][] = $this->title;
$pageSize = ["10" => "10", "25" => "25", "50" => "50", "100" => "100"];
?>
<div class="user-index">

    <h3 class="title"><?= Html::encode($this->title) ?></h3>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
    <div class="user_filter">
        <div id="userButtons">
            <?= Html::a('Create User', ['create'], ['class' => 'btn btn-success']) ?>
        </div>
        <div id="userDropdownContainer">
            <?php $form = ActiveForm::begin([
                'type' => ActiveForm::TYPE_HORIZONTAL,
                'formConfig' => ['labelSpan' => 7, 'deviceSize' => ActiveForm::SIZE_SMALL],
                'method' => 'post',
                'options' => [
                    'id' => 'UserForm',
                ]
            ]); ?>
            <label id="userPageSizeLabel">
                <?= $form->field($model, 'pagesize')->dropDownList($pageSize, ['value' => $userPageSizeParams, 'id' => 'userPageSize'])->label("Records Per Page"); ?>
            </label>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
    <div id="userGridViewContainer">
        <div id="userGV" class="userForm">
            <?php Pjax::begin(['id' => 'userGridview', 'timeout' => false]) ?>
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'bootstrap' => false,
                'export' => false,
                'pjax' => true,
                'summary' => '',
                'columns' => [

                    //'UserID',
                    [
                        'label' => 'Username',
                        'attribute' => 'UserName',
                        'filter' => '<input class="form-control" name="filterusername" value="' . Html::encode($searchModel['UserName']) . '" type="text">'
                    ],
                    [
                        'label' => 'First Name',
                        'attribute' => 'UserFirstName',
                        'filter' => '<input class="form-control" name="filterfirstname" value="' . Html::encode($searchModel['UserFirstName']) . '" type="text">'
                    ],
                    [
                        'label' => 'Last Name',
                        'attribute' => 'UserLastName',
                        'filter' => '<input class="form-control" name="filterlastname" value="' . Html::encode($searchModel['UserLastName']) . '" type="text">'
                    ],
                    [
                        'label' => 'Role Type',
                        'attribute' => 'UserAppRoleType',
                        'filter' => '<input class="form-control" name="filterroletype" value="' . Html::encode($searchModel['UserAppRoleType']) . '" type="text">'
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
                ],
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
</div>
