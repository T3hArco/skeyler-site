$(function () {


  // handles clicking a quoted post link
  $('a.postLink').on('click', function () {
    var postId = 0;
    ($(this).attr('href') || '').replace(/postId=(\d+)/i, function (z, postIdTemp) {
      postId = postIdTemp;
    });

    // if the post is on the current page, scroll to it
    if ($('.post-' + postId).length) {
      var offsetTop = $('.post-' + postId).offset().top || 0;
      $('body').animate({scrollTop : offsetTop}, 250);
      return false;
    }
    // otherwise continue with normal browser behavior
    return true;
  });

  // handles clicking the quoteLink
  $('.quoteLink').on('click', function () {
    var $post = $(this).closest('.post');
    var bbcode = $post.data('bbcode') || '';
    var postId = $post.data('postId');
    var username = $post.find('.userLink').text();

    // remove nested quotes because they dont work and we dont want pyramids of quotes
    bbcode = bbcode.replace(/\[quote="[^"]+?" postid="\d+"\].*?\[\/quote\]\s*/gi, '');

    var quoteText = '[quote="' + ent(username) + '" postid="' + postId + '"]' + bbcode + '[/quote]\n';

    var $postContent = $('#postContent');
    var oldText = $postContent.val();
    var oldCaret = $postContent.caret();


    var newText = oldText.substr(0, oldCaret.start) + quoteText + oldText.slice(oldCaret.end);

    $postContent.focus().val(newText).caret(oldCaret.start + quoteText.length);



    return false;
  });


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
        case 66: // ctrl+b
        case 73: // ctrl+i
          e.stopPropagation();
          e.preventDefault();
          return false;
          break;
        case 86:  //ctrl+v
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
            } else if (/^https?:\/\/(?:www\.)?youtube\.com\/watch\?v=([a-z0-9_-]+)/i.test(url)) {
              url.replace(/^https?:\/\/(?:www\.)?youtube\.com\/watch\?v=([a-z0-9_-]+)/i, function (z, youtubeId) {
                url = youtubeId;
              });
              return '[youtube]' + url + '[/youtube]';
            } else {
              return '[url]' + url + '[/url]';
            }
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

  if ($('#parsedContent').length && $('#postContent').length) {
    $('#parsedContent').html(bbcode.parse($('#postContent').val()));
  }

  ////// END BBCode

});


function ent(str){
  return (str||'').toString().replace(/[<>'"&]/g, function(a){
    return ent.replaces[a];
  });
}
ent.replaces = {
  '<' : '&lt;',
  '>' : '&gt;',
  '\'' : '&#39;',
  '"' : '&quot;',
  '&' : '&amp;'
};






