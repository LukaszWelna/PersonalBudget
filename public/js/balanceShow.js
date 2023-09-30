$(document).ready(function(){
    $("#idChoosePeriod").bind("change", function () {
        if(this.value == '')
        $("#periodModal").modal("show")
    }).change();
});

$(document).ready(function(){
    $("#submitForm").click(function(){
        if ($("#idChoosePeriod option:selected").val() == '') {
            $("#periodModal").modal("show")
        }
    });
});

$.validator.addMethod("notAnother", function(value, element) {
    return (value != '');
}, "");

$("#formChoosePeriod").validate({
    rules: {
        choosePeriod: {
            notAnother: true
        }
    }        
});

$("#formAnotherPeriod").validate({
    rules: {
        startDate: {
            dateTime: true,
            min: "2000-01-01",
            dateNotGreaterThanToday: true
        },
        endDate: {
            dateTime: true,
            min: "2000-01-01",
            dateNotGreaterThanToday: true
        },
    },
    messages: {
        startDate: {
            min: "Please enter a date equal or greather than 01-01-2000."
            },
        endDate: {
            min: "Please enter a date equal or greather than 01-01-2000."
        }
        },
    errorPlacement: function (error, element) {
        if (element.attr("name") == "startDate")
            error.insertAfter("#divStartDate")
        else if (element.attr("name") == "endDate")
            error.insertAfter("#divEndDate")
    }
    
});