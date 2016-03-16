
<?php

use Yii;
use yii\helpers\Html;

/**
 * Created by PhpStorm.
 * User: avicente
 * Date: 3/10/2016
 * Time: 1:24 PM
 */

?>
<script type="text/javascript">
    alert("Did Andre make it anywhere???");
    $(document).ready(function () {
        var token = <?php Yii::$app->session["token"]?>;
        $.ajax({
            type: "GET",
            url: "http://api.southerncrossinc.com/index.php?r=project%2Fget-all",
            dataType: "JSON",
            beforeSend: function(xhr) {
                alert(token);
                xhr.setRequestHeader("Authorization", 'Basic '+btoa(token));
            },
            failure: function (data) {
                alert(data);
            },
            success: function (data) {
                alert("Hello");
                var projectDropdown = $('#projects_dropdown');
                JSON.parse(data); //Everything works now!!
                $.each(data, function (i, item) {
                    projectDropdown.append("<li><a data-description='Image Animation'" +
                        " href='http://scct.southerncrossinc.com/index.php?r=project%2Fview&id='" + item['ProjectID'] + ">" + item['ProjectName'] + "</a></li>");
                });
                projectDropdown.append("</ul></li>");
            }
        });
    });
</script>

