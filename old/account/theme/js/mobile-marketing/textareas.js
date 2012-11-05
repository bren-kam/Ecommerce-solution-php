head.js( '/js2/?f=charCount', function() {
    $('textarea').charCount({
        css : 'counter bold'
        , cssExceeded : 'error'
        , counterText : 'Characters Left: '
    });
});