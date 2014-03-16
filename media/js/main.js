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
      $('html, body').animate({scrollTop: offsetTop}, 250);
      return false;
    }
    // otherwise continue with normal browser behavior
    return true;
  });

  // handles clicking the quoteLink
  $('.quote').on('click', function () {
    var $post = $(this).closest('.post');
    var bbcode = $post.data('bbcode') || '';
    var postId = $post.data('postId');
    var username = $post.find('.userLink:eq(0)').text();

    // remove nested quotes because they dont work and we dont want pyramids of quotes
    bbcode = bbcode.replace(/\[quote=".+?" postid="\d+"\].*?\[\/quote\]\s*/gi, '');

    var quoteText = '[quote="' + username + '" postid="' + postId + '"]' + bbcode + '[/quote]\n';

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
  // TODO: verify there are no race conditions here
  $('#postContent').on('keyup', function (e) {

    // all the browsers are the worst!
    var canExecInsertText = false;
    try {
      canExecInsertText = document.execCommand && document.queryCommandEnabled('insertText');
    } catch (e) {
    }

    if (!e) {
      e = {};
    }

    var newContents = $(this).val();

    var ctrl = e.metaKey || e.ctrlKey;
    var key = e.keyCode;

    var modifiedText = false;
    var caret = $(this).caret();
    var modifiedCaretStart = caret.start;
    var modifiedCaretEnd = caret.end;

    if (ctrl) {

      var insertText = '';

      switch (key) {
        case 86: // pressed ctrl+v
          var copiedText = newContents.substring(oldCaret.start, caret.start);
          var copiedTextLength = copiedText.length;

          // the amount of displacement caused by adding strings to the text pasted
          var copiedTextOffset = 0;
          // if a url/img start tag is already there, we don't want to auto-add tags
          if (newContents.substring(oldCaret.start - 5, oldCaret.start) in {'[img]': 1, '[url]': 1}) {
            break;
          }
          // checks if a url was pasted
          copiedText = copiedText.replace(/^(https?:\/\/[^\s]*)/i, function (z, url) {
            // if it's a url, check if it is an image, so bbcode can be autowrapped around the pasted text
            if (/\.(bmp|gif|jpe?g|png)\??.*?$/i.test(url) || /^http:\/\/cloud\.steampowered\.com\/ugc\//.test(url)) {
              copiedTextOffset = 11;
              return '[img]' + url + '[/img]';
            } else if (/^https?:\/\/(?:www\.)?youtube\.com\/watch\?v=([a-z0-9_-]+)/i.test(url)) {
              var originalUrlLength = url.toString().length;
              url.replace(/^https?:\/\/(?:www\.)?youtube\.com\/watch\?v=([a-z0-9_-]+)/i, function (z, youtubeId) {
                url = youtubeId;
              });
              copiedTextOffset = url.toString() - originalUrlLength;
              return '[youtube]' + url + '[/youtube]';
            } else {
              copiedTextOffset = 11;
              return '[url]' + url + '[/url]';
            }
          });
          modifiedText = newContents.substr(0, oldCaret.start) + copiedText + newContents.slice(oldCaret.start + copiedTextLength);
          modifiedCaretStart = caret.start + copiedTextOffset;
          modifiedCaretEnd = caret.start + copiedTextOffset;
          insertText = copiedText;
          break;
        case 66: // pressed ctrl+b
          modifiedText = newContents.substr(0, caret.start) + '[b]' + caret.text + '[/b]' + newContents.slice(caret.end);
          modifiedCaretStart = caret.start;
          modifiedCaretEnd = caret.end + 7;
          insertText = '[b]' + caret.text + '[/b]';
          break;
        case 73: // pressed ctrl+i
          modifiedText = newContents.substr(0, caret.start) + '[i]' + caret.text + '[/i]' + newContents.slice(caret.end);
          modifiedCaretStart = caret.start;
          modifiedCaretEnd = caret.end + 7;
          insertText = '[i]' + caret.text + '[/i]';
          break;
      }
      if (modifiedText !== false) {
        // if the execCommand is available, use it to fix undos
        if (canExecInsertText) {
          // if pasting, need to undo the existing paste
          if (key == 86) {
            document.execCommand('undo', false);
          }
          document.execCommand('insertText', false, insertText);
        } else {
          // otherwise, replace the textarea with the modified version
          $(this).val(modifiedText);
        }
        $(this).caret(modifiedCaretStart, modifiedCaretEnd);
      }
    } else {
      // didn't press ctrl
      switch (key) {
        case 13:
          // lists add [*] when you press enter
          var nearestBeforeListStart = newContents.substring(0, modifiedCaretStart).lastIndexOf('[list]');
          var nearestBeforeListEnd = newContents.substring(0, modifiedCaretStart).lastIndexOf('[/list]');
          var nearestAfterListEnd = newContents.substring(modifiedCaretStart).lastIndexOf('[/list]');

          // if no [list] before the caret
          // if there's [/list] after the nearest [list]
          // if there's no [/list]
          if (nearestBeforeListStart == -1
            || (nearestBeforeListEnd != -1 && nearestBeforeListEnd > nearestBeforeListStart)
            || nearestAfterListEnd == -1) {
            break;
          }
          var insertText = '[*]';
          var caretOffset = insertText.length;

          if (newContents.substring(modifiedCaretStart - 7, modifiedCaretStart + 7) === '[list]\n[/list]') {
            insertText += '\n';
          }

          if (canExecInsertText) {
            document.execCommand('insertText', false, insertText);
          } else {
            //otherwise update the textarea with the new content
            modifiedText = newContents.substr(0, caret.start) + caret.text + insertText + newContents.slice(caret.end);
            $(this).val(modifiedText);
          }
          $(this).caret(modifiedCaretStart + caretOffset, modifiedCaretEnd + caretOffset);

          break;
      }
    }

    var parsedContent = bbcode.parse($(this).val());

    if (parsedContent.length == 0) {
      parsedContent = '<em>[Type some text in the textarea and it will update down here as a preview.]</em>';
    }

    $('#parsedContent').html(parsedContent);
    if (!_.isEmpty(e)) {
      Draft.save($(this).val());
    }

  });

  if ($('#parsedContent').length && $('#postContent').length) {

    var parsedContent = bbcode.parse($('#postContent').val());
    if (parsedContent.length == 0) {
      parsedContent = '<em>[Type some text in the textarea and it will update down here as a preview.]</em>';
    }
    $('#parsedContent').html(parsedContent);
  }

  $('#postTitle').on('keyup', function () {
    var title = $(this).val();
    if (title.length == 0) {
      title = 'Your title could be here!';
    }
    $('#parsedTitle').text(title);
  });

  ////// END BBCode


  //chatbox
  if ($('#chatbox').length) {
    $chatbox = $('#chatbox');
    $chats = $chatbox.find('.chats');
    chatbox = {
      isEnabled: isChatboxEnabled,
      displayedCount: 0,
      lowestId: 0,
      highestId: 0,
      timeout: 5000,
      maxCount: 5,
      playNoise: false
    };
    if (chatbox.isEnabled) {
      updateChatbox();
      chatbox.interval = setTimeout(updateChatbox, chatbox.timeout);
    }
  }

  // submit a message to the chatbox
  $('#chatboxPostForm').on('submit', function () {
    var content = $(this).find('#chatboxPost').val();
    if (content.length > 0) {
      $div = $('<div>')
        .addClass('chat-message')
        .text('Sending message...')
        .appendTo($chats)
      ;
      $chats.scrollTop($chats.get(0).scrollHeight + 200);
      $(this).find('#chatboxPost').val('');

      $.post('/api/chatboxPost.php', {content: content}, function (json) {
        data = json;
        $chats.find('.chat-message').remove();
        if (data.success) {
          $div = $('<div>')
            .addClass('chat chat-fake')
            .append(
              $('<span>').text(writeDate(data.timestamp, 'small') + ' '),
              $('<a>')
                .addClass('userLink tag-' + data.rankStr)
                .attr('href', '/user.php?userId=' + data.userId)
                .text(data.username),
              $('<span>').text(': ' + data.content)
            )
          ;
          $chats.append($div);
          $chats.scrollTop($chats.get(0).scrollHeight + 200);
        } else {
          $div = $('<div>')
            .addClass('chat-message chat-error')
            .text('Error sending message.')
            .appendTo($chats)
          ;
          $chats.scrollTop($chats.get(0).scrollHeight + 200);
        }

      })
    }
    return false;
  });


  // show/hide the mod stuff
  $('.modDropdown .modButton').on('click', function () {
    $that = $(this);
    $that.add($that.closest('.postOptions')).addClass('active');
    $that.closest('.modDropdown').find('.mod').show();
    $that.show();
    $(window).off('click.hideMod, touchstart.hideMod').on('click.hideMod, touchstart.hideMod', function (e) {
      if ($(e.target).closest('.mod').length) {
        return true;
      }
      $('.mod').hide();
      $('.modButton').add('.postOptions').removeClass('active');
      $(window).off('click.hideMod, touchstart.hideMod');
      return false;
    });

    return false;
  });

  // you're modding someone on a thread!!!
  $('.mod-thread a').on('click', function () {
    var threadData = $('.posts').data();
    $el = $(this).closest('li');

    var postData = {};
    var modType = $el.data('modType');
    var sendNow = true;

    var callback = function (data) {

      var cb = noop();

      // if successful, we want to redirect/refresh
      if (data.success) {
        cb = function () {
          document.location = document.location;
        }
      }

      handleNotices(data, cb);

      // make the dropdowns go away
      $(window).click();
    };

    var send = function (cb) {
      cb = cb || noop;
      var cb2 = function (data) {
        callback(data);
        cb();
      };
      $.post('/mod/' + modType + '.php', postData, cb2);
    };

    switch (modType) {
      case 'closeThread':
        postData = {
          threadId: threadData.threadId
        };
        break;
      case 'openThread':
        postData = {
          threadId: threadData.threadId
        };
        break;
      case 'stickyThread':
        postData = {
          threadId: threadData.threadId
        };
        break;
      case 'unstickyThread':
        postData = {
          threadId: threadData.threadId
        };
        break;
      case 'moveThread':
        sendNow = false;
        $.getJSON('/api/getForumList.php', function (data) {

          var $popup = $('<div>').addClass('popup');
          var $popupContainer = $('<div>').addClass('popupContainer');
          var $select = $('<select>');
          var forums = data.forumList;
          var depth = -1;

          function loopSelect(forum) {
            depth += 1;
            for (var forumId in forum['subforums']) {
              var $option = $('<option>')
                  .val(forumId)
                  .text(strRepeat('--', depth) + forum['subforums'][forumId].name)
                  .appendTo($select)
                ;
              loopSelect(forum['subforums'][forumId]);
            }
            depth -= 1;
          }

          loopSelect(forums);

          var $submit = $('<button>').text('Move Thread!').on('click', function () {
            postData = {
              threadId: threadData.threadId,
              forumId: $select.val()
            };
            send(function () {
              $popupContainer.remove();
            });

          });

          $h4 = $('<h4>').html('It is my expert diagnosis that this thread needs to be moved.<br />But where to?');

          var $cancel = $('<a>')
              .attr('href', '#')
              .text('Close! Undo! Undo! I changed my mind!')
              .addClass('popupCloseLink')
              .on('click', function () {
                $popupContainer.remove();
                return false;
              })
            ;

          $popup
            .append($h4, $select, $submit, $cancel)
          ;
          $(window).on('keydown.popup', function (e) {
            var key = e.keyCode;
            if (key == 27) {
              $cancel.click();
              $(window).off('keydown.popup');
            }
          });

          $popupContainer
            .append($popup)
            .appendTo($('body'))
          ;


        });

        break;
      case 'deleteThread':
        var z = confirm('Are you sure you want to delete this thread? It will be moved to the deleted forum.');
        if (!z) {
          return false;
        }
        postData = {
          threadId: threadData.threadId
        };
        break;
      case 'renameThread':
        sendNow = false;

        var $popup = $('<div>').addClass('popup');
        var $popupContainer = $('<div>').addClass('popupContainer');
        var $input = $('<input>')
            .val($('.threadTitle').text())
            .attr('placeholder', $('.threadTitle').text())
          ;

        var $submit = $('<button>').text('Edit Thread Title!').on('click', function () {
          postData = {
            threadId: threadData.threadId,
            title: $input.val()
          };
          send(function () {
            $popupContainer.remove();
          });

        });

        $h4 = $('<h4>').html('It is my expert diagnosis that this thread needs to be renamed.<br />But what to?');

        var $cancel = $('<a>')
            .attr('href', '#')
            .text('Close! Undo! Undo! I changed my mind!')
            .addClass('popupCloseLink')
            .on('click', function () {
              $popupContainer.remove();
              return false;
            })
          ;

        $popup
          .append($h4, $input, $submit, $cancel)
        ;

        $(window).on('keydown.popup', function (e) {
          var key = e.keyCode;
          if (key == 27) {
            $cancel.click();
            $(window).off('keydown.popup');
          }
        });

        $popupContainer
          .append($popup)
          .appendTo($('body'))
        ;


        break;
      default:
        alert('Sorry, mah man. This functionality does not exist yet.');
        return;
    }

    if (sendNow) {
      send();
    }

    return false;
  });

  // you're modding someone on a post!!!
  $('.mod-post a').on('click', function () {
    var post_data = $(this).closest('.post').data();
    $el = $(this).closest('li');
    var modType = $el.data('modType');
    var sendNow = true;
    var postData = {};
    var $post = $(this).closest('.post');


    var callback = function (data) {
      var cb = noop();
      // if successful, we want to redirect/refresh
      if (data.success) {
      }
      handleNotices(data, cb);
      // make the dropdowns go away
      $(window).click();
    };

    var send = function (cb) {
      cb = cb || noop;
      var cb2 = function (data) {
        callback(data);
        cb(data);
      };
      $.post('/mod/' + modType + '.php', postData, cb2);
    };

    var sendCallback = noop();

    switch (modType) {
      case 'editPost':
        sendNow = false;
        document.location = '/forums/editPost.php?postId=' + post_data.postId;
        break;
      case 'deletePost':
        var z = confirm('Are you sure you want to delete this very good post???');
        // not z! nazi!!!!
        if (!z) {
          alert('Post was not deleted! Crisis Averted!');
          return false;
        }
        postData = {
          postId: post_data.postId
        };
        sendCallback = function (data) {
          if (data.success) {
            $post.css('opacity', 0.5);
          } else {
            if (data.noPostsRemain) {
              var z = confirm('Can\'t delete a post if its the only post in a thread. Would you like to delete the thread instead?');
              if (z) {
                $('.mod-thread .mod-deleteThread a').click();
              }
            }
          }
        };
        break;
      default:
        alert('Sorry, mah man. This functionality does not exist yet.');
        return;
    }

    if (sendNow) {
      send(sendCallback);
    }

    return false;
  });


  // you're modding someone on their user profile!!
  $('.mod-user a').on('click', function () {
    var userData = $(this).closest('.profile').data();
    var $el = $(this).closest('li');
    var modType = $el.data('modType');
    var sendNow = true;
    var postData = {};

    var callback = function (data) {
      var cb = noop();
      // if successful, we want to redirect/refresh
      if (data.success) {
      }
      handleNotices(data, cb);
      // make the dropdowns go away
      $(window).click();
    };

    var send = function (cb) {
      cb = cb || noop;
      var cb2 = function (data) {
        callback(data);
        cb();
      };
      $.post('/mod/' + modType + '.php', postData, cb2);
    };

    var sendCallback = noop();

    switch (modType) {
      case 'promoteUser':
        sendNow = false;
        var $popup = $('<div>').addClass('popup');
        var $popupContainer = $('<div>').addClass('popupContainer');
        var $input = $('<input>').val(userData.rank);
        var userId = userData.userId;

        var $submit = $('<button>').text('Pick this user\'s new rank!!!').on('click', function () {
          postData = {
            userId: userId,
            newRank: $input.val()
          };
          $.post('/mod/userChangeRank.php', postData, function (data) {
            $popupContainer.remove();
            handleNotices(data, function (a) {
              document.location = document.location;
            });

          });
        });

        var $cancel = $('<a>')
            .attr('href', '#')
            .text('Close! Undo! Undo! I changed my mind!')
            .addClass('popupCloseLink')
            .on('click', function () {
              $popupContainer.remove();
              return false;
            })
          ;

        $h4 = $('<h4>').html('WHOA THIS USER IS MOVING UP IN THE WORLD!');
        $popup
          .append($h4, $input, $submit, $cancel)
        ;
        $popupContainer
          .append($popup)
          .appendTo($('body'))
        ;

        $(window).on('keydown.popup', function (e) {
          var key = e.keyCode;
          if (key == 27) {
            $cancel.click();
            $(window).off('keydown.popup');
          }
        });

        break;
      case 'demoteUser':


        sendNow = false;
        var $popup = $('<div>').addClass('popup');
        var $popupContainer = $('<div>').addClass('popupContainer');
        var $input = $('<input>').val(userData.rank);
        var userId = userData.userId;

        var $submit = $('<button>').text('Pick this user\'s new rank!!!').on('click', function () {
          postData = {
            userId: userId,
            newRank: $input.val()
          };
          $.post('/mod/userChangeRank.php', postData, function (data) {
            $popupContainer.remove();
            handleNotices(data, function (a) {
              document.location = document.location;
            });

          });
        });

        var $cancel = $('<a>')
            .attr('href', '#')
            .text('Close! Undo! Undo! I changed my mind!')
            .addClass('popupCloseLink')
            .on('click', function () {
              $popupContainer.remove();
              return false;
            })
          ;

        var $h4 = $('<h4>').html('PUNISH THIS USER FOR THEIR INSOLENCE!');
        var $rankList = $('<div>')
            .text('Available Ranks Loading!!!....')
            .addClass('rankList')
          ;
        $popup
          .append($h4, $input, $submit, $rankList, $cancel)
        ;
        $popupContainer
          .append($popup)
          .appendTo($('body'))
        ;

        $.get('/mod/getRanks.php', function (data) {
          $('.rankList').html(data.out);
        });

        $(window).on('keydown.popup', function (e) {
          var key = e.keyCode;
          if (key == 27) {
            $cancel.click();
            $(window).off('keydown.popup');
          }
        });


        break;
      default:
        alert('Sorry, mah man. This functionality does not exist yet.');
        return;
    }

    if (sendNow) {
      send(sendCallback);
    }

    return false;
  });


  // you're doing forum modding!!!
  $('.mod-forum a').on('click', function () {

    var $el = $(this).closest('li');
    var modType = $el.data('modType');

    var $h2 = $(this).closest('.modDropdown').prev('h2');
    var forumData = $h2.data();

    var sendNow = true;
    var postData = {};

    var forumId = forumData.forumId;

    var callback = function (data) {
      var cb = noop();
      // if successful, we want to redirect/refresh
      if (data.success) {
      }
      handleNotices(data, cb);
      // make the dropdowns go away
      $(window).click();
    };

    var send = function (cb) {
      cb = cb || noop;
      var cb2 = function (data) {
        callback(data);
        cb();
      };
      $.post('/mod/' + modType + '.php', postData, cb2);
    };

    var sendCallback = noop();

    switch (modType) {
      case 'renameForum':
        sendNow = false;

        var name = $h2.text();
        var description = forumData.forumDescription;


        var $popup = $('<div>').addClass('popup');
        var $popupContainer = $('<div>').addClass('popupContainer');
        var $input = $('<input>').val(name).attr('placeholder', 'Forum Name');
        var $inputDescription = $('<input>').val(description).attr('placeholder', 'Forum Description');

        var $submit = $('<button>').text('Change this forum\'s name and description!!').on('click', function () {
          postData = {
            forumId: forumId,
            name: $input.val(),
            description: $inputDescription.val()
          };
          $.post('/mod/renameForum.php', postData, function (data) {
            $popupContainer.remove();
            handleNotices(data, function (a) {
              document.location = document.location;
            });

          });
        });

        var $cancel = $('<a>')
            .attr('href', '#')
            .text('Close! Undo! Undo! I changed my mind!')
            .addClass('popupCloseLink')
            .on('click', function () {
              $popupContainer.remove();
              return false;
            })
          ;

        $h4 = $('<h4>').html('CHECK OUT THIS WICKED SICK FORUM RENAME!');
        $popup
          .append($h4, $input, $('<br>'), $inputDescription, $('<br>'), $submit, $cancel)
        ;
        $popupContainer
          .append($popup)
          .appendTo($('body'))
        ;

        $(window).on('keydown.popup', function (e) {
          var key = e.keyCode;
          if (key == 27) {
            $cancel.click();
            $(window).off('keydown.popup');
          }
        });

        break;

      case 'renameForum':
        sendNow = false;

        var name = $h2.text();
        var description = forumData.forumDescription;


        var $popup = $('<div>').addClass('popup');
        var $popupContainer = $('<div>').addClass('popupContainer');
        var $input = $('<input>').val(name).attr('placeholder', 'Forum Name');
        var $inputDescription = $('<input>').val(description).attr('placeholder', 'Forum Description');

        var $submit = $('<button>').text('Change this forum\'s name and description!!').on('click', function () {
          postData = {
            forumId: forumId,
            name: $input.val(),
            description: $inputDescription.val()
          };
          $.post('/mod/renameForum.php', postData, function (data) {
            $popupContainer.remove();
            handleNotices(data, function (a) {
              document.location = document.location;
            });

          });
        });

        var $cancel = $('<a>')
            .attr('href', '#')
            .text('Close! Undo! Undo! I changed my mind!')
            .addClass('popupCloseLink')
            .on('click', function () {
              $popupContainer.remove();
              return false;
            })
          ;

        $h4 = $('<h4>').html('CHECK OUT THIS WICKED SICK FORUM RENAME!');
        $popup
          .append($h4, $input, $('<br>'), $inputDescription, $('<br>'), $submit, $cancel)
        ;
        $popupContainer
          .append($popup)
          .appendTo($('body'))
        ;

        $(window).on('keydown.popup', function (e) {
          var key = e.keyCode;
          if (key == 27) {
            $cancel.click();
            $(window).off('keydown.popup');
          }
        });

        break;
      case 'createNewForum':
        sendNow = false;
        document.location = '/mod/editForum.php?parentId=' + forumId;
        break;
      case 'editForum':
        sendNow = false;
        document.location = '/mod/editForum.php?forumId=' + forumId;
        break;
      default:
        alert('Sorry, mah man. This functionality does not exist yet.');
        return;
    }

    if (sendNow) {
      send(sendCallback);
    }

    return false;
  });


  $('.postContent img').one('load',function () {
      var $that = $(this);
      var img = $(this).get(0);

      function onload() {
        var imageMaxWidth = 656;

        if (img.naturalWidth > imageMaxWidth) {
          $that
            .addClass('tinyImage')
            .attr('data-width', img.naturalWidth)
            .attr('data-height', img.naturalHeight)
            .on('click', function () {
              var $postContent = $(this).closest('.postContent');
              if ($postContent.length == 0) {
                $(this).toggleClass('expanded');
                return;
              }
              $(this).toggleClass('expanded');
              if ($postContent.find('.tinyImage.expanded').length == 0) {
                $postContent.removeClass('expanded');
              } else {
                $postContent.addClass('expanded');
              }
            })
          ;
          $('<a>')
            .attr('href', $that.attr('src'))
            .attr('target', '_blank')
            .addClass('dimensions')
            .text(img.naturalWidth + ' x ' + img.naturalHeight)
            .insertBefore($that)
          ;
        }
      }

      setTimeout(onload, 100);

    }
  ).each(function () {
      if (this.complete) $(this).load();
    }
  );

  // edit staff roster
  $('.staffRoster .user .info h3 .job').on('dblclick', function () {
    var $popup = $('<div>').addClass('popup');
    var $popupContainer = $('<div>').addClass('popupContainer');
    var $input = $('<input>').val($(this).text());
    var userId = $(this).closest('.user').data('userId');

    var $submit = $('<button>').text('Edit User Title!').on('click', function () {
      postData = {
        userId: userId,
        title: $input.val()
      };
      $.post('/mod/editStaffInfo.php', postData, function (data) {
        $popupContainer.remove();
        handleNotices(data, function (a) {
          document.location = document.location;
        });

      });
    });

    $h4 = $('<h4>').html('Changing your job title?');
    $popup
      .append($h4, $input, $submit)
    ;
    $popupContainer
      .append($popup)
      .appendTo($('body'))
    ;
  });

  $('.staffRoster .user .info p').on('dblclick', function () {
    var $popup = $('<div>').addClass('popup');
    var $popupContainer = $('<div>').addClass('popupContainer');
    var $input = $('<input>').val($(this).text());
    var userId = $(this).closest('.user').data('userId');

    var $submit = $('<button>').text('Edit User Title!').on('click', function () {
      postData = {
        userId: userId,
        description: $input.val()
      };
      $.post('/mod/editStaffInfo.php', postData, function (data) {
        $popupContainer.remove();
        handleNotices(data, function (a) {
          document.location = document.location;
        });

      });
    });

    $h4 = $('<h4>').html('Changing your job description?');
    $popup
      .append($h4, $input, $submit)
    ;
    $popupContainer
      .append($popup)
      .appendTo($('body'))
    ;
  });

  // handles clicking tabs
  $('.tabs li a').on('click', function () {
    var $tabs = $(this).closest('.tabs');
    var $tabsItems = $tabs.parent().find('.tabItem');

    $tabsItems.removeClass('selected');
    $tabs.find('li').removeClass('selected');

    var $li = $(this).closest('li');
    $li.addClass('selected');

    var tabId = $li.data('tab');

    $tabsItems.each(function () {
      if ($(this).data('tab') == tabId) {
        $(this).addClass('selected');
        return false;
      }
    });

    return false;

  });


  // handles drafts
  if (localStorage['post-new-draft']) {
    localStorage['post-old-draft'] = localStorage['post-new-draft'];
    localStorage['post-old-timestamp'] = localStorage['post-new-timestamp'];
    localStorage['post-new-draft'] = '';
    localStorage['post-new-timestamp'] = 0;
  }

  if (localStorage['post-old-draft'] && $('#postContent').val() != localStorage['post-old-draft']) {
    $('.loadDraft').text('Load Draft from ' + writeDate(localStorage['post-old-timestamp'] || 0, 'short'));
  }

  $('.loadDraft').on('click', function () {
    Draft.load();
    return false;
  });


  var wordsOfCurse = ['poopy'];

  //replace the very very bad words!!!!
  function grabTextNodes(parentEl) {
    var textNodes = [];

    function grabInner(node) {
      if ((node || {}).nodeType == 3) {
        if (node && !node.nodeValue.match(/^\s*$/)) {
          textNodes.push(node);
        }
      }
      else {
        var childNodes = (node || {}).childNodes || [];
        var len = childNodes.length;
        for (var a = 0; a < len; a += 1) {
          grabInner(childNodes[a]);
        }
      }
    }

    grabInner(parentEl);
    return textNodes;
  }

  var textNodes = grabTextNodes($('.posts').get(0));

  var regex = new RegExp('(' + wordsOfCurse.join('|') + ')', 'gi');

  for (var nodeIndex = 0; nodeIndex < textNodes.length; nodeIndex += 1) {
    var textNode = textNodes[nodeIndex];

    var replacementNode = document.createElement('span');
    if (!regex.test(textNode.data)) {
      continue;
    }
    replacementNode.innerHTML = textNode.data.replace(regex, '<span class="curse" title="This word is very very bad and has been censored for your protection">$1</span>');
    textNode.parentNode.insertBefore(replacementNode, textNode);
    textNode.parentNode.removeChild(textNode);
  }


});

