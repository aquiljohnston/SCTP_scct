<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<!-- Start Footer Section -->
<div class="row">
	<div class="col-md-12">
		<div id="footer_container">
			<div class="footer">
				<div class="row">
					<p>
				
					</p>

				</div>
				<div class="row">					
					<div class="col-md-4" id="address">
			
					</div>
		
					<div class="col-md-4 footer_links">

					</div>
		
					<div class="col-md-4 footer_social">

					</div>

				</div>
			</div>
			<!-- Start Copyright Section -->
			<div class="row">
				<div class="col-md-12">
					<div class="copyright-section">
			
					</div>

				</div>
			</div>
			<!-- End Copyright Section -->
		</div>
	</div>        
</div>
<!-- End Footer Section -->

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
