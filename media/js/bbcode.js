/*
 Copyright Scott Graham (obst) 2013 - 2070

 This parses bbcode into html, while entitying all the stuff that shouldn't be html.
 Usage: bbcode.parse('content') where 'content' is the text you want to convert from bbcode to html

 todo:
 fix quotes inside quotes
 proper url escaping on [url] / [img]
 [spoilers]?
 fix ctrl+z/x for the ones that add additional text

 If anything is broken, talk to obstipator: http://steamcommunity.com/id/obstipator

 . -> (?:.|\n|\r)

 */


bbcode = {};

// quick replaces of [x]abc[/x] to <y>abc</y>
// don't use characters that have special meaning in regex or you'll have to edit the parser a bit
bbcode.basicReplacements = {
  'b': 'strong',
  'strong': 'strong',
  'i': 'em',
  'em': 'em',
  'u': 'u',
  's': 's',
  'p': 'p',
  'quote': 'blockquote',
  'heading': 'h3',
  'title': 'h3'
};

bbcode.entityReplacements = {
  '<': '&lt;',
  '>': '&gt;',
  '\'': '&#39;',
  '"': '&quot;',
  '&': '&amp;'
};

bbcode.codeTags = [];

// entities characters involved in html
bbcode.entityHtml = function (str) {
  return str.replace(/[<>'"&]/g, function (a) {
    return bbcode.entityReplacements[a];
  });
};

// converts new lines to <br />s
bbcode.nl2br = function (str) {
  return str.replace(/(\n\r?|\r\n?)/g, '<br />');
};

// parses the easy replacements from bbcode.basicReplacements
bbcode.parseBasic = function (str) {
  var validReplacements = [];
  for (k in bbcode.basicReplacements) {
    validReplacements.push(k);
  }
  //i'm pretty sure this is right, but I might have confused some double escapes
  var pattern = new RegExp('\\[(' + validReplacements.join('|') + ')\\]((?:.|\n|\r)*?)\\[\\/\\1\\]', 'gi');

  return str.replace(pattern, function (z, tag, content) {
    return '<' + bbcode.basicReplacements[tag] + '>' + bbcode.parseBasic(content) + '</' + bbcode.basicReplacements[tag] + '>';
  });
};

// parses urls in the form [url="http://google.com"]abc[/url] to <a href="http://google.com">abc</a>
// also parses urls in the form [url]http://google.com[/url] to <a href="http://google.com">http://google.com</a>
// TODO: need to url escape the url, but don't remember which func was the good one (will prob have to unentity it first)
bbcode.parseUrl = function (str) {
  return str.replace(/\[url=&quot;(https?:\/\/.*?)&quot;\]((?:.|\n|\r)*?)\[\/url\]/gi,function (z, url, content) {
    return '<a href="' + url + '">' + content + '</a>';
  }).replace(/\[url\](https?:\/\/.*?)\[\/url\]/gi, function (z, url) {
      return '<a href="' + url + '">' + url + '</a>';
    });
};

// parses imgs in the form [img]http://google.com[/img] to <img src="http://google.com" />
// TODO: need to url escape the url, but don't remember which func was the good one (will prob have to unentity it first)
bbcode.parseImg = function (str) {
  return str.replace(/\[img\](https?:\/\/.*?)\[\/img\]/gi, function (z, url) {
    return '<img src="' + url + '" />';
  });
};

// parses a quote in the form [quote="name" postid="123"]text[/quote]
bbcode.parseQuote = function (str) {
  return str.replace(/\[quote(?:=&quot;([^"]+?)&quot;(?: postid=&quot;(\d+)&quot;)?)?\]((?:.|\n|\r)*?)\[\/quote\]/gi, function (z, username, postId, quoteText) {
    if (username && postId) {
      return '<blockquote><a class="postLink" href="/forums/thread.php?postId=' + postId + '">' + username + ' posted:</a><br/>' + quoteText + '</blockquote>';
    }
    if (username) {
      return '<blockquote><span class="postLink">' + username + ' posted:</span><br/>' + quoteText + '</blockquote>';
    }
    return '<blockquote>' + quoteText + '</blockquote>';
  });
};

// parses a list in the form [list]contents[/list] with contents having [*] for each list item
bbcode.parseList = function (str) {
  return str.replace(/\[list\]((?:.|\n|\r)*?)\[\/list\]/gi, function (z, contents) {
    var out = '<ul>';
    out += contents.replace(/\[\*\]([^\n]*?\n)/gi, function (y, contents) {
      return '<li>' + contents + '</li>';
    });
    out += '</ul>';
    return out;
  })
};

bbcode.parseYoutube = function (str) {
  return str.replace(/\[youtube\](?:http:\/\/(?:www)?\.youtube\.com\/watch\?v=)?([a-z0-9_-]+)\[\/youtube\]/gi, function (z, youtubeId) {
    return '<iframe id="ytplayer" type="text/html" width="640" height="390" src="http://www.youtube.com/embed/' + youtubeId + '" frameborder="0"></iframe><br/>';
  });
}

// removes a code tag from parsing, storing it to be brought back later
bbcode.parseCodeStart = function (str) {
  var i = 0;
  bbcode.codeTags = [];
  return str.replace(/\[code\]((?:.|\n|\r)*?)\[\/code\]/gi, function (z, code) {
    bbcode.codeTags.push(code);
    return '<code' + i + '>';
  });
};

// brings back the contents of stored code tags
bbcode.parseCodeEnd = function (str) {
  return str.replace(/<code(\d+)>/gi, function (z, codeId) {
    return '<pre>' + bbcode.codeTags[codeId] + '</pre>';
  });
};


bbcode.parse = function (str) {
  str = (str || '').toString();

  // first escape all special chars <>&'" etc
  str = bbcode.entityHtml(str);
  //alert('escaped:\n'+str)

  // take out all code tags so we don't do any replacements on their contents
  str = bbcode.parseCodeStart(str);

  // then parse the basic tags
  str = bbcode.parseBasic(str);

  // then parse urls, imgs, quotes, lists
  str = bbcode.parseUrl(str);
  str = bbcode.parseImg(str);
  str = bbcode.parseQuote(str);
  str = bbcode.parseList(str);
  str = bbcode.parseYoutube(str);

  // next to last, bring back all code tags
  str = bbcode.parseCodeEnd(str);

  // last, convert new lines to <br>
  str = bbcode.nl2br(str);

  return str;
};
