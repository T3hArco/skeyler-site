$(function () {


  /////// BBCode

  // caches from keydown for use in keyup if necessary
  // todo: fix ctrl+z
  var oldContents = '';
  var oldCaret = {};

  // handles keyboard shortcuts on keydown
  $('#postContent').on('keydown', function (e) {
    oldContents = $(this).val();

    var ctrl = e.metaKey || e.ctrlKey;
    var key = e.keyCode;

    if (ctrl) {
      switch (key) {
        case 66:
        case 73:
          e.stopPropagation();
          e.preventDefault();
          return false;
          break;
        case 86:
          oldCaret = $(this).caret();
          break;
      }
    }
  });


  // handles keyboard shortcuts on keyup
  $('#postContent').on('keyup', function (e) {
    var newContents = $(this).val();

    var ctrl = e.metaKey || e.ctrlKey;
    var key = e.keyCode;

    var modifiedText = false;

    if (ctrl) {
      var caret = $(this).caret();
      console.log(caret)
      var modifiedCaretStart = caret.start;
      var modifiedCaretEnd = caret.end;

      switch (key) {
        case 86: // pressed ctrl+v
          var copiedText = newContents.substring(oldCaret.start, caret.start);
          var copiedTextLength = copiedText.length;
          // if a url/img start tag is already there, we don't want to auto-add tags
          if (newContents.substring(oldCaret.start - 5, oldCaret.start) in {'[img]': 1, '[url]': 1}) {
            break;
          }
          // checks if a url was pasted
          copiedText = copiedText.replace(/^(https?:\/\/[^\s]*)/i, function (z, url) {
            // if it's a url, check if it is an image, so bbcode can be autowrapped around the pasted text
            if (/\.(bmp|gif|jpe?g|png)\??.*?$/i.test(url) || /^http:\/\/cloud\.steampowered\.com\/ugc\//.test(url)) {
              return '[img]' + url + '[/img]';
            } else {
              return '[url]' + url + '[/url]';
            }
            // todo: also catch youtubes
          });
          modifiedText = newContents.substr(0, oldCaret.start) + copiedText + newContents.slice(oldCaret.start + copiedTextLength);
          modifiedCaretStart = caret.start + 11;
          modifiedCaretEnd = caret.start + 11;
          break;
        case 66: // pressed ctrl+b
          modifiedText = newContents.substr(0, caret.start) + '[b]' + caret.text + '[/b]' + newContents.slice(caret.end);
          modifiedCaretStart = caret.start;
          modifiedCaretEnd = caret.end + 7;
          break;
        case 73: // pressed ctrl+i
          modifiedText = newContents.substr(0, caret.start) + '[i]' + caret.text + '[/i]' + newContents.slice(caret.end)
          modifiedCaretStart = caret.start;
          modifiedCaretEnd = caret.end + 7;
          break;
      }
      if (modifiedText !== false) {
        $(this).val(modifiedText);
        $(this).caret(modifiedCaretStart, modifiedCaretEnd);
      }
    }

    $('#parsedContent').html(bbcode.parse($(this).val()));

  });

  if($('#parsedContent').length && $('#postContent').length) {
    $('#parsedContent').html(bbcode.parse($('#postContent').val()));
  }

  ////// END BBCode

});







