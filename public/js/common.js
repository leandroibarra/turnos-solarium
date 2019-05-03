/**
 * Generate message implementing Bootstrap Notify plugin.
 *
 * @param string psMessage
 * @param string psType OPTIONAL
 * @param boolean pbAllowDismiss OPTIONAL
 */
function showNotify(psMessage, psType='info', pbAllowDismiss=false) {
    var sType = '';

    switch (psType.toLowerCase()) {
        case 'primary':
        case 'secondary':
        case 'info':
        case 'light':
        case 'dark':
        case 'success':
        case 'warning':
            sType = psType.toLowerCase();
            break;
        case 'danger':
        default:
            sType = 'danger';
            break;
    }

    jQuery.notify(
        {
            message: psMessage
        },
        {
            type: sType,
            allow_dismiss: pbAllowDismiss,
            mouse_over: 'pause',
            placement: {
                from: 'top',
                align: 'center'
            },
            animate: {
                enter: 'animated fadeInDown',
                exit: 'animated fadeOutUp'
            },
            template: '' +
                '<div data-notify="container" class="col-xs-11 col-sm-4 px-3 alert alert-{0}" role="alert">' +
                '	<button type="button" class="close" data-dismiss="alert" data-notify="dismiss">' +
                '		<small aria-hidden="true">&times;</small>' +
                '	</button>' +
                '	<span data-notify="title">{1}</span>' +
                '	<span data-notify="message">{2}</span>' +
                '	<div class="progress" data-notify="progressbar">' +
                '		<div class="progress-bar progress-bar-{0}" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%;"></div>' +
                '	</div>' +
                '</div>'
        }
    );
}