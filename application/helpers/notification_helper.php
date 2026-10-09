<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Queues a Bootstrap alert for the current visitor.
 *
 * Stored in the CI session so it survives a redirect (PRG pattern) but is
 * also available on the same request, which covers validation failures that
 * re-render a form without redirecting.
 *
 * @param string $type    One of: success, danger, warning, info.
 * @param string $message Message text (or HTML when $raw is TRUE).
 * @param bool   $raw     Skip escaping when the message contains safe markup.
 * @return void
 */
if ( ! function_exists('set_notification'))
{
	function set_notification($type, $message, $raw = FALSE)
	{
		$CI =& get_instance();

		$notifications = $CI->session->userdata('notifications');
		if ( ! is_array($notifications))
		{
			$notifications = array();
		}

		$notifications[] = array(
			'type'    => $type,
			'message' => $message,
			'raw'     => (bool) $raw
		);

		$CI->session->set_userdata('notifications', $notifications);
	}
}

/**
 * Renders and clears every queued notification as Bootstrap 5 alerts.
 *
 * @return string Alert markup, or an empty string when nothing is queued.
 */
if ( ! function_exists('render_notifications'))
{
	function render_notifications()
	{
		$CI =& get_instance();

		$notifications = $CI->session->userdata('notifications');
		if (empty($notifications))
		{
			return '';
		}

		$CI->session->unset_userdata('notifications');

		$allowed = array('success', 'danger', 'warning', 'info');
		$html = '';

		foreach ($notifications as $notification)
		{
			$type = in_array($notification['type'], $allowed, TRUE) ? $notification['type'] : 'info';
			$message = empty($notification['raw'])
				? html_escape($notification['message'])
				: $notification['message'];

			$html .= '<div class="alert alert-' . $type . ' alert-dismissible fade show" role="alert">'
				. $message
				. '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>'
				. '</div>';
		}

		return '<div class="container-fluid notification-area px-3 px-md-4">' . $html . '</div>';
	}
}
