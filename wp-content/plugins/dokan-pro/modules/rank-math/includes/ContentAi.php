<?php

namespace WeDevs\DokanPro\Modules\RankMath;

defined( 'ABSPATH' ) || exit;

use RankMath\Helper;
use MyThemeShop\Helpers\WordPress;
use RankMath\ContentAI\Content_AI;

/**
 * Schema manger class
 *
 * @since 3.5.0
 */
class ContentAi extends Content_AI {

    /**
     * Class constructor
     *
     * @since 3.5.0
     */
    public function __construct() {
        parent::__construct();
        $this->editor_scripts();
    }

    /**
     * Enqueue assets for post editors.
     *
     * @since 3.5.0
     *
     * @return void
     */
    public function editor_scripts() {
        if ( ! in_array( WordPress::get_post_type(), (array) Helper::get_settings( 'general.content_ai_post_types' ), true ) ) {
            return;
        }

        wp_enqueue_style( 'rank-math-common' );

        wp_enqueue_style( 'rank-math-content-ai' );

        wp_enqueue_script( 'rank-math-content-ai' );
    }
}
