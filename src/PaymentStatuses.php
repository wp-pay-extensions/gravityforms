<?php
/**
 * Payment statuses
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2018 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Extensions\GravityForms
 */

namespace Pronamic\WordPress\Pay\Extensions\GravityForms;

use Pronamic\WordPress\Pay\Core\Statuses as Core_Statuses;

/**
 * Title: WordPress pay extension Gravity Forms payment statuses
 * Description:
 * Copyright: Copyright (c) 2005 - 2018
 * Company: Pronamic
 *
 * @author  Remco Tolsma
 * @version 2.0.0
 * @since   1.0.0
 */
class PaymentStatuses {
	/**
	 * Payment status processing
	 *
	 * @var string
	 */
	const PROCESSING = 'Processing';

	/**
	 * Payment status active
	 *
	 * @var string
	 */
	const ACTIVE = 'Active';

	/**
	 * Payment status cancelled
	 *
	 * @var string
	 */
	const CANCELLED = 'Cancelled';

	/**
	 * Payment status expired
	 *
	 * @var string
	 */
	const EXPIRED = 'Expired';

	/**
	 * Payment status failed
	 *
	 * @var string
	 */
	const FAILED = 'Failed';

	/**
	 * Payment status reversed
	 *
	 * @var string
	 */
	const REVERSED = 'Reversed';

	/**
	 * Payment status approved
	 *
	 * @var string
	 */
	const APPROVED = 'Approved';

	/**
	 * Payment status paid
	 *
	 * @var string
	 */
	const PAID = 'Paid';

	/**
	 * Payment status denied
	 *
	 * @var string
	 */
	const DENIED = 'Denied';

	/**
	 * Payment status pending
	 *
	 * @var string
	 */
	const PENDING = 'Pending';

	/**
	 * Payment status refunded
	 *
	 * @var string
	 */
	const REFUNDED = 'Refunded';

	/**
	 * Payment status voided
	 *
	 * @var string
	 */
	const VOIDED = 'Voided';

	/**
	 * Transform a Pronamic Pay status to a Gravity Forms payment status.
	 *
	 * @param string $status OmniKassa 2.0 status.
	 * @return string|null
	 */
	public static function transform( $status ) {
		switch ( $status ) {
			case Core_Statuses::SUCCESS:
				return self::PAID;
			case Core_Statuses::CANCELLED:
				return self::CANCELLED;
			case Core_Statuses::EXPIRED:
				return self::EXPIRED;
			case Core_Statuses::FAILURE:
				return self::FAILED;
			case Core_Statuses::OPEN:
				return self::PROCESSING;
			case Core_Statuses::OPEN:
				return self::PROCESSING;
			default:
				return null;
		}
	}
}
