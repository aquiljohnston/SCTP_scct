<?php
/**
 * Created by PhpStorm.
 * User: tzhang
 * Date: 10/30/2017
 * Time: 5:11 PM
 */
use yii\helpers\Url;
$this->registerCss("#top-wrapper { display:none } .copyright-section{display:none} .footerabove {display:none} .wrap > .container{padding: 60px 15px 20px;}");
?>
<div class="imageContainer">
    <img src="<?= Url::to('/../images/' . $Photo1Path) ?>" class="img-responsive showImage">
</div>
