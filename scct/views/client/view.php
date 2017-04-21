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
                'value' => function($model, $widget) {
                    return Html::a($model->CreatedUserName, ['user/view', 'id' => $model->CreatedUserID]);;
                },
                'format' => 'html'
            ],
            'ClientModifiedDate',
            [
                'label' => 'Client Last Modified By',
                'value' => function($model, $widget) {
                    if($model->ModifiedUserID != 0) {
                        return Html::a($model->ModifiedUserName, ['user/view', 'id' => $model->ModifiedUserID]);;
                    } else {
                        return "Not modified since created";
                    }
                },
                'format' => 'html'
            ],
        ],
    ]) ?>

</div>
