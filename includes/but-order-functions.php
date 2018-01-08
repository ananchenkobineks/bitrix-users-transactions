<?php
/**
 * BUT Order Functions
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get all existing order statuses.
 *
 */
function but_get_order_status( $key ) {

	$statuses = [
		'NEW'					=> 'Ожидает обработки менеджером',
		'3' 					=> 'Ожидает подтверждения клиентом',
		'1' 					=> 'Ожидание предоплаты',
		'PREPARATION' 			=> 'Ожидает Выкупа',
		'PREPAYMENT_INVOICE'	=> 'Выкуплен',
		'2' 					=> 'Частично доставлен',
		'EXECUTING' 			=> 'Доставлен',
		'4' 					=> 'Доставлен',
		'WON' 					=> 'Доставлен',
		'LOSE' 					=> 'Отменен',
	];

	return $statuses[ $key ];
}

function get_transaction_types() {

	return [
		0 => __( 'Списание средств на предоплату', 'bitrix-ut' ),
		1 => __( 'Списание средств за комиссию и доставку', 'bitrix-ut' ),
		2 => __( 'Внесение средств на предоплату', 'bitrix-ut' ),
		3 => __( 'Внесение средств за комиссию и доставку', 'bitrix-ut' ),
		4 => __( 'Произвольный комментарий', 'bitrix-ut' ),
	];
}


function get_transaction_type_name( $key ) {

	$types = get_transaction_types();

	return $types[ $key ];
}


function get_transaction_type_class( $key ) {

	if( $key <= 1 ) {
		$class = "pay-in";
	} elseif( $key <= 3 ) {
		$class = "write-off";
	} else {
		$class = "admin-comment";
	}

	return $class;
}

function get_transaction_statuses() {
	return [
		0 => __( 'Ожидает подтверждения', 'bitrix-ut' ),
		1 => __( 'Подтверждено', 'bitrix-ut' ),
		2 => __( 'Отклонено', 'bitrix-ut' ),
	];
}

function get_transaction_status_name( $key ) {

	$types = get_transaction_statuses();

	return $types[ $key ];
}

function get_transaction_status_class( $key ) {

	if( $key == 0 ) {
		$class = "waiting";
	} elseif( $key == 1 ) {
		$class = "confirmed";
	} elseif( $key == 2 ) {
		$class = "canceled";
	}

	return $class;
}