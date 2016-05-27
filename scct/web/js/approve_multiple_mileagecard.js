/**
 * Created by avicente on 3/17/2016.
 */

$(function() {

    var approved;
    var totalMileage;

        $('#multiple_mileage_card_approve_btn').prop('disabled', true); //TO DISABLED
        $(".kv-row-select input[type=checkbox]").click(function(){
            var pks = $('#w0').yiiGridView('getSelectedRows');

            for(var i=0; i < pks.length; i++){

                //retrieve the approved value for the mileage card based off of current checkbox
                approved = $(".kv-row-select input[mileagecardid="+pks[i]+"]").attr("approved");

                //retrieve the total mileage for the mileage card based off of current checkbox
                totalMileage = $(".kv-row-select input[mileagecardid="+pks[i]+"]").attr("totalmileage");

                if((approved == "Yes" || approved == "yes") || totalMileage < 0){
                    approved = "Yes";
                    break;
                }else{
                    continue;
                }
            }

            if (!pks || pks.length != 0 && approved != "Yes"){
                $('#multiple_mileage_card_approve_btn').prop('disabled', false); //TO ENABLE

                // triggered when checkbox selected
                $('#multiple_mileage_card_approve_btn').click(function(){

                    $.ajax({
                        type: 'POST',
                        url: 'index.php?r=mileage-card/approve-multiple',
                        data: {mileageCardId: pks},
                        success: function(data) {
                            $.pjax.reload({container:'#w0'});
                        }
                    });
                });
            }else {
                $('#multiple_mileage_card_approve_btn').prop('disabled', true);
            }

        });
});

//
//    $('#multiple_mileage_card_approve_btn').click(function(){
//    var model = $(this).attr('model');
//    var pks = $('#w1').yiiGridView('getSelectedRows');
//    if (!pks || 0 !== pks.length) {
//        $.ajax({
//            type: 'POST',
//            url: 'index.php?r=mileage-card/approve-multiple',
//            data: {mileageCardId: pks},
//            success: function(data) {
//                $.pjax.reload({container:'#w0'});
//            }
//        });
//    } else {
//        return false;
//    }
//});
