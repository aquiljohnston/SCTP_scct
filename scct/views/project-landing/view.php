<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Project Landing';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="projectlanding-index">

    <h3><?= Html::encode($this->title) ?></h3>

    <!-- Table for Projects -->
    <?= GridView::widget([
        //'id' => 'equipmentWidget',
        'dataProvider' => $singleprojectProvider,
        //'layout' => "{items}\n{pager}",
        //'caption' => 'All Projects',

        'columns' => [
			['class' => 'yii\grid\SerialColumn'],
            'ProjectID',
			'ProjectDescription',
			'ProjectNotes',
			'ProjectType',
			'ProjectStatus',
			'ProjectStartDate',
			'ProjectEndDate',
			'ProjectCreateDate',
			'ProjectCreateBy',

        ],
    ]); ?>

</div>
