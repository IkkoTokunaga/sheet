$('textarea').on('input', function () {
    if ($(this).outerHeight() > this.scrollHeight) {
        $(this).height('60px')
    }
    while ($(this).outerHeight() < this.scrollHeight) {
        $(this).height($(this).height() + 1)
    }
});