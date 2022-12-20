// Initialize DateRangePicker
const daterangepicker = (
    elementSelector,
    drops = "auto",
    timePicker = false,
    parentEl = null
) => {
    let config = {
        singleDatePicker: true,
        timePicker: false,
        showDropdowns: true,
        autoUpdateInput: true,
        autoApply: true,
        locale: {
            cancelLabel: "Hapus",
            applyLabel: "Terapkan",
            format: "YYYY-MM-DD",
        },
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
};
