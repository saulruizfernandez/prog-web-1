$(function () {
  function validateForm(data) {
    const errors = [];

    if (!/^[a-zA-Z0-9\s]*$/.test(data.nickname)) {
      errors.push("Nickname can only have letters and numbers");
    }
    if (!/^[a-zA-Z\s]*$/.test(data.nome)) {
      errors.push("The name can only contain letters");
    }
    if (!/^[a-zA-Z\s]*$/.test(data.cognome)) {
      errors.push("The surname can only contain letters");
    }

    return errors;
  }

  function fill_form_values(record_to_update) {
    $("#edit_dialog form input").each(function () {
      if ($(this).attr("name") === "codice") {
        $(this).val($("#" + record_to_update.toString() + "_codice").text());
      } else if ($(this).attr("name") === "nickname") {
        $(this).val($("#" + record_to_update.toString() + "_nickname").text());
      } else if ($(this).attr("name") === "nome") {
        $(this).val($("#" + record_to_update.toString() + "_nome").text());
      } else if ($(this).attr("name") === "cognome") {
        $(this).val($("#" + record_to_update.toString() + "_cognome").text());
      } else if ($(this).attr("name") === "dataNascita") {
        $(this).val(
          $("#" + record_to_update.toString() + "_dataNascita")
            .text()
            .trim()
        );
      }
    });
  }

  $(".delete_button").on("click", function () {
    const record_to_delete = $(this).attr("id").replace(/\D/g, "");
    $("#delete_dialog").dialog({
      resizable: false,
      show: {
        effect: "blind",
        duration: 400,
      },
      hide: {
        effect: "explode",
        duration: 400,
      },
      height: "auto",
      width: 400,
      modal: true,
      buttons: {
        Delete: function () {
          $.post(
            "src/php/user/delete_data.php",
            {
              id: record_to_delete,
            },
            function (response) {
              if (response && !response.success) {
                $("#error_log_message").text(response.error);
              } else {
                // Force table reload after deletion
                $("#search_filter form").submit();
              }
            }
          );
          $(this).dialog("close");
        },
        Cancel: function () {
          $(this).dialog("close");
        },
      },
    });
  });
  $(".edit_button").on("click", function () {
    const record_to_update = $(this).attr("id").replace(/\D/g, "");
    fill_form_values(record_to_update);
    $("#edit_dialog").dialog({
      resizable: false,
      show: {
        effect: "blind",
        duration: 400,
      },
      hide: {
        effect: "explode",
        duration: 400,
      },
      height: "auto",
      width: 400,
      modal: true,
      buttons: {
        "Update information": function () {
          const data = {
            id: record_to_update,
            nickname: $(this).find('input[name="nickname"]').val(),
            nome: $(this).find('input[name="nome"]').val(),
            cognome: $(this).find('input[name="cognome"]').val(),
          };
          const errors = validateForm(data);
          if (errors.length > 0) {
            $("#error_log_message").text(errors.join("........"));
            $(this).dialog("close");
            return;
          }
          $.post(
            "src/php/user/edit_data.php",
            {
              id: record_to_update,
              nickname: $(this).find('input[name="nickname"]').val(),
              nome: $(this).find('input[name="nome"]').val(),
              cognome: $(this).find('input[name="cognome"]').val(),
              dataNascita: $(this).find('input[name="dataNascita"]').val(),
            },
            function (response) {
              if (response && !response.success) {
                $("#error_log_message").text(response.error);
              } else {
                // Force table reload after update
                $("#search_filter form").submit();
              }
            },
            "json"
          ).fail(function (jqXHR, textStatus, errorThrown) {
            alert("Server error: " + textStatus + " - " + errorThrown);
          });
          $(this).dialog("close");
        },
        Cancel: function () {
          $(this).dialog("close");
        },
      },
    });
  });
  $("#add_button_user").on("click", function () {
    const record_to_add = $(this).attr("id").replace(/\D/g, "");
    fill_form_values(record_to_add);
    $("#add_dialog").dialog({
      resizable: false,
      show: {
        effect: "blind",
        duration: 400,
      },
      hide: {
        effect: "explode",
        duration: 400,
      },
      height: "auto",
      width: 400,
      modal: true,
      buttons: {
        "Add information": function () {
          const data = {
            id: record_to_add,
            nickname: $(this).find('input[name="nickname"]').val(),
            nome: $(this).find('input[name="nome"]').val(),
            cognome: $(this).find('input[name="cognome"]').val(),
          };
          const errors = validateForm(data);
          if (errors.length > 0) {
            $("#error_log_message").text(errors.join("........"));
            $(this).dialog("close");
            return;
          }
          $.post(
            "src/php/user/add_data.php",
            {
              id: record_to_add,
              nickname: $(this).find('input[name="nickname"]').val(),
              nome: $(this).find('input[name="nome"]').val(),
              cognome: $(this).find('input[name="cognome"]').val(),
              dataNascita: $(this).find('input[name="dataNascita"]').val(),
            },
            function (response) {
              if (response && !response.success) {
                $("#error_log_message").text(response.error);
              } else {
                // Force table reload after addition
                $("#search_filter form").submit();
              }
            },
            "json"
          ).fail(function (jqXHR, textStatus, errorThrown) {
            alert("Server error: " + textStatus + " - " + errorThrown);
          });
          $(this).dialog("close");
        },
        Cancel: function () {
          $(this).dialog("close");
        },
      },
    });
  });
});

$(function () {
  $("#dataNascita").datepicker({
    dateFormat: "dd/mm/yy", // dd/mm/yyyy en jQuery UI
    changeMonth: true,
    changeYear: true,
    yearRange: "1900:2025",
  });
});
