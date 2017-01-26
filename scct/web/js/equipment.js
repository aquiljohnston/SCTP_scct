$(function () {
    var jqEquipmentDropDowns = $('#equipmentDropdownContainer');
    var jqEquipmentPageSize = jqEquipmentDropDowns.find('#equipmentPageSize');

    jqEquipmentPageSize.on('change', function (event) {
        reloadGridView();
        event.preventDefault();
        return false;
    });

    $(document).off('click', "#equipmentPagination ul li a").on('click', "#equipmentPagination ul li a", function () {
        $('#loading').show();
        $('#equipmentGridview').on('pjax:success', function () {
            $('#loading').hide();
        });
    });

    function reloadGridView() {
        var form = jqEquipmentDropDowns.find("#equipmentForm");
        if (form.find(".has-error").length) {
            return false;
        }
        $('#loading').show();
        $.pjax.reload({
            type: 'POST',
            url: form.attr("action"),
            container: '#equipmentGridview', // id to update content
            data: form.serialize(),
            timeout: 99999
        }).done(function () {
            $('#loading').hide();
        });
    }
});
