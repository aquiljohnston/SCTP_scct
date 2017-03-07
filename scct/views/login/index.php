<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\LoginForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\widgets\Pjax;

$this->title = 'Login';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-login" id="login_header">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php if($loginError): ?>
        <div class="alert alert-warning">
            Incorrect username or password.
        </div>
    <?php endif; ?>
    <p>Please fill out the following fields to login:</p>

	<?php yii\widgets\Pjax::begin(['id' => 'loginForm']) ?>
    <?php $form = ActiveForm::begin([
        'id' => 'login-form',
        'options' => ['class' => 'form-horizontal'],
        'fieldConfig' => [
            'template' => "{label}\n<div class=\"col-sm-6 col-lg-3\">{input}</div>\n<div class=\"col-sm-8 col-lg-8\">{error}</div>",
            'labelOptions' => ['class' => 'col-sm-1 col-md-1 col-lg-1 control-label'],
        ],
    ]); ?>

        <?= $form->field($model, 'username')->textInput(['placeholder'=>'Username', 'id' => 'username']) ?>

        <?= $form->field($model, 'password')->passwordInput(['placeholder'=>'Password', 'id' => 'password']) ?>

        <div class="form-group">
            <div class="col-lg-offset-1 col-lg-11">
                <?= Html::button('Login', ['class' => 'btn btn-primary', 'name' => 'login-button', 'id' => 'loginButton', 'onclick' => 'getLocation(event);']) ?>
            </div>
        </div>

    <?php ActiveForm::end(); ?>
	<?php yii\widgets\Pjax::end();?>
</div>

<script type="text/javascript">
	localStorage.clear();

	var geoLocationKeys = [];
	var geoLocationData = [];
	var geoLocationError = "";

	function getLocation() {	
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