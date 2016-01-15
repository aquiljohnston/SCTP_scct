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

	<div class="wrap">
		<!--start-header-->
		<div class="header navbar-fixed-top" id="top-wrapper">
			<div class="container" id="top-header">
				<div class="logo">
		
				</div>	
				<div class="login-info">
				
				</div>
				<div class="logout">
					<input type='button' value='LOGOUT' id='logout_btn'>
				</div>
			</div>
			<div class="container">
			<?php if (Yii::$app->user->isGuest){?>
					<div class="loginMenu sc_megamenu"></div>
			<?php }else{?>
			<?php $userRole = Yii::$app->authManager->getRolesByUser(Yii::$app->session['userID']);?>
			<?php $role = current($userRole);?>
			<?php Yii::Trace("Session userID is : ".$role->name);?>
			<?php if(($role->name) == "admin"){?>
					<div class="adminMenu sc_megamenu"></div>
			<?php }else if (($role->name) == "projectManager"){ ?>
					<div class="menu sc_megamenu"></div>
			<?php }else{ ?>
					<div class="menu sc_megamenu"></div>
				<?php }?>
			<?php }?>
			</div>
		</div>
		<!--//End-header-->
		
		<div class="container">
			
			<?= $content ?>
		</div>

	</div>

	<!-- Start Copyright Section -->
	<div class="container">
		<div class="row">
			<div class="col-md-12">
				<div class="copyright-section">

				</div>

			</div>
		</div>
	</div>
	<!-- End Copyright Section -->

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
