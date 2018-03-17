<?php
class MDB_Items_Table extends WP_List_Table 
{     
    public function export()
    {
        header('Content-Type: text/csv; charset=utf-8');
        $file_name = 'filename=' . $_GET['type'] . '.csv';        
        header('Content-Disposition: attachment; ' . $file_name);
        
        $output = fopen('php://output', 'w');
       
        $page_num = 1;
        while (true)
        {
            $this->prepare_items($page_num, MDB_MAX_ITEMS_REQUEST);
            if (count($this->items) == 0) 
                break;
            
            if ($page_num == 1) {
                fputcsv($output, array_values($this->_column_headers[0]));  
            }
    
            foreach ($this->items as $item) {
                 fputcsv($output, array_values($item));
            }
            
            $page_num++;
        }
    }
    
    public function display_tablenav($which) 
    {
    ?>
        <div class="tablenav <?php echo esc_attr($which); ?>">
            <?php $this->pagination($which);?>
            <br class="clear" />
        </div>
    <?php
    }
    
    
    // This function overrides WP_List_Table::print_column_headers()
    // It is identical except query parameter 'paged' is kept to allow sorting within a certain page 
	public function print_column_headers( $with_id = true ) 
    {
		list( $columns, $hidden, $sortable, $primary ) = $this->get_column_info();

		$current_url = set_url_scheme( 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] );
		//MDB statement removed  -> $current_url = remove_query_arg( 'paged', $current_url );

		if ( isset( $_GET['orderby'] ) ) {
			$current_orderby = $_GET['orderby'];
		} else {
			$current_orderby = '';
		}

		if ( isset( $_GET['order'] ) && 'desc' === $_GET['order'] ) {
			$current_order = 'desc';
		} else {
			$current_order = 'asc';
		}

		if ( ! empty( $columns['cb'] ) ) {
			static $cb_counter = 1;
			$columns['cb'] = '<label class="screen-reader-text" for="cb-select-all-' . $cb_counter . '">' . __( 'Select All' ) . '</label>'
				. '<input id="cb-select-all-' . $cb_counter . '" type="checkbox" />';
			$cb_counter++;
		}

		foreach ( $columns as $column_key => $column_display_name ) {
			$class = array( 'manage-column', "column-$column_key" );

			if ( in_array( $column_key, $hidden ) ) {
				$class[] = 'hidden';
			}

			if ( 'cb' === $column_key )
				$class[] = 'check-column';
			elseif ( in_array( $column_key, array( 'posts', 'comments', 'links' ) ) )
				$class[] = 'num';

			if ( $column_key === $primary ) {
				$class[] = 'column-primary';
			}

			if ( isset( $sortable[$column_key] ) ) {
				list( $orderby, $desc_first ) = $sortable[$column_key];

				if ( $current_orderby === $orderby ) {
					$order = 'asc' === $current_order ? 'desc' : 'asc';
					$class[] = 'sorted';
					$class[] = $current_order;
				} else {
					$order = $desc_first ? 'desc' : 'asc';
					$class[] = 'sortable';
					$class[] = $desc_first ? 'asc' : 'desc';
				}

				$column_display_name = '<a href="' . esc_url( add_query_arg( compact( 'orderby', 'order' ), $current_url ) ) . '"><span>' . $column_display_name . '</span><span class="sorting-indicator"></span></a>';
			}

			$tag = ( 'cb' === $column_key ) ? 'td' : 'th';
			$scope = ( 'th' === $tag ) ? 'scope="col"' : '';
			$id = $with_id ? "id='$column_key'" : '';

			if ( !empty( $class ) )
				$class = "class='" . join( ' ', $class ) . "'";

			echo "<$tag $scope $id $class>$column_display_name</$tag>";
		}
	}
}