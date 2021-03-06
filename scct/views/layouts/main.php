<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;
use kartik\widgets\Spinner;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<meta http-equiv="Pragma" content="no-cache">
	<meta http-equiv="no-cache">
	<meta http-equiv="Expires" content="-1">
	<meta http-equiv="Cache-Control" content="no-cache">
	<?= Html::csrfMetaTags() ?>
	<link rel="shortcut icon" href="<?= Yii::$app->request->baseUrl ?>/SC_star_logo.ico" type="image/x-icon" />
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
	<div id="loading">		
		<div id="loading-image"><?= Spinner::widget(['preset' => 'medium', 'color' => 'black']);?></div>
		<div class="clearfix"></div>
	</div>
<?php $this->beginBody() ?>
	<div class="wrap">
		<!--start-header-->
		<div class="header navbar-fixed-top" id="top-wrapper">
			<div class="container" id="top-header">
			
			<?php if(Yii::$app->user->isGuest){?>
				<div class="sc_logout_logo">	
				
				</div>	
			<?php }else{?>
				<div class="logo">
		
				</div>	
			<?php }?>
				<div class="login-info">
				
				</div>
			<?php if(Yii::$app->user->isGuest){?>
				<div class="logout_hide">
					
				</div>
			<?php }else{?>
				<div class="logout">
					<input type='button' value='LOGOUT' id='logout_btn'>
				</div>
			    <div id="UserInfo">Welcome, <?php echo Yii::$app->session['UserFirstName'];?><?php echo" "?><?php echo Yii::$app->session['UserLastName'];?></div>
			<?php }?>
			</div>
			<div class="container">
			<?php if (Yii::$app->user->isGuest): ?>
				<div class="loginMenu sc_megamenu"></div>
			<?php else:?>
				<div class="menu sc_megamenu" id="<?php echo Yii::$app->session['userID']; ?>"></div>
			<?php endif; ?>
			</div>
		</div>
		<!--//End-header-->
		
		<div class="container">
			
			<?= $content ?>
		</div>

	</div>
	<!--start footerabove -->
	<div class="footerabove">
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