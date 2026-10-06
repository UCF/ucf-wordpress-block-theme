<?php
/**
 * Events: the Events block.
 *
 * CONTEXT: upcoming events from the university events system — today mock data
 * (data/mock/events.json), eventually UCF-Events-Plugin. The mock dates are offsets in days
 * from today, so the sample list is always current and the "past events drop off" rule is
 * exercised every day rather than only after the data ages.
 *
 * The shape `ucf_theme_mock_data( 'events' )` returns:
 *
 *     events  list of { title, offset (days from today), time, location, url }
 *
 * A real source returns `date` (YYYY-MM-DD) instead of `offset`; `ucf_theme_upcoming_events()`
 * accepts either.
 *
 * @package ucf-wordpress-block-theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // No direct access.
}

/**
 * Register the Events block.
 *
 * @return void
 */
function ucf_theme_register_event_blocks() {
	register_block_type(
		'ucf/events',
		array(
			'api_version'     => 3,
			'title'           => __( 'Events', 'ucf-wordpress-block-theme' ),
			'description'     => __( 'Upcoming events from the events calendar. Past events drop off on their own.', 'ucf-wordpress-block-theme' ),
			'category'        => 'ucf', // SYNC: UCF_THEME_BLOCK_CATEGORY in includes/blocks.php.
			'icon'            => 'calendar-alt',
			'keywords'        => array( 'calendar', 'upcoming' ),
			'attributes'      => array(
				'count' => array(
					'type'    => 'number',
					'default' => 3,
				),
			),
			'supports'        => array( 'html' => false ),
			'render_callback' => 'ucf_theme_render_events',
		)
	);
}
add_action( 'init', 'ucf_theme_register_event_blocks' );

/**
 * Upcoming events, soonest first, each with a resolved `date`.
 *
 * @param array<int, array<string, mixed>> $events Events, with `offset` or `date`.
 * @param string                           $today  Today, as YYYY-MM-DD.
 * @param int                              $count  How many to return.
 * @return array<int, array<string, mixed>> Events from today on.
 */
function ucf_theme_upcoming_events( array $events, $today, $count ) {
	$base     = strtotime( $today . ' 00:00:00 UTC' );
	$upcoming = array();

	foreach ( $events as $event ) {
		if ( ! isset( $event['date'] ) ) {
			$event['date'] = gmdate( 'Y-m-d', $base + (int) $event['offset'] * DAY_IN_SECONDS );
		}

		// SPEC: a list with last spring's events on it reads as an abandoned site.
		if ( $event['date'] >= $today ) {
			$upcoming[] = $event;
		}
	}

	usort(
		$upcoming,
		static function ( $a, $b ) {
			return strcmp( $a['date'], $b['date'] );
		}
	);

	return array_slice( $upcoming, 0, max( 1, (int) $count ) );
}

/**
 * Render the Events block.
 *
 * @param array<string, mixed> $attributes Block attributes.
 * @return string Markup.
 */
function ucf_theme_render_events( $attributes ) {
	$events = ucf_theme_upcoming_events(
		ucf_theme_mock_data( 'events' )['events'] ?? array(),
		current_time( 'Y-m-d' ),
		$attributes['count'] ?? 3
	);

	if ( ! $events ) {
		return '';
	}

	foreach ( $events as $event ) {
		ucf_theme_structured_data( ucf_theme_event_node( $event ) );
	}

	return sprintf(
		'<div %s>%s%s</div>',
		get_block_wrapper_attributes(),
		ucf_theme_events_markup( $events ),
		ucf_theme_sample_note()
	);
}

/**
 * The event list.
 *
 * @param array<int, array<string, mixed>> $events Events with a resolved `date`.
 * @return string Markup.
 */
function ucf_theme_events_markup( array $events ) {
	$months = array( 'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec' );
	$items  = '';

	foreach ( $events as $event ) {
		list( , $month, $day ) = array_map( 'intval', explode( '-', $event['date'] ) );

		// A11Y: the date block is decoration for the eye; the full date is in the `<time>`
		// beside it, so the block is hidden from assistive technology rather than read twice.
		$items .= sprintf(
			'<li class="ucf-event"><div class="ucf-event__date" aria-hidden="true"><span class="ucf-event__month">%s</span><span class="ucf-event__day">%d</span></div><div><h3 class="wp-block-heading ucf-event__title is-style-sans has-heading-5-font-size"><a href="%s">%s</a></h3><p class="ucf-event__meta"><span><time datetime="%s">%s</time>, %s</span><span>%s</span></p></div></li>',
			esc_html( $months[ $month - 1 ] ),
			$day,
			esc_url( $event['url'] ),
			esc_html( $event['title'] ),
			esc_attr( $event['date'] ),
			esc_html( ucf_theme_format_date( $event['date'] ) ),
			esc_html( $event['time'] ),
			esc_html( $event['location'] )
		);
	}

	return '<ul class="ucf-events">' . $items . '</ul>';
}

/**
 * The `Event` node.
 *
 * @param array<string, mixed> $event An event with a resolved `date`.
 * @return array<string, mixed> Node.
 */
function ucf_theme_event_node( array $event ) {
	$online = 'Online' === $event['location'];

	return array(
		'@type'               => 'Event',
		'name'                => $event['title'],
		'startDate'           => $event['date'],
		'url'                 => $event['url'],
		'eventAttendanceMode' => $online ? 'https://schema.org/OnlineEventAttendanceMode' : 'https://schema.org/OfflineEventAttendanceMode',
		'location'            => $online
			? array(
				'@type' => 'VirtualLocation',
				'url'   => $event['url'],
			)
			: array(
				'@type' => 'Place',
				'name'  => $event['location'],
			),
	);
}
