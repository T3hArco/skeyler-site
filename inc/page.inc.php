<?php
class Page
{
  public $title = '';
  protected $echoedHeader = 'waiting';
  protected $echoedFooter = 'waiting';

  /**
   *
   */
  public function __construct()
  {
    register_shutdown_function(array($this, 'footer'));
  }

  /**
   * Returns whether the header/footer has been echoed already
   *
   * @param string $type = enum('header','footer')
   */
  public function echoed($type)
  {
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
  public function header($title = '', $classes = array())
  {
    if ($this->echoedHeader != 'waiting') { //echo'ing or already echo'd
      return false;
    }
    if ($title) {
      $this->title = $title;
    }
    $this->echoedHeader = 'echoing';

    global $local;
    $local['title'] = $this->title;
    include ROOT . '/views/common/header.php';

    $this->echoedHeader = 'done';
  }


  /**
   * Echoes the footer
   */
  public function footer()
  {
    global $startTime, $local;
    $this->header();
    if ($this->echoedFooter != 'waiting') { //echo'ing or already echo'd
      return false;
    }
    $this->echoedFooter = 'echoing';

    $local['startTime'] = $startTime;
    $local['endTime'] = microtime(true);

    include ROOT . '/views/common/footer.php';

    $this->echoedFooter = 'done';
    exit;
  }


}

?>