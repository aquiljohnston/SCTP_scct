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

    <p>
        <?= Html::a('Home', ['index'], ['class' => 'btn btn-success', 'id' => 'home_btn']) ?>
    </p>

</div>
