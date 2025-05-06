$(function () {
  function validateForm(data) {
    const errors = [];
    if (!/^[a-zA-Z0-9]*$/.test(data.nome)) {
      errors.push("Name can only have letters and numbers");
    }
    return errors;
  }

  function fill_form_values(record_to_update) {
    $("#edit_dialog form input").each(function () {
      if ($(this).attr("name") === "createdby") {
        $(this).val(
          $("#" + record_to_update.toString() + "_createdby")
            .text()
            .trim()
        );
      } else if ($(this).attr("name") === "code") {
        $(this).val($("#" + record_to_update.toString() + "_codice").text());
      } else if ($(this).attr("name") === "name") {
        $(this).val($("#" + record_to_update.toString() + "_nome").text());
      } else if ($(this).attr("name") === "creationdate") {
        $(this).val(
          $("#" + record_to_update.toString() + "_dataCreazione")
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
            "src/php/group/delete_data.php",
            {
              codice: record_to_delete,
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
            nome: $(this).find('input[name="name"]').val(),
          };
          const errors = validateForm(data);
          if (errors.length > 0) {
            $("#error_log_message").text(errors.join("........"));
            $(this).dialog("close");
            return;
          }
          $.post(
            "src/php/group/edit_data.php",
            {
              codice: record_to_update,
              creatoDa: $(this).find('input[name="createdby"]').val(),
              nome: $(this).find('input[name="name"]').val(),
              dataCreazione: $(this).find('input[name="creationdate"]').val(),
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
  $("#add_button_group").on("click", function () {
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
            nome: $(this).find('input[name="name"]').val(),
          };
          const errors = validateForm(data);
          if (errors.length > 0) {
            $("#error_log_message").text(errors.join("........"));
            $(this).dialog("close");
            return;
          }
          $.post(
            "src/php/group/add_data.php",
            {
              codice: record_to_add,
              creatoDa: $(this).find('input[name="createdby"]').val(),
              nome: $(this).find('input[name="name"]').val(),
              dataCreazione: $(this).find('input[name="creationdate"]').val(),
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
