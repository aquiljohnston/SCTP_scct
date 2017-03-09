<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\LoginForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\widgets\Pjax;
use kartik\widgets\Spinner;

$this->title = 'Login';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-login" id="login_header">
	<div id="logincontainer">
		<div id="loginbox">
			<?php echo Html::img('@web/logo/background.png') ?>	
		</div>
		
		<div class="login_logo">
			<a href=""><?php echo Html::img('@web/logo/SCLogo-white-lettering.png') ?></a>	
		</div>

		<?php yii\widgets\Pjax::begin(['id' => 'loginForm']) ?>
			<?php $form = ActiveForm::begin([
				'id' => 'login-form',
				'options' => ['class' => 'form-horizontal'],
				'fieldConfig' => [
					'template' => "{label}\n<div class=\"col-sm-6 col-lg-offset-1 col-lg-8\">{input}</div>\n<div class=\"col-sm-12 col-lg-12\">{error}</div>",
				],
			]); ?>

				<?= $form->field($model, 'username')->textInput(['placeholder'=>'Username', 'id' => 'username']) ?>

				<?= $form->field($model, 'password')->passwordInput(['placeholder'=>'Password', 'id' => 'password', 'onkeyup' => 'enterKeyPress(event);']) ?>
				
				<?php if($loginError): ?>
				<div class="alert alert-warning">
					Incorrect username or password.
				</div>
				<?php endif; ?>
				<div class="form-group">
					<div class="col-lg-12">
						<?= Html::button('Login', ['class' => 'btn btn-primary', 'name' => 'login-button', 'id' => 'loginButton', 'onclick' => 'getLocation();']) ?>
					</div>
				</div>
			<?php ActiveForm::end(); ?>
			
			<div id="loginLoading">
				<div id="loading-image"><?= Spinner::widget(['preset' => 'medium', 'color' => 'black']);?></div>
				<div class="clearfix"></div>
			</div>
		<?php yii\widgets\Pjax::end();?>
		
	</div>
		<!--<div class="col-lg-offset-1" style="color:#999;">
			You may login with <strong>admin/admin</strong> or <strong>demo/demo</strong>.<br>
			Ref. code <code>app\models\User::$users</code>.
		</div>-->
</div>
<script type="text/javascript">
	localStorage.clear();

	var geoLocationKeys = [];
	var geoLocationData = [];
	var geoLocationError = "";
	var keyPressed = 0;
	
	function enterKeyPress(event){
		var x = event.keyCode;
		if(x == 13 && keyPressed == 0 ) {
			getLocation();
			keyPressed++;
			window.setTimeout(function() { keyPressed = 0; }, 3000 );
		}
	}
	function getLocation() {		
		var loginHeight = document.getElementById('login-form').offsetHeight;
		document.getElementById('loginLoading').style.height = loginHeight+'px';
		document.getElementById('loginButton').disabled = true;		
		document.getElementById('loginLoading').style.display = "block";
		if (navigator.geolocation) {		
				navigator.geolocation.getCurrentPosition(showPosition, showError);
		} else { 
			geoLocationError = "Geolocation is not supported by this browser.";
		}
	}
	
	function showPosition(position) {
		//create array of geo location data
		geoLocationData.push(position.coords.latitude);
		geoLocationData.push(position.coords.longitude);
		geoLocationData.push(position.coords.accuracy);
		geoLocationData.push(position.coords.altitude);
		geoLocationData.push(position.coords.altitudeAccuracy);
		geoLocationData.push(position.coords.heading);
		geoLocationData.push(position.coords.speed);
		geoLocationData.push(position.timestamp);
		
		//send geolocation data
		$.pjax.reload({
			url: '/login',
			data: {GeoData: geoLocationData, 
				   username: $('#username').val(),
				   password: $('#password').val()					
				},
			container: "#loginForm",	
			type: 'POST',
			timeout: 99999
		});	
		geoLocationData = [];
	}

	function showError(error) {
		switch(error.code) {
			case error.PERMISSION_DENIED:
				geoLocationError = "User denied the request for Geolocation."
				break;
			case error.POSITION_UNAVAILABLE:
				geoLocationError = "Location information is unavailable."
				break;
			case error.TIMEOUT:
				geoLocationError = "The request to get user location timed out."
				break;
			case error.UNKNOWN_ERROR:
				geoLocationError = "An unknown error occurred."
				break;
		}
		//send error message
		$.pjax.reload({
			url: '/login',
			data: {GeoData: geoLocationError, 
				   username: $('#username').val(),
				   password: $('#password').val()
					
				},
			container: "#loginForm",	
			type: 'POST',
			timeout: 99999
		});		
	}
</script>