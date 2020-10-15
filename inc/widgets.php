<?php

/**
 * Add DOMS_Exchange_Currency widget.
 */
class DOMS_Exchange_Currency extends WP_Widget
{

  /**
   * Register widget with WordPress.
   */
  public function __construct()
  {
    parent::__construct(
      'doms_exchange_currency', // Base ID
      'Currency Exchance Rate', // Name
      array(
        'description' => __('display exchange rate between selected currencies', 'doms-rates')
      ) // Args
    );
  }

  /**
   * Front-end display of widget.
   *
   * @see WP_Widget::widget()
   *
   * @param array $args     Widget arguments.
   * @param array $instance Saved values from database.
   */
  public function widget($args, $instance)
  {
    extract($args);
    $title         = apply_filters('widget_title', $instance['title']);
    $urlSource     = $instance['urlSource'] ? $instance['urlSource'] : '';
    $featured_image = $instance['featuredImage'] ? $instance['featuredImage'] : 0;
    $items_to_show = $instance['itemsToShow'] ? $instance['itemsToShow'] : 3;
    $cols = 12 / $items_to_show;
    $colClass = $cols >= 3 ? $cols : 4;
    $i = 0;

    echo $before_widget;
    if (!empty($title)) {
      echo '<h5 class="widgettitle"><span>' . $title . '</span></h5>';
    }

    /*
    *  Show Feed items
    */
    ?>
    <div id="bsaFeedWidget">
      <div class="row">
        <?php
          $content = file_get_contents($urlSource);
          $x = new SimpleXmlElement($content);

          foreach ($x->channel->item as $item) {
            if($i++ < $items_to_show) {
            ?>
            <div class="col-md-<?php echo $colClass; ?> mb-3">
              <div class="rss-container">
                <a class="text-center d-block" href="<?php echo esc_url($item->link); ?>" title="<?php printf(__('%s', 'doms-rates'), $item->title); ?>">
                <?php
                preg_match('<img([\w\W]+?) />', $item->description, $matches );
                $images = $matches;
                $image = '<'. $images[0] . ' />';
                if( $featured_image ) {
                  if( $item->image->url) {
                    $feedThumbnail = $item->image->url;
                  } elseif( (bool)$images[0] ) {
                    $doc = new DOMDocument();
                    $doc->loadHTML($image);
                    $xpath = new DOMXPath($doc);
                    $feedThumbnail = $xpath->evaluate("string(//img/@src)");
                  } else {
                    $feedThumbnail = plugin_dir_url(__DIR__) . 'public/img/logo_bsa.png';
                  }
                ?>
                  <img class="rss_image" src="<?php echo $feedThumbnail; ?>" />
                <?
                }
                  echo esc_html($item->title);
                ?>
                </a>
              </div>
            </div>
            <?php
            } else {
              continue;
            }
          }
        ?>
      </div>
    </div>
    <?php
    wp_reset_postdata();

    echo $after_widget;
  }

  /**
   * Back-end widget form.
   *
   * @see WP_Widget::form()
   *
   * @param array $instance Previously saved values from database.
   */
  public function form($instance)
  {
    if (isset($instance['title'])) {
      $title = $instance['title'];
    } else {
      $title = '';
    }

    $base_rate = isset($instance['baseRate']) ? $instance['baseRate'] : 'USD';
    $fetchAPI  = fetch_save_api();
    $rates     = $fetchAPI->rates;
    ?>
    <p>
      <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'doms-rates'); ?></label>
      <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
    </p>
    <?php
    /* Base Currency dropdown */
    ?>
    <p>
      <label for="<?php echo $this->get_field_id('baseRate'); ?>"><?php _e('Select base currency', 'doms-rates'); ?></label>
      <select name="<?php echo $this->get_field_name('baseRate'); ?>" id="<?php echo $this->get_field_id('baseRate'); ?>" class="widefat">
        <?php
        foreach ($rates as $key => $value) {
        ?>
          <option value="<?php echo $key; ?>" <?php selected($base_rate, $key); ?>><?php echo $key; ?></option>
        <?php
        }
        ?>
      </select>
    </p>
    <?php
    // Currency Rates
    $currencies      = $fetchAPI->rates;
    $currency        = isset($instance['foreignRateCurrencies']) ? $instance['foreignRateCurrencies'] : [];

    foreach ($currencies as $key => $value) {
      $toChecked = isset($currency[$key]) ? (array) $currency[$key] : [];
      ?>
      <label for="<?php echo $this->get_field_id( 'foreignRateCurrencies').$key; ?>" style="padding-right: 1rem;"> <?php echo $key; ?> <input type="checkbox" name="<?php echo $this->get_field_name( 'foreignRateCurrencies') ; ?>[]" id="<?php echo $this->get_field_id('foreignRateCurrencies') . $key; ?>" value="<?php echo $value; ?>" <?php checked( in_array($key, $toChecked), 1) ?>></label>
      <?php
    }
  }

  /**
   * Sanitize widget form values as they are saved.
   *
   * @see WP_Widget::update()
   *
   * @param array $new_instance Values just sent to be saved.
   * @param array $old_instance Previously saved values from database.
   *
   * @return array Updated safe values to be saved.
   */
  public function update($new_instance, $old_instance)
  {
    $instance                  = array();
    $instance['title']         = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';
    $instance['baseRate']      = (!empty($new_instance['baseRate'])) ? strip_tags($new_instance['baseRate']) : '';
    $instance['foreignRateCurrencies'] = (!empty($instance['foreignRateCurrencies'])) ? strip_tags($new_instance['foreignRateCurrencies']) : '';
    return $instance;
  }
} // class DOMS_Exchange_Currency

// Register DOMS_Exchange_Currency widget
add_action('widgets_init', 'register_doms_exchange_rate_widget');

function register_doms_exchange_rate_widget()
{
  register_widget('DOMS_Exchange_Currency');
}
