let scanner = new Instascan.Scanner({
    mirror: false,
    video: document.getElementById("preview")
});
scanner.addListener("scan", function (content) {
    var input = document.getElementById("id_qrCode");
    input.value = content;
    document.getElementById("FormQrcode").submit();
});
Instascan.Camera.getCameras()
        .then(function (cameras) {
            if (cameras.length > 0) {
                if (cameras.length > 1) {
                    scanner.start(cameras[1]);
                } else {
                    scanner.start(cameras[0]);
                }
            } else {
                console.error("No cameras found.");
            }
        })
        .catch(function (e) {
            console.error(e);
        });
result = scanner.scan();
if (result) {
    var input = document.getElementById("id_qrCode");
    input.value = result.content;
    document.getElementById("FormQrcode").submit();
}
