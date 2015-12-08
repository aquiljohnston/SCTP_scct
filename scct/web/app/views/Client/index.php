<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ClientSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Clients';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="client-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Client', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'ClientID',
            'ClientName',
            'ClientContactTitle',
            'ClientContactFName',
            'ClientContactMI',
            // 'ClientContactLName',
            // 'ClientPhone',
            // 'ClientEmail:email',
            // 'ClientAddr1',
            // 'ClientAddr2',
            // 'ClientCity',
            // 'ClientState',
            // 'ClientZip4',
            // 'ClientTerritory',
            // 'ClientActiveFlag',
            // 'ClientDivisionsFlag',
            // 'ClientComment',
            // 'ClientCreateDate',
            // 'ClientCreatorUserID',
            // 'ClientModifiedDate',
            // 'ClientModifiedBy',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
