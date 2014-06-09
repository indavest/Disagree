function renderBookmarkletInstructions(IDofTarget) {

    // Gets the browser requesting the content, using browser_detect.js
    var thisBrowser = BrowserDetect.browser;

    // Default values
    var defaults = {
        dragText:"Add this link to your Bookmarks Bar",
        browser:"your Browser",
        unicodeMagic:"", //"&#9906; ", // FIXME: Not working in many cases....temporarily removing.
        installationInstructions:"<ol>" +
            "<li>Add the &ldquo;Pin It&rdquo; button to your Bookmarks bar</li>" +
            "<li>When you are browsing the web, push the &ldquo;Pin It&rdquo; button to pin an image</li>" +
            "</ol>",
        videoSource:"http://www.youtube.com/embed/gRwy0mOAJ7U"
    };

    // Browser-specific values
    var instructions = {
        Generic:{
            unicodeMagic:defaults.unicodeMagic,
            dragText:defaults.dragText,
            browser:defaults.browser,
            installationInstructions:defaults.installationInstructions,
            videoSource:defaults.videoSource
        },
        Chrome:{
            unicodeMagic:defaults.unicodeMagic,
            dragText:defaults.dragText,
            browser:"Chrome",
            installationInstructions:"<ol>" +
                "<li>Display your Bookmarks by clicking the <strong>Wrench Icon > Tools > Always Show Bookmarks Bar</strong></li>" +
                "<li>Drag the &ldquo;Pin It&rdquo; button to your Bookmarks bar</li>" +
                "<li>When you are browsing the web, push the &ldquo;Pin It&rdquo; button to pin an image</li>" +
                "</ol>",
            videoSource:defaults.videoSource
        },
        Safari:{
            unicodeMagic:defaults.unicodeMagic,
            dragText:defaults.dragText,
            browser:"Safari",
            installationInstructions:"<ol>" +
                "<li>Display your Bookmarks by clicking <strong>View > Show Bookmarks Bar</strong></li>" +
                "<li>Drag the &ldquo;Pin It&rdquo; button to your Bookmarks bar</li>" +
                "<li>When you are browsing the web, push the &ldquo;Pin It&rdquo; button to pin an image</li>" +
                "</ol>",
            videoSource:"http://www.youtube.com/embed/L2tImC_2Gnw"
        },
        Firefox:{
            unicodeMagic:defaults.unicodeMagic,
            dragText:defaults.dragText,
            browser:"Firefox",
            installationInstructions:"<ol>" +
                "<li>Display your Bookmarks Bar by clicking <strong>View > Toolbars > Bookmarks Toolbar</strong></li>" +
                "<li>Drag the &ldquo;Pin It&rdquo; button to your Bookmarks Toolbar</li>" +
                "<li>When you are browsing the web, push the &ldquo;Pin It&rdquo; button to pin an image</li>" +
                "</ol>",
            videoSource:"http://www.youtube.com/embed/IXOQ9LO627U"
        },
        Explorer:{
            unicodeMagic:"",
            dragText:"Right-click and select &ldquo;Add to Favorites > Favorites Bar&rdquo;",
            browser:"Internet Explorer",
            installationInstructions:"<ol>" +
                "<li>Display your Favorites Bar by clicking <strong>Tools > Toolbars > Favorites Bar</strong></li>" +
                "<li>Right-click the &ldquo;Pin It&rdquo; button and select &ldquo;Add to Favorites&rdquo;</li>" +
                "<li>On the pop-up window, select &ldquo;Create in: Favorites Bar&rdquo;</li>" +
                "<li>When you are browsing the web, push the &ldquo;Pin It&rdquo; button to pin an image</li>" +
                "</ol>",
            videoSource:"http://www.youtube.com/embed/jl-zKQ5na1A"
        },
        Opera:{
            unicodeMagic:"",
            dragText:defaults.dragText,
            browser:"Opera",
            installationInstructions:"<ol>" +
                "<li>Display your Favorites Bar by clicking <strong>View > Toolbars > Bookmarks Bar</strong></li>" +
                "<li>Drag the &ldquo;Pin It&rdquo; button to your Bookmarks bar</li>" +
                "<li>When you are browsing the web, push the &ldquo;Pin It&rdquo; button to pin an image</li>" +
                "</ol>",
            videoSource:defaults.videoSource
        }
    };

    // If you're not in one of our 5 special browsers, use the generic instructions
    if (thisBrowser in instructions) {
        var browserName = thisBrowser;
    } else {
        var browserName = "Generic";
    }

    // Stitches together the browser-specific HTML
    var RenderedHtml =
        "<div id='PinButton'>" +
            "<div id='ButtonHolder'>" +
            "<a onclick=\"alert('Drag me to the bookarks bar'); return false;\" href=\"javascript:void((function(){var e=document.createElement('script');e.setAttribute('type','text/javascript');e.setAttribute('charset','UTF-8');e.setAttribute('src','http://assets.pinterest.com/js/pinmarklet.js?r='+Math.random()*99999999);document.body.appendChild(e)})());\" title=\"Pin It\" id=\"BigPinItButton\"><strong>" + instructions[browserName].unicodeMagic + "Pin It</strong><span></span></a>" +
            "<p id='ButtonInstructions'>&larr;&nbsp;&nbsp;" + instructions[browserName].dragText + "</p>" +
            "</div>" +
            "<p>To install the &ldquo;Pin It&rdquo; button in " + instructions[browserName].browser + ":</p>" +
            "<div id='InstallationInstructions' class='small'>" + instructions[browserName].installationInstructions + "</div>" +
            "<p>Once installed in your browser, the &ldquo;Pin It&rdquo; button lets you grab an image from any website and add it to one of your pinboards. When you pin from a website, we automatically grab the source link so we can credit the original creator.</p>" +
            "<iframe width='640' height='390' src='" + instructions[browserName].videoSource + "?rel=0&amp;hd=1' frameborder='0' allowfullscreen></iframe>" +
            "</div>";

    // Render the whole thing in the element with the id of parameter passed into the function
    jQuery('#' + IDofTarget).html(RenderedHtml);

}