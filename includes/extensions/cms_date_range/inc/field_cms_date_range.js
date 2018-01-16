jQuery(function ($) {
    if ($(".cms-datetime-range-from").length !== 0 && $(".cms-datetime-range-to").length !== 0) {
        var date_from = $(".cms-datetime-range-from").val(),
            date_to = $(".cms-datetime-range-to").val();
        var dateFormat = "yy-mm-dd",
            from = $(".cms-datetime-range-from")
                .datepicker({
                    defaultDate: "+1w",
                    changeMonth: true,
                    numberOfMonths: 1,
                    dateFormat: dateFormat,
                    minDate: new Date()
                })
                .on("change", function () {
                    var date = getDate(this);
                    if (date) {
                        date.setDate(date.getDate() + 1);
                    }
                    to.datepicker("option", "minDate", date);
                }),
            to = $(".cms-datetime-range-to").datepicker({
                defaultDate: "+1w",
                changeMonth: true,
                numberOfMonths: 1,
                dateFormat: dateFormat,
                minDate: date_from.length ? date_from : new Date()
            })
                .on("change", function () {
                    var date = getDate(this);
                    if (date) {
                        date.setDate(date.getDate() - 1);
                    }
                    from.datepicker("option", "maxDate", date);
                });
        if (date_to.length) {
            from.datepicker("option", "maxDate", date_to);
        }

        function getDate(element) {
            var date;
            try {
                date = $.datepicker.parseDate(dateFormat, element.value);
            } catch (error) {
                date = null;
            }

            return date;
        }

    }
    if ($(".cms-date").length !== 0) {
        var date_input = $(".cms-date").val();
        var dateFormat_date = "yy-mm-dd",
            cms_date = $(".cms-date").datepicker({
                defaultDate: "+1w",
                changeMonth: true,
                numberOfMonths: 1,
                dateFormat: dateFormat_date,
                minDate: new Date()
            });
    }
});