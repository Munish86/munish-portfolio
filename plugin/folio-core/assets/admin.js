/* Folio Core admin: settings tabs, image / gallery / file pickers, repeater rows, colour pickers. */
(function ($) {
  "use strict";

  // Settings tabs (all panels are in one form, so every tab is saved together).
  $(document).on("click", ".folio-tabs .nav-tab", function (e) {
    e.preventDefault();
    var tab = $(this).data("tab");
    $(".folio-tabs .nav-tab").removeClass("nav-tab-active");
    $(this).addClass("nav-tab-active");
    $(".folio-tab-panel").attr("hidden", true).filter("[data-panel='" + tab + "']").removeAttr("hidden");
    try { sessionStorage.setItem("folioTab", tab); } catch (err) { /* private mode */ }
    if (history.replaceState) history.replaceState(null, "", "#tab-" + tab);
  });

  $(function () {
    var start = (location.hash || "").replace("#tab-", "");
    try { start = start || sessionStorage.getItem("folioTab") || ""; } catch (err) { /* private mode */ }
    if (start) $(".folio-tabs .nav-tab[data-tab='" + start + "']").trigger("click");

    $(".folio-color").wpColorPicker();
    // Gallery thumbnails can be dragged into order.
    $(".folio-media__preview--grid").sortable({
      update: function () {
        var box = $(this).closest(".folio-media");
        box.find("input[type=hidden]").val($(this).find("img").map(function () { return $(this).data("id"); }).get().join(","));
      },
    });
    $(".folio-repeater__rows").sortable({ handle: ".folio-repeater__handle", axis: "y", update: function () { reindex($(this).closest(".folio-repeater")); } });
  });

  // Image (one) and gallery (several) pickers.
  $(document).on("click", ".folio-media__pick", function () {
    var box = $(this).closest(".folio-media");
    var multiple = box.data("multiple") === 1 || box.data("multiple") === "1";
    var input = box.find("input[type=hidden]");
    var ids = (input.val() || "").split(",").filter(Boolean).map(Number);
    var frame;
    if (multiple) {
      frame = wp.media({ title: "Choose images (Ctrl/Shift-click for several)", library: { type: "image" }, multiple: "add", button: { text: "Use these images" } });
      frame.on("open", function () {
        var sel = frame.state().get("selection");
        sel.reset();
        ids.forEach(function (id) { var a = wp.media.attachment(id); a.fetch(); sel.add(a); });
      });
      frame.on("select", function () {
        var models = frame.state().get("selection").models;
        // Keep the order already arranged for images that stay; new ones go at the end.
        var chosen = models.map(function (m) { return m.id; });
        var order = ids.filter(function (id) { return chosen.indexOf(id) !== -1; });
        chosen.forEach(function (id) { if (order.indexOf(id) === -1) order.push(id); });
        input.val(order.join(","));
        var prev = box.find(".folio-media__preview").empty();
        order.forEach(function (id) {
          var a = wp.media.attachment(id).toJSON();
          var src = a.sizes && a.sizes.thumbnail ? a.sizes.thumbnail.url : a.url;
          prev.append($("<img>").attr({ src: src, alt: "", "data-id": id }));
        });
        box.find(".folio-media__clear").prop("hidden", order.length === 0);
      });
    } else {
      frame = wp.media({ title: "Choose image", library: { type: "image" }, multiple: false, button: { text: "Use this image" } });
      frame.on("select", function () {
        var a = frame.state().get("selection").first().toJSON();
        input.val(a.id);
        var src = a.sizes && a.sizes.medium ? a.sizes.medium.url : a.url;
        box.find(".folio-media__preview").html($("<img>").attr({ src: src, alt: "" }));
        box.find(".folio-media__clear").prop("hidden", false);
      });
    }
    frame.open();
  });

  $(document).on("click", ".folio-media__clear", function () {
    var box = $(this).closest(".folio-media");
    box.find("input[type=hidden]").val("");
    box.find(".folio-media__preview").empty();
    $(this).prop("hidden", true);
  });

  // File picker (any file, stores the URL), e.g. CV PDF.
  $(document).on("click", ".folio-file__pick", function () {
    var input = $(this).closest(".folio-file").find("input");
    var frame = wp.media({ title: "Choose file", multiple: false, button: { text: "Use this file" } });
    frame.on("select", function () { input.val(frame.state().get("selection").first().toJSON().url); });
    frame.open();
  });

  // Repeater rows.
  function reindex(rep) {
    var name = rep.data("name");
    rep.find(".folio-repeater__rows .folio-repeater__row").each(function (i) {
      $(this).find("[name]").each(function () {
        var n = $(this).attr("name");
        var rest = n.substring(name.length).replace(/^\[[^\]]*\]/, "[" + i + "]");
        $(this).attr("name", name + rest);
      });
    });
  }

  $(document).on("click", ".folio-repeater__add", function () {
    var rep = $(this).closest(".folio-repeater");
    var html = rep.find("template.folio-repeater__template").html();
    var i = rep.find(".folio-repeater__rows .folio-repeater__row").length;
    var row = $(html.split("__i__").join(String(i)));
    rep.find(".folio-repeater__rows").append(row);
    row.find("input,textarea").first().trigger("focus");
  });

  $(document).on("click", ".folio-repeater__remove", function () {
    var rep = $(this).closest(".folio-repeater");
    $(this).closest(".folio-repeater__row").remove();
    reindex(rep);
  });
})(jQuery);
