// Validate form responsible for adding new expense
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

// Show category limits, spent money and calculate left money using AJAX 
const amountField = document.querySelector("#expenseAmount");
const dateField = document.querySelector("#expenseDate");
const categoryField = document.querySelector("#expenseSource");

const limitInfoField = document.querySelector("#limitInfo");
const moneySpentField = document.querySelector("#moneySpent");
const moneyLeftField = document.querySelector("#moneyLeft");

[categoryField, dateField].forEach(function(element) {
    element.addEventListener("change", async function() {
        await showCategoryLimit();
        await showMoneySpent();
        showCashLeft();
     });
});

window.addEventListener("load", () => {
    showCategoryLimit();
    showMoneySpent();
});

amountField.addEventListener("input", () => {
    showCashLeft();
});

// Show category limit in proper field
const showCategoryLimit = async () => {
    let category = categoryField.options[categoryField.selectedIndex].text.trim();
    if (category === "Choose option") {
        limitInfoField.textContent = "Category required";
    } else {
        let categoryDashed = category.replace(/\s+/g, "-");
        let limitAmount = await getCategoryLimit(categoryDashed);
        if (limitAmount === null) { 
            limitInfoField.textContent = "No limit set";
        } else {
            limitInfoField.textContent = `${limitAmount} PLN`;
        }
    }
}

// Get chosen category monthly limit
const getCategoryLimit = async (category) => {
    try {
        const res = await fetch(`../api/limit/${category}`);
        return await res.json();;
    } catch (e) {
        console.log('ERROR', e);
    }

}

// Show spent money in proper field
const showMoneySpent = async () => {
    let category = categoryField.options[categoryField.selectedIndex].text.trim();
    let date = dateField.value;
    if ((category === "Choose option") || (date === '')) {
        moneySpentField.textContent = "Category & date required";
    } else {
        let categoryDashed = category.replace(/\s+/g, "-");
        let moneySpentAmount = await getMoneySpent(categoryDashed, date);
        moneySpentField.textContent = `${moneySpentAmount} PLN`;
    }
}

// Get spent money in month in chosen category
const getMoneySpent = async (category, date) => {
    try {
        const res = await fetch(`../api/amount/${category}/${date}`);
        return await res.json();;
    } catch (e) {
        console.log('ERROR', e);
    }

}
// Show cash left in proper field
const showCashLeft = () => {
    if (moneyLeftField.classList.contains("moneyLeftPlus")) {
        moneyLeftField.classList.remove("moneyLeftPlus")
    }
    if (moneyLeftField.classList.contains("moneyLeftMinus")) {
        moneyLeftField.classList.remove("moneyLeftMinus")
    }

    let category = categoryField.options[categoryField.selectedIndex].text.trim();
    let date = dateField.value;
    let amount = amountField.value;

    if ((category === "Choose option") || (date === '') || (amount === '')) {
        moneyLeft.textContent = "Category, date & amount required";
    } else {
        let limitInfo = limitInfoField.textContent;
        let moneySpent = moneySpentField.textContent;
        limitInfo = limitInfo.replace(/[^0-9\.]/g, '');
        moneySpent = moneySpent.replace(/[^0-9\.]/g, '');  

        if ((limitInfo === '')) {
            moneyLeftField.textContent = "No limit";
        } else {
            limitInfo = Number(limitInfo);
            moneySpent = Number(moneySpent);
            amount = amountField.value;
            cashLeft = limitInfo - moneySpent - amount;
            if (cashLeft >=0) {
                moneyLeftField.classList.add("moneyLeftPlus");
            } else {
                moneyLeftField.classList.add("moneyLeftMinus");
            }
            moneyLeftField.textContent = `${cashLeft.toFixed(2)} PLN`;
        }
    }
}
