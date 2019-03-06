<?php

class GFLE_Display {

	public function hook() {
		add_filter( 'gform_pre_render', [ $this, 'display_form' ], 10, 1 );
	}

	/**
	 * @param $form
	 */
	public function display_form( $form ) {
		if ( $form ) {
			$yes = true;
		}

		return $form;
	}

}