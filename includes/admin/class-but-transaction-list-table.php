<?php
/**
 * Transaction List Table
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if( !class_exists('WP_List_Table') ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class BUT_Transaction_List_Table extends WP_List_Table {

	public function get_columns(){
		$columns = array(
			'title' 	=> 'Назначение',
			'status'    => 'Статус',
			'amount'    => 'Сумма',
			'date'		=> 'Дата',
			'comment'	=> 'Комментарий',
			'action'	=> ''
		);
		return $columns;
	}

	public function prepare_items() {
		global $wpdb;

		$per_page = $this->get_items_per_page('transactions_per_page', 50);
		$columns = $this->get_columns();
		$hidden = array();

        $sortable = $this->get_sortable_columns();

		$this->_column_headers = array($columns, $hidden, $sortable);

		$table_name = $wpdb->prefix . "btx_transactions";
		$transactions = $wpdb->get_results("SELECT * FROM $table_name WHERE type = '2' OR type = '3'");

		$data = [];

		foreach( $transactions as $info ) {

			$actions = '';
			$btx_order_id = get_post_meta( $info->order_id, '_btx_order_id', true );

			if( $info->status == 0 ) {
				$actions = '
				<a href="'. $this->get_transaction_status_url( 'accept', $info->id, $info->order_id, $btx_order_id ) .'" class="accept"><span class="dashicons dashicons-yes"></span></a>
				<a href="'. $this->get_transaction_status_url( 'dismiss', $info->id, $info->order_id, $btx_order_id ) .'" class="dismiss"><span class="dashicons dashicons-no-alt"></span></a>';
			}

			$data[] = [
				'id' 			=> $info->id,
				'title' 		=> get_transaction_type_name( $info->type ).'<span>Bitrix ID: '.$btx_order_id.'</span>',
				'status' 		=> get_transaction_status_name( $info->status ),
				'amount' 		=> $info->amount,
				'date'			=> date( 'd-m-Y H:i',  strtotime( $info->date ) ),
				'comment' 		=> $info->comment,
				'action'  		=> $actions,
				'status_key'  	=> $info->status,
				'type_key'  	=> $info->type,
			];
		}

		usort( $data, array( $this, 'usort_reorder' ) );

		$current_page = $this->get_pagenum();
		$total_items = count($data);
		$data = array_slice( $data, (($current_page-1)*$per_page), $per_page );

		$this->set_pagination_args( array(
            'total_items' => $total_items,
            'per_page'    => $per_page,
            'total_pages' => ceil($total_items/$per_page)
        ) );

		$this->items = $data;
	}

	public function column_default( $item, $column_name ) {
		switch( $column_name ) { 
			case 'title':
			case 'status':
			case 'amount':
			case 'date':
			case 'comment':
			case 'action':
				return $item[ $column_name ];
		default:
			return print_r( $item, true );
		}
	}

	public function get_sortable_columns() {
        $sortable_columns = [
        	'title'     => [ 'title', false ],
            'status'    => [ 'status', false ],
        ];
        return $sortable_columns;
    }

    public function single_row( $item ) {

		echo '<tr class="'.get_transaction_type_class( $item['type_key'] ).' '. get_transaction_status_class( $item['status_key'] ).'">';
		$this->single_row_columns( $item );
		echo "</tr>\n";
    }

    public function usort_reorder( $a, $b ) {
		$orderby = ( ! empty( $_GET['orderby'] ) ) ? $_GET['orderby'] : 'date';
		$order = ( ! empty($_GET['order'] ) ) ? $_GET['order'] : 'desc';
		$result = strcmp( $a[$orderby], $b[$orderby] );
		return ( $order === 'asc' ) ? $result : -$result;
	}

	private function get_transaction_status_url( $nonce, $trans_id, $wp_order_id, $btx_order_id ) {

		$url = wp_nonce_url( admin_url( 'admin-post.php' ), $nonce.'_'.$trans_id );

		$query = build_query( array(
			'transaction_id' => $trans_id,
			'wp_order_id' => $wp_order_id,
			'btx_order_id' => $btx_order_id,
			'action' => 'process_transaction'
		) );

		return $url.'&'.$query;
	}

}