<?php

$hasBokkenHtaccess = file_exists(BOKKEN_PATH.'/.htaccess');

// FOLDERS
createBokkenDefaultFile('assets');
createBokkenDefaultFile('assets/styles');
createBokkenDefaultFile('assets/scripts');
createBokkenDefaultFile('controllers');
createBokkenDefaultFile('public');
createBokkenDefaultFile('public/assets');
createBokkenDefaultFile('public/assets/media');
createBokkenDefaultFile('public/media');
createBokkenDefaultFile('services');
createBokkenDefaultFile('vendors');
createBokkenDefaultFile('views');
createBokkenDefaultFile('views/partials');
createBokkenDefaultFile('views/cache');

// FILES
createBokkenDefaultFile('.htaccess',
"
RewriteEngine on

RewriteRule ^public/.+$ - [L,END]
RewriteRule ^index\.php$ - [L,END]
RewriteRule ^(.+)$ public/$1
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule . index.php
"
);

if (!file_exists(BOKKEN_PATH.'/controllers/defaults.php')) {
  createBokkenDefaultFile('index.php',
  file_get_contents(BOKKEN_PATH.'/index.php').
  "

// ======== BOKKEN AUTO GENERATED CONTENT ========
// session_start();
// DataBase::open( 'host', 'user', 'pass', 'table' );
require __DIR__.'/router.php';
// DataBase::close();
  ",
  true);
}

createBokkenDefaultFile('controllers/defaults.php',
"
<?php
return array(
  'site' => array(
    'title'     => 'Title',
    'lang'      => 'es-CL',
    'char'      => 'UTF-8',
    'favicon'   => 'assets/images/favicon.png',
    'metatags'  => array(),
    'social'    => array(
      'name'        => 'Title',
      'description' => 'Description',
      'image'       => 'assets/images/social.png'
    )
  )
);
"
);

createBokkenDefaultFile('router.php',
"
<?php
Router::init();

Router::asset('/*', 'assets/style.less', 'assets/style.min.css');
Router::asset('/*', 'assets/script.js', 'assets/script.min.js');

Router::get('/', 'home');

Router::end();
"
);

createBokkenDefaultFile('controllers/home.php',
"
<?php
return array(
  'welcome' => array(
    'message' => 'Welcome to <b>Booken</b><br>You can edit these content in <em>controllers/home.php</em> and <em>views/home.php</em>'
  )
);
"
);

createBokkenDefaultFile('views/index.php',
"
<!DOCTYPE html>
<html lang=\"{{site.lang}}\">
<head>{{>head}}</head>
<body>
{{>content}}
{{#assets.scripts}}
<script src=\"{{url.base}}{{file}}?t={{time}}\"></script>
{{/assets.scripts}}
</body>
</html>
"
);

createBokkenDefaultFile('views/home.php',
"
{{>header}}
<main>
  <div class=\"container-sm\">
    <h1 class=\"text-center\">Bokken</h1>
    <p class=\"text-center\">{{welcome.message}}</p>
  </div>
</main>
{{>footer}}
"
);

createBokkenDefaultFile('views/partials/head.php',
"
<meta charset=\"{{site.char}}\">
<meta name=\"viewport\" content=\"width=device-width, initial-scale=1, user-scalable=no\">
<meta http-equiv=\"X-UA-Compatible\" content=\"ie=edge\">
<title>{{site.title}}</title>
<link rel=\"shortcut icon\" href=\"{{url.base}}{{site.favicon}}\" type=\"image/x-icon\">
<link rel=\"canonical\" content=\"{{url.base}}\">
<meta name=\"description\" content=\"{{site.social.description}}\">

<meta property=\"og:type\" content=\"website\">
<meta property=\"og:site_name\" content=\"{{site.social.name}}\">
<meta property=\"og:image\" content=\"{{url.base}}{{site.social.image}}\">

{{#site.metatags}}
{{.}}
{{/site.metatags}}

{{#assets.styles}}
<link rel=\"stylesheet\" href=\"{{url.base}}{{file}}?t={{time}}\" type=\"text/css\">
{{/assets.styles}}
"
);

createBokkenDefaultFile('views/partials/header.php',
"
<header></header>
"
);

createBokkenDefaultFile('views/partials/footer.php',
"
<footer></footer>
"
);

if (!file_exists(BOKKEN_PATH.'/assets/style.less')) {
  createBokkenDefaultFile('assets/styles/elementary.less',
  file_get_contents('https://raw.githubusercontent.com/MR-0/elementary-js/master/elementary.less')
  );
}

createBokkenDefaultFile('assets/style.less',
"
@import './styles/elementary.less';
"
);

if (!file_exists(BOKKEN_PATH.'/aassets/script.js')) {
  createBokkenDefaultFile('assets/scripts/elementary.js',
  file_get_contents('https://raw.githubusercontent.com/MR-0/elementary-js/master/elementary.js')
  );
}

createBokkenDefaultFile('assets/script.js',
"
require('./scripts/elementary.js');
"
);

if (!$hasBokkenHtaccess) {
  header('location: ./');
  $defaultsBokkenContent = file_get_contents(BOKKEN_PATH.'/Bokken/Bokken.php');
  $defaultsBokkenContent = str_replace('require __DIR__.\'/assets/defaults.php\';', '//require __DIR__.\'/assets/defaults.php\';', $defaultsBokkenContent);
  createBokkenDefaultFile('Bokken/Bokken.php', $defaultsBokkenContent, true);
}


function createBokkenDefaultFile ($path, $content = null, $force = false) {
  $path = BOKKEN_PATH.'/'.$path;
  
  if (!file_exists($path) || $force) {
    if (is_null($content)) {
      mkdir($path, 0744);
    }
    else {
      $file = fopen($path, 'w');

      if ($file) {
        fwrite($file, $content);
        fclose($file);
      }
    }
  }
}