/* =============================================================
 * bootstrap-combobox.js v1.1.8
 * =============================================================
 * Copyright 2012 Daniel Farrell
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 * ============================================================ */

(function( $ ) {

 "use strict";

 /* COMBOBOX PUBLIC CLASS DEFINITION
  * ================================ */

  var Combobox = function ( element, options ) {
    this.options = $.extend({}, $.fn.combobox.defaults, options);
    this.template = this.options.template || this.template
    this.$source = $(element);
    this.$container = this.setup();
    this.$element = this.$container.find('input[type=text]');
    this.$target = this.$container.find('input[type=hidden]');
    this.$button = this.$container.find('.dropdown-toggle');
    this.$menu = $(this.options.menu).appendTo('body');
    this.matcher = this.options.matcher || this.matcher;
    this.sorter = this.options.sorter || this.sorter;
    this.highlighter = this.options.highlighter || this.highlighter;
    this.shown = false;
    this.selected = false;
    this.refresh();
    this.transferAttributes();
    this.listen();
  };

  Combobox.prototype = {

    constructor: Combobox

  , setup: function () {
      var combobox = $(this.template());
      this.$source.before(combobox);
      this.$source.hide();
      return combobox;
    }

  , disable: function() {
      this.$element.prop('disabled', true);
      this.$button.attr('disabled', true);
      this.disabled = true;
      this.$container.addClass('combobox-disabled');
    }

  , enable: function() {
      this.$element.prop('disabled', false);
      this.$button.attr('disabled', false);
      this.disabled = false;
      this.$container.removeClass('combobox-disabled');
    }
  , parse: function () {
      var that = this
        , map = {}
        , source = []
        , selected = false
        , selectedValue = '';
      this.$source.find('option').each(function() {
        var option = $(this);
        if (option.val() === '') {
          that.options.placeholder = option.text();
          return;
        }
        map[option.text()] = option.val();
        source.push(option.text());
        if (option.prop('selected')) {
          selected = option.text();
          selectedValue = option.val();
        }
      })
      this.map = map;
      if (selected) {
        this.$element.val(selected);
        this.$target.val(selectedValue);
        this.$container.addClass('combobox-selected');
        this.selected = true;
      }
      return source;
    }

  , transferAttributes: function() {
    this.options.placeholder = this.$source.attr('data-placeholder') || this.options.placeholder
    if(this.options.appendId !== "undefined") {
    	this.$element.attr('id', this.$source.attr('id') + this.options.appendId);
    }
    this.$element.attr('placeholder', this.options.placeholder)
    this.$target.prop('name', this.$source.prop('name'))
    this.$target.val(this.$source.val())
    this.$source.removeAttr('name')  // Remove from source otherwise form will pass parameter twice.
    this.$element.attr('required', this.$source.attr('required'))
    this.$element.attr('rel', this.$source.attr('rel'))
    this.$element.attr('title', this.$source.attr('title'))
    this.$element.attr('class', this.$source.attr('class'))
    this.$element.attr('tabindex', this.$source.attr('tabindex'))
    this.$source.removeAttr('tabindex')
    if (this.$source.attr('disabled')!==undefined)
      this.disable();
  }

  , select: function () {
      var val = this.$menu.find('.active').attr('data-value');
      this.$element.val(this.updater(val)).trigger('change');
      this.$target.val(this.map[val]).trigger('change');
      this.$source.val(this.map[val]).trigger('change');
      this.$container.addClass('combobox-selected');
      this.selected = true;
      return this.hide();
    }

  , updater: function (item) {
      return item;
    }

  , show: function () {
      var pos = $.extend({}, this.$element.position(), {
        height: this.$element[0].offsetHeight
      });

      this.$menu
        .insertAfter(this.$element)
        .css({
          top: pos.top + pos.height
        , left: pos.left
        })
        .show();

      $('.dropdown-menu').on('mousedown', $.proxy(this.scrollSafety, this));

      this.shown = true;
      return this;
    }

  , hide: function () {
      this.$menu.hide();
      $('.dropdown-menu').off('mousedown', $.proxy(this.scrollSafety, this));
      this.$element.on('blur', $.proxy(this.blur, this));
      this.shown = false;
      return this;
    }

  , lookup: function (event) {
      this.query = this.$element.val();
      return this.process(this.source);
    }

  , process: function (items) {
      var that = this;

      items = $.grep(items, function (item) {
        return that.matcher(item);
      })

      items = this.sorter(items);

      if (!items.length) {
        return this.shown ? this.hide() : this;
      }

      return this.render(items.slice(0, this.options.items)).show();
    }

  , template: function() {
      if (this.options.bsVersion == '2') {
        return '<div class="combobox-container"><input type="hidden" /> <div class="input-append"> <input type="text" autocomplete="off" /> <span class="add-on dropdown-toggle" data-dropdown="dropdown"> <span class="caret"/> <i class="icon-remove"/> </span> </div> </div>'
      } else {
        return '<div class="combobox-container"> <input type="hidden" /> <div class="input-group"> <input type="text" autocomplete="off" /> <span class="input-group-addon dropdown-toggle" data-dropdown="dropdown"> <span class="caret" /> <span class="glyphicon glyphicon-remove" /> </span> </div> </div>'
      }
    }

  , matcher: function (item) {
      return ~item.toLowerCase().indexOf(this.query.toLowerCase());
    }

  , sorter: function (items) {
      var beginswith = []
        , caseSensitive = []
        , caseInsensitive = []
        , item;

      while (item = items.shift()) {
        if (!item.toLowerCase().indexOf(this.query.toLowerCase())) {beginswith.push(item);}
        else if (~item.indexOf(this.query)) {caseSensitive.push(item);}
        else {caseInsensitive.push(item);}
      }

      return beginswith.concat(caseSensitive, caseInsensitive);
    }

  , highlighter: function (item) {
      var query = this.query.replace(/[\-\[\]{}()*+?.,\\\^$|#\s]/g, '\\$&');
      return item.replace(new RegExp('(' + query + ')', 'ig'), function ($1, match) {
        return '<strong>' + match + '</strong>';
      })
    }

  , render: function (items) {
      var that = this;

      items = $(items).map(function (i, item) {
        i = $(that.options.item).attr('data-value', item);
        i.find('a').html(that.highlighter(item));
        return i[0];
      })

      items.first().addClass('active');
      this.$menu.html(items);
      return this;
    }

  , next: function (event) {
      var active = this.$menu.find('.active').removeClass('active')
        , next = active.next();

      if (!next.length) {
        next = $(this.$menu.find('li')[0]);
      }

      next.addClass('active');
    }

  , prev: function (event) {
      var active = this.$menu.find('.active').removeClass('active')
        , prev = active.prev();

      if (!prev.length) {
        prev = this.$menu.find('li').last();
      }

      prev.addClass('active');
    }

  , toggle: function () {
    if (!this.disabled) {
      if (this.$container.hasClass('combobox-selected')) {
        this.clearTarget();
        this.triggerChange();
        this.clearElement();
      } else {
        if (this.shown) {
          this.hide();
        } else {
          this.clearElement();
          this.lookup();
        }
      }
    }
  }

  , scrollSafety: function(e) {
      if (e.target.tagName == 'UL') {
          this.$element.off('blur');
      }
  }
  , clearElement: function () {
    this.$element.val('').focus();
  }

  , clearTarget: function () {
    this.$source.val('');
    this.$target.val('');
    this.$container.removeClass('combobox-selected');
    this.selected = false;
  }

  , triggerChange: function () {
    this.$source.trigger('change');
  }

  , refresh: function () {
    this.source = this.parse();
    this.options.items = this.source.length;
  }

  , listen: function () {
      this.$element
        .on('focus',    $.proxy(this.focus, this))
        .on('blur',     $.proxy(this.blur, this))
        .on('keypress', $.proxy(this.keypress, this))
        .on('keyup',    $.proxy(this.keyup, this));

      if (this.eventSupported('keydown')) {
        this.$element.on('keydown', $.proxy(this.keydown, this));
      }

      this.$menu
        .on('click', $.proxy(this.click, this))
        .on('mouseenter', 'li', $.proxy(this.mouseenter, this))
        .on('mouseleave', 'li', $.proxy(this.mouseleave, this));

      this.$button
        .on('click', $.proxy(this.toggle, this));
    }

  , eventSupported: function(eventName) {
      var isSupported = eventName in this.$element;
      if (!isSupported) {
        this.$element.setAttribute(eventName, 'return;');
        isSupported = typeof this.$element[eventName] === 'function';
      }
      return isSupported;
    }

  , move: function (e) {
      if (!this.shown) {return;}

      switch(e.keyCode) {
        case 9: // tab
        case 13: // enter
        case 27: // escape
          e.preventDefault();
          break;

        case 38: // up arrow
          e.preventDefault();
          this.prev();
          this.fixMenuScroll();
          break;

        case 40: // down arrow
          e.preventDefault();
          this.next();
          this.fixMenuScroll();
          break;
      }

      e.stopPropagation();
    }

  , fixMenuScroll: function(){
      var active = this.$menu.find('.active');
      if(active.length){
          var top = active.position().top;
          var bottom = top + active.height();
          var scrollTop = this.$menu.scrollTop();
          var menuHeight = this.$menu.height();
          if(bottom > menuHeight){
              this.$menu.scrollTop(scrollTop + bottom - menuHeight);
          } else if(top < 0){
              this.$menu.scrollTop(scrollTop + top);
          }
      }
  }

  , keydown: function (e) {
      this.suppressKeyPressRepeat = ~$.inArray(e.keyCode, [40,38,9,13,27]);
      this.move(e);
    }

  , keypress: function (e) {
      if (this.suppressKeyPressRepeat) {return;}
      this.move(e);
    }

  , keyup: function (e) {
      switch(e.keyCode) {
        case 40: // down arrow
         if (!this.shown){
           this.toggle();
         }
         break;
        case 39: // right arrow
        case 38: // up arrow
        case 37: // left arrow
        case 36: // home
        case 35: // end
        case 16: // shift
        case 17: // ctrl
        case 18: // alt
          break;

        case 9: // tab
        case 13: // enter
          if (!this.shown) {return;}
          this.select();
          break;

        case 27: // escape
          if (!this.shown) {return;}
          this.hide();
          break;

        default:
          this.clearTarget();
          this.lookup();
      }

      e.stopPropagation();
      e.preventDefault();
  }

  , focus: function (e) {
      this.focused = true;
    }

  , blur: function (e) {
      var that = this;
      this.focused = false;
      var val = this.$element.val();
      if (!this.selected && val !== '' ) {
        this.$element.val('');
        this.$source.val('').trigger('change');
        this.$target.val('').trigger('change');
      }
      if (!this.mousedover && this.shown) {setTimeout(function () { that.hide(); }, 200);}
    }

  , click: function (e) {
      e.stopPropagation();
      e.preventDefault();
      this.select();
      this.$element.focus();
    }

  , mouseenter: function (e) {
      this.mousedover = true;
      this.$menu.find('.active').removeClass('active');
      $(e.currentTarget).addClass('active');
    }

  , mouseleave: function (e) {
      this.mousedover = false;
    }
  };

  /* COMBOBOX PLUGIN DEFINITION
   * =========================== */
  $.fn.combobox = function ( option ) {
    return this.each(function () {
      var $this = $(this)
        , data = $this.data('combobox')
        , options = typeof option == 'object' && option;
      if(!data) {$this.data('combobox', (data = new Combobox(this, options)));}
      if (typeof option == 'string') {data[option]();}
    });
  };

  $.fn.combobox.defaults = {
    bsVersion: '4'
  , menu: '<ul class="typeahead typeahead-long dropdown-menu"></ul>'
  , item: '<li><a href="#" class="dropdown-item"></a></li>'
  };

  $.fn.combobox.Constructor = Combobox;

}( window.jQuery ));


!(function (a, b) {
    "use strict";
    "function" == typeof define && define.amd ? define(["jquery"], b) : "object" == typeof exports ? (module.exports = b(require("jquery"))) : (a.bootbox = b(a.jQuery));
})(this, function a(b, c) {
    "use strict";
    function d(a) {
        var b = q[o.locale];
        return b ? b[a] : q.en[a];
    }
    function e(a, c, d) {
        a.stopPropagation(), a.preventDefault();
        var e = b.isFunction(d) && d(a) === !1;
        e || c.modal("hide");
    }
    function f(a) {
        var b,
            c = 0;
        for (b in a) c++;
        return c;
    }
    function g(a, c) {
        var d = 0;
        b.each(a, function (a, b) {
            c(a, b, d++);
        });
    }
    function h(a) {
        var c, d;
        if ("object" != typeof a) throw new Error("Please supply an object of options");
        if (!a.message) throw new Error("Please specify a message");
        return (
            (a = b.extend({}, o, a)),
            a.buttons || (a.buttons = {}),
            (a.backdrop = a.backdrop ? "static" : !1),
            (c = a.buttons),
            (d = f(c)),
            g(c, function (a, e, f) {
                if ((b.isFunction(e) && (e = c[a] = { callback: e }), "object" !== b.type(e))) throw new Error("button with key " + a + " must be an object");
                e.label || (e.label = a), e.className || (e.className = 2 >= d && f === d - 1 ? "btn-primary" : "btn-default");
            }),
            a
        );
    }
    function i(a, b) {
        var c = a.length,
            d = {};
        if (1 > c || c > 2) throw new Error("Invalid argument length");
        return 2 === c || "string" == typeof a[0] ? ((d[b[0]] = a[0]), (d[b[1]] = a[1])) : (d = a[0]), d;
    }
    function j(a, c, d) {
        return b.extend(!0, {}, a, i(c, d));
    }
    function k(a, b, c, d) {
        var e = { className: "bootbox-" + a, buttons: l.apply(null, b) };
        return m(j(e, d, c), b);
    }
    function l() {
        for (var a = {}, b = 0, c = arguments.length; c > b; b++) {
            var e = arguments[b],
                f = e.toLowerCase(),
                g = e.toUpperCase();
            a[f] = { label: d(g) };
        }
        return a;
    }
    function m(a, b) {
        var d = {};
        return (
            g(b, function (a, b) {
                d[b] = !0;
            }),
            g(a.buttons, function (a) {
                if (d[a] === c) throw new Error("button key " + a + " is not allowed (options are " + b.join("\n") + ")");
            }),
            a
        );
    }
    var n = {
            dialog: "<div class='bootbox modal' tabindex='-1' role='dialog'><div class='modal-dialog'><div class='modal-content'><div class='modal-body'><div class='bootbox-body'></div></div></div></div></div>",
            header: "<div class='modal-header'><h4 class='modal-title'></h4></div>",
            footer: "<div class='modal-footer'></div>",
            closeButton: "<button type='button' class='bootbox-close-button close' data-dismiss='modal' aria-hidden='true'>&times;</button>",
            form: "<form class='bootbox-form'></form>",
            inputs: {
                text: "<input class='bootbox-input bootbox-input-text form-control' autocomplete=off type=text />",
                textarea: "<textarea class='bootbox-input bootbox-input-textarea form-control'></textarea>",
                email: "<input class='bootbox-input bootbox-input-email form-control' autocomplete='off' type='email' />",
                select: "<select class='bootbox-input bootbox-input-select form-control'></select>",
                checkbox: "<div class='checkbox'><label><input class='bootbox-input bootbox-input-checkbox' type='checkbox' /></label></div>",
                date: "<input class='bootbox-input bootbox-input-date form-control' autocomplete=off type='date' />",
                time: "<input class='bootbox-input bootbox-input-time form-control' autocomplete=off type='time' />",
                number: "<input class='bootbox-input bootbox-input-number form-control' autocomplete=off type='number' />",
                password: "<input class='bootbox-input bootbox-input-password form-control' autocomplete='off' type='password' />",
            },
        },
        o = { locale: "en", backdrop: !0, animate: !0, className: null, closeButton: !0, show: !0, container: "body" },
        p = {};
    (p.alert = function () {
        var a;
        if (((a = k("alert", ["ok"], ["message", "callback"], arguments)), a.callback && !b.isFunction(a.callback))) throw new Error("alert requires callback property to be a function when provided");
        return (
            (a.buttons.ok.callback = a.onEscape = function () {
                return b.isFunction(a.callback) ? a.callback() : !0;
            }),
            p.dialog(a)
        );
    }),
        (p.confirm = function () {
            var a;
            if (
                ((a = k("confirm", ["cancel", "confirm"], ["message", "callback"], arguments)),
                (a.buttons.cancel.callback = a.onEscape = function () {
                    return a.callback(!1);
                }),
                (a.buttons.confirm.callback = function () {
                    return a.callback(!0);
                }),
                !b.isFunction(a.callback))
            )
                throw new Error("confirm requires a callback");
            return p.dialog(a);
        }),
        (p.prompt = function () {
            var a, d, e, f, h, i, k;
            if (
                ((f = b(n.form)),
                (d = { className: "bootbox-prompt", buttons: l("cancel", "confirm"), value: "", inputType: "text" }),
                (a = m(j(d, arguments, ["title", "callback"]), ["cancel", "confirm"])),
                (i = a.show === c ? !0 : a.show),
                (a.message = f),
                (a.buttons.cancel.callback = a.onEscape = function () {
                    return a.callback(null);
                }),
                (a.buttons.confirm.callback = function () {
                    var c;
                    switch (a.inputType) {
                        case "text":
                        case "textarea":
                        case "email":
                        case "select":
                        case "date":
                        case "time":
                        case "number":
                        case "password":
                            c = h.val();
                            break;
                        case "checkbox":
                            var d = h.find("input:checked");
                            (c = []),
                                g(d, function (a, d) {
                                    c.push(b(d).val());
                                });
                    }
                    return a.callback(c);
                }),
                (a.show = !1),
                !a.title)
            )
                throw new Error("prompt requires a title");
            if (!b.isFunction(a.callback)) throw new Error("prompt requires a callback");
            if (!n.inputs[a.inputType]) throw new Error("invalid prompt type");
            switch (((h = b(n.inputs[a.inputType])), a.inputType)) {
                case "text":
                case "textarea":
                case "email":
                case "date":
                case "time":
                case "number":
                case "password":
                    h.val(a.value);
                    break;
                case "select":
                    var o = {};
                    if (((k = a.inputOptions || []), !k.length)) throw new Error("prompt with select requires options");
                    g(k, function (a, d) {
                        var e = h;
                        if (d.value === c || d.text === c) throw new Error("given options in wrong format");
                        d.group && (o[d.group] || (o[d.group] = b("<optgroup/>").attr("label", d.group)), (e = o[d.group])), e.append("<option value='" + d.value + "'>" + d.text + "</option>");
                    }),
                        g(o, function (a, b) {
                            h.append(b);
                        }),
                        h.val(a.value);
                    break;
                case "checkbox":
                    var q = b.isArray(a.value) ? a.value : [a.value];
                    if (((k = a.inputOptions || []), !k.length)) throw new Error("prompt with checkbox requires options");
                    if (!k[0].value || !k[0].text) throw new Error("given options in wrong format");
                    (h = b("<div/>")),
                        g(k, function (c, d) {
                            var e = b(n.inputs[a.inputType]);
                            e.find("input").attr("value", d.value),
                                e.find("label").append(d.text),
                                g(q, function (a, b) {
                                    b === d.value && e.find("input").prop("checked", !0);
                                }),
                                h.append(e);
                        });
            }
            return (
                a.placeholder && h.attr("placeholder", a.placeholder),
                a.pattern && h.attr("pattern", a.pattern),
                f.append(h),
                f.on("submit", function (a) {
                    a.preventDefault(), a.stopPropagation(), e.find(".btn-primary").click();
                }),
                (e = p.dialog(a)),
                e.off("shown.bs.modal"),
                e.on("shown.bs.modal", function () {
                    h.focus();
                }),
                i === !0 && e.modal("show"),
                e
            );
        }),
        (p.dialog = function (a) {
            a = h(a);
            var c = b(n.dialog),
                d = c.find(".modal-dialog"),
                f = c.find(".modal-body"),
                i = a.buttons,
                j = "",
                k = { onEscape: a.onEscape };
            if (
                (g(i, function (a, b) {
                    (j += "<button data-bb-handler='" + a + "' type='button' class='btn " + b.className + "'>" + b.label + "</button>"), (k[a] = b.callback);
                }),
                f.find(".bootbox-body").html(a.message),
                a.animate === !0 && c.addClass("fade"),
                a.className && c.addClass(a.className),
                "large" === a.size && d.addClass("modal-lg"),
                "small" === a.size && d.addClass("modal-sm"),
                a.title && f.before(n.header),
                a.closeButton)
            ) {
                var l = b(n.closeButton);
                a.title ? c.find(".modal-header").prepend(l) : l.css("margin-top", "-10px").prependTo(f);
            }
            return (
                a.title && c.find(".modal-title").html(a.title),
                j.length && (f.after(n.footer), c.find(".modal-footer").html(j)),
                c.on("hidden.bs.modal", function (a) {
                    a.target === this && c.remove();
                }),
                c.on("shown.bs.modal", function () {
                    c.find(".btn-primary:first").focus();
                }),
                c.on("escape.close.bb", function (a) {
                    k.onEscape && e(a, c, k.onEscape);
                }),
                c.on("click", ".modal-footer button", function (a) {
                    var d = b(this).data("bb-handler");
                    e(a, c, k[d]);
                }),
                c.on("click", ".bootbox-close-button", function (a) {
                    e(a, c, k.onEscape);
                }),
                c.on("keyup", function (a) {
                    27 === a.which && c.trigger("escape.close.bb");
                }),
                b(a.container).append(c),
                c.modal({ backdrop: a.backdrop, keyboard: !1, show: !1 }),
                a.show && c.modal("show"),
                c
            );
        }),
        (p.setDefaults = function () {
            var a = {};
            2 === arguments.length ? (a[arguments[0]] = arguments[1]) : (a = arguments[0]), b.extend(o, a);
        }),
        (p.hideAll = function () {
            return b(".bootbox").modal("hide"), p;
        });
    var q = {
        br: { OK: "OK", CANCEL: "Cancelar", CONFIRM: "Sim" },
        cs: { OK: "OK", CANCEL: "ZruÅ¡it", CONFIRM: "Potvrdit" },
        da: { OK: "OK", CANCEL: "Annuller", CONFIRM: "Accepter" },
        de: { OK: "OK", CANCEL: "Abbrechen", CONFIRM: "Akzeptieren" },
        el: { OK: "Î•Î½Ï„Î¬Î¾ÎµÎ¹", CANCEL: "Î‘ÎºÏÏÏ‰ÏƒÎ·", CONFIRM: "Î•Ï€Î¹Î²ÎµÎ²Î±Î¯Ï‰ÏƒÎ·" },
        en: { OK: "OK", CANCEL: "Cancel", CONFIRM: "OK" },
        es: { OK: "OK", CANCEL: "Cancelar", CONFIRM: "Aceptar" },
        et: { OK: "OK", CANCEL: "Katkesta", CONFIRM: "OK" },
        fi: { OK: "OK", CANCEL: "Peruuta", CONFIRM: "OK" },
        fr: { OK: "OK", CANCEL: "Annuler", CONFIRM: "D'accord" },
        he: { OK: "××™×©×•×¨", CANCEL: "×‘×™×˜×•×œ", CONFIRM: "××™×©×•×¨" },
        id: { OK: "OK", CANCEL: "Batal", CONFIRM: "OK" },
        it: { OK: "OK", CANCEL: "Annulla", CONFIRM: "Conferma" },
        ja: { OK: "OK", CANCEL: "ã‚­ãƒ£ãƒ³ã‚»ãƒ«", CONFIRM: "ç¢ºèª" },
        lt: { OK: "Gerai", CANCEL: "AtÅ¡aukti", CONFIRM: "Patvirtinti" },
        lv: { OK: "Labi", CANCEL: "Atcelt", CONFIRM: "ApstiprinÄt" },
        nl: { OK: "OK", CANCEL: "Annuleren", CONFIRM: "Accepteren" },
        no: { OK: "OK", CANCEL: "Avbryt", CONFIRM: "OK" },
        pl: { OK: "OK", CANCEL: "Anuluj", CONFIRM: "PotwierdÅº" },
        pt: { OK: "OK", CANCEL: "Cancelar", CONFIRM: "Confirmar" },
        ru: { OK: "OK", CANCEL: "ÐžÑ‚Ð¼ÐµÐ½Ð°", CONFIRM: "ÐŸÑ€Ð¸Ð¼ÐµÐ½Ð¸Ñ‚ÑŒ" },
        sv: { OK: "OK", CANCEL: "Avbryt", CONFIRM: "OK" },
        tr: { OK: "Tamam", CANCEL: "Ä°ptal", CONFIRM: "Onayla" },
        zh_CN: { OK: "OK", CANCEL: "å–æ¶ˆ", CONFIRM: "ç¡®è®¤" },
        zh_TW: { OK: "OK", CANCEL: "å–æ¶ˆ", CONFIRM: "ç¢ºèª" },
    };
    return (
        (p.init = function (c) {
            return a(c || b);
        }),
        p
    );
});


var temp_json = [];
var calendarDisabledSelected = false;
destination_triggered = false;
var calendarMaxBookingDate;

function setDisabled(date, checkin, checkout, current_widget) {
    var to_return = "";
    var state = true;
    if (!$.isEmptyObject(temp_json) && temp_json.last_update) {
        var array = temp_json;
        var _date = moment(date).format("YYYY-MM-DD");
        if (typeof array[_date] != "undefined") {
            var disabled = "";
            if (array[_date]["status"].indexOf("booked") !== -1 && array[_date]["status"].indexOf("-start") === -1 && array[_date]["status"].indexOf("-end") === -1) {
                disabled = " disabled ";
                state = false;
            }
            to_return = disabled + array[_date]["status"];
        } else {
            to_return = "";
        }
        if (checkin && moment(checkin, "DD/MM/YYYY").format("YYYY-MM-DD") == _date) {
            if (to_return.indexOf("disabled") == -1 && to_return.indexOf("-start") == -1) to_return += " stay-selected stay-selected-checkin";
            else {
                calendarDisabledSelected = true;
            }
        } else if (checkout && moment(checkout, "DD/MM/YYYY").format("YYYY-MM-DD") == _date) {
            if (to_return.indexOf("disabled") == -1 && to_return.indexOf("-stop") == -1) to_return += " stay-selected stay-selected-checkout";
            else {
                console.log("set calendarDisabledSelected");
                calendarDisabledSelected = true;
            }
        } else if (checkin && checkout && moment(checkin, "DD/MM/YYYY").format("YYYY-MM-DD") < _date && moment(checkout, "DD/MM/YYYY").format("YYYY-MM-DD") > _date) {
            if (to_return.indexOf("disabled") == -1 && to_return.indexOf("-start") == -1 && to_return.indexOf("-stop") == -1) to_return += " stay-selected";
            else {
                calendarDisabledSelected = true;
            }
        } else to_return += " debug";
    }
    if (calendarMaxBookingDate !== null && (calendarMaxBookingDate == 0 || calendarMaxBookingDate < _date)) to_return += " inquire-only";
    return [state, to_return];
}
function onShow(current) {
    if ($("#calendar_url").length) {
        $.post(
            $("#calendar_url").attr("data-url"),
            function (response) {
                if (response.last_update)
                    $(".availability-calendar")
                        .find(".legend-text")
                        .html("Last Update: " + moment(response.last_update).format("DD/MM/YY HH:mm:ss"));
                temp_json = response;
            },
            "json"
        );
    } else temp_json = [];
}   

var form_activeWidget;
function IsEmail(email) {
    var regex = /^([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
    return regex.test(email);
}
var min_stay_msg = "";
function setDateOnSelectCheckin(id, date, autofocus, trigger_id, is_booking) {
    var checkin = moment(date, "DD/MM/YYYY");
    var checkout = moment(date, "DD/MM/YYYY");
    var min_stay = null;
    var is_multiple = $("form.multiple-villas").length;
    if (!is_multiple) {
        $("#villa_rates_box tr[data-from]").each(function () {
            if ($(this).attr("data-from") <= checkin.format("YYYY-MM-DD") && $(this).attr("data-to") >= checkin.format("YYYY-MM-DD")) {
                min_stay = parseInt($(this).attr("data-min-nights"));
            }
        });
    }
    checkin.add(1, "days");
    checkout.add(min_stay, "days");
    if (!is_multiple) {
        $("#villa_rates_box tr[data-from]").each(function () {
            if ($(this).attr("data-from") <= checkout.format("YYYY-MM-DD") && $(this).attr("data-to") >= checkout.format("YYYY-MM-DD")) {
                var checkoutMinStay = parseInt($(this).attr("data-min-nights"));
                if (checkoutMinStay > min_stay) {
                    checkout.add(checkoutMinStay - min_stay, "days");
                    min_stay = checkoutMinStay;
                }
            }
        });
        if (is_booking && min_stay === null) {
            bootbox.alert(
                '<h4>Book Now not available.</h4><br />To book these dates please <a href="' + window.location.href.replace("/booking/true", "") + '">contact us</a> and one of our sales team will be in touch shortly.<br /><br />'
            );
            return false;
        }
    }
    if (is_booking) {
        var c = temp_json[checkout.format("YYYY-MM-DD")];
        if (trigger_id && typeof c == "object" && (c.status == "booked" || c.status == "booked-end" || c.status == "pending" || c.status == "pending-end")) {
            $("#" + trigger_id).val("");
            bootbox.alert("Selected date is not available because for this date minimum stay is " + min_stay + " night.");
        }
    } else {
        setTimeout(function () {
            if (checkout.diff(moment($("#" + id).val(), "DD/MM/YYYY")) >= 0) $("#" + id).val(checkout.format("DD/MM/YYYY"));
            $("#" + id).addClass("filled");
            if (!is_multiple && parseInt(min_stay)) min_stay_msg = '<p style="text-align: center; color: #D9534F">In the selected season minimum stay is ' + min_stay + " night" + (min_stay > 1 ? "s" : "") + " </p>";
            else min_stay_msg = "";
            if (autofocus) $("#" + id).focus();
        }, 300);
    }
}
function checkAvailability(is_booking) {
    if (is_booking) {
        var checkin = moment($("#reservation3_from").val(), "DD/MM/YYYY");
        var checkout = moment($("#reservation3_to").val(), "DD/MM/YYYY");
        var result = false;
        do {
            checkin.add(1, "days");
            var c = temp_json[checkin.format("YYYY-MM-DD")];
            if (typeof c == "object" && (c.status == "booked" || c.status == "booked-end" || c.status == "pending" || c.status == "pending-end")) {
                result = true;
                break;
            }
        } while (checkin.format("YYYY-MM-DD") < checkout.format("YYYY-MM-DD"));
        if (result) {
            $("#reservation3_to").val("");
            bootbox.alert("Selected period is not available.");
        }
    }
}
function setMaxBookingDate() {
    calendarMaxBookingDate = 0;
    $("#villa_rates_box tr[data-to]").each(function () {
        calendarMaxBookingDate = !calendarMaxBookingDate || calendarMaxBookingDate < $(this).attr("data-to") ? $(this).attr("data-to") : calendarMaxBookingDate;
    });
}
function beforeShowHelper(dp, onshowdisabled) {
    $(".ui-datepicker").addClass("notranslate");
    if (!(onshowdisabled == true)) onShow($(dp));
    $("#ui-datepicker-div").removeClass(function () {
        return $("input").get(0).id;
    });
    $("#ui-datepicker-div").removeClass("reservation3_from");
    $("#ui-datepicker-div").removeClass("reservation2_from");
    $("#ui-datepicker-div").removeClass("reservation3_to");
    $("#ui-datepicker-div").removeClass("reservation2_to");
    $("#ui-datepicker-div").addClass($(dp).attr("id"));
    setTimeout(function () {
        $("#ui-datepicker-div").append(
            '<div class="legend zabuto_calendar"><span class="legend-block"><ul class="legend"><li class="event-styled label label-warning"></li><span>Pending</span></ul></span><span class="legend-block"><ul class="legend"><li class="event-styled label label-danger"></li><span>Booked</span></ul></span><span class="legend-block"><ul class="legend"><li class="event-styled label label-info"></li><span>Promotion</span></ul></span></div>'
        );
        if (temp_json.last_update) $("#ui-datepicker-div").append('<div style="text-align: center; font-size: 90%; padding-bottom: 10px;">' + "Last Update: " + moment(temp_json.last_update).format("DD/MM/YY HH:mm:ss") + "</div>");
        $(".ui-datepicker").css("z-index", 99999999999999);
        if (min_stay_msg) {
            if ($("#DatapickerHeaderMsg").length) $("#DatapickerHeaderMsg").remove();
            $("#ui-datepicker-div").prepend('<div id="DatapickerHeaderMsg">' + min_stay_msg + "</div>");
        }
    }, 0);
}


var calculateTotalPriceRequest = 0;
var bedroomsSelectState = 0;


function initPaymentListener() {
    $('#modalInquiry').hide();
    setTimeout(listenForPayment, 1000)
}

function listenForPayment() {
    if($('.pin-payment-button-overlay-iframe').length) {
    setTimeout(listenForPayment, 500)
    }else showCompletedMessage('We are confirming booking with Villa owner, if confirmed here are our banking details to confirm the rental. Confirmation shouldn\'t take more than 24 hours, we will contact them soon.')
}

function validBookNowForm(event) {
    event.preventDefault();
    
    if($('.pin-payment-button-overlay-iframe').length) $('.pin-payment-button-overlay-iframe').remove();
    if(calculateTotalPriceRequest) {
        $('.pin-payment-button').attr('href', 
            'https://pay.pin.net.au/qkbo/test?amount_editable=false&amount='+calculateTotalPriceRequest.total+'&currency='+calculateTotalPriceRequest.currency
                + '&email=' + $('#yourEmail').val() 
                + '&address_city=' + $('input[name=city]').val() 
                + '&address_state=' + $('input[name=state]').val() 
                + '&address_postcode=' + $('input[name=zip]').val()
                + '&description=' + 'Payment for Villa 501 from '+$('#reservation3_from').val() +' to '+$('#reservation3_to').val()                                                            
        )
    }
    if($('#terms-container input').is(':checked')) {
        if($(this).hasClass('pin-payment-button')){
            $(this).find('style').remove();
            initPaymentListener();
        }
        else showCompletedMessage($(this).attr('data-msg'))
    }
    else {
        $('#terms-container').addClass('alert alert-danger');
    }
}


function showCompletedMessage(msg) {
    $('#modalInquiry .modal-header h3').html('')
    $('#modalInquiry .modal-body').html('<div style="padding: 5% 10% 8%"><h2>Thank You</h2>'+msg+'</div>');
    $('#modalInquiry .modal-footer').html('')
    $('#modalInquiry').show()
}

function calculateTotalPrice() {
    $('#BookingSummary').html('...');
    
    $.get('/villas/getBookingSummary?villa_id=393&from='+$('#reservation3_from').val()+'&to='+$('#reservation3_to').val()+'&rooms='+bedroomsSelectState+'&payment_type='+$('#payment_type').val(), function(jdata){
    // var selectBedrooms = $('<div>'+data+'</div>').find('select[name=bedrooms]').addClass('white').clone();
    
    $('#BookingSummary').html(jdata.priceWidget);
    
    $('#BedroomsFromGroup').html(jdata.bedroomsWidget);
    
    $('#BedroomsFromGroup > select').change(function(){
        bedroomsSelectState = $(this).val();
        calculateTotalPrice();
    });
    if(bedroomsSelectState) $('#BedroomsFromGroup > select').val(bedroomsSelectState)
    
    $('#BedroomsFromGroup > select').addClass('white').selectpicker();
    
    $('#terms-container input').removeAttr('disabled');
    
    $('#paymentDep').html('Pay '+jdata.data.type+': ' + (jdata.data.total ? jdata.data.toPayWithCurrency : 'N/A') + ' <span style="margin-left: 10%; margin-top: -8px; position: absolute; font-size: 200%" class="glyphicon glyphicon-arrow-right"></span>');
    
    calculateTotalPriceRequest = jdata.data;
    
    if(jdata.data.total == 0) $('#inquiry_form_submit').addClass('disabled');
    else $('#inquiry_form_submit').removeClass('disabled');
    
    }, 'json');

// By selecting this, I acknowledge that I have read and   accepted the  Property Policies & Details, Supplemental Terms & Included Amenities

}



if ($(window).width() > 600){
    
    var dpMonthYear = {month: 4, year: 2023,}
    var form_activeWidget = null;
    
    $('#reservation3_from, #reservation3_to').focus(function(){
        form_activeWidget = $(this);
    })
    
    // $('#datepickerHelper').datepicker({
    //     dateFormat: 'dd/mm/yy',
    //     numberOfMonths: 3,
    //     minDate: "today",
    //     defaultDate: "today",
    //     onSelect: function(date) {
    //         var d = moment(date, 'DD/MM/YYYY');
    //         form_activeWidget.val(date);
    //         $('#'+form_activeWidget.attr('id')).addClass('filled');
    //         if(form_activeWidget.attr('id') == 'reservation3_from') setDateOnSelectCheckin('reservation3_to', date, true, 'reservation3_from', false);
    //         else if(form_activeWidget.attr('id') == 'reservation3_to') checkAvailability(false);
        
    //     },
    //     beforeShow: function(input, inst ){
    //         inst.dpDiv.css({marginTop: form_activeWidget.attr('id') == 'reservation3_from' ? '35px' : '85px'});  
            
    //         var selectedDate = $(input).datepicker( "getDate" );
    //         if(selectedDate) {
    //         var d = moment(selectedDate);
    //         if(d.format('M') != dpMonthYear.month)
    //         {
    //         if(parseInt(d.format('M')) - dpMonthYear.month == 2) $(input).datepicker('setDate','01/'+(dpMonthYear.month + 2)+'/'+dpMonthYear.year)
    //         else $(input).datepicker('setDate','01/'+dpMonthYear.month+'/'+dpMonthYear.year)
    //         }
            
    //         }
    //         beforeShowHelper(this)
    //     },
    //     onChangeMonthYear: function( year, month, inst)
    //     {
    //         dpMonthYear = {year: year, month: month};
    //         beforeShowHelper(this, true)
    //     },
    //     beforeShowDay: function(date) {  
    //         if($('form.multiple-villas').length) return [true, ''];
    //         else return setDisabled(date, $('#reservation3_from').val(), $('#reservation3_to').val());
    //     },
    //     onClose: function(dateText, inst )
    //     {
    //     }
    
    // });
}
    
function showCalendar() {
    console.log('h');
    setTimeout(function(){
        $('#datepickerHelper').datepicker('show')
    }, 10)
}
function showCalendar() {
    $('#datepickerHelper').datepicker('show')
   
}
   
            
  //onShow($('#calendar_url'));
  // Date Ranges
        
  // Select Pickers
    
   $('.selectpicker').selectpicker();


// Sliders  
        
var min_value = 0;
var max_value = 9000;

$('#search-price').slider({}).on('slideStop', function(ev){
    var values = $(this).data('slider').getValue();
    $('#search_price_hidden_min').val(values[0]).trigger('change');
    $('#search_price_hidden_max').val(values[1]).trigger('change');
});
            
$('#search-rooms').slider({}).on('slideStop', function(ev){
    var values = $(this).data('slider').getValue();
    console.log(values);
    $('#search_rooms_hidden').val(values[0]+'_'+values[1])
    $('#search_rooms_hidden').trigger('change');
});
            
    
//show thumbs toggle
 //$('input, textarea').placeholder();

$(document).on("click", "#inquiry_form_submit", function (e) {
    
    $("#inquiry_form .has-error").removeClass("has-error");
    $("#inquiry_form .alert.alert-danger").removeClass("alert alert-danger");
    var error_msg = "";
    var has_errors = false;
    if ($("#date_from").val().length == 0) {
        $("#date_from").parent().addClass("has-error");
        error_msg += '<div class="col-md-12"> - Checkin Date is required.</div>';
        has_errors = true;
    }
    if ($("#date_to").val().length == 0) {
        $("#date_to").parent().addClass("has-error");
        error_msg += '<div class="col-md-12"> - Checkout Date is required.</div>';
        has_errors = true;
    }
    if ($("#date_from").val().indexOf("-") !== -1) {
        if ($("#date_from").val() >= $("#date_to").val()) {
            $("#date_to").parent().addClass("has-error");
            error_msg += '<div class="col-md-12"> - Checkout Date is incorrect.</div>';
            has_errors = true;
        }
    } else if (moment($("#date_from").val(), "DD/MM/YYYY").format("YYYYMMDD") >= moment($("#date_to").val(), "DD/MM/YYYY").format("YYYYMMDD")) {
        $("#date_to").parent().addClass("has-error");
        error_msg += '<div class="col-md-12"> - Checkout Date is incorrect.</div>';
        has_errors = true;
    }
    if ($("#select-guests-2").val().length == 0 || $("#select-guests-2").val() == "0") {
        $("#select-guests-2").parent().addClass("has-error");
        error_msg += '<div class="col-md-12"> - Adults Number is required.</div>';
        has_errors = true;
    }
    if ($("#yourEmail").val().length == 0) {
        $("#yourEmail").parent().addClass("has-error");
        error_msg += '<div class="col-md-12"> - Your Email Address is required.</div>';
        has_errors = true;
    }
    if ($("#yourEmail").val().length > 0 && !IsEmail($("#yourEmail").val())) {
        $("#yourEmail").parent().addClass("has-error");
        error_msg += '<div class="col-md-12"> - Your Email Address is not valid.</div>';
        has_errors = true;
    }
    if ($("#fname").val().length == 0) {
        $("#fname").parent().addClass("has-error");
        error_msg += '<div class="col-md-12"> - Your First Name is required.</div>';
        has_errors = true;
    }
    if ($("#lname").val().length == 0) {
        $("#lname").parent().addClass("has-error");
        error_msg += '<div class="col-md-12"> - Your Last Name is required.</div>';
        has_errors = true;
    }
    // if ($("#phone").val().length == 0) {
    //     $("#phone").parent().addClass("has-error");
    //     error_msg += '<div class="col-md-12"> - Your Phone is required.</div>';
    //     has_errors = true;
    // }
    if ($("#country").val().length == 0 || $("#country").val() == "Country") {
        $("#country").parent().addClass("has-error");
        error_msg += '<div class="col-md-12"> - Your Country is required.</div>';
        has_errors = true;
    }
    if ($(this).attr("data-type") == "booking") {
        if (!$("#terms-container input").is(":checked")) {
            $("#terms-container").addClass("alert alert-danger");
            has_errors = true;
        }
        if (parseFloat(calculateTotalPriceRequest.total) <= 0) {
            bootbox.alert("Book Now for this period is not available yet. Please contact with our agent by inquiry form to rent this villa.");
            has_errors = true;
        }
        if ($("#payment_type").val() != "Credit Card" && $("#payment_type").val() != "TT / Wire Transfer") {
            error_msg += '<div class="col-md-12"> - You must select Payment Type.</div>';
            $("#payment_type").parent().addClass("has-error");
            has_errors = true;
        }
        if ($("input[name=address]").val().length == 0) {
            error_msg += '<div class="col-md-12"> - Address is required.</div>';
            $("input[name=address]").parent().addClass("has-error");
            has_errors = true;
        }
        if ($("input[name=city]").val().length == 0) {
            error_msg += '<div class="col-md-12"> - City is required.</div>';
            $("input[name=city]").parent().addClass("has-error");
            has_errors = true;
        }
        if ($("input[name=state]").val().length == 0) {
            error_msg += '<div class="col-md-12"> - State is required.</div>';
            $("input[name=state]").parent().addClass("has-error");
            has_errors = true;
        }
        if ($("select[name=bedrooms]").val() === "") {
            error_msg += '<div class="col-md-12"> - No. of Bedrooms is required.</div>';
            $("select[name=bedrooms]").parent().addClass("has-error");
            has_errors = true;
        }
        if ($("input[name=zip]").val().length == 0) {
            error_msg += '<div class="col-md-12"> - Zip is required.</div>';
            $("input[name=zip]").parent().addClass("has-error");
            has_errors = true;
        }
        var ccResult = ccValidation();
        if (ccResult[1]) {
            has_errors = true;
            error_msg += ccResult[0];
        }
    }
    
    if (has_errors) {
        if (error_msg) bootbox.alert({
            closeButton: false,
            className: "enquiry-alert",
            message: '<h3>You must correct the errors in the following fields:</h3><div class="row">' + error_msg + '</div>',
        });
        return false;
    }
    
    var form = $(this).parent("#inquiry_form");
    if (form.hasClass("ajax")) {
        e.preventDefault();
        var data = {};
        // Serialize form data
        var formData = form.serialize();
    
        formData.split('&').forEach(function(pair) {
          var keyValue = pair.split('=');
          var key = decodeURIComponent(keyValue[0]);
          var value = decodeURIComponent(keyValue[1] || '');
          if (value) {
            data[key] = value;
          }
        });
        data = {
            action: "inquiry_form", 
            ...data
        }
    
        $.ajax({
            type : "POST",
            url : ajax_url.ajaxurl,
            data : {
                action: "inquiry_form", 
                ...data
            },
            success: function(ajaxResponse) {
                
                if(ajaxResponse.success) {
                    var popup = document.getElementById('popup');
                    popup.style.display = 'none';
                    bootbox.alert(ajaxResponse.data ? ajaxResponse.data[1] : "Error! Try again!");
                    var form = document.getElementById('inquiry_form');
                    form.reset();
                }else {
                    bootbox.alert(ajaxResponse.data ? ajaxResponse.data[1] : "Error! Try again!");
                }
    
            },
            error:function(error){
                console.log('message Error' + JSON.stringify(error));
            }                    
        });
    }
    else {
        jQuery('form#inquiry_form').submit();
        //form.submit();
    }
    // if (form.hasClass("ajax"))
    //     $(".modal-dialog").prepend('<div id="modal-preloader" style="width: 100%; height: 100%; position: absolute; background: url(/images/ajax-loader.gif) rgba(255,255,255,0.8) no-repeat center center; z-index: 10"></div>');
    // else form.prepend('<div id="modal-preloader" style="width: 100%; height: 100%; position: absolute; background: url(/images/ajax-loader-bluebg.gif) rgba(27, 51, 106, 0.8) no-repeat center center; z-index: 10"></div>');
    
    // $.post($(this).attr("data-url_check_captcha"), form.serialize(), function (response) {
    //     if (response == 1) {
    //         $.post(form.attr("action"), form.serialize(), function (response) {
    //             $(form.hasClass("ajax") ? ".modal-dialog" : "body")
    //                 .find("#modal-preloader")
    //                 .remove();
    //             if (response == "ok") {
    //                 ga("send", "event", "New Inquiry", "Sent");
    //                 window["optimizely"] = window["optimizely"] || [];
    //                 window.optimizely.push(["trackEvent", "inquiry_form_sent"]);
    //                 var msg = "";
    //                 if ($("#payment_type").val() == "Credit Card")
    //                     msg = "Your booking is now tentatively confirmed, subject to the property ownerâ€™s approval. Once approved your credit card will be charged and your booking confirmed.";
    //                 else if ($("#payment_type").val() == "TT / Wire Transfer")
    //                     msg =
    //                         "Thank you for your reservations request. Your booking is now tentatively confirmed, subject to the property ownerâ€™s approval. Once approved you will receive an email containing our account information.  Required payment will be due within 2 days of receiving this email.";
    //                 else msg = "Thank you for your inquiry, an experienced reservation specialist will contact you shortly.";
    //                 msg +=
    //                     "<br /><br /><b>*** IMPORTANT INFORMATION â€“ BEWARE FRAUDSTERS ***</b>" +
    //                     '<div style="font-size: 80%">' +
    //                     "<p>It has come to our attention that scammers/hackers are attempting to gain access to clients inquiry information from villa rental agencies.</p>" +
    //                     '<p>If you receive an email from another source claiming to have "Last minute Cancellations" or "Special Offers" and offering big discounts, usually with links to actual genuine websites;</p>' +
    //                     "<p>BE WARNED!!</p>" +
    //                     "<p>People have fallen victim to such scammers phishing attempts and in this way lost considerable sums of money.</p>" +
    //                     "<p>Please stay vigilant and ONLY use reputable/verifiable companies when renting holiday accommodation on the internet. Villa Getaways Ltd has been successfully providing its clients safe and enjoyable villa rentals since 2001. Trust the source.</p>" +
    //                     "</div>";
    //                 if (form.hasClass("ajax")) {
    //                     bootbox.alert('<span style="font-size: 18px;">' + msg + "</span>");
    //                     $("#modalInquiry .modal-content .close").trigger("click");
    //                 } else {
    //                     form.html('<span style="font-size: 18px; color: #fff">' + msg + "</span>");
    //                     window.scrollTo(0, 0);
    //                 }
    //             } else {
    //                 bootbox.alert(response ? response : "Error! Try again!");
    //             }
    //         });
    //     } else {
    //         $(form.hasClass("ajax") ? ".modal-dialog" : "body")
    //             .find("#modal-preloader")
    //             .remove();
    //         bootbox.alert("Please enter a valid code!");
    //     }
    // });
    
    //return false;
} );
    
$(document).ready(function(){
    $('#country').combobox({});   
    // $("#country").select2({
    //     placeholder: "Country",
    //     allowClear: true
    // });
    $('#reservation3_from').attr('type','date');
    $('#reservation3_from').css('background-position','90px 11px');
    $('#reservation3_to').attr('type','date');
    $('#reservation3_to').css('background-position','90px 11px');
    $('#reservation3_from').datepicker('show');
    // $('#reservation2_from').datepicker({
    //       dateFormat: 'dd/mm/yy',
    //       numberOfMonths: 3,
    //       minDate: 'today',
    //       beforeShowDay: function(date) {  
    //           console.log(date)
    //             return setDisabled(date, $('#reservation2_from').val(), $('#reservation2_to').val());
    //       }, 
    //       beforeShow: function(){
    //           beforeShowHelper(this);
    //       },
    //       onSelect: function(date)
    //       {
    //           setDateOnSelectCheckin('reservation2_to', date, true)
    //       }
    //       ,
    //       onChangeMonthYear: function(year, month, inst) { 
    //           onShow($('#calendar_url'));
    //       }
    // });
    
    // $('#reservation2_to').datepicker({
    //       dateFormat: 'dd/mm/yy',
    //       numberOfMonths: 3,
    //       minDate: '+1d',
    //       beforeShowDay: function(date) {  
    //           console.log(date)
    //             return setDisabled(date, $('#reservation2_from').val(), $('#reservation2_to').val());
    //       }, 
    //       beforeShow: function(input, inst){
    //             beforeShowHelper(this);
    //       }
    // });
    
    
    
    // $('#reservation3_from, #reservation3_to').change(function(){
    //     console.log('autopicked:', $(this).attr('min'));
    //     if($(this).attr('type') == 'date' && $(this).val() < $(this).attr('min')) {
    //         $(this).val($(this).attr('min'))  
    //     } 
    // }); 
    
    if ($(window).width() > 600){
        $('.selectpicker').selectpicker();  
    }
    if ($(window).width() < 768){
        $('#select-guests-2').css('width', '92%');  
        $('#select-guests-2').parent().append('<span style="color: red; padding-left: 5px;">*</span>');
    }
    if ($(window).width() < 992){
        $('html, body').scrollTop(0)
    }
    
    
    $('#inquiry_form input').keydown(function(e)
    {
    if(e.which > 46) $(this).addClass('filled');
    });
    
    $('#inquiry_form input').keyup(function(e)
    {
    if($(this).val() == '')  $(this).removeClass('filled');
    else $(this).addClass('filled');
    });
    
    $('#inquiry_form input').change(function()
    {
    if($(this).val() == '')  $(this).removeClass('filled');
    else $(this).addClass('filled');
    });
    
    $('#inquiry_form select').change(function()
    {
    if($(this).val() == '')  $(this).removeClass('filled');
    else $(this).addClass('filled');
    });
    
    $('#inquiry_form input').each(function(){
    if($(this).val() == '')  $(this).removeClass('filled');
    else $(this).addClass('filled');
    })
    
    // $("#date_from").datepicker({
    //     defaultDate: "today",
    //     changeMonth: true,
    //     changeYear: true,
    //     numberOfMonths: 1,
    //     dateFormat: "dd-mm-yy",
    //     minDate: "today"
    // });
    // $("#date_to").datepicker({
    //     defaultDate: "+1d",
    //     changeMonth: true,
    //     changeYear: true,
    //     numberOfMonths: 1,
    //     dateFormat: "dd-mm-yy",
    //     minDate: "+1d"
    // });
    
    // //jQuery('#date_from').datepicker('show');
    // $("#date_from").datepicker("option", "onSelect", function(selectedDate, inst) {
        
    //     $("#date_from").datepicker().trigger('blur');
    //     setTimeout(function() {
    //         $("#date_to").datepicker('show');
    //     }, 100);
    // });
})

$(document).ready(function() {
    if(window.location.pathname == "/inquiry/") {
        // Initialize the datepicker for the "from" input field and show the calendar
        $("#date_from").datepicker({
            defaultDate: "today",
            changeMonth: true,
            changeYear: true,
            numberOfMonths: 1,
            dateFormat: "dd-mm-yy",
            minDate: "today",
            onSelect: function(date) {
                $("#date_to").datepicker("option", "minDate", date);
                // When a date is selected in the "from" input field, open the calendar for the "to" input field
                setTimeout(function() {
                    $("#date_to").datepicker("show");
                }, 100);
                
            }
        }).datepicker("show"); // Open the calendar for the "from" input field on page load
    
        // Initialize the datepicker for the "to" input field
        $("#date_to").datepicker({
            defaultDate: "+1d",
            changeMonth: true,
            changeYear: true,
            numberOfMonths: 1,
            dateFormat: "dd-mm-yy",
            minDate: "+1d",
            onSelect: function(date) {
                // When a date is selected in the "to" input field, hide the calendar
                $("#date_to").datepicker("show");
            },
            onClose: function(dateText, inst) {
                // When the calendar for the "to" input field is closed, check if a date is selected
                if ($("#date_to").val() === "") {
                    // If no date is selected, hide the calendar for the "to" input field
                    $("#date_to").datepicker("show");
                }
            }
        });
    
        // Hide the calendar for the "to" input field initially
        $("#date_to").datepicker("hide");    
    }
    
    
});