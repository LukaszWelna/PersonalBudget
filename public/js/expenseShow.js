$("#formExpense").validate({
    rules: {
        amount: {
            required: true,
            amountMin: true,
            max: 99999999.99,
            decimalPlaces: true
        },
        dateOfExpense: {
            dateTime: true,
            min: "2000-01-01",
            dateNotGreaterThanToday: true
        },
        expenseCategoryAssignedToUserId: {
            required: true
        },
        paymentMethodAssignedToUserId: {
            required: true
        }
    },
    messages: {
        amount: {
            max: "Please enter a value lower than 100000000."
            },
        dateOfExpense: {
            min: "Please enter a date equal or greather than 01-01-2000."
        }
        },
    errorPlacement: function (error, element) {
        if (element.attr("name") == "amount")
            error.insertAfter("#divExpenseAmount")
        else if (element.attr("name") == "dateOfExpense")
            error.insertAfter("#divExpenseDate")
        else if (element.attr("name") == "paymentMethodAssignedToUserId")
            error.insertAfter("#divPaymentMethod")
        else if (element.attr("name") == "expenseCategoryAssignedToUserId")
            error.insertAfter("#divExpenseSource")
    }
});

$("#expenseSource, #expenseDate").on("change", async function() {
    await showCategoryLimit();
    await showMoneySpent();
    showCashLeft();
});

$(window).on("load", function() {
    showCategoryLimit();
    showMoneySpent();
});

const showCategoryLimit = async () => {
    let category = ($.trim($("#expenseSource option:selected").text()));
    if (category === "Choose option") {
        $("#limitInfo").text("Category required");
    } else {
        let categoryDashed = category.replace(/\s+/g, "-");
        let limitAmount = await getCategoryLimit(categoryDashed);
        if (limitAmount === null) { 
            $("#limitInfo").text("No limit set");
        } else {
            $("#limitInfo").text(`${limitAmount} PLN`);
        }
    }
}

const getCategoryLimit = async (category) => {
    try {
        const res = await fetch(`../api/limit/${category}`);
        const amount = await res.json();
        return amount;
    } catch (e) {
        console.log('ERROR', e);
    }

}

const showMoneySpent = async () => {
    let category = ($.trim($("#expenseSource option:selected").text()));
    let date = $("#expenseDate").val();
    if ((category === "Choose option") || (date === '')) {
        $("#moneySpent").text("Category & date required");
    } else {
        let categoryDashed = category.replace(/\s+/g, "-");
        let moneySpentAmount = await getMoneySpent(categoryDashed, date);
        $("#moneySpent").text(`${moneySpentAmount} PLN`);
    }
}

const getMoneySpent = async (category, date) => {
    try {
        const res = await fetch(`../api/amount/${category}/${date}`);
        const amount = await res.json();
        return amount;
    } catch (e) {
        console.log('ERROR', e);
    }

}

$("#expenseAmount").on("input", function() {
    showCashLeft();
});

const showCashLeft = () => {
    if ($("#moneyLeft").hasClass("moneyLeftPlus")) {
        $("#moneyLeft").removeClass("moneyLeftPlus")
    }
    if ($("#moneyLeft").hasClass("moneyLeftMinus")) {
        $("#moneyLeft").removeClass("moneyLeftMinus")
    }

    let category = ($.trim($("#expenseSource option:selected").text()));
    let date = $("#expenseDate").val();
    let amount = $("#expenseAmount").val();

    if ((category === "Choose option") || (date === '') || (amount === '')) {
        $("#moneyLeft").text("Category, date & amount required");
    } else {
        let limitInfo = $("#limitInfo").text();
        let moneySpent = $("#moneySpent").text();
        limitInfo = limitInfo.replace(/[^0-9\.]/g, '');
        moneySpent = moneySpent.replace(/[^0-9\.]/g, '');  

        if ((limitInfo === '')) {
            $("#moneyLeft").text("No limit");
        } else {
            limitInfo = Number(limitInfo);
            moneySpent = Number(moneySpent);
            amount = $("#expenseAmount").val();
            cashLeft = limitInfo - moneySpent - amount;
            if (cashLeft >=0) {
                $("#moneyLeft").addClass("moneyLeftPlus");
            } else {
                $("#moneyLeft").addClass("moneyLeftMinus");
            }
            $("#moneyLeft").text(`${cashLeft.toFixed(2)} PLN`);
        }
    }
}
