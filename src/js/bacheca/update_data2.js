$(function () {
  function validateForm(data) {
    const errors = [];
    if (!/^[a-zA-Z0-9\s,.-/]*$/.test(data.nome)) {
      errors.push("Name can only have letters, numbers, spaces and periods");
    }
    return errors;
  }

  $.datepicker.setDefaults({
    dateFormat: "dd/mm/yy",
  });
  $(".datepicker").datepicker();

  $("#search_filter form").on("submit", function (e) {
    const dateField = $(this).find(".datepicker");
    const originalDate = dateField.val();
    if (originalDate) {
      const parts = originalDate.split("/");
      const formattedDate = parts[2] + "-" + parts[1] + "-" + parts[0];
      dateField.val(formattedDate);
    }
  });

  function fill_form_values(record_to_update) {
    $("#edit_dialog form input").each(function () {
      if ($(this).attr("name") === "codiceUtente") {
        $(this).val(
          $("#" + record_to_update + "_codiceUtente")
            .text()
            .trim()
        );
      } else if ($(this).attr("name") === "nome") {
        $(this).val(
          $("#" + record_to_update + "_nome")
            .text()
            .trim()
        );
      } else if ($(this).attr("name") === "dataCreazione") {
        $(this).val(
          $("#" + record_to_update + "_dataCreazione")
            .text()
            .trim()
        );
      }
    });
  }

  $(".delete_button").on("click", function () {
    const record_to_delete = $(this).attr("id").replace(/\D/g, "");
    const nome = $("#" + record_to_delete + "_nome")
      .text()
      .trim();

    console.log("ID to delete:", record_to_delete);
    console.log("Nome to delete:", nome);
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
            "src/php/bacheca/delete_data.php",
            {
              id: record_to_delete,
              nome: nome,
            },
            function (response) {
              if (response && !response.success) {
                console.log("error");
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
            nome: $(this).find('input[name="nome"]').val(),
          };
          const errors = validateForm(data);
          if (errors.length > 0) {
            $("#error_log_message").text(errors.join("........"));
            $(this).dialog("close");
            return;
          }
          $.post(
            "src/php/bacheca/edit_data.php",
            {
              id: record_to_update,
              codiceUtente: $(this).find('input[name="codiceUtente"]').val(),
              nome: $(this).find('input[name="nome"]').val(),
              dataCreazione: (() => {
                const formContext = $(this);
                const originalDate = formContext
                  .find('input[name="dataCreazione"]')
                  .val();
                if (originalDate) {
                  const parts = originalDate.split("/");
                  const formattedDate = `${parts[2]}-${parts[1]}-${parts[0]}`;
                  return formattedDate;
                }
                return "";
              })(),
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

  $("#add_button_bacheca").on("click", function () {
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
            nome: $(this).find('input[name="nome"]').val(),
          };
          const errors = validateForm(data);
          if (errors.length > 0) {
            $("#error_log_message").text(errors.join("........"));
            $(this).dialog("close");
            return;
          }
          $.post(
            "src/php/bacheca/add_data.php",
            {
              id: record_to_add,
              codiceUtente: $(this).find('input[name="codiceUtente"]').val(),
              nome: $(this).find('input[name="nome"]').val(),
              dataCreazione: (() => {
                const formContext = $(this);
                const originalDate = formContext
                  .find('input[name="dataCreazione"]')
                  .val();
                if (originalDate) {
                  const parts = originalDate.split("/");
                  const formattedDate = `${parts[2]}-${parts[1]}-${parts[0]}`;
                  return formattedDate;
                }
                return "";
              })(),
            },
            function (response) {
              if (response && !response.success) {
                $("#error_log_message").text(response.error);
              } else {
                // Force table reload after addition
                $("#search_add_filter form").submit();
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
