<?php
/**
 * An interface to provide remote demonstrations for use with certain LfD algorithms and the PR2
 * robot.
 *
 * @author     Russell Toris <rctoris@wpi.edu>
 * @copyright  2013 Robert Bosch LLC
 * @license    BSD -- see LICENSE file
 * @version    March, 8 2013
 * @package    api.robot_environments.interfaces.bosch_pr2_remote_demonstration_interface
 * @link       http://ros.org/wiki/bosch_pr2_remote_demonstration_interface
 */

/**
 * Generate the HTML for the interface. All HTML is echoed.
 * @param robot_environment $re The associated robot_environment object for this interface
 */
function generate($re) {
	// check if we have all the widget information we have
	if(count($re->get_widgets_by_name('MJPEG Stream')) < 2) {
		create_error_page('Not enough MJPEG streams.', $re->get_user_account());
	} else if(!$teleop = $re->get_widgets_by_name('Keyboard Teleop')) {
		create_error_page('No Keyboard Teloperation settings found.', $re->get_user_account());
	} else if(!$re->authorized()) {
		create_error_page('Invalid experiment for the current user.', $re->get_user_account());
	} else { // here we can spit out the HTML for our interface?>
<!DOCTYPE html>
<html>
<head>
	<?php $re->create_head() // grab the header information ?>
<script type="text/javascript"
  src="https://raw.github.com/RobotWebTools/pr2runstopjs/groovy-devel/pr2runstop.js"></script>
<script type="text/javascript"
  src="https://raw.github.com/RobotWebTools/rosbagjs/groovy-devel/topiclogger.js"></script>
<title>Basic Teleop Interface</title>

	<?php $re->make_ros() // connect to ROS ?>

<script type="text/javascript">
  ros.on('error', function() {
    alert('Lost communication with ROS.');
  });

  /**
   * Setup an JavaScrip widgets we need once the page is loaded.
   */
  function start() {
    // initialize the run-stop widget
    var pr2RunStop = new PR2RunStop({
      ros : ros,
      divID : 'run-stop',
      size : 33
    });

    // initialize the logger widget
    var logger = new TopicLogger({
      ros : ros,
      divID : 'logger'
    });

    // create the global display
    var rmsDisplay = new RMSDisplay({
      ros : ros,
      divID : 'scene',
      width : 1180,
      height : 600,
      background : '#101010',
      gridColor : '#BD4647'
    });

    // setup the buttons
    $('body').bind('DOMSubtreeModified', function() {
      $('button').button();
    });
  }
</script>
</head>
<body onload="start()">
  <section id="interface">
    <table>
      <tr>
        <td rowspan="2">
          <div id="run-stop"></div>
          <div id="speed-container">
          <?php echo create_keyboard_teleop_with_slider($teleop[0])?>
          </div>
          <div id="video1">
          <?php echo create_multi_mjpeg_canvas_by_envid($re->get_envid(), 400, 300, 0)?>
          </div>
          <div id="video2">
          <?php echo create_multi_mjpeg_canvas_by_envid($re->get_envid(), 400, 300, 1)?>
          </div>
        </td>
        <td><h2>PR2 Remote Demonstrations</h2></td>
        <td><img src="../img/logo.png"></td>
      </tr>
      <tr>
        <td colspan="2"><div id="scene"></div></td>
      </tr>
      <tr>
        <td colspan="3">
          <div id="logger"></div>
        </td>
      </tr>
    </table>
    <?php create_footer()?>
  </section>
</body>
</html>
          <?php
	}
}
?>
