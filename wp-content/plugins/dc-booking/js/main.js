(function ($) {
    $(document).ready(function () {
        var disableDays = function (date) {

            for (var i = 0; i < disabledArr.length; i++) {
                var From = disabledArr[i].from.split("-");
                var To = disabledArr[i].to.split("-");

                var FromDate = new Date(From[0], From[1] - 1, From[2]);
                var ToDate = new Date(To[0], To[1] - 1, To[2]);

                var found = false;
                if (date >= FromDate && date <= ToDate) {
                    found = true;
                    return [false, "red"];
                }
            }

            if (!found) {
                return [true, ""];
            }
        };


        $('#field-checkin').datepicker({
            beforeShowDay: disableDays,
            minDate: 0
        });
        $('#field-checkout').datepicker({
            beforeShowDay: disableDays,
            minDate: 0
        });

        ///////////////////////////
        $('#book').toggle(function () {
                $('.book-form').fadeIn(500);
            },
            function () {
                $('.book-form').fadeOut(300);
            });

        ///////////////////////
        $('#availab-calendar').datepicker({
            beforeShowDay: disableDays,
            minDate: 0,
            numberOfMonths: [3, 3]
        });
        $('#availab').toggle(function () {
                $('.availab-calendar-box').fadeIn(500);
            },
            function () {
                $('.availab-calendar-box').fadeOut(400);
            });

        //////////////////
        $('#filter-chin-date').datepicker({
            minDate: 0,
            numberOfMonths: [3, 2]
        });
        $('#filter-chout-date').datepicker({
            minDate: 0,
            numberOfMonths: [3, 2]
        });

        ////////////////
        $('#reviews').toggle(function () {
                $('.reviews-box').fadeIn(500);
            },
            function () {
                $('.reviews-box').fadeOut(300);
            });



    });
})(jQuery);
