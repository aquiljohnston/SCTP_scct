<?php

/* @var $this yii\web\View */
/* @var $name string */
/* @var $message string */
/* @var $exception Exception */

use yii\helpers\Html;

$this->title = $name;
?>
<div class="site-error">

    <h1 class="title"><?= Html::encode($this->title) ?></h1>

    <div class="alert alert-danger">
        <?= nl2br(Html::encode($message)) ?>
    </div>

    <p>
        The above error occurred while the Web server was processing your request.
    </p>
    <p>
        Please contact us if you think this is a server error. Thank you.
    </p>

</div>
<?php /* this is a dirty, dirty hack and should be replaced with something more substantial */ ?>
<script type="text/javascript">
    var redirect = <?php if(strpos($this->title, '401' ) !== false) echo "true"; else echo "false"; ?>;
    if(redirect) {
        window.location.href="/login/user-logout";
    }
</script>
