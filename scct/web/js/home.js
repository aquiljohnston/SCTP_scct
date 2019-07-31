$(function () {
    // notification read listener
    $(document).off('click', '#homeNotificationWidget-container .glyphicon').on('click', '#homeNotificationWidget-container .glyphicon', function (e) {
        sendNotificationReadUpdate(this);
    });
});

function sendNotificationReadUpdate(button) {
	$dataKey = $(button).closest('tr').data('key');
	$.ajax({
		type: 'POST',
		url: '/home/set-notification-read',
		data: {
			params: $dataKey
		}
	});
}