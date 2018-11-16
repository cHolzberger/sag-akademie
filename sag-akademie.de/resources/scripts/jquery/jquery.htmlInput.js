/*
HTML Input for jQuery
Copyright (c) 2008 Anthony Johnston
http://www.antix.co.uk    
        
version 1.0.2
*/

/// <reference path="http://code.jquery.com/jquery-latest.min.js" />
/// <reference path="jquery.htmlClean.js" />

(function($) {
    var frames = 0; // counter incremented as frames added for identification

    // Convert the contol passed into an html editor
    $.htmlInput = function(input, options) {
        /// <summary>Convert a textarea element into a html editor<summary>
        /// <param name="input">textarea element (required)</param>
        /// <param name="options">options (optional)</param>
        options = jQuery.extend($.htmlInput.defaults, options);

        var control = this;
        var contentWindow, contentDocument;
        var selectedElement;

        var $input = $(input);

        this.setLink = function(hRef, target, type) {
            /// <summary>Add a new or edit an existing link<summary>

            if (hRef == "http:/".concat("/")
                || hRef == "") { hRef == null; }

            // set focus so that execCommand works
            control.focus();

            // check for a current link
            var link = findFirst(control.selectedElement, ["a"]);
            if (hRef == null) {
                if (link != null) {
                    // remove link
                    contentDocument.execCommand("unLink", false, null);
                }
                return;
            } else if (link == null) {
                // add a new link
                var content = control.getSelectedHtml(), after;
                var afterIndex = $.htmlClean.trimEndIndex(content);
                if (afterIndex > -1) {
                    after = content.substring(afterIndex);
                    content = content.substring(0, afterIndex);
                }
                control.insertHtml("<a href='".concat(hRef, "'>", content, "</a>", after));
                //contentDocument.execCommand("createLink", false, hRef);
                link = findFirst(control.getSelected(), ["a"]);
            }

            // set properties
            link = $(link);
            link.attr("href", hRef);
            if (target != null && target.length > 0) { link.attr("target", target); }
            else { link.removeAttr("target"); }
            if (type != null && type.length > 0) { link.attr("type", type); }
            else { link.removeAttr("type"); }

            control.update();
        }

        this.setImage = function(src, className) {
            /// <summary>Add a new or edit an existing image<summary>

            // set focus so that execCommand works
            control.focus();

            // check for a current image
            var image = findFirst(control.getSelectedElement, ["img"]);
            if (image == null && src != null) {
                // add a new image
                contentDocument.execCommand("insertimage", false, src);
                control.selectedElement = control.getSelected();
                image = findFirst(control.selectedElement, ["img"]);
            } else if (image != null && src == null) {
                // do nothing
                return;
            }

            // set properties
            image = $(image);
            image.attr("src", src);
            if (className != null && className.length > 0) { image.attr("class", className); }
            else { image.removeAttr("class"); }

            control.update();
        }

        this.update = function() {
            /// <summary>Update the editor, clean the html and show the status of each tool button<summary>

            var html = $.htmlClean.trim(contentWindow.document.body.innerHTML);
            if (html.length < 7) { contentDocument.execCommand("formatblock", false, "<p>") }
            $input.attr("value", $.htmlClean(html));
            //$input.attr("value", html);

            // get the currently selected element
            control.selectedElement = control.getSelected();

            // show button statuses
            for (var name in this.toolbar.tools) {
                var tool = this.toolbar.tools[name];
                var value = false;
                switch (name) {
                    case Tools.bulletList.command:
                    case Tools.numberList.command:
                        var listItem = findFirstTagName(control.selectedElement, ["ul", "ol"]);
                        value = (name == Tools.bulletList.command && listItem == "ul")
                                || (name == Tools.numberList.command && listItem == "ol")
                        break;
                    case Tools.bold.command:
                        value = findFirst(control.selectedElement, ["strong", "b", ["span", /weight:\s*bold/i]]); break;
                    case Tools.italic.command:
                        value = findFirst(control.selectedElement, ["em", "i", ["span", /style:\s*italic/i]]); break;
                    case Tools.superscript.command:
                        value = findFirst(control.selectedElement, ["sup", ["span", /-align:\s*super/i]]); break;
                    case Tools.subscript.command:
                        value = findFirst(control.selectedElement, ["sub", ["span", /-align:\s*sub/i]]); break;
                    case Tools.block.command:
                        tool.command.val(findFirstTagName(control.selectedElement, ["p", "h1", "h2", "h3", "h4", "h5", "h6", "pre"]));
                        continue;
                    case Tools.link.command:
                        value = findFirst(control.selectedElement, ["a"]); break;
                }

                value
                    ? tool.addClass("selected")
                    : tool.removeClass("selected");
            }
        }
        if (window.getSelection) {
            this.getSelected = function() {
                var selection = contentWindow.getSelection();
                if (selection.rangeCount > 0) {
                    var range = selection.getRangeAt(0);
                    return range.collapsed || range.startContainer.childNodes.length == 0
                        ? selection.focusNode
                        : range.startContainer.childNodes[range.startOffset];
                }
            }
            this.getSelectedHtml = function() {
                var selection = contentWindow.getSelection();
                if (selection.rangeCount > 0) {
                    var range = selection.getRangeAt(0);
                    var el = document.createElement("div");
                    el.appendChild(range.cloneContents());
                    return el.innerHTML;
                }
            }
            this.setSelected = function(item) {
                var selection = contentWindow.getSelection();
                selection.removeAllRanges();
                if (item) {
                    var range = contentDocument.createRange();
                    range.selectNodeContents(item);
                    selection.addRange(range);
                }
            }
            this.insertHtml = function(html) {
                contentDocument.execCommand("insertHTML", null, html);
                control.update();
            }
        } else { // ie
            this.getSelected = function() {
                control.focus();
                var range = contentDocument.selection.createRange();
                return range.item
                        ? range.item(0)
                        : range.parentElement();
            }
            this.getSelectedHtml = function() {
                control.focus();
                var range = contentDocument.selection.createRange();
                return range.htmlText;
            }
            this.setSelected = function(item) {
                try {
                    control.focus();
                    var range = contentDocument.selection.createRange();
                    range = contentDocument.body.createTextRange();
                    range.moveToElementText(item);
                    range.select();
                } catch (e) { }
            }
            this.insertHtml = function(html) {
                var range = contentDocument.selection.createRange();
                if (range.item) {
                    range.item(0).outerHTML = html;
                } else {
                    range.pasteHTML(html);
                }
                control.update();
            }
        }

        this.cleanEditor = function() {
            /// <summary>htmlClean the editor content</summary>
            contentWindow.document.body.innerHTML
                    = $.htmlClean(contentWindow.document.body.innerHTML);
        }

        this.applyCommand = function(command, value) {
            /// <summary>htmlClean the editor content</summary>
            /// <param name="command">Command to apply, such as 'bold', 'formatBlock' etc</param>
            /// <param name="value">Any value associated with the command such as 'h1' with 'formatBlock'</param>
            control.focus();
            switch (command) {
                default:
                    contentDocument.execCommand(command, false, null);

                    break;
                case Tools.block.command:
                    contentDocument.execCommand(command, false, "<" + value + ">");

                    break;
                case Tools.bulletList.command:
                case Tools.numberList.command:
                    contentDocument.execCommand(command, false, null);

                    break;
            }
            control.update();
        }

        this.setContent = function(html) {
            /// <summary>Set the content window and document up, using the passed html as the body</summary>
            /// <param name="html">Html string used for the content document body</param>
            contentWindow = $frame.attr("contentWindow") ? $frame.attr("contentWindow") : window.frames[frameId].window;
            contentDocument = contentWindow.document;

            html = "<html><head>".concat(
                    "<link href='", options.styleUrl, "' rel='stylesheet' type='text/css' />",
                    "<style type='text/css'>body{overflow:auto;}</style></head><body class='editor'>", html, "</body></html>");
            try {
                contentDocument.designMode = "on";
                contentDocument.open();
                contentDocument.write(html);
                contentDocument.close();
            } catch (e) { }

            try {
                // stop moz using inline styles, can't do anything about webkit at the mo
                contentDocument.execCommand('useCSS', false, true); // off (true=off!)            
                contentDocument.execCommand('styleWithCSS', false, false); // new implementation of the same thing
            } catch (ex) { }

            if ($.browser.msie) {
                $frame.blur(function() { control.blur(); });
            } else {
                $(contentWindow).blur(function() { control.blur(); });
            }
            $(contentDocument).keyup(function() { control.update(); });
            $(contentDocument).mouseup(function() { control.update(); });

            if ($.browser.msie) {
                contentDocument.body.onpaste = function() { window.setTimeout(control.cleanEditor, 0); };
                contentDocument.onbeforedeactivate = function() {
                    try {
                        control.bookmark = contentDocument.selection.createRange();
                    } catch (ex) { }
                };
                contentDocument.onactivate = function() {
                    // if a book mark has been saved restore its selection, ie only
                    if (control.bookmark) {
                        control.bookmark.select();
                        control.bookmark = null;
                    }
                };
            } else if ($.browser.mozilla) {
                try {
                    contentDocument.body.addEventListener('input', control.cleanEditor, false);
                } catch (ex) { }
            }
        }

        this.resetContent = function() {
            ///<summary>Reset the content using the current html</summary>
            control.setContent(control.html());
        }

        this.html = function(html) {
            /// <summary>Get or set the content document html</summary>
            /// <param name="html">Html string</param>
            if (html != undefined) {
                contentDocument.body.innerHTML = html;
            }
            else { return contentDocument.body.innerHTML; }
        }

        this.focus = function() {
            /// <summary>Actions on focus of the editor</summary>
            contentWindow.focus();
        }
        this.blur = function() {
            /// <summary>Actions on blur of the editor</summary>
            $input.change();
        }

        // set up the toolbar and editor
        var $toolbar = $("<div class='toolbar'></div>");
        this.toolbar = new Toolbar($toolbar[0], function(e) {
            var $command = $(this);
            switch ($command.attr("name")) {
                case "link":
                    var link = findFirst(control.selectedElement, ["a"]);
                    if (link && !$.browser.msie) { control.setSelected(link); }
                    options.editLink(e, control, link, control.setLink);

                    break;
                case "image":
                    options.editImage(e, control, findFirst(control.selectedElement, ["img"]), control.setImage);

                    break;
                default:
                    control.applyCommand($command.attr("name"), $command.attr("value"));
                    break;
            }
        });
        this.toolbar.addButtons(options);

        frames++;
        var $frame = $("<iframe frameborder='no' border='0' marginWidth='0' marginHeight='0' leftMargin='0' topMargin='0' allowTransparency='true' scroll='yes'>");
        var frameId = "htmlInput_".concat(frames);
        $frame.attr("id", frameId);
        $frame.attr("src", $.browser.msie ? "javascript:false;" : "javascript:;");

        if (!options.debug) {
            // hide the original textarea
            $input.css("display", "none");
        }

        // add to container
        var $container = $("<div style='position:relative;overflow:hidden;'></div>");
        $container.attr("id", $input.attr("id"));
        $container.attr("class", $input.attr("class"));
        $input.before($container);
        $container.append($toolbar);
        $container.append($frame);

        // set the content
        this.setContent($.htmlClean($input.attr("value")));

        // position
        $container.height($input.height());
        $container.width($input.width());

        var toolbarHeight = $toolbar.height();

        $frame.height($input.height() - toolbarHeight);
        $frame.width($input.width());
        var frameHeight = $frame.height();

        // allow access
        this.document = contentDocument;
        this.element = $container[0];

        if ($.browser.mozilla || $.browser.safari) {
            $frame.parents().bind("DOMNodeInserted", function(e) {
                if ($(e.target).find("#".concat(frameId)).length == 1) {
                    window.setTimeout(control.resetContent, 0);
                    e.stopPropagation();
                }
            });
        }

        return this;
    }

    /* Helpers */
    function findFirstTagName(element, tags) {
        /// <summary>Find the first matching element up the heirachy starting from, and including, the element passed</summary>
        /// <param name="element">Element to start search from</param>
        /// <param name="tags">A string array of tags to look for</param>
        /// <returns>Element tagName found, or null if not found</returns>

        element = findFirst(element, tags);
        return element == null ? "" : element.tagName.toLowerCase();
    }

    function findFirst(element, tags) {
        /// <summary>Find the first matching element up the heirachy starting from, and including, the element passed</summary>
        /// <param name="element">Element to start search from</param>
        /// <param name="tags">A string array of tags to look for</param>
        /// <returns>Element found, or null if not found</returns>

        while (element != null
                && (findFirstMatch(element, tags) == -1)) {
            element = element.parentNode;
        }

        return element;
    }
    function findFirstMatch(element, tags) {
        if (element.tagName) {
            var tag = element.tagName.toLowerCase();
            for (var i = 0; i < tags.length; i++) {
                if (tags[i] == tag
                    || (tags[i].constructor == Array && tags[i][0] == tag
                        && tags[i][1].test(element.getAttribute("style")))) { return i; }
            }
        }
        return -1;
    }
    function findHRef(element) {
        element = findFirst(element, ["a"]);
        return element == null ? "" : element.href;
    }

    function editPopup(e, control, element, urlProperty, showRemove, callBack) {
        /// <summary>Shows a popup in the toolbar requesting a Url for an image or link</summary>
        var $popup = $("<div id='Popup' style='float:left;'><label style='float:left;width:35px;padding:3px 5px 0 0;text-align:right'>url</label></div>");
        var $input = $("<input style='float:left;margin-top:1px' />");
        var $ok = $("<span class='tool'><a style='width:55px'>ok</a></span>");
        var $cancel = $("<span class='tool'><a style='width:55px'>cancel</a></span>");

        $popup.append($input);
        $popup.append($ok);
        $popup.append($cancel);
        $popup.click(function(e) { e.stopPropagation(); });

        $toolbar = $(control.toolbar.element);
        $toolbar.append($popup);
        $input.width($toolbar.width() > 500 ? 335 : $toolbar.width() - 175);
        if (element) {
            if (showRemove) { $cancel.contents("a").text("remove"); }
            $input.val(element[urlProperty]);
        } else {
            $input.val("");
        }

        $tools = $toolbar.contents("span");
        var toolsHeight = $tools.height() + 5;
        $tools.animate({ marginTop: -toolsHeight }, {
            duration: 500,
            complete: function() {
                $input.focus();
            }
        });

        var $button = $ok.add($cancel);
        $button.mouseover(function() { $(this).addClass("hover"); });
        $button.mouseout(function() { $(this).removeClass("hover"); });
        $button.click(function(e) {
            callBack($(this).contents("a").text() == "ok" ? $input.val() : null);
            $tools.animate({ marginTop: 0 }, {
                duration: 250,
                complete: function() {
                    $tools.unbind();
                    $(document).add(control.document).unbind("click.editPopup");
                    $button.unbind();
                    $input.unbind();
                    $popup.unbind();
                    $popup.remove();
                }
            });
            e.preventDefault();
        });
        $(document)
            .add(control.document)
            .bind("click.editPopup", function() { $cancel.click() });
        $input.keydown(function(e) {
            switch (e.which) {
                case 13: $ok.click(); break;
                case 27: $cancel.click(); break;
            }
        });
    }

    function editLink(e, control, anchor, callBack) {
        /// <summary>Show the standard url popup in the toolbar for a link</summary>
        editPopup(e, control, anchor, "href", true, callBack);
    }

    function editImage(e, control, image, callBack) {
        /// <summary>Show the standard url popup in the toolbar for an image</summary>
        editPopup(e, control, image, "src", false, callBack);
    }

    // objects
    function Toolbar(element, onCommand) {
        this.element = $(element);
        this.tools = {};

        this.element.click(function(e) { e.stopPropagation(); });

        this.add = function(name, command) {
            var $tool = $("<span class='tool " + name + "'></span>");

            this.element.append($tool);
            if (command) {
                $tool.append(command);

                command.attr("name", name);
                $tool.command = command;
            }
            $tool.selected = function(value) {
                if (value) { this.addClass("selected"); }
                else { this.removeClass("selected"); }
            }

            this.tools[name] = $tool;
            return $tool;
        }

        this.addButton = function(tool) {
            var command = $("<a title='" + tool.tooltip + "' tabindex='0'>" + tool.content + "</a>");

            var $tool = this.add(tool.command, command);

            command.click(onCommand);
            $tool.mouseover(function() { $(this).addClass("hover"); });
            $tool.mouseout(function() { $(this).removeClass("hover"); });
        }

        this.addList = function(tool) {
            var command = $("<select title='" + tool.tooltip + "' tabindex=0></select>");

            var $tool = this.add(tool.command, command);

            command.change(onCommand);
            for (var value in tool.content) {
                var item = "<option value='".concat(value, "'>", tool.content[value], "</option>");
                command.append(item);
            }
        }

        this.addSeparator = function(tool) {
            this.add(tool.command, null);
        }

        this.addButtons = function(options) {
            for (var name in options.tools) {
                var tool = options.tools[name];
                if (tool.add) { this["add" + tool.type](tool); }
            }
        }
    }

    // Tool object
    function Tool(type, command, content, tooltip) {
        this.type = type;
        this.command = command;
        this.content = content;
        this.add = true;
        this.tooltip = tooltip;
    }

    // types of tools
    var ToolTypes = {
        Button: "Button",
        List: "List",
        Separator: "Separator"
    };

    // Tools
    var Tools = {
        bold: new Tool(ToolTypes.Button, "bold", "<strong>B</strong>", "selection bold"),
        italic: new Tool(ToolTypes.Button, "italic", "<em>I</em>", "selection itallic"),
        superscript: new Tool(ToolTypes.Button, "superscript", "X<sup>2</sup>", "selection superscript"),
        subscript: new Tool(ToolTypes.Button, "subscript", "X<sub>2</sub>", "selection subscript"),
        subSeparator: new Tool(ToolTypes.Separator),
        removeFormat: new Tool(ToolTypes.Button, "removeformat", "~", "remove selection formatting"),
       
        bulletList: new Tool(ToolTypes.Button, "insertUnorderedList", "&bull; &equiv;", "selection as a numbered list"),
        numberList: new Tool(ToolTypes.Button, "insertOrderedList", "1 &equiv;", "selection as a bulleted list"),
        increaseIndent: new Tool(ToolTypes.Button, "indent", "&gt; &equiv;", "increase indent"),
        decreaseIndent: new Tool(ToolTypes.Button, "outdent", "&lt; &equiv;", "decrease indent"),
        decreaseIndentSeparator: new Tool(ToolTypes.Separator),
        link: new Tool(ToolTypes.Button, "link", "<strong>&infin;</strong>", "add/edit link"),
        image: new Tool(ToolTypes.Button, "image", "&#10065;", "add/edit image")
    };

    // defaults
    $.htmlInput.defaults = {
        styleUrl: "Styles/Editor.css",
        editLink: editLink,
        editImage: editImage,
        debug: false,
        tools: Tools
    };

})(jQuery);