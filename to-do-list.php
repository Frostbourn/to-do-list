<?php

/**
 * @link              https://jakubskowronski.com
 * @since             1.0.0 
 * @package           To_Do_List
 *
 * @wordpress-plugin
 * Plugin Name:       To do list
 * Plugin URI:        wordpress.org/plugins/to-do-list
 * Description:       Simple Wordpress To Do List plugin.
 * Version:           1.0.0
 * Author:            Jakub Skowronski
 * Author URI:        https://jakubskowronski.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       to-do-list
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// Create database on plugin activation
function activate_to_do_list() {
	global $wpdb;
	$table_name = $wpdb->prefix . 'todolist';
	$charset_collate = $wpdb->get_charset_collate();

	$sql = "CREATE TABLE `$table_name` (
		`id` mediumint(9) NOT NULL AUTO_INCREMENT,
		`created` datetime DEFAULT '0000-00-00' NOT NULL,
		`task` varchar(255) NOT NULL,
		`done` tinyint(1) DEFAULT '0' NOT NULL, 
		PRIMARY KEY  ( id )
	) $charset_collate;";

	if ( $wpdb->get_var( "SHOW TABLES LIKE '$table_name'" ) != $table_name ) {
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );
	}
}
register_activation_hook( __FILE__, 'activate_to_do_list' );

//Add plugin to admin menu
function add_admin_page_content() {
  add_menu_page( 'To do list', 'To do list', 'manage_options', __FILE__, 'admin_page_content', 'dashicons-saved' );
}
add_action( 'admin_menu', 'add_admin_page_content' );
  
//Render tasks list
function admin_page_content() {
  global $wpdb;
	$table_name = $wpdb->prefix . 'todolist';
    ?>
    <p class="lead my-4">This is a simple to-do-list plugin.</p>
    <hr class="my-4">  
    <form class="form-inline d-flex justify-content-center mb-3" action="" method="post">
      <div class="form-group mx-sm-3 mb-2">
        <input type="text" class="form-control form-control-lg" name="task" placeholder="What you want to do..?">
      </div>
      <button type=" submit" class="btn btn-primary mb-2" name="save">
        <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-plus-circle-fill" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
          <path fill-rule="evenodd" d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM8.5 4.5a.5.5 0 0 0-1 0v3h-3a.5.5 0 0 0 0 1h3v3a.5.5 0 0 0 1 0v-3h3a.5.5 0 0 0 0-1h-3v-3z" />
        </svg>
      </button>
    </form>
    <table class="table table-striped">
      <thead>
          <tr>
              <th width="5%">#</th>
              <th width="20%">Created</th>
              <th width="50%">Task</th>
              <th width="25%">Actions</th>
          </tr>
      </thead>
      <tbody>
      <?php
        $results = $wpdb->get_results( "SELECT * FROM $table_name" );
        foreach ( $results as $result ) {
          echo "
            <tr class='" . ( ( $result->done ) ? 'done' : 'undone' ) . "'>
              <td width='5%'>$result->id</td>
              <td width='20%'>$result->created</td>
              <td width='50%'>$result->task</td>
              <td width='25%'class='buttons'>
                <a class='btn' style='padding: 0;' href='admin.php?page=to-do-list%2Fto-do-list.php&toggle=$result->id'>
                  <button type='button' class='btn btn-success btn-sm' title='Mark as done'>
                    <svg width='1em' height='1em' viewBox='0 0 16 16' class='bi bi-check-square' fill='currentColor' xmlns='http://www.w3.org/2000/svg'>
                      <path fill-rule='evenodd' d='M14 1H2a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1zM2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H2z'/>
                      <path fill-rule='evenodd' d='M10.97 4.97a.75.75 0 0 1 1.071 1.05l-3.992 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425a.236.236 0 0 1 .02-.022z'/>
                    </svg>
                  </button>
                </a>
                <a class='btn' style='padding: 0;' href='admin.php?page=to-do-list%2Fto-do-list.php&edit=$result->id'>
                  <button type='button' class='btn btn-secondary btn-sm'title='Edit'>
                    <svg width='1em' height='1em' viewBox='0 0 16 16' class='bi bi-pencil-square' fill='currentColor' xmlns='http://www.w3.org/2000/svg'>
                      <path d='M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456l-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z'></path>
                      <path fill-rule='evenodd' d='M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5v11z'></path>
                    </svg>
                  </button>
                </a> 
                <a class='btn' style='padding: 0;' href='admin.php?page=to-do-list%2Fto-do-list.php&delete=$result->id'>
                  <button type='button' class='btn btn-danger btn-sm'title='Delete'>
                    <svg width='1em' height='1em' viewBox='0 0 16 16' class='bi bi-x' fill='currentColor' xmlns='http://www.w3.org/2000/svg'>
                      <path fill-rule='evenodd' d='M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z'/>
                    </svg>
                  </button>
                </a>
              </td>
            </tr>
          ";
        }
      ?>
      </tbody>
      <?php
  //Add task
  if  (isset( $_POST['save'] ) ) {
    if ( empty( $_POST['task'] ) ) {
      echo "<script>alert( You must fill the task );</script>";
    }
    else {
      $created = current_time( 'mysql' );
      $task = $_POST['task']; 
      $wpdb->insert(
        $table_name,
        array(
          'created' => $created,
          'task' => $task,
          'done' => '0'
        )
      );
      echo "<script>location.replace('admin.php?page=to-do-list%2Fto-do-list.php');</script>";
    }
  }

	// Mark as read
	if ( isset( $_GET['toggle'] ) ) {
		$id = $_GET['toggle'];
		$done = $_GET['done'];
    $wpdb->query( "UPDATE $table_name SET `done` = IF( `done`= 0, 1, 0 ) WHERE id='$id'" );
    echo "<script>location.replace('admin.php?page=to-do-list%2Fto-do-list.php');</script>";
  }

  // Edit task
	if ( isset( $_GET['edit'] ) ) {
    $id = $_GET['edit'];
    $results = $wpdb->get_results( "SELECT * FROM $table_name WHERE id='$id'" );
    foreach ( $results as $result ) {
      $task = $result->task;
      $done = $result->done;
	  }
		?>
    <div class="card card-outline-secondary">
        <div class="card-header">
            <h3 class="mb-0">Edit task #<?php echo $id; ?></h3>
        </div>
        <form class="form-inline d-flex justify-content-center my-3" action="" method="post">
            <input type="hidden" name="id" value="<?php echo $id; ?>">
            <input class="form-control form-control-lg" type="text" name="task" value="<?php echo $task; ?>">
            <div class="card-footer">
                <div class="float-right">
                    <button type="submit" class="btn btn-success my-2" name="update">Update</button>
                    <button type="reset" class="btn btn-danger my-2" value="Reset"
                        onClick="window.location='admin.php?page=to-do-list%2Fto-do-list.php';">Reset</button>
                </div>
            </div>
        </form>
    </div>
    <?php
  }

  // Update task
  if ( isset( $_POST['update'] ) ) {
    $id = $_POST['id'];
    $task = $_POST['task'];
    $wpdb->update(
      $table_name,
      array(
        'task' => $task
      ),
      array(
          'id'=>$id
      )
    );
	  echo "<script>location.replace( 'admin.php?page=to-do-list%2Fto-do-list.php' );</script>";
  }

  // Delete task
  if ( isset( $_GET['delete'] ) ) {
    $id = $_GET['delete'];
    $wpdb->delete(
      $table_name,
      array(
        'id' => $id
      )
    );
    echo "<script>location.replace( 'admin.php?page=to-do-list%2Fto-do-list.php' );</script>";
  }
  
  // CSS & JS
  function custom_scripts() {
    wp_enqueue_style( 'bootstrap', '//stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css' );
    wp_enqueue_style( 'custom-styles', plugin_dir_url( __FILE__ ) . 'admin/css/styles.css' );
    wp_enqueue_script( 'bootstrap-jQuery', '//code.jquery.com/jquery-3.4.1.slim.min.js' );
    wp_enqueue_script( 'bootstrap-bundle', 'https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js' );
  }

  add_action('admin_print_styles', 'custom_scripts');
}