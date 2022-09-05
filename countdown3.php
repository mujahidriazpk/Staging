<html>
<head>
<title>ajax count down test</title>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
<script src="/wp-content/plugins/woocommerce-simple-auctions/js/jquery.countdown.min.js"></script> <!--maybe you shouldn't hotlink this file ;-) -->
</head>
<body>
<div id="defaultCountdown"></div>
<script>
    function serverTime() {
        var time = null;
        $.ajax({
            url: 'getAuctionData.php',
            async: false,
            dataType: 'text',
            success: function (text) {
                time = new Date(text);
            },
            error: function (http, message, exc) {
                time = new Date();
            }
        });
        return time;
    }

    $(function () {
        $("#defaultCountdown").countdown({
            until: new Date("Jun 24, 2022 16:00:00 +0000"),
            serverSync: serverTime
        });
    });
</script>
<p id="demo"></p>

</body>
</html>
