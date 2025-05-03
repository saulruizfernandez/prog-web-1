$(function () {
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
            "src/php/user/delete_data.php",
            {
              id: record_to_delete,
            },
            function (response) {
              if (!response.success) {
                alert("Error in deletion.");
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
              if (!response.success) {
                alert("Error in update.");
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
              if (!response.success) {
                alert("Error in addition.");
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
