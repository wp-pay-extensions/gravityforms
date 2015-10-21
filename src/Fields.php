<?php

/**
 * Title: WordPress pay extension Gravity Forms fields
 * Description:
 * Copyright: Copyright (c) 2005 - 2015
 * Company: Pronamic
 * @author Remco Tolsma
 * @version 1.0.0
 */
class Pronamic_WP_Pay_Extensions_GravityForms_Fields {
	/**
	 * Bootstrap
	 */
	public static function bootstrap() {
		add_filter( 'gform_enable_credit_card_field', '__return_true' );

		add_filter( 'gform_add_field_buttons', array( __CLASS__, 'add_field_buttons' ) );
		add_filter( 'gform_field_input',       array( __CLASS__, 'acquirer_field_input' ), 10, 5 );
		add_filter( 'gform_field_type_title',  array( __CLASS__, 'field_type_title' ) );
	}

	/**
	 * Acquirrer field input
	 *
	 * @param string $field_content
	 * @param string $field
	 * @param string $value
	 * @param string $lead_id
	 * @param string $form_id
	 */
	public static function acquirer_field_input( $field_content, $field, $value, $lead_id, $form_id ) {
		$type = RGFormsModel::get_input_type( $field );

		if ( Pronamic_WP_Pay_Extensions_GravityForms_IssuerDropDown::TYPE === $type ) {
			$id            = $field['id'];
			$field_id      = IS_ADMIN || 0 === $form_id ? "input_$id" : 'input_' . $form_id . "_$id";

			$class_suffix  = RG_CURRENT_VIEW === 'entry' ? '_admin' : '';
			$size          = rgar( $field, 'size' );

			$class         = $size . $class_suffix;
			$css_class     = trim( esc_attr( $class ) . ' gfield_ideal_acquirer_select' );

			$tab_index     = GFCommon::get_tabindex();

			$disabled_text = ( IS_ADMIN && 'entry' !== RG_CURRENT_VIEW ) ? "disabled='disabled'" : '';

			$html = '';

			$feed = get_pronamic_gf_pay_conditioned_feed_by_form_id( $form_id, true );

			/**
			 * Developing warning:
			 * Don't use single quotes in the HTML you output, it is buggy in combination with SACK
			 */
			if ( IS_ADMIN ) {
				if ( null === $feed ) {
					$html .= sprintf(
						"<a class='ideal-edit-link' href='%s' target='_blank'>%s</a>",
						add_query_arg( 'post_type', 'pronamic_pay_gf', admin_url( 'post-new.php' ) ),
						__( 'Create iDEAL feed', 'pronamic_ideal' )
					);
				} else {
					$html .= sprintf(
						"<a class='ideal-edit-link' href='%s' target='_blank'>%s</a>",
						get_edit_post_link( $feed->id ),
						__( 'Edit iDEAL feed', 'pronamic_ideal' )
					);
				}
			}

			$html_input = '';
			$html_error = '';

			if ( null !== $feed ) {
				$gateway = Pronamic_WP_Pay_Plugin::get_gateway( $feed->config_id );

				if ( $gateway ) {
					$issuer_field = $gateway->get_issuer_field();

					$error = $gateway->get_error();

					if ( is_wp_error( $error ) ) {
						$html_error .= Pronamic_WP_Pay_Plugin::get_default_error_message();
						$html_error .= '<br /><em>' . $error->get_error_message() . '</em>';
					} elseif ( $issuer_field ) {
						$choices = $issuer_field['choices'];

						$options = Pronamic_WP_HTML_Helper::select_options_grouped( $choices, $value );
						// Double quotes are not working, se we replace them with an single quote
						$options = str_replace( '"', '\'', $options );

						$html_input  = '';
						$html_input .= sprintf( "<select name='input_%d' id='%s' class='%s' %s %s>", $id, $field_id, $css_class, $tab_index, $disabled_text );
						$html_input .= sprintf( '%s', $options );
						$html_input .= sprintf( '</select>' );
					}
				}
			}

			if ( $html_error ) {
				$html .= sprintf( "<div class='gfield_description validation_message'>" );
				$html .= sprintf( '%s', $html_error );
				$html .= sprintf( '</div>' );
			} else {
				$html .= sprintf( "<div class='ginput_container ginput_ideal'>" );
				$html .= sprintf( '%s', $html_input );
				$html .= sprintf( '</div>' );
			}

			$field_content = $html;
		}

		return $field_content;
	}

	/**
	 * Add field buttons
	 *
	 * @param array $groups
	 */
	public static function add_field_buttons( $groups ) {
		// Fields
		$fields = array(
			array(
				'class'     => 'button',
				'value'     => __( 'Issuer Drop Down', 'pronamic_ideal' ),
				'data-type' => Pronamic_WP_Pay_Extensions_GravityForms_IssuerDropDown::TYPE,
			),
		);

		// Backwards compatibility version 1.9
		// @see https://github.com/gravityforms/gravityforms/blob/1.9/js/form_editor.js#L24-L26
		if ( Pronamic_WP_Pay_Extensions_GravityForms_GravityForms::version_compare( '1.9', '<' ) ) {
			foreach ( $fields as &$field ) {
				$field['onclick'] = sprintf( "StartAddField('%s');", $field['data-type'] );
			}
		}

		// Group
		$group = array(
			'name'   => 'ideal_fields',
			'label'  => __( 'iDEAL Fields', 'pronamic_ideal' ),
			'fields' => $fields,
		);

		$groups[] = $group;

		return $groups;
	}

	/**
	 * Field type title
	 *
	 * @param string $type
	 */
	public static function field_type_title( $type ) {
		switch ( $type ) {
			case Pronamic_WP_Pay_Extensions_GravityForms_IssuerDropDown::TYPE:
				return __( 'Issuer Drop Down', 'pronamic_ideal' );
		}

		return $type;
	}
}
