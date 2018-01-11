jQuery(function ($) {

    var date_from = $(".cms-datetime-range-from").val(),
        date_to = $(".cms-datetime-range-to").val();
    var dateFormat = "dd/mm/yy",
        from = $(".cms-datetime-range-from")
            .datepicker({
                defaultDate: "+1w",
                changeMonth: true,
                numberOfMonths: 1,
                dateFormat: dateFormat,
                maxDate: date_to.length ? date_to : new Date(),
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

    function getDate(element) {
        var date;
        try {
            date = $.datepicker.parseDate(dateFormat, element.value);
        } catch (error) {
            date = null;
        }

        return date;
    }
});