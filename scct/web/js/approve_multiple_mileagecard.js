/**
 * Created by avicente on 3/17/2016.
 */

$('#multiple_mileage_card_approve_btn').click(function(){
    var model = $(this).attr('model');
    var pks = $('#w1').yiiGridView('getSelectedRows');
    if (!pks || 0 !== pks.length) {
        $.ajax({
            type: 'POST',
            url: 'index.php?r=mileage-card/approve-multiple',
            data: {mileageCardId: pks},
            success: function(data) {
                $.pjax.reload({container:'#w0'});
            }
        });
    } else {
        return false;
    }
});
