$(function () {
    $("#dialog-confirm").dialog({
      dialogClass: "delete-dialog",
      draggable: false,
      autoOpen: false,
      resizable: false,
      height: "auto",
      width: 400,
      modal: true,
      show: {
        effect: "fade",
        duration: 500
      },
      buttons: [
        {
          text: "Delete",
          class: "custom-button custom-delete-button",
          click: function () {
            var frm = $("<form>");
            frm.attr("method", "post");
            frm.attr("action", "/settings/delete");
            frm.appendTo("body");
            frm.submit();
          }
        },
        {
          text: "Cancel",
          class: "custom-button custom-cancel-button",
          click: function () {
            $(this).dialog("close");
          }
        }
      ]
    });
  });

  $("#deleteButton").on("click", function () {
    $("#dialog-confirm").dialog("open");
  });

  $(window).resize(function () {
    $("#dialog-confirm").dialog("option", "position", {
      my: "center",
      at: "center",
      of: window
    });
  });

  $(document).ready(function () {
    $("#buttonShowProfile").click(function () {
      $("#divProfile").toggle();
    });

    $("#buttonShowAddIncomeCategory").click(function () {
      $("#divAddIncomeCategory").toggle();
    });

    $("#buttonShowAddExpenseCategory").click(function () {
      $("#divAddExpenseCategory").toggle();
    });

    $("#buttonShowAddPaymentMethod").click(function () {
      $("#divAddPaymentMethod").toggle();
    });

    $("#buttonShowEditIncomeCategory").click(function () {
      $("#divEditIncomeCategory").toggle();
    });

    $("#buttonShowEditExpenseCategory").click(function () {
      $("#divEditExpenseCategory").toggle();
    });

    $("#buttonShowEditPaymentMethod").click(function () {
      $("#divEditPaymentMethod").toggle();
    });

    $("#buttonShowDeleteIncomeCategory").click(function () {
      $("#divDeleteIncomeCategory").toggle();
    });

    $("#buttonShowDeleteExpenseCategory").click(function () {
      $("#divDeleteExpenseCategory").toggle();
    });

    $("#buttonShowDeletePaymentMethod").click(function () {
      $("#divDeletePaymentCategory").toggle();
    });

    // Activate expense category limit
    $("#editExpenseCategoryLimit").attr("disabled", true);

    $("#activateLimit").change(function() {
      $("#editExpenseCategoryLimit").attr("disabled", !(this.checked));

      if (!(this.checked)) {
        $("#editExpenseCategoryLimit").val('');
      }
    });
    
    $("#expenseSource").change(function() {
      $("#editExpenseCategory").val($.trim($("#expenseSource option:selected").text()));
    })

  });

  $("#formAddIncomeCategory").validate({
      rules: {
          newIncomeCategory: {
              required: true,
              onlyLetters: true,
              maxlength: 25,
              remote: "/settings/validateNewIncomeCategory"
          }
      },
      messages: {
        newIncomeCategory: {
          onlyLetters: "Income category must contain only letters.",
          remote: "Name of category already exists in database."
              },
          },
      errorPlacement: function (error, element) {
          if (element.attr("name") == "newIncomeCategory")
              error.insertAfter("#divFormAddIncomeCategory")
      }
  });

  $("#formAddExpenseCategory").validate({
      rules: {
          newExpenseCategory: {
              required: true,
              onlyLetters: true,
              maxlength: 25,
              remote: "/settings/validateNewExpenseCategory"
          }
      },
      messages: {
        newExpenseCategory: {
          onlyLetters: "Expense category must contain only letters.",
          remote: "Name of category already exists in database."
              },
          },
      errorPlacement: function (error, element) {
          if (element.attr("name") == "newExpenseCategory")
              error.insertAfter("#divFormAddExpenseCategory")
      }
  });

  $("#formAddPaymentMethod").validate({
      rules: {
          newPaymentMethod: {
              required: true,
              onlyLetters: true,
              maxlength: 25,
              remote: "/settings/validateNewPaymentMethod"
          }
      },
      messages: {
        newPaymentMethod: {
          onlyLetters: "Payment method must contain only letters.",
          remote: "Name of payment method already exists in database."
              },
          },
      errorPlacement: function (error, element) {
          if (element.attr("name") == "newPaymentMethod")
              error.insertAfter("#divFormAddPaymentMethod")
      }
  });

  $("#formEditIncomeCategory").validate({
      rules: {
        editIncomeCategory: {
              required: true,
              onlyLetters: true,
              maxlength: 25,
              remote: {
                url: "/settings/validateNewIncomeCategory",
                data: {
                  incomeCategoryAssignedToUserId: function() {
                    return $("#incomeSource").val();
                  }
                }
              }
          },
          incomeCategoryAssignedToUserId: {
        required: true
      }
      },
      messages: {
        editIncomeCategory: {
          onlyLetters: "Income category must contain only letters.",
          remote: "Name of category already exists in database."
              },
          },
      errorPlacement: function (error, element) {
          if (element.attr("name") == "editIncomeCategory")
              error.insertAfter("#divFormEditIncomeCategory")
          if (element.attr("name") == "incomeCategoryAssignedToUserId")
              error.insertAfter("#divIncomeSource")
      }
  });

  $("#formEditExpenseCategory").validate({
      rules: {
        editExpenseCategory: {
              required: true,
            onlyLetters: true,
              maxlength: 25,
              remote: {
                url: "/settings/validateNewExpenseCategory",
                data: {
                  expenseCategoryAssignedToUserId: function() {
                    return $("#expenseSource").val();
                  }
                }
              }  
          },
          expenseCategoryAssignedToUserId: {
        required: true
      },
      categoryLimit: {
            required: true,
            amountMin: true,
            max: 99999999.99,
            decimalPlaces: true
      }
      },
      messages: {
        editExpenseCategory: {
          onlyLetters: "Expense category must contain only letters.",
          remote: "Name of category already exists in database."
              },
        categoryLimit: {
            max: "Please enter a value lower than 100000000."
              },
          },
      errorPlacement: function (error, element) {
          if (element.attr("name") == "editExpenseCategory")
              error.insertAfter("#divFormEditExpenseCategory")
          if (element.attr("name") == "expenseCategoryAssignedToUserId")
              error.insertAfter("#divExpenseSource")
          if (element.attr("name") == "categoryLimit")
              error.insertAfter("#divFormEditExpenseCategoryLimit")
      }
  });
  
  $("#formEditPaymentMethod").validate({
      rules: {
        editPaymentMethod: {
              required: true,
              onlyLetters: true,
              maxlength: 25,
              remote: {
                url: "/settings/validateNewPaymentMethod",
                data: {
                  paymentMethodAssignedToUserId: function() {
                    return $("#selectPaymentMethod").val();
                  }
                }
              }
          },
          paymentMethodAssignedToUserId: {
        required: true
      }
      },
      messages: {
        editPaymentMethod: {
          onlyLetters: "Payment method must contain only letters.",
          remote: "Name of payment already exists in database."
              },
          },
      errorPlacement: function (error, element) {
          if (element.attr("name") == "editPaymentMethod")
              error.insertAfter("#divFormEditPaymentMethod")
          if (element.attr("name") == "paymentMethodAssignedToUserId")
              error.insertAfter("#divPayment")
      }
  });

  $("#formDeleteIncomeCategory").validate({
      rules: {
          incomeCategoryAssignedToUserId: {
            required: true
      }
      },
      errorPlacement: function (error, element) {
          if (element.attr("name") == "incomeCategoryAssignedToUserId")
              error.insertAfter("#divDeleteIncomeSource")
      }
  });

  $("#formDeleteExpenseCategory").validate({
      rules: {
          expenseCategoryAssignedToUserId: {
            required: true
      }
      },
      errorPlacement: function (error, element) {
          if (element.attr("name") == "expenseCategoryAssignedToUserId")
              error.insertAfter("#divDeleteExpenseSource")
      }
  });

  $("#formDeletePaymentMethod").validate({
      rules: {
          paymentMethodAssignedToUserId: {
            required: true
      }
      },
      errorPlacement: function (error, element) {
          if (element.attr("name") == "paymentMethodAssignedToUserId")
              error.insertAfter("#divDeletePaymentMethod")
      }
  });
  
  $(document).ready(function() {
    $("#dialogDelete").dialog({
      dialogClass: "delete-dialog",
      draggable: false,
      autoOpen: false,
      resizable: false,
      height: "auto",
      width: 400,
      modal: true,
      show: {
        effect: "fade",
        duration: 500
      },
      buttons: [
        {
        text: "Delete",
        class: "custom-button custom-delete-button",
        click: function () {
          $(this).dialog("close");
          var formId = $(this).data("formId");
          $("#" + formId).off("submit").submit();
        } 
        },
        {
          text: "Cancel",
          class: "custom-button custom-cancel-button",
          click: function () {
          $(this).dialog("close");
        }
      }
    ]
    });
    
    $("#formDeleteIncomeCategory, #formDeleteExpenseCategory, #formDeletePaymentMethod").submit(function (e) {
      e.preventDefault(); 
      $("#dialogDelete").data("formId", this.id);

      var selectedVal = $("#" + this.id + " option:selected").val();

      if (selectedVal != "") {
        $("#dialogDelete").dialog("open");
      }
    });
  });
 
  $(window).resize(function () {
    $("#dialogDelete").dialog("option", "position", {
      my: "center",
      at: "center",
      of: window  
    });
  });