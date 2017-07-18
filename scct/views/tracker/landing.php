<?php
/**
 * Created by PhpStorm.
 * User: jpatton
 * Date: 6/21/2017
 * Time: 4:04 PM
 */
use yii\bootstrap\Html;

$this->title = 'Tracker';
$this->params['breadcrumbs'][] = $this->title;

?>
<div id="trackerLanding">
    <h3 class="title"><?= Html::encode($this->title) ?></h3>
    <label for="mapGrid">Map Grid (In Progress or Completed): </label>
    <?php
    echo Html::dropDownList("mapGrid", null, $dropdown, []);
    ?>
</div>