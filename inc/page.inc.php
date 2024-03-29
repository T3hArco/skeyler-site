<?php
class Page
{
  public $title = '';
  protected $echoedHeader = 'waiting';
  protected $echoedFooter = 'waiting';
  protected $navigationItem;
  protected $navigationSubItem;
  protected $pageClasses;
  public $breadcrumbs;

  /**
   *
   */
  public function __construct($navigationItem = '', $navigationSubItem = '') {
    $this->navigationItem = $navigationItem;
    if (!$navigationSubItem) {
      $navigationSubItem = $navigationItem;
    }
    $this->navigationSubItem = $navigationSubItem;
    $this->pageClasses = array();
    register_shutdown_function(array($this, 'footer'));
  }

  /**
   * Returns whether the header/footer has been echoed already
   *
   * @param string $type = enum('header','footer')
   */
  public function echoed($type) {
    switch ($type) {
      case 'header':
        return ($this->echoedHeader == 'done');
        break;
      case 'footer':
        return ($this->echoedFooter == 'done');
        break;
    }
  }

  /**
   * Echoes the header
   */
  public function header($title = '', $classes = array()) {
    global $local, $isJson;
    if ($this->echoedHeader != 'waiting') { //echo'ing or already echo'd
      return false;
    }
    if ($title) {
      $this->title = $title;
    }
    $this->echoedHeader = 'echoing';

    if (!$isJson) {
      $newsItems = Cache::load('news');
      if ($newsItems) {
        foreach ($newsItems as $news) {
          Notice::message($news);
        }
      }

      $local['navigationItem'] = $this->navigationItem;
      $local['navigationSubItem'] = $this->navigationSubItem;
      $local['title'] = $this->title;
      $local['pageClasses'] = $this->pageClasses;
      if((!isset($local['breadcrumbs']) || !$local['breadcrumbs']) && $this->breadcrumbs){
        $local['breadcrumbs'] = $this->breadcrumbs;
      }
      include ROOT . '/views/common/header.php';
    }
    else {
      header('Content-type: application/json');
    }

    $this->echoedHeader = 'done';
  }


  /**
   * Echoes the footer
   */
  public function footer() {
    global $startTime, $local, $isJson;
    $this->header();
    if ($this->echoedFooter != 'waiting') { //echo'ing or already echo'd
      return false;
    }
    $this->echoedFooter = 'echoing';

    $local['totalMs'] = round((microtime(true) - $startTime) * 1000);

    if (!$isJson) {
      include ROOT . '/views/common/footer.php';
    }
    else {
      echo json_encode($local);
    }
    $this->echoedFooter = 'done';
    exit;
  }

  public function setClasses($classes) {
    $classes = (array) $classes;
    if (count($this->pageClasses) > 0) {
      $this->pageClasses = array_merge((array) $this->pageClasses, $classes);
    }
    else {
      $this->pageClasses = $classes;
    }
  }


}

?>