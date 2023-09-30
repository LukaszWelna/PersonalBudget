$("#formIncome").validate({
    rules: {
        amount: {
            required: true,
            amountMin: true,
            max: 99999999.99,
            decimalPlaces: true
        },
        dateOfIncome: {
            dateTime: true,
            min: "2000-01-01",
            dateNotGreaterThanToday: true
        },
        incomeCategoryAssignedToUserId: {
            required: true
        }
    },
    messages: {
        amount: {
            max: "Please enter a value lower than 100000000."
            },
        dateOfIncome: {
            min: "Please enter a date equal or greather than 01-01-2000."
        }
        },
    errorPlacement: function (error, element) {
        if (element.attr("name") == "amount")
            error.insertAfter("#divIncomeAmount")
        else if (element.attr("name") == "dateOfIncome")
            error.insertAfter("#divIncomeDate")
        else if (element.attr("name") == "incomeCategoryAssignedToUserId")
            error.insertAfter("#divIncomeSource")
    }
});