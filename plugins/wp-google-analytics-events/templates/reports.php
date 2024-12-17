
<?php
$ajax_nonce = wp_create_nonce( "wpflow_save_view" );


function printReportsAd() { ?>
  <div class="ga-report-ad">
    <div id="ga-report-ad-title">WP Google Analytics Events Pro - Unlock more insights</div>
    <div id="ga-report-ad-content">
      <div class="ga-report-ad-content-item"><img src="<?php echo GAE_PLUGIN_URL;?>images/250g10.png" /></div>
      <div class="ga-report-ad-content-item">
        <ul>
          <li>
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="w-8 mr-4 icon-trending-up"><path class="primary" d="M3.7 20.7a1 1 0 1 1-1.4-1.4l6-6a1 1 0 0 1 1.4 0l3.3 3.29 4.3-4.3a1 1 0 0 1 1.4 1.42l-5 5a1 1 0 0 1-1.4 0L9 15.4l-5.3 5.3z"></path><path class="secondary" d="M16.59 8l-2.3-2.3A1 1 0 0 1 15 4h6a1 1 0 0 1 1 1v6a1 1 0 0 1-1.7.7L18 9.42l-4.3 4.3a1 1 0 0 1-1.4 0L9 10.4l-5.3 5.3a1 1 0 1 1-1.4-1.42l6-6a1 1 0 0 1 1.4 0l3.3 3.3L16.59 8z"></path></svg>
            <span>
                  Top Links
                </span>
          </li>
          <li>
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="w-8 mr-4 icon-trending-up"><path class="primary" d="M3.7 20.7a1 1 0 1 1-1.4-1.4l6-6a1 1 0 0 1 1.4 0l3.3 3.29 4.3-4.3a1 1 0 0 1 1.4 1.42l-5 5a1 1 0 0 1-1.4 0L9 15.4l-5.3 5.3z"></path><path class="secondary" d="M16.59 8l-2.3-2.3A1 1 0 0 1 15 4h6a1 1 0 0 1 1 1v6a1 1 0 0 1-1.7.7L18 9.42l-4.3 4.3a1 1 0 0 1-1.4 0L9 10.4l-5.3 5.3a1 1 0 1 1-1.4-1.42l6-6a1 1 0 0 1 1.4 0l3.3 3.3L16.59 8z"></path></svg>
            <span>
                 Self Hosted Video
                </span>
          </li>
        </ul>
      </div>
      <div class="ga-report-ad-content-item">
        <ul>
          <li>
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="w-8 mr-4 icon-trending-up"><path class="primary" d="M3.7 20.7a1 1 0 1 1-1.4-1.4l6-6a1 1 0 0 1 1.4 0l3.3 3.29 4.3-4.3a1 1 0 0 1 1.4 1.42l-5 5a1 1 0 0 1-1.4 0L9 15.4l-5.3 5.3z"></path><path class="secondary" d="M16.59 8l-2.3-2.3A1 1 0 0 1 15 4h6a1 1 0 0 1 1 1v6a1 1 0 0 1-1.7.7L18 9.42l-4.3 4.3a1 1 0 0 1-1.4 0L9 10.4l-5.3 5.3a1 1 0 1 1-1.4-1.42l6-6a1 1 0 0 1 1.4 0l3.3 3.3L16.59 8z"></path></svg>
            <span>
                  Top Vimeo
                </span>
          </li>
          <li>
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="w-8 mr-4 icon-trending-up"><path class="primary" d="M3.7 20.7a1 1 0 1 1-1.4-1.4l6-6a1 1 0 0 1 1.4 0l3.3 3.29 4.3-4.3a1 1 0 0 1 1.4 1.42l-5 5a1 1 0 0 1-1.4 0L9 15.4l-5.3 5.3z"></path><path class="secondary" d="M16.59 8l-2.3-2.3A1 1 0 0 1 15 4h6a1 1 0 0 1 1 1v6a1 1 0 0 1-1.7.7L18 9.42l-4.3 4.3a1 1 0 0 1-1.4 0L9 10.4l-5.3 5.3a1 1 0 1 1-1.4-1.42l6-6a1 1 0 0 1 1.4 0l3.3 3.3L16.59 8z"></path></svg>
            <span>
                 Top YouTube
                </span>
          </li>
        </ul>
      </div>
      <div class="ga-report-ad-content-item">
        <a class="button-primary button-large" target="_blank" href="https://wpflow.com/upgrade/?utm_source=wpadmin&amp;utm_medium=banner&amp;utm_campaign=genreal">
          <span class="btn-title ">Upgrade Now</span>
        </a>
      </div>
    </div>
  </div>
  <div class="ga-flex-break"></div>

  <?php
}
?>
<link href="https://ga-dev-tools.appspot.com/public/css/index.css" rel="stylesheet">

