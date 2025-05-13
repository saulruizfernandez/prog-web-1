$(function () {
  function validateForm(data) {
    const errors = [];

    if (!/^[a-zA-Z0-9]*$/.test(data.titolo)) {
      errors.push("Title can only have letters and numbers");
    }
    if (!/^([0-9]*\.[0-9]*)?$/.test(data.dimensione)) {
      errors.push("The dimension can only be a decimal number");
    }
    if (
      !/^((https?:\/\/)?([a-zA-Z0-9-]+\.)+[a-zA-Z]{2,}(\/[a-zA-Z0-9-._~:/?#[\]@!$&'()*+,;=]*)?)?$/.test(
        data.uurl
      )
    ) {
      errors.push("The URL you inserted is not valid");
    }

    return errors;
  }

  function fill_form_values(record_to_update) {
    $("#edit_dialog form input").each(function () {
      if ($(this).attr("name") === "uploadedby") {
        $(this).val(
          $("#" + record_to_update.toString() + "_uploadedby").text()
        );
      } else if ($(this).attr("name") === "number") {
        $(this).val($("#" + record_to_update.toString() + "_number").text());
      } else if ($(this).attr("name") === "title") {
        $(this).val($("#" + record_to_update.toString() + "_title").text());
      } else if ($(this).attr("name") === "dimension") {
        $(this).val(
          parseFloat(
            $("#" + record_to_update.toString() + "_dimension")
              .text()
              .trim()
          )
        );
      } else if ($(this).attr("name") === "uurl") {
        $(this).val($("#" + record_to_update.toString() + "_url").text());
      }
    });
    $("#filetypeselect").val(
      $("#" + record_to_update.toString() + "_filetype")
        .text()
        .trim()
    );
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
            "src/php/multimedia-file/delete_data.php",
            {
              filenumber: record_to_delete,
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
            titolo: $(this).find('input[name="title"]').val(),
            dimensione: $(this).find('input[name="dimension"]').val(),
            uurl: $(this).find('input[name="uurl"]').val(),
          };
          const errors = validateForm(data);
          if (errors.length > 0) {
            $("#error_log_message").text(errors.join("........"));
            $(this).dialog("close");
            return;
          }
          $.post(
            "src/php/multimedia-file/edit_data.php",
            {
              filenumber: record_to_update,
              uploadedby: $(this).find('input[name="uploadedby"]').val(),
              title: $(this).find('input[name="title"]').val(),
              dimension: $(this).find('input[name="dimension"]').val(),
              uurl: $(this).find('input[name="uurl"]').val(),
              filetype: $(this).find('select[name="filetype"]').val(),
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
  $("#add_button_mult_file").on("click", function () {
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
            titolo: $(this).find('input[name="title"]').val(),
            dimensione: $(this).find('input[name="dimension"]').val(),
            uurl: $(this).find('input[name="uurl"]').val(),
          };
          const errors = validateForm(data);
          if (errors.length > 0) {
            $("#error_log_message").text(errors.join("........"));
            $(this).dialog("close");
            return;
          }
          $.post(
            "src/php/multimedia-file/add_data.php",
            {
              filenumber: record_to_add,
              uploadedby: $(this).find('input[name="uploadedby"]').val(),
              title: $(this).find('input[name="title"]').val(),
              dimension: $(this).find('input[name="dimension"]').val(),
              uurl: $(this).find('input[name="uurl"]').val(),
              filetype: $(this).find('select[name="filetype"]').val(),
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
