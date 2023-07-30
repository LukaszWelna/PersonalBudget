/**
 * Add jQuery Validation plugin method for a amount
 * 
 * Valid amount must contain max 2 digits after decimal point
 * Valid amount must be greater than 0
 * 
 */

$.validator.addMethod("decimalPlaces", function(value, element) {
    return (! /[0-9]*\.[0-9]{3,}/.test(value));
}, "Amount must contain max 2 digits after decimal point.");

$.validator.addMethod("amountMin", function(value, element) {
    return (value > 0);
}, "Please enter a value greater than 0.");