<?php
use yii\helpers\Url;
use app\assets\MileageCardAsset;

//register assets
MileageCardAsset::register($this);

$this->registerCss("#top-wrapper { display:none } .copyright-section{display:none} .footerabove {display:none} .wrap > .container{padding: 60px 15px 20px;}");
?>
<div class="imageContainer">
    <img src="<?= Url::to('/../images/' . $photoPath) ?>" class="img-responsive showImage">
</div>
