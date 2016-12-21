<?php
class Sections_Admin_Table extends Tools_WP_List_Table {

   /**
    * Constructor, we override the parent to pass our own arguments
    * We usually focus on three parameters: singular and plural labels, as well as whether the class supports AJAX.
    */
    function __construct() {
      global $wpdb;
       parent::__construct( array(
      'singular'=> 'wp_list_text_link', //Singular label
      'plural' => 'wp_list_test_links', //plural label, also this well be one of the table css class
      'ajax'   => false //We won't support Ajax for this table
      ) );
      $this->table_name = $wpdb->prefix . "pl_data_sections";
    }


    /**
 * Add extra markup in the toolbars before or after the list
 * @param string $which, helps you decide if you add the markup after (bottom) or before (top) the list
 */
function extra_tablenav( $which ) {
   if ( $which == "top" ){
      //The code that goes before the table is here
      echo"<em>This tool is experimental, use at your own risk!!</em>";
      echo"<style>#col_id{width:60px}#col_uid{width:100px}#col_edit{width:120px}</style>";
   }
}

/**
 * Define the columns that are going to be used in the table
 * @return array $columns, the array of columns to use with the table
 */
function get_columns() {
   return $columns= array(
      'col_id'=>__('ID'),
      'col_uid'=>__('UID'),
      'col_edit'=>__('Edit'),
      'col_draft'=>__('Draft'),
      'col_live'=>__('Live')
   );
}

/**
 * Decide which columns to activate the sorting functionality on
 * @return array $sortable, the array of columns that can be sorted by the user
 */
public function get_sortable_columns() {
   return $sortable = array(
      'col_id'=> array( 'id', true ),
      'col_uid'=> array( 'uid', true )
   );
}


/**
 * Prepare the table with different parameters, pagination, columns and table elements
 */
function prepare_items() {
   global $wpdb, $_wp_column_headers;
   $screen = get_current_screen();

   /* -- Preparing your query -- */
        $query = "SELECT * FROM $this->table_name";

        // if search...

        if( isset( $_POST['s'] ) ) {
          $s = esc_sql( $_POST['s'] );
          $query .= " WHERE uid LIKE '$s' OR live LIKE '%$s%' OR draft LIKE '%$s%'";
        }

   /* -- Ordering parameters -- */
       //Parameters that are going to be used to order the result
       $orderby = !empty($_GET["orderby"]) ? esc_sql($_GET["orderby"]) : 'ASC';
       $order = !empty($_GET["order"]) ? esc_sql($_GET["order"]) : '';
       if(!empty($orderby) & !empty($order)){ $query.=' ORDER BY '.$orderby.' '.$order; }

   /* -- Pagination parameters -- */
        //Number of elements in your table?
        $totalitems = $wpdb->query($query); //return the total number of affected rows
        //How many to display per page?
        $perpage = 15;
        //Which page is this?
        $paged = !empty($_GET["paged"]) ? esc_sql(  $_GET["paged"]) : '';
        //Page Number
        if(empty($paged) || !is_numeric($paged) || $paged<=0 ){ $paged=1; }
        //How many pages do we have in total?
        $totalpages = ceil($totalitems/$perpage);
        //adjust the query to take pagination into account
       if(!empty($paged) && !empty($perpage)){
          $offset=($paged-1)*$perpage;
         $query.=' LIMIT '.(int)$offset.','.(int)$perpage;
       }

   /* -- Register the pagination -- */
      $this->set_pagination_args( array(
         "total_items" => $totalitems,
         "total_pages" => $totalpages,
         "per_page" => $perpage,
      ) );
      //The pagination links are automatically built according to those parameters

   /* -- Register the Columns -- */
      $columns = $this->get_columns();
      $hidden = array();
      $sortable = $this->get_sortable_columns();
      $this->_column_headers = array($columns, $hidden, $sortable);

   /* -- Fetch the items -- */
      $this->items = $wpdb->get_results($query);
}


/**
 * Display the rows of records in the table
 * @return string, echo the markup of the rows
 */
function display_rows() {

   //Get the records registered in the prepare_items method
   $records = $this->items;

   //Get the columns registered in the get_columns and get_sortable_columns methods
   list( $columns, $hidden ) = $this->get_column_info();

   //Loop for each record
   if(!empty($records)){foreach($records as $rec){

      //Open the line
        echo '<tr id="record_'.$rec->id.'">';
      foreach ( $columns as $column_name => $column_display_name ) {

         //Style attributes for each col
         $class = "class='$column_name column-$column_name'";
         $style = "";
         if ( in_array( $column_name, $hidden ) ) $style = ' style="display:none;"';
         $attributes = $class . $style;

         //edit link
         $editlink  = '/wp-admin/tools.php?page=sections_admin&action=edit&uid='.$rec->uid;
         $deletelink  = '/wp-admin/tools.php?page=sections_admin&action=delete&uid='.$rec->uid;

         //Display the cell
         switch ( $column_name ) {
            case "col_id":  echo '<td '.$attributes.'>'.stripslashes($rec->id).'</td>';   break;
            case "col_uid": echo '<td '.$attributes.'>'.stripslashes($rec->uid).'</td>'; break;
            case "col_draft": echo '<td '.$attributes.'>'.stripslashes( esc_html($rec->draft) ).'</td>'; break;
            case "col_live": echo '<td '.$attributes.'>'. stripslashes( esc_html( $rec->live ) ).'</td>'; break;
            case 'col_edit': echo '<td '.$attributes.'>' . '<a href="'.$editlink.'" class="button button-primary">Edit</a><!--&nbsp;<a href="'.$deletelink.'" class="button button-primary">Delete</a>--></td>';
         }
      }

      //Close the line
      echo'</tr>';
   }}
}

  function column_default( $item, $column_name ) {
  switch( $column_name ) {
    case 'id':
    case 'uid':
    case 'edit':
    case 'draft':
    case 'live':
      return $item[ $column_name ];
    default:
      return print_r( $item, true ) ; //Show the whole array for troubleshooting purposes
  }
}

}
