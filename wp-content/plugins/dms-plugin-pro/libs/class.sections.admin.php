<?php
class PL_Pro_Sections_Admin {
  function __construct() {
    global $wpdb;
    $this->table_name = $wpdb->prefix . "pl_data_sections";
    add_action( 'admin_menu', array( $this, 'add_menu' ) );
    if( isset( $_POST['sections_admin_update'] ) ) {
      $this->update();
    }
    if( isset( $_POST['sections_admin_delete'] ) ) {
      $this->delete_section();
    }
  }

  function add_menu() {
    add_management_page( 'Sections Admin', 'Sections Admin', 'activate_plugins', 'sections_admin', array( $this, 'draw_page' ) );
  }

  function draw_page() {

    if( isset( $_GET['action'] ) ) {
      return $this->actions();
    }

    if(!class_exists( 'Tools_WP_List_Table' ) ) {
      require_once( 'class-wp-list-table.php' );
    }
    include_once( 'class.sections.admin.table.php' );
    $updated = ( isset( $_GET['updated'] ) ) ? '<div class="updated fade">Section Deleted!</div>' : '';
    //Prepare Table of elements
    $wp_list_table = new Sections_Admin_Table();
    echo '<div class="wrap"><h2>Sections Admin Beta</h2>';
    echo $updated;
    $wp_list_table->prepare_items();
    ?>
    <form method="post">
  <input type="hidden" name="page" value="sections_admin" />
  <?php $wp_list_table->search_box('search', 'live'); ?>
</form>
<?php
    $wp_list_table->display();
    echo '</div>';
  }

  function actions() {
    $action = $_GET['action'];

    switch ($action) {
      case 'edit':
        $this->edit();
        break;
      case 'delete':
        $this->delete_check();
        break;
    }
  }

  function edit() {

    global $wpdb;
    $uid = $_GET['uid'];
    $query = "SELECT * FROM $this->table_name where uid = '$uid'";
    $data = $wpdb->get_row($query);
    $updated = ( isset( $_GET['updated'] ) ) ? '<div class="updated fade">Section Updated!</div>' : '';

    echo '<div class="wrap"><form method="POST"><input type="hidden" name="page" value="sections_admin" /><input type="hidden" name="sections_admin_update" value="true" />';
    echo $updated;
    printf( '<p><h3>UID: %s</h3></p>', $data->uid );
    printf( '<input type="hidden" name="uid" value="%s" />', $data->uid );
    printf( '<p><h3>Draft</h3><textarea style="width:80%%;height:300px" name="draft">%s</textarea></p>', stripslashes( $data->draft ) );
    printf( '<p><h3>Live</h3><textarea style="width:80%%;height:300px" name="live">%s</textarea></p>', stripslashes( $data->live ) );
    submit_button();
    echo '</form></div>';
  }

  function update() {
    if( isset( $_POST['page']) && 'sections_admin' == $_POST['page'] ) {
      global $wpdb;
      $draft = stripslashes_deep( $_POST['draft'] );
      $live = stripslashes_deep( $_POST['live'] );
    }
    $query = $wpdb->prepare("UPDATE $this->table_name SET draft = %s, live = %s WHERE uid = %s limit 1", $draft, $live, $_POST['uid']);
    $result = $wpdb->get_results( $query );
    wp_safe_redirect( admin_url( 'tools.php?page=sections_admin&action=edit&updated=true&uid=' . $_POST['uid'] ) );
  }

  function delete_check() {

    $uid = $_GET['uid'];
    echo '<div class="wrap"><form method="POST"><input type="hidden" name="page" value="sections_admin" /><input type="hidden" name="sections_admin_delete" value="true" />';

    echo '<h1>Are you sure?</h1>';
    printf('<h4>You are about to delete the section with id %s, there is no way to undelete!</h4>', $uid );
    printf( '<input type="hidden" name="uid" value="%s" />', $uid );

    submit_button( sprintf( 'I understand the risks, please delete section [%s]', $uid ), 'delete' );
    echo '</form></div>';
  }

  function delete_section() {

    if( isset( $_POST['sections_admin_delete'] ) && 'true' == $_POST['sections_admin_delete'] ) {
      $uid = $_POST['uid'];
      global $wpdb;
      $query = $wpdb->prepare("DELETE from $this->table_name WHERE uid = '%s' limit 1", $uid);
      $result = $wpdb->get_results( $query );
      wp_safe_redirect( admin_url( 'tools.php?page=sections_admin&updated=true' ) );
    }
  }
}
