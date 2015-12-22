<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\TimeCardSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Time Cards';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="time-card-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Time Card', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            //'TimeCardID',
            'TimeCardStartDate',
            'TimeCardEndDate',
            'TimeCardHoursWorked',
            //'TimeCardProjectID',
            //'TimeCardTechID',
            'TimeCardApproved:datetime',
            'TimeCardSupervisorName',
            'TimeCardComment',
            'TimeCardCreateDate',
            //'TimeCardCreatedBy',
            //'TimeCardModifiedDate',
            //'TimeCardModifiedBy',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
