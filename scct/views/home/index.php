<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Home';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-index">

    <h3><?= Html::encode($this->title) ?></h3>
    <center><h1>Hello <?=$firstName; $lastName;?></h1></center>

    <!-- Table for Unaccepted Equipment -->
    <?= GridView::widget([
        'dataProvider' => $equipmentProvider,
        'layout' => "{items}\n{pager}",
        'caption' => 'Unaccepted Equipment',

        'columns' => [
            'Name',
            'Serial Number',
            'Client Name',
            'Project Name',

            ['class' => 'yii\grid\ActionColumn',
                'header' => 'View Equipment',
                'template' => '{view}',
                'urlCreator' => function ($action, $model, $key, $index) {
                    if ($action === 'view') {
                        $url ='index.php?r=equipment%2Fview&id='.$model["EquipmentID"];
                        return $url;
                    }
                }
            ],
        ],
    ]); ?>

    <!-- Table for Unapproved Time Cards -->
    <?= GridView::widget([
        'dataProvider' => $timeCardProvider,
        'layout' => "{items}\n{pager}",
        'caption' => 'Unapproved Time Cards',

        'columns' => [
            'UserFirstName',
            'UserLastName',
            'TimeCardStartDate',
            'TimeCardEndDate',

            ['class' => 'yii\grid\ActionColumn',
                'header' => 'View Time Card',
                'template' => '{view}',
                'urlCreator' => function ($action, $model, $key, $index) {
                    if ($action === 'view') {
                        $url ='index.php?r=time-card%2Fview&id='.$model["TimeCardID"];
                        return $url;
                    }
                }
            ],
        ],
    ]); ?>

    <!-- Table for Unapproved Mileage Cards -->
    <?= GridView::widget([
        'dataProvider' => $mileageCardProvider,
        'layout' => "{items}\n{pager}",
        'caption' => 'Unapproved Mileage Cards',

        'columns' => [
            'UserFirstName',
            'UserLastName',
            'MileageStartDate',
            'MileageEndDate',
            'MileageCardBusinessMiles',
            'MileageCardPersonalMiles',

            ['class' => 'yii\grid\ActionColumn',
                'header' => 'View Mileage Card',
                'template' => '{view}',
                'urlCreator' => function ($action, $model, $key, $index) {
                    if ($action === 'view') {
                        $url ='index.php?r=mileage-card%2Fview&id='.$model["MileageCardID"];
                        return $url;
                    }
                }
            ],
        ],
    ]); ?>

</div>
