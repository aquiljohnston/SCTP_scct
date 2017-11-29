$(function(){
    var jqMileageCardFilter = $('#mileage_card_filter');
    var jqMCDropDowns = $('#mileageCardDropdownContainer');
    var jqDateSelection = jqMileageCardFilter.find('#mileageCardDateRange');
    var jqMCPageSize = jqMCDropDowns.find('#mileageCardPageSize');


    jqDateSelection.on('change', function (event) {
        event.preventDefault();
        var selected = $(this).find(":selected").val();
        if(selected == "other") {
            $('#datePickerContainer').css("display", "block");
        }else {
            $('#datePickerContainer').css("display", "none");

            reloadMileageCardGridView();
        }
    });

    jqMCPageSize.on('change', function (event) {
        $('#mileageCardPageNumber').val(1);
        reloadMileageCardGridView();
        event.preventDefault();
        return false;
    });

    $(document).off('click', "#MCPagination ul li a").on('click', "#MCPagination ul li a", function (event) {
        var page = $(this).data('page') + 1; // Shift by one to 1-index instead of 0-index.
        $('#mileageCardPageNumber').val(page);
        reloadMileageCardGridView();
        event.preventDefault();
        return false;
    });
	
	// mileagecard filter listener
    $(document).off('keypress', '#mileageCardFilter').on('keypress', '#mileageCardFilter', function (e) {
        if (e.keyCode === 13 || e.keyCode === 10) {
            e.preventDefault();
			$('#mileageCardPageNumber').val(1);
            reloadMileageCardGridView();
        }
    });

    $(document).off('click', '#mileageCardSearchCleanFilterButton').on('click', '#mileageCardSearchCleanFilterButton', function (){
        $('#mileageCardFilter').val("");
        reloadMileageCardGridView();
    });
    
    function reloadMileageCardGridView() {
        var form = jqMCDropDowns.find("#MileageCardForm");
        if (form.find(".has-error").length){
            return false;
        }
        $('#loading').show();
        $.pjax.reload({
            type: 'GET',
            url: form.attr("action"),
            container: '#mileageCardGridview', // id to update content
            data: form.serialize(),
            timeout: 99999
        });
        $('#mileageCardGridview').on('pjax:success', function () {
            $('#loading').hide();
            applyOnClickListeners();
        });
        $('#mileageCardGridview').on('pjax:error', function () {
            $('#loading').hide();
            location.reload();
        });
    }
});
