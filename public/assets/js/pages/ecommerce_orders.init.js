/******/ (() => { // webpackBootstrap
    var __webpack_exports__ = {};
    /*!**************************************************!*\
      !*** ./resources/js/pages/form-advanced.init.js ***!
      \**************************************************/
    /*
    Template Name: Minible - Admin & Dashboard Template
    Author: Themesbrand
    Website: https://themesbrand.com/
    Contact: themesbrand@gmail.com
    File: Form Advanced Js File
    */
    !function ($) {
        "use strict";

        var AdvancedForm = function AdvancedForm() {};

        AdvancedForm.prototype.init = function () {
            // Select2
            // $(".select2").select2();
            $(".select2-limiting").select2({
                maximumSelectionLength: 2
            });
            $(".select2-search-disable").select2({
                minimumResultsForSearch: Infinity
            }); //colorpicker start
        }, //init
            $.AdvancedForm = new AdvancedForm(), $.AdvancedForm.Constructor = AdvancedForm;
    }(window.jQuery), //Datepicker
        function ($) {
            "use strict";

            $.AdvancedForm.init();
        }(window.jQuery);
    $(function () {
        'use strict';

        var $date = $('.docs-date');
        var $container = $('.docs-datepicker-container');
        var $trigger = $('.docs-datepicker-trigger');
        var options = {
            show: function show(e) {
                console.log(e.type, e.namespace);
            },
            hide: function hide(e) {
                console.log(e.type, e.namespace);
            },
            pick: function pick(e) {
                console.log(e.type, e.namespace, e.view);
            }
        };
        $date.on({
            'show.datepicker': function showDatepicker(e) {
                console.log(e.type, e.namespace);
            },
            'hide.datepicker': function hideDatepicker(e) {
                console.log(e.type, e.namespace);
            },
            'pick.datepicker': function pickDatepicker(e) {
                console.log(e.type, e.namespace, e.view);
            }
        }).datepicker(options);
        $('.docs-options, .docs-toggles').on('change', function (e) {
            var target = e.target;
            var $target = $(target);
            var name = $target.attr('name');
            var value = target.type === 'checkbox' ? target.checked : $target.val();
            var $optionContainer;

            switch (name) {
                case 'container':
                    if (value) {
                        value = $container;
                        $container.show();
                    } else {
                        $container.hide();
                    }

                    break;

                case 'trigger':
                    if (value) {
                        value = $trigger;
                        $trigger.prop('disabled', false);
                    } else {
                        $trigger.prop('disabled', true);
                    }

                    break;

                case 'inline':
                    $optionContainer = $('input[name="container"]');

                    if (!$optionContainer.prop('checked')) {
                        $optionContainer.click();
                    }

                    break;

                case 'language':
                    $('input[name="format"]').val($.fn.datepicker.languages[value].format);
                    break;
            }

            options[name] = value;
            $date.datepicker('reset').datepicker('destroy').datepicker(options);
        });
        $('.docs-actions').on('click', 'button', function (e) {
            var data = $(this).data();
            var args = data.arguments || [];
            var result;
            e.stopPropagation();

            if (data.method) {
                if (data.source) {
                    $date.datepicker(data.method, $(data.source).val());
                } else {
                    result = $date.datepicker(data.method, args[0], args[1], args[2]);

                    if (result && data.target) {
                        $(data.target).val(result);
                    }
                }
            }
        });
    }); // flatpickr

    flatpickr('#datepicker-basic', {
        defaultDate: new Date()
    });
    flatpickr('#datepicker-datetime', {
        enableTime: true,
        dateFormat: "Y-m-d H:i:s",
        defaultDate: new Date()
    });
    flatpickr('#datepicker-humanfd', {
        altInput: true,
        altFormat: "F j, Y",
        dateFormat: "Y-m-d",
        defaultDate: new Date()
    });
    flatpickr('#datepicker-minmax', {
        minDate: "today",
        defaultDate: new Date(),
        maxDate: new Date().fp_incr(14) // 14 days from now

    });
    flatpickr('#datepicker-disable', {
        onReady: function onReady() {
            this.jumpToDate("2025-01");
        },
        disable: ["2025-01-30", "2025-02-21", "2025-03-08", new Date(2025, 4, 9)],
        dateFormat: "Y-m-d",
        defaultDate: new Date()
    });
    flatpickr('#datepicker-multiple', {
        mode: "multiple",
        dateFormat: "Y-m-d",
        defaultDate: new Date()
    });
    flatpickr('#datepicker-range', {
        mode: "range",
        defaultDate: new Date()
    });
    flatpickr('#datepicker-timepicker', {
        enableTime: true,
        noCalendar: true,
        dateFormat: "H:i",
        defaultDate: new Date()
    });
    flatpickr('#datepicker-inline', {
        inline: true,
        defaultDate: new Date()
    });
    /******/ })()
;