
define(["jquery"], function ($) {
    "use strict";
    return {
        waitFor: function (elt, callback) {
            var initializer = null;
            initializer = setInterval(function () {
                if ($(elt).length > 0) {
                    setTimeout(callback, 500);
                    clearInterval(initializer);
                }
            }, 200);
        },
        
       
        initializeHours: function (id) {
            this.waitFor("#hours", function () {
                if ($("#hours").val() === "") {
                    $("#hours").val("{}");
                }
                var data = JSON.parse($('#hours').val());
                
                for (var day in data) {
                    $("#" + day).prop("checked", true);
                    var time = data[day];
                    $("#" + day + "_open").val(time.from);
                    $("#" + day + "_close").val(time.to);
                    if (typeof time.lunch_from !== "undefined") {
                        $("#" + day + "_lunch").prop("checked", true);    
                        $("#" + day + "_lunch_open").val(time.lunch_from);
                        $("#" + day + "_lunch_close").val(time.lunch_to);
                    } else {
                        $("#" + day + "_lunch").prop("checked", false);
                    }
                }
                $('.' + id + "_day").each(function () {
                    if (!$(this).prop("checked")) {
                        $(this).parent().parent().find("SELECT")[0].disabled = true;
                        $(this).parent().parent().find("SELECT")[1].disabled = true;
                    }
                });
                
            }.bind(this));
        },
        activeField: function (e, id) {
            var enabled = $(e).prop("checked");
            $(e).parent().parent().find("SELECT")[0].disabled = !enabled;
            $(e).parent().parent().find("SELECT")[1].disabled = !enabled;

          
            this.summary(id);
        },
        activeFieldLunch: function (e, id) {
            $(e).parent().parent().find("SELECT")[0].disabled = !$(e).prop("checked");
            $(e).parent().parent().find("SELECT")[1].disabled = !$(e).prop("checked");
            this.summary(id);
        },
        summary: function (id) {
            var hours = {};
            $('.' + id + "_day").each(function (e) {
                if ($(this).prop("checked")) {
                    hours[$(this).val()] = {
                        from: $(this).parent().parent().find("SELECT")[0].value,
                        to: $(this).parent().parent().find("SELECT")[1].value
                    };
                }
            });
            
            $("#hours").val(JSON.stringify(hours));
        }
    };
});