<!-- Load the JavaScript API client and Sign-in library. -->

<div class="ga-reports-no-auth">
  <div class="ga-content">
    <h2>Connect with Google Analytics</h2>
    To view the reports please click the button below to login to Google Analytics.
    <br />
    <button id="ga-auth" class="button btn-primary">Log in to Google Analytics</button>
  </div>
</div>

<div class="ga-view-form">
  <div class="ga-content">
    <h2>Google Analytics Account</h2>
    Select the website's Google Analytics account and view view from the list below:
    <div class="ViewSelector" id="view-selector" ></div>
    <button id="ga-save-view" class="button btn-primary" data-nonce="<?php echo $ajax_nonce; ?>">Save</button>
    <?php
    $options	 = get_option( 'ga_events_options' );
    ?>
  </div>
</div>


<div class="ga-reports-section">
    <div class="ga-report-date"><input type="text" id="daterange" name="daterange"></div>
</div>



<?php
  $active_page = isset( $_GET[ 'page' ] ) ? $_GET[ 'page' ] : 'wp-google-analytics-events';

  if ($active_page == 'wp-google-analytics-events') {
?>

    <div class="ga-reports-section">
      <div class="ga-report-full">
        <h2>All Events over time</h2>
        <div id="all-events-chart" style="min-height: 340px;" class="top-chart"></div>
      </div>
      <div class="ga-flex-break"></div>
      <?php printReportsAd(); ?>
      <div class="ga-report-side">
          <div id="top10-downloads-chart"></div>
        </div>
        <div class="ga-report-side ">
          <div id="top10-email-links-chart"></div>
        </div>
      <div class="ga-flex-break"></div>
      <div class="ga-report-side ">
          <div id="top10-telephone-number-links-chart"></div>
      </div>
      <div class="ga-report-side ga-report-side-right">
        <div id="top10-links-chart"></div>
      </div>
    </div>


<?php } else if ($active_page == 'wp-google-analytics-events-click') { ?>
    <div class="ga-reports-section">
      <div class="ga-report-full">
          <h2>All Click Events over time</h2>
          <div id="all-clicks-chart" style="min-height: 340px;" class="top-chart"></div>
      </div>
      <div class="ga-flex-break"></div>
      <?php printReportsAd(); ?>
      <div class="ga-report-side">
        <div id="top10-clicks-by-category-chart"></div>
      </div>
      <div class="ga-report-side ">
        <div id="top10-clicks-by-label-chart"></div>
      </div>
      <div class="ga-report-side ga-report-side-right">
        <div id="top10-clicks-by-action-chart"></div>
      </div>
    </div>
<?php } else if ($active_page == 'wp-google-analytics-events-scroll') { ?>
    <div class="ga-reports-section">
        <div class="ga-report-full">
          <h2>All Scroll Events over time</h2>
          <div id="all-scroll-events-chart" style="min-height: 340px;" class="top-chart"></div>
        </div>
        <div class="ga-flex-break"></div>
      <?php printReportsAd(); ?>
        <div class="ga-report-side">
          <div id="top10-scroll-by-category-chart"></div>
        </div>
        <div class="ga-report-side ">
          <div id="top10-scroll-by-label-chart"></div>
        </div>
        <div class="ga-report-side ga-report-side-right">
          <div id="top10-scroll-by-action-chart"></div>
        </div>
    </div>
<?php } ?>


