/**
 * Add jQuery Validation plugin method for a letters check
 * 
 * Valid value must contain only letters
 * 
 */

$.validator.addMethod("onlyLetters", function(value, element) {
    return this.optional(element) || /^[a-zA-Z\s]+$/i.test(value);
}, "New income category must contain only letters.");