function drawProgressBar(color, width, percent) {
    var pixels = width * (percent / 100);
    document.write('<div class="smallish-progress-wrapper" style="width: ' + width + 'px">');
    document.write('<div class="smallish-progress-bar" style="width: ' + pixels + 'px; background-color: ' + color + ';"></div>');
    document.write('<div class="smallish-progress-text" style="width: ' + width + 'px">' + percent + '%</div>');
    document.write('</div>');
}
