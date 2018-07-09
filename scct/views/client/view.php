<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\client */

$this->title = $model->ClientName;
$this->params['breadcrumbs'][] = ['label' => 'Clients', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="client-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
		<?= Html::a('Back', ['index'], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Update', ['update', 'id' => $model->ClientID], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Deactivate', ['deactivate', 'id' => $model->ClientID], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to deactivate this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <div id="detailViewContainer" style="overflow-y: auto; max-height: 430px;">
        <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'ClientID',
			'ClientAccountID',
            'ClientName',
            'ClientContactTitle',
            'ClientContactFName',
            'ClientContactMI',
            'ClientContactLName',
            'ClientPhone',
            'ClientEmail:email',
            'ClientAddr1',
            'ClientAddr2',
            'ClientCity',
            'ClientState',
            'ClientZip4',
            'ClientTerritory',
            'ClientActiveFlag',
            //'ClientDivisionsFlag',
            'ClientComment',
            'ClientCreateDate',
            [
                'label' => 'Client Created By',
                'attribute' => 'CreatedUserName',
                'value' => call_user_func(function($model) {
                    return Html::a($model->CreatedUserName, ['user/view', 'username' => $model->CreatedUserName]);
                }, $model),
                'format' => 'html'
            ],
            'ClientModifiedDate',
            [
                'label' => 'Client Last Modified By',
                'attribute' => 'ModifiedUserName',
                'value' => call_user_func(function($model) {
                    return Html::a($model->ModifiedUserName, ['user/view', 'username' => $model->ModifiedUserName]);;
                }, $model),
                'format' => 'html'
            ],
        ],
    ]) ?>
    </div>

</div>
