'use strict';

(function (document, Joomla) {
    document.addEventListener('DOMContentLoaded', function () {
        var modal = new tingle.modal({
            footer: true,
            stickyFooter: false,
            closeMethods: ['escape'],
            closeLabel: "Close"
        });

        modal.addFooterBtn('Close', Joomla.getOptions('btnClass', 'tingle-btn') + ' ' + Joomla.getOptions('btnPrimaryClass', 'tingle-btn--primary'), function () {
            modal.close();
        });

        var html5QrcodeScanner = new Html5QrcodeScanner("reader", { fps: 1 }, /* verbose= */false);

        var storage = window.sessionStorage;

        var checkInInterval =  Joomla.getOptions('checkInInterval', 15000);

        html5QrcodeScanner.render(onScanSuccess, onScanFailure);

        function onScanSuccess(decodedText, decodedResult) {
            // If the same QRCODE was scanned in less than 30 seconds, do not check it again
            if (storage.getItem(decodedText) !== null) {
                var currentTime = Date.now();

                if (currentTime - storage.getItem(decodedText) < checkInInterval) {
                    return;
                }
            }

            storage.setItem(decodedText, Date.now());

            Joomla.request({
                url: Joomla.getOptions('checkinUrl') + '&value=' + decodedText,
                method: 'GET',
                perform: true,
                onSuccess: function onSuccess(response) {
                    var json = JSON.parse(response);

                    if (json.success) {
                        modal.setContent('<div class="' + Joomla.getOptions('textSuccessClass') + '">' + json.message + '</div>');
                    } else {
                        modal.setContent('<div class="' + Joomla.getOptions('textWarningClass') + '">' + json.message + '</div>');
                    }

                    modal.open();
                },
                onError: function onError(error) {
                    alert(error);
                }
            });
        }

        function onScanFailure(error) {}
    });
})(document, Joomla);