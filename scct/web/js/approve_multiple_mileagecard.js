/**
 * Created by avicente on 3/17/2016.
 */

// This function is called in this file and in mileage_card.js
function applyOnClickListeners() {
    // More efficient to select once and save selection
    var multipleMileageCardApproveButton = $('#multiple_mileage_card_approve_btn');
    var checkboxSelector = $("#mileageCardGV .kv-row-select input[type=checkbox]");
    var filters = $('.filters');

    //We turn the click listeners off so that we don't have duplicate events firing
    multipleMileageCardApproveButton.off("click");
    checkboxSelector.off("click");
    filters.off(".loadingIndicator"); // Namespace loadingIndicator

    // Add the listeners

    //Add it to the loadingIndicator namespace so we can get rid of it selectively to prevent duplicates.
    filters.on("change.loadingIndicator", function(e) {
        $('#loading').show();
        // Loading hide is in mileage_card.js
    });

    multipleMileageCardApproveButton.click(function (e) {
        var pks = $('#w0').yiiGridView('getSelectedRows');
        var confirmBox;
        if (pks.length == 1) {
            confirmBox = confirm('Are you sure you want to approve the selected mileage card?');
        } else {
            confirmBox = confirm('Are you sure you want to approve all ' + pks.length + ' selected mileage cards?');
        }
        if (confirmBox) {
            $('#loading').show();
            $.ajax({
                type: 'POST',
                url: '/mileage-card/approve-multiple',
                data: {mileageCardId: pks},
                success: function (data) {
                    $.pjax.reload({container: '#w0'});
                    $('#loading').hide();
                }
            });
        } else {
            e.stopImmediatePropagation();
            e.preventDefault();
        }
    });

    checkboxSelector.click(function () {
        var primaryKeys = $('#w0').yiiGridView('getSelectedRows');

        for (var i = 0; i < primaryKeys.length; i++) {

            //retrieve the approved value for the mileage card based off of current checkbox
            approved = $(".kv-row-select input[mileagecardid=" + primaryKeys[i] + "]").attr("approved");

            //retrieve the total mileage for the mileage card based off of current checkbox
            totalMileage = $(".kv-row-select input[mileagecardid=" + primaryKeys[i] + "]").attr("totalmileage");

            if ((approved == "Yes" || approved == "yes") || totalMileage < 0) {
                approved = "Yes";
                break;
            }
            //else {
            //     else continue
            //}
            // If adding code after this line in this loop consider uncommenting the above three lines. As of now it is unnecessary.
        }

        if (!primaryKeys || primaryKeys.length != 0 && approved != "Yes") {
            $('#multiple_mileage_card_approve_btn').prop('disabled', false); //TO ENABLE


        } else {
            $('#multiple_mileage_card_approve_btn').prop('disabled', true);
        }

    });
}
$(function () {

    var approved;
    var totalMileage;
    applyOnClickListeners();

    // this doesn't need to be done more than once because there is no pjax on the page this is on.

    // disable single approve button once user clicked it
    $('#enable_single_approve_btn_id_mileagecard').click(function (e) {
        $(this).addClass('disabled');
    });
    $('#multiple_mileage_card_approve_btn').prop('disabled', true); //TO DISABLE


});