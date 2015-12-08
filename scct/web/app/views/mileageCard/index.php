<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\MileageCardSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Mileage Cards';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="mileage-card-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Mileage Card', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'MileageCardID',
            'MileageCardEmpID',
            'MileageCardTechID',
            'MileageCardProjectID',
            'MileageCardType',
            // 'MileageCardAppStatus',
            // 'MileageCardCreateDate',
            // 'MileageCardCreatedBy',
            // 'MileageCardModifiedDate',
            // 'MileageCardModifiedBy',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