///////////////// end of jquery onload

var $chatbox, $chats, chatbox;

function noop() {
  return function () {
  };
}

function handleNotices(data, callback) {
  callback = callback || noop;
  if (data.notices) {
    for (var noticeType in data.notices) {
      for (var i in data.notices[noticeType]) {
        // make this note do an alert.
        // todo: make it popup something i guess
        alert(data.notices[noticeType][i]);
      }
    }
  }
  if (!!data.success) {
    callback();
  }
  return !!data.success;
}

function ent(str) {
  return (str || '').toString().replace(/[<>'"&]/g, function (a) {
    return ent.replaces[a];
  });
}
ent.replaces = {
  '<': '&lt;',
  '>': '&gt;',
  '\'': '&#39;',
  '"': '&quot;',
  '&': '&amp;'
};

function writeDate(timestamp, type) {
  var date = new Date(timestamp * 1000);
  var hours = date.getHours();
  var amPm = hours > 11 ? 'pm' : 'am';
  hours = hours % 12;
  if (hours == 0) {
    hours = 12;
  }
  switch (type) {
    case 'small':
      return padLeft(hours, 2) + ':' + padLeft(date.getMinutes(), 2) + amPm;
      break;
    case 'date':
      return writeDate.days[date.getDay()] + ', ' + writeDate.months[date.getMonth()] + ' ' + date.getDate() + ', ' + date.getFullYear();
    case 'short':
      return writeDate.monthsShort[date.getMonth()] + ' ' + date.getDate() + ' @ ' + hours + ':' + padLeft(date.getMinutes(), 2) + ':' + padLeft(date.getSeconds(), 2) + amPm;
    default:
      return date.toString();
  }
}
writeDate.months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
writeDate.days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
writeDate.monthsShort = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'June', 'July', 'Aug', 'Sept', 'Oct', 'Nov', 'Dec'];

