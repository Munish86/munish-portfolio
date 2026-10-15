/* Folio theme: light/dark switch, mobile menu, header state, reveal on scroll, number counters,
   project filters, image lightbox, cursor glow. No libraries. */
(function () {
  "use strict";
  var doc = document.documentElement;
  var body = document.body;
  var reduce = window.matchMedia("(prefers-reduced-motion: reduce)").matches;

  // Light / dark switch (the first choice is made in <head> to avoid a flash).
  document.querySelectorAll(".theme-toggle").forEach(function (btn) {
    btn.addEventListener("click", function () {
      var next = doc.getAttribute("data-theme") === "dark" ? "light" : "dark";
      doc.setAttribute("data-theme", next);
      try { localStorage.setItem("folio-theme", next); } catch (e) { /* private mode */ }
    });
  });

  // Mobile menu.
  var toggle = document.querySelector(".nav-toggle");
  if (toggle) {
    var setOpen = function (open) {
      body.classList.toggle("nav-open", open);
      toggle.setAttribute("aria-expanded", open ? "true" : "false");
    };
    toggle.addEventListener("click", function () { setOpen(!body.classList.contains("nav-open")); });
    document.addEventListener("keydown", function (e) { if (e.key === "Escape") setOpen(false); });
    document.querySelectorAll(".site-nav a").forEach(function (a) { a.addEventListener("click", function () { setOpen(false); }); });
  }

  // Header border once the page is scrolled.
  var header = document.querySelector(".site-header");
  var onScroll = function () { if (header) header.classList.toggle("is-scrolled", window.scrollY > 8); };
  window.addEventListener("scroll", onScroll, { passive: true });
  onScroll();

  // Count up numbers like "80+", "98", "+38%", "4.9/5", "$2M" when they come into view.
  var countUp = function (el) {
    var text = el.getAttribute("data-count") || "";
    var m = text.match(/^(\D*?)(\d+(?:[.,]\d+)?)(.*)$/);
    if (!m || reduce) return;
    var prefix = m[1], numText = m[2], suffix = m[3];
    var decimals = (numText.split(/[.,]/)[1] || "").length;
    var target = parseFloat(numText.replace(",", "."));
    var start = null, dur = 1400;
    var step = function (t) {
      if (start === null) start = t;
      var p = Math.min(1, (t - start) / dur);
      var eased = 1 - Math.pow(1 - p, 3);
      el.textContent = prefix + (target * eased).toFixed(decimals) + suffix;
      if (p < 1) requestAnimationFrame(step); else el.textContent = text;
    };
    requestAnimationFrame(step);
  };

  // Reveal on scroll, with a small stagger for items in the same row.
  var items = Array.prototype.slice.call(document.querySelectorAll(".reveal"));
  if (!("IntersectionObserver" in window) || reduce) {
    items.forEach(function (el) { el.classList.add("is-in"); });
  } else {
    var io = new IntersectionObserver(function (entries) {
      var batch = 0;
      entries.forEach(function (entry) {
        if (!entry.isIntersecting) return;
        var el = entry.target;
        el.style.setProperty("--d", Math.min(batch, 5) * 0.08 + "s");
        batch++;
        el.classList.add("is-in");
        el.querySelectorAll("[data-count]").forEach(countUp);
        io.unobserve(el);
      });
    }, { rootMargin: "0px 0px -8% 0px", threshold: 0.08 });
    items.forEach(function (el) { io.observe(el); });
  }

  // Project filters on the Work page (links still work without JavaScript).
  var grid = document.querySelector("[data-filter-grid]");
  if (grid) {
    var buttons = document.querySelectorAll(".filters [data-filter]");
    var empty = document.querySelector(".filters__empty");
    buttons.forEach(function (btn) {
      btn.addEventListener("click", function (e) {
        e.preventDefault();
        var f = btn.getAttribute("data-filter");
        buttons.forEach(function (b) { b.classList.toggle("is-active", b === btn); });
        var shown = 0;
        grid.querySelectorAll(".work-card").forEach(function (card) {
          var types = (card.getAttribute("data-types") || "").split(" ");
          var show = f === "*" || types.indexOf(f) !== -1;
          card.classList.toggle("is-hidden", !show);
          if (show) { shown++; card.classList.add("is-in"); }
        });
        if (empty) empty.hidden = shown > 0;
      });
    });
  }

  // Lightbox for gallery images.
  var box = document.querySelector(".lightbox");
  if (box) {
    var img = box.querySelector("img");
    var last = null;
    var close = function () { box.hidden = true; img.removeAttribute("src"); body.style.overflow = ""; if (last) last.focus(); };
    document.querySelectorAll("[data-lightbox]").forEach(function (a) {
      a.addEventListener("click", function (e) {
        e.preventDefault();
        last = a;
        var inner = a.querySelector("img");
        img.src = a.href;
        img.alt = inner ? inner.alt : "";
        box.hidden = false;
        body.style.overflow = "hidden";
        box.querySelector(".lightbox__close").focus();
      });
    });
    box.addEventListener("click", function (e) { if (e.target !== img) close(); });
    document.addEventListener("keydown", function (e) { if (e.key === "Escape" && !box.hidden) close(); });
  }

  // Soft glow following the mouse (only with a real mouse).
  var glow = document.querySelector(".cursor-glow");
  if (glow && !reduce && window.matchMedia("(pointer: fine)").matches) {
    var x = 0, y = 0, raf = 0;
    window.addEventListener("mousemove", function (e) {
      x = e.clientX; y = e.clientY;
      body.classList.add("has-glow");
      if (!raf) raf = requestAnimationFrame(function () { glow.style.transform = "translate(" + x + "px," + y + "px)"; raf = 0; });
    }, { passive: true });
    document.addEventListener("mouseleave", function () { body.classList.remove("has-glow"); });
  }
})();
