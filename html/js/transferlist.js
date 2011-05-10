function gettransferlist() {
    //$(\'#transferlist\').html(\'Loading..\');
    $.get('index.php?page=transferlist', function(data) {
        $('#transferlist').html(data);
        //window.setTimeout(update, 10000);
    });
};
