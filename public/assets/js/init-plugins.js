// Initialize DateRangePicker
const daterangepicker = (
    elementSelector,
    drops = "auto",
    autoUpdate = false,
    autoApply = false,
    timePicker = false,
    parentEl = null
) => {
    let config = {
        singleDatePicker: true,
        timePicker: false,
        showDropdowns: true,
        autoUpdateInput: autoUpdate,
        autoApply: autoApply,
        locale: {
            format: "YYYY-MM-DD",
        },
        cancelLabel: "Hapus",
        applyLabel: "Terapkan",

        drops: drops,
    };

    // if timePicker is true add timePicker24Hour to config
    if (timePicker) {
        config.timePicker = true;
        config.timePicker24Hour = true;
        config.locale.format = "YYYY-MM-DD HH:mm:ss";
    }

    if (parentEl) {
        config.parentEl = parentEl;
    }

    $(`${elementSelector}`).daterangepicker(config);

    $(`${elementSelector}`).on("apply.daterangepicker", function (ev, picker) {
        if (timePicker) {
            $(this).val(picker.startDate.format("YYYY-MM-DD HH:mm:ss"));
        } else {
            $(this).val(picker.startDate.format("YYYY-MM-DD"));
        }
    });

    $(`${elementSelector}`).on("cancel.daterangepicker", function (ev, picker) {
        $(this).val("");
    });
};
