function gettransferlist() {
    //$(\'#transferlist\').html(\'Loading..\');
    $.get('index.php?page=transferlist', function(data) {
        $('#transferlist').html(data);
        //window.setTimeout(update, 10000);
    });
};

function reloadtransferlist() {
    var refreshId = setInterval(
        function() { gettransferlist(); },
        9000);
};
