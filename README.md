skeyler-site
============

after git clone:
- make a skeyler database
- copy inc/config.default.inc.php to inc/config.inc.php and fill in the variables
- make a vhost local.skeyler.com link to path/to/htdocs/skeyler/controllers
- make a vhost media.local.skeyler.com link to path/to/htdocs/skeyler/media
- install compass (read config.rb on how to install)

controllers/ is the root of the site
media/ contains all media. http://media.skeyler.com/ will have media/ as it's root and maybe run on nginx

everything in controllers should include this code at the beginning:
require '../_.php';

and then use "$Page = new Page();"   to init a page.
"$Page->header('page title');" echos the header
The footer is automatically included when everything exits

inc/funcs.inc.php has custom functions

to fetch rows:  "$rows = DB::q($query)->fetchAll();"
if you want to pass arguments, use: "$rows = DB::q($query, $binds)->fetchAll();"
where $binds is an array of values to replace the question marks in the query or an associative array to replace :keys

example:
$query = 'SELECT * FROM tbl WHERE id = ?;';
$binds = [5];
$rows = DB::q($query, $binds)->fetchAll();

example 2:
$query = 'SELECT * FROM tbl WHERE a = :asdf AND b = :poop;';
$binds = array(
  'asdf' => 3456,
  'poop' => 'butts',
);
$rows = DB::q($query, $binds)->fetchAll();

in the controllers, run the "view($variables);" function to pass variables to the view

the view needs the same name and path in views/ as it does controllers/