function padLeft(str, length, paddingChar) {
  str = str.toString();
  if (_.isUndefined(paddingChar)) {
    paddingChar = '0';
  }
  paddingChar = paddingChar.toString();
  while (str.length < length) {
    str = paddingChar + str;
  }
  return str;
}

function strRepeat(str, count) {
  return new Array(count + 1).join(str) || '';
}

function updateChatbox() {
  $.getJSON('/api/chatbox.php', {id: chatbox.highestId}, function (data) {
    clearTimeout(chatbox.interval);
    chatbox.maxCount = data.maxCount;
    data.highestId = parseInt(data.highestId, 10);
    data.lowestId = parseInt(data.lowestId, 10);
    var addedNew = false;
    var atBottom = ($chats.get(0).scrollHeight - $chats.scrollTop() <= $chats.outerHeight() + 14); // 14 is some extra padding. like if you're on the second to last line
    for (var i = _.max([chatbox.lowestId + 1, data.lowestId]); i <= data.highestId; i++) {
      if (!data.chats[i] || $chats.find('.chat-' + i).length > 0) {
        continue;
      }
      addedNew = i;
      // if it's a new day, add the date
      var prevDate = $('.chat-' + (i - 1)).data('timestamp') || 0;
      var newDate = data.chats[i].timestamp;

      var prevDateStr = writeDate(prevDate, 'date');
      var newDateStr = writeDate(newDate, 'date');

      if (prevDateStr != newDateStr) {
        $date = $('<div>')
          .addClass('chat-date')
          .text(newDateStr)
        ;
        $chats.append($date);
      }

      // add the new message
      $div = $('<div>')
        .data('chatId', i)
        .addClass('chat chat-' + i)
        .append(
          $('<span>').text(writeDate(data.chats[i].timestamp, 'small') + ' '),
          $('<a>')
            .addClass('userLink tag-' + data.users[data.chats[i].userId].rankStr)
            .attr('href', '/user.php?userId=' + data.users[data.chats[i].userId].id)
            .text(data.users[data.chats[i].userId].name),
          $('<span>').text(': ' + data.chats[i].content)
        )
        .data('timestamp', data.chats[i].timestamp)
      ;
      $chats.append($div);
    }
    if (data.highestId) {
      chatbox.highestId = data.highestId;
    }
    var chatCount = $chats.find('.chat').length;
    for (var i = 0; i < chatCount - chatbox.maxCount - 1; i++) {
      $chats.find('.chat:eq(0)').remove();
      $chats.find('.chat-date + .chat-date').prev().remove();
    }

    if ($chats.find('.chat-fake').length > 0) {
      $chats.find('.chat-fake').remove();
    }
    // if new stuff was added, and we're at the bottom of the screen
    if (addedNew && atBottom) {
      $chats.scrollTop($chats.get(0).scrollHeight + 200);
    }

    if (chatbox.playNoise && addedNew) {
      // if they have multiple tabs open, don't let it ding for each tab
      if (parseInt(localStorage['lastNoiseChatId'] || 0, 10) < addedNew) {
        localStorage['lastNoiseChatId'] = addedNew;
        $('#chatboxAudio').get(0).play();
      }

    }

    chatbox.lowestId = $chats.find('.chat:eq(0)').data('chatId');
    chatbox.interval = setTimeout(updateChatbox, chatbox.timeout);
  });
}

