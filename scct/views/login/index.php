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

        <?= $form->field($model, 'username')->textInput(['placeholder' => 'Username', 'id' => 'username']) ?>

        <?= $form->field($model, 'password')->passwordInput(['placeholder' => 'Password', 'id' => 'password', 'onkeypress' => "enterKeyPress(event)"]) ?>

        <?php if ($loginError): ?>
            <div class="alert alert-warning">
                Incorrect username or password.
            </div>
        <?php endif; ?>
        <div class="form-group">
            <div class="col-lg-12">
                <?= Html::button('Login', ['class' => 'btn btn-primary', 'name' => 'login-button', 'id' => 'loginButton', 'onclick' => 'PostLoginForm();']) ?>
            </div>
        </div>
        <?php ActiveForm::end(); ?>

        <div id="loginLoading">
            <div id="loading-image"><?= Spinner::widget(['preset' => 'medium', 'color' => 'black']); ?></div>
            <div class="clearfix"></div>
        </div>
        <?php yii\widgets\Pjax::end(); ?>

    </div>
    <!--<div class="col-lg-offset-1" style="color:#999;">
        You may login with <strong>admin/admin</strong> or <strong>demo/demo</strong>.<br>
        Ref. code <code>app\models\User::$users</code>.
    </div>-->
</div>
<script type="text/javascript">
    localStorage.clear(); // Clear out the menus in case the user's session expired and they didn't hit the logout button

    var keyPressed = 0;
    function PostLoginForm() {
        var form = $('login-form');
        $('#loading').show();
        $.pjax.reload({
            url: form.attr('action'),
            data: {

                username: $('#username').val(),
                password: $('#password').val()
            },
            container: "#loginForm",
            type: 'POST',
            timeout: 99999
        }).done(function () {
            $('#loading').hide();
        });
    }

    function enterKeyPress(event) {
        var x = event.keyCode;
        if (x === 13) {
            PostLoginForm();
            keyPressed++;
            window.setTimeout(function () {
                keyPressed = 0;
            }, 3000);
        }
    }
</script>