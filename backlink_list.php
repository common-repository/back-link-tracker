<?php
extract($_GET);
if(@$delete=='success')
echo '<div class="updated"><p><strong> Member Deleted Successfully</strong></p></div>';
if(isset($_GET['saved']))
{
	echo '<div class="updated"><p><strong> Member Saved Successfully</strong></p></div>';
}
if( ! class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
    }

    class MANAGE_WP_BACKLINK extends WP_List_Table
    {
        function __construct()
        {
        global $status, $page;
        parent::__construct( array('items',));
        }
        function column_default($item, $column_name)
        {  
			//print_r($column_name);
			switch($column_name)
            {
			case 'refer_url': 
			echo $item['refer_url']; break;
			case 'redirect_url': 
			echo $item['redirect_url']; break;
			case 'status': 
			echo $item['status']; break;
			case 'action': 
			echo "<a href='?page=edit_link&id=".$item['id']."'>Edit</a>"; break;
			default: echo date('j-M-Y');
            return false;
            }
        }
        function column_Title($item)
        {
			$actions=array('edit'=>sprintf('<a href="?page=add_members&view=update&id='.$item['id'].'">Edit</a>',$_REQUEST['page'],$item['id'],'edit'),
            'delete' => sprintf('<a href="?page=%s&action=%s&id=%s" onclick="return wptml_del_tst();">Delete</a>',$_REQUEST['page'],'delete',$item['id']),);
		 	return sprintf('%1$s%3$s',
           /*$1%s*/ $item['refer_url'],
		   /*$2%s*/ $item['id'],
           /*$3%s*/ $this->row_actions($actions));
        }
        function column_cb($item)
        {
        return sprintf('<input type="checkbox" name="check[]" value="%1$s" />', $item['id'] );
        }
        function get_columns()
        {
        $columns = array('cb'=>'<input type="checkbox"/>','refer_url'=>'Refer URL','redirect_url'=>'Redirect URL','status'=>'Status','action'=>'Action');
        return $columns;
        }
        function get_sortable_columns()
        {
        $sortable_columns = array('title'=> array('Refer URL',false), 
                                  );
		 
        return $sortable_columns;
        }
        function get_bulk_actions() 
        {
        $actions = array('trash' => 'Trash');
        return $actions;
        }
        function process_bulk_action() 
        {
            global $wpdb;
            extract($_REQUEST);
            $table_name = $wpdb->prefix . "back_link";
            if( 'delete'===$this->current_action() ) 
            {
                $screen = get_current_screen();
                $query = "delete  FROM $table_name where id=$id ";
                $wpdb->query($query);
				/*echo '<script>window.location = "?page=manage_members&delete=success";</script>';*/
            }
            if(isset($check))
            {
                if( 'trash'===$this->current_action() ) 
                {
                    foreach($check as $trashid)
                    {
                        $query = "delete  FROM $table_name where id=$trashid ";
                        $wpdb->query($query);
                    }
					echo '<script>window.location = "?page=manage_link&delete=success";</script>';
                }
            }
        }
        function prepare_items()
        {
			if(isset($_POST['s']))
			{
				$name = $_POST['s'];
				$name = explode(' ', $name);
				if(isset($name[0]))
				{
					$fname = $name[0];
				}
				else
				{
					$fname = '';
				}
				if(isset($name[1]))
				{
					$lname = $name[1];
				}
				else
				{
					$lname = '';
				}
			}
            $per_page =20;
            global $wpdb;
            $screen = get_current_screen();
            $table_name = $wpdb->prefix . "back_link";
            $query = "SELECT * FROM $table_name";
			$example_data=$wpdb->get_results($query, ARRAY_A);
            $columns = $this->get_columns();
            $hidden = array();
            $sortable = $this->get_sortable_columns();
            $this->_column_headers = array($columns, $hidden, $sortable);       
            $this->process_bulk_action();
            $tdata = array();
            if(isset($example_data))
            {
                foreach($example_data as $k)
                {
					$item = $k;
                    $actions = $this->column_Title($item) ;
                    $tdata[]=array();
                    //echo $k['Title'];
                }
            }
            if(isset($actions) && isset($k))
            {
                $data=array( $k, $actions );
            }      
            $data = $example_data;
            if(isset($_REQUEST['orderby']))
            {
                function usort_reorder($a,$b)
                {
                $orderby = (!empty($_REQUEST['orderby'])) ? $_REQUEST['orderby'] : 'date';
                $order = (!empty($_REQUEST['order'])) ? $_REQUEST['order'] : 'desc';
                $result = strcmp($a[$orderby], $b[$orderby]);
                return ($order==='asc') ? $result : -$result;
                }
                usort($data, 'usort_reorder');
            }
            $current_page = $this->get_pagenum();
            $total_items = count($data);
            $data = array_slice($data,(($current_page-1)*$per_page),$per_page);
            $this->items = $data;   
            $this->set_pagination_args(array('total_items'=>$total_items,'per_page'=> $per_page,
                'total_pages'=>ceil($total_items/$per_page)) );
        }
    }
    ?> 
    <div class="wrap">
        <div id="icon-edit" class="icon32"><br/></div>
        <form id="movies-filter" method="post">
            <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
            <?php 
            $testListTable = new MANAGE_WP_BACKLINK();
            $testListTable->prepare_items();
            ?>
            <div>  <?php $testListTable->display();
                ?>
            </div>
     </form>
    </div>