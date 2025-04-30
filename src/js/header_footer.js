// Load header and footer html templates in all the windows (for each of the main tables)
$(function () {
  $("#header").load("header.html", function () {
  });
  $("#footer").load("footer.html");
});