var Draft = {
  save: function (content) {
    var now = Math.floor(new Date() / 1000);
    localStorage['post-new-draft'] = content;
    localStorage['post-new-timestamp'] = now;
    $('.saveDraft').text('Draft saved at: ' + writeDate(now, 'short'));
  },
  load: function () {
    var now = Math.floor(new Date() / 1000);
    var oldDraft = localStorage['post-old-draft'];
    var newDraft = localStorage['post-new-draft'] || '';
    var oldTimestamp = localStorage['post-old-timestamp'];
    var newTimestamp = localStorage['post-new-timestamp'];

    if (newDraft) {
      localStorage['post-old-draft'] = newDraft;
      localStorage['post-new-draft'] = oldDraft;
      localStorage['post-old-timestamp'] = newTimestamp || now;
      localStorage['post-new-timestamp'] = oldTimestamp || now;

      if (localStorage['post-old-draft'] != localStorage['post-new-draft']) {
        $('.loadDraft').text('Load Draft from ' + writeDate(localStorage['post-old-timestamp'] || 0, 'short'));
      } else {
        $('.loadDraft').text('');
      }
    }
    $('#postContent').val(oldDraft).trigger('keyup', {});
  }
};

function getHiddenProp() {
  var prefixes = ['webkit', 'moz', 'ms', 'o'];
  // if 'hidden' is natively supported just return it
  if ('hidden' in document) {
    return 'hidden';
  }
  // otherwise loop over all the known prefixes until we find one
  for (var i = 0; i < prefixes.length; i++) {
    if ((prefixes[i] + 'Hidden') in document) {
      return prefixes[i] + 'Hidden';
    }
  }
  // otherwise it's not supported
  return null;
}

function isHidden() {
  var prop = getHiddenProp();
  if (!prop) {
    return false;
  }
  return document[prop];
}
// use the property name to generate the prefixed event name
var visProp = getHiddenProp();
if (visProp) {
  var evtname = visProp.replace(/[H|h]idden/, '') + 'visibilitychange';
  document.addEventListener(evtname, visChange);
}
function visChange() {
  if (!chatbox.isEnabled) {
    return;
  }
  clearTimeout(chatbox.interval);
  if (isHidden()) {
    // change chatbox interval to 60 seconds when the page isn't visible
    chatbox.timeout = 60000;
    chatbox.playNoise = false;
  } else {
    // swap the chatbox interval back to 5 seconds when the page is visible again
    chatbox.timeout = 5000;
    chatbox.playNoise = false;
  }
  chatbox.interval = setTimeout(updateChatbox, chatbox.timeout);
}



