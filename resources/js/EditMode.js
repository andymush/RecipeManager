var EditMode = {};
EditMode._enabled = false;

EditMode.init = function() {
    $.get(EditMode._uri, function(enabled) {
        if (enabled == true) {
            EditMode.enable();
        } else {
            EditMode.disable();
        }
    });
}

EditMode.toggle = function() {
    if(EditMode._enabled) {
        EditMode.disable();
    } else {
        EditMode.enable();
    }
}

EditMode.enable = function() {
    EditMode._enabled = true;
    EditMode.$_input.prop('checked', true);
    EditMode.$_item.removeClass('hidden forced');
    $.get(`${EditMode._uri}/enable`);

}

EditMode.disable = function() {
    EditMode._enabled = false;
    EditMode.$_input.prop('checked', false);
    EditMode.$_item.addClass('hidden forced');
    $.get(`${EditMode._uri}/disable`);
}


$(document).ready(function () {
    EditMode.$_switch = $('.switch.edit-mode');

    EditMode.$_switch.parent().removeClass('hidden forced'); // Show switch only if JS is enabled

    if (EditMode.$_switch.length) {
        EditMode.$_item   = $('.edit-mode.item');
        EditMode.$_input  = $('.switch.edit-mode input[type=checkbox]');
        EditMode._enabled = false;
        EditMode._uri     = '/edit-mode';

        EditMode.init();

        EditMode.$_input.change(function() {
            EditMode.toggle();
        });
    }
});
