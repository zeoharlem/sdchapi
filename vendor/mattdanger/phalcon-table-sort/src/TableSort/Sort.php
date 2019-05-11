<?php
namespace TableSort;

use Phalcon\Mvc\User\Component;

/**
 * TableSort\Sort
 */
class Sort extends Component
{

  public static $down_arrow = '<i class="fa fa-angle-down">';
  public static $up_arrow = '<i class="fa fa-angle-up">';

  /**
   * Return a formatted URI string with sort order
   *
   * @param string $key
   * @param string $default_sort
   * @return string
   */
  public static function sortLink($key, $default_sort = 'ASC')
  {

    $di = new \Phalcon\DI();
    $q = $di->getDefault()->getRequest()->getQuery();
    unset($q['page']);
    unset($q['_url']);

    if (!empty($q['sort']) && $q['sort'] == $key) {
      // Sort key exists already, invert "order" value.

      if (!empty($q['order'])) {

        if ($q['order'] == 'DESC') {
          $q['order'] = 'ASC';
        } else {
          $q['order'] = 'DESC';
        }

      } else {

        $q['order'] = $default_sort;

      }

    } else {
      $q['sort'] = $key;
      $q['order'] = $default_sort;
    }

    // Build the query string & return
    return http_build_query($q);

  }

  /**
   * Return a sort icon
   *
   * @param string $key
   * @param bool $force_render 
   * @return string
   */
  public static function sortIcon($key, $default = FALSE)
  {

    $di = new \Phalcon\DI();
    $q = $di->getDefault()->getRequest()->getQuery();
    unset($q['_url']);

    $html = '';

    if ($default && empty($q['sort'])) {

      $html = self::$down_arrow;

    } else {
    
      if (!empty($q['sort']) && $q['sort'] == $key) {
  
        $order = $q['order'] ?: 'desc';
        $angle = strtolower($order) == 'asc' ? 'down' : 'up';
        $html = self::${$angle . '_arrow'};
  
      }

    }

    return $html;
  }

}
