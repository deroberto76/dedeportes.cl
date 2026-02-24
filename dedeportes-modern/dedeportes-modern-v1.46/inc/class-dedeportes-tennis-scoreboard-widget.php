<?php
/**
 * Dedeportes Tennis Scoreboard Widget
 * 
 * A custom widget that displays a tennis scoreboard with up to 5 sets.
 *
 * @package Dedeportes_Modern
 */

class Dedeportes_Tennis_Scoreboard_Widget extends WP_Widget
{

    /**
     * Register widget with WordPress.
     */
    public function __construct()
    {
        parent::__construct(
            'dedeportes_tennis_scoreboard_widget', // Base ID
            esc_html__('Dedeportes: Marcador Tenis', 'dedeportes-modern'), // Name
            array('description' => esc_html__('Marcador de tenis con soporte para 5 sets. Se oculta si no hay tenistas.', 'dedeportes-modern'), ) // Args
        );
    }

    /**
     * Front-end display of widget.
     *
     * @see WP_Widget::widget()
     *
     * @param array $args     Widget arguments.
     * @param array $instance Saved values from database.
     */
    public function widget($args, $instance)
    {
        $player1 = !empty($instance['player1']) ? $instance['player1'] : '';
        $player2 = !empty($instance['player2']) ? $instance['player2'] : '';

        // Hide if no players defined
        if (empty($player1) || empty($player2)) {
            return;
        }

        $title = !empty($instance['title']) ? $instance['title'] : '';
        $competition = !empty($instance['competition']) ? $instance['competition'] : '';
        $footer_text = !empty($instance['footer_text']) ? $instance['footer_text'] : '';
        $is_live = !empty($instance['is_live']) ? true : false;

        // Sets Player 1
        $p1_sets = array();
        for ($i = 1; $i <= 5; $i++) {
            $p1_sets[$i] = isset($instance['p1_set' . $i]) ? $instance['p1_set' . $i] : '';
        }

        // Sets Player 2
        $p2_sets = array();
        for ($i = 1; $i <= 5; $i++) {
            $p2_sets[$i] = isset($instance['p2_set' . $i]) ? $instance['p2_set' . $i] : '';
        }

        echo $args['before_widget'];

        if (!empty($title)) {
            echo $args['before_title'] . apply_filters('widget_title', $title) . $args['after_title'];
        }
        ?>

        <div class="widget-content">
            <?php if (!empty($competition)): ?>
                <div class="scoreboard-competition">
                    <?php echo esc_html($competition); ?>
                </div>
            <?php endif; ?>

            <div class="tennis-scoreboard-container">
                <table class="scoreboard-table tennis-table">
                    <thead>
                        <tr>
                            <th class="player-col"><?php esc_html_e('Tenista', 'dedeportes-modern'); ?></th>
                            <th class="set-col">1</th>
                            <th class="set-col">2</th>
                            <th class="set-col">3</th>
                            <th class="set-col">4</th>
                            <th class="set-col">5</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Player 1 -->
                        <tr>
                            <td class="player-name"><?php echo esc_html($player1); ?></td>
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <td class="set-score"><?php echo esc_html($p1_sets[$i]); ?></td>
                            <?php endfor; ?>
                        </tr>
                        <!-- Player 2 -->
                        <tr>
                            <td class="player-name"><?php echo esc_html($player2); ?></td>
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <td class="set-score"><?php echo esc_html($p2_sets[$i]); ?></td>
                            <?php endfor; ?>
                        </tr>
                    </tbody>
                </table>
            </div>

            <?php if (!empty($footer_text) || $is_live): ?>
                <div class="scoreboard-footer">
                    <?php if ($is_live): ?>
                        <span class="live-indicator" aria-hidden="true"></span>
                        <span class="live-text"><?php esc_html_e('EN VIVO', 'dedeportes-modern'); ?></span>
                    <?php endif; ?>
                    <?php echo esc_html($footer_text); ?>
                </div>
            <?php endif; ?>
        </div>

        <?php
        echo $args['after_widget'];
    }

    /**
     * Back-end widget form.
     *
     * @see WP_Widget::form()
     *
     * @param array $instance Previously saved values from database.
     */
    public function form($instance)
    {
        $title = !empty($instance['title']) ? $instance['title'] : '';
        $competition = !empty($instance['competition']) ? $instance['competition'] : '';
        $player1 = !empty($instance['player1']) ? $instance['player1'] : '';
        $player2 = !empty($instance['player2']) ? $instance['player2'] : '';
        $footer_text = !empty($instance['footer_text']) ? $instance['footer_text'] : '';
        $is_live = !empty($instance['is_live']);

        // Helper to get set value safely
        $get_set = function ($p, $s) use ($instance) {
            return isset($instance["p{$p}_set{$s}"]) ? $instance["p{$p}_set{$s}"] : '';
        };
        ?>

        <!-- Widget Title -->
        <p>
            <label
                for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php esc_html_e('Título:', 'dedeportes-modern'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>"
                name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text"
                value="<?php echo esc_attr($title); ?>">
        </p>

        <!-- Competition -->
        <p>
            <label
                for="<?php echo esc_attr($this->get_field_id('competition')); ?>"><?php esc_html_e('Competencia:', 'dedeportes-modern'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('competition')); ?>"
                name="<?php echo esc_attr($this->get_field_name('competition')); ?>" type="text"
                value="<?php echo esc_attr($competition); ?>">
        </p>

        <hr>

        <!-- Player 1 -->
        <p>
            <label
                for="<?php echo esc_attr($this->get_field_id('player1')); ?>"><strong><?php esc_html_e('Tenista 1:', 'dedeportes-modern'); ?></strong></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('player1')); ?>"
                name="<?php echo esc_attr($this->get_field_name('player1')); ?>" type="text"
                value="<?php echo esc_attr($player1); ?>">
        </p>
        <p>
            <?php for ($i = 1; $i <= 5; $i++): ?>
                <label style="margin-right: 5px;">S<?php echo $i; ?>:
                    <input class="tiny-text" id="<?php echo esc_attr($this->get_field_id('p1_set' . $i)); ?>"
                        name="<?php echo esc_attr($this->get_field_name('p1_set' . $i)); ?>" type="text"
                        value="<?php echo esc_attr($get_set(1, $i)); ?>" size="2">
                </label>
            <?php endfor; ?>
        </p>

        <hr>

        <!-- Player 2 -->
        <p>
            <label
                for="<?php echo esc_attr($this->get_field_id('player2')); ?>"><strong><?php esc_html_e('Tenista 2:', 'dedeportes-modern'); ?></strong></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('player2')); ?>"
                name="<?php echo esc_attr($this->get_field_name('player2')); ?>" type="text"
                value="<?php echo esc_attr($player2); ?>">
        </p>
        <p>
            <?php for ($i = 1; $i <= 5; $i++): ?>
                <label style="margin-right: 5px;">S<?php echo $i; ?>:
                    <input class="tiny-text" id="<?php echo esc_attr($this->get_field_id('p2_set' . $i)); ?>"
                        name="<?php echo esc_attr($this->get_field_name('p2_set' . $i)); ?>" type="text"
                        value="<?php echo esc_attr($get_set(2, $i)); ?>" size="2">
                </label>
            <?php endfor; ?>
        </p>

        <hr>

        <!-- Footer Info -->
        <p>
            <label
                for="<?php echo esc_attr($this->get_field_id('footer_text')); ?>"><?php esc_html_e('Texto Pie:', 'dedeportes-modern'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('footer_text')); ?>"
                name="<?php echo esc_attr($this->get_field_name('footer_text')); ?>" type="text"
                value="<?php echo esc_attr($footer_text); ?>">
        </p>

        <!-- Is Live Checkbox -->
        <p>
            <input class="checkbox" type="checkbox" <?php checked($is_live); ?>
                id="<?php echo esc_attr($this->get_field_id('is_live')); ?>"
                name="<?php echo esc_attr($this->get_field_name('is_live')); ?>" />
            <label
                for="<?php echo esc_attr($this->get_field_id('is_live')); ?>"><?php esc_html_e('En Vivo', 'dedeportes-modern'); ?></label>
        </p>

        <?php
    }

    /**
     * Sanitize widget form values as they are saved.
     */
    public function update($new_instance, $old_instance)
    {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title'])) ? sanitize_text_field($new_instance['title']) : '';
        $instance['competition'] = (!empty($new_instance['competition'])) ? sanitize_text_field($new_instance['competition']) : '';
        $instance['player1'] = (!empty($new_instance['player1'])) ? sanitize_text_field($new_instance['player1']) : '';
        $instance['player2'] = (!empty($new_instance['player2'])) ? sanitize_text_field($new_instance['player2']) : '';
        $instance['footer_text'] = (!empty($new_instance['footer_text'])) ? sanitize_text_field($new_instance['footer_text']) : '';
        $instance['is_live'] = !empty($new_instance['is_live']);

        // Save sets
        for ($i = 1; $i <= 5; $i++) {
            $instance['p1_set' . $i] = isset($new_instance['p1_set' . $i]) ? sanitize_text_field($new_instance['p1_set' . $i]) : '';
            $instance['p2_set' . $i] = isset($new_instance['p2_set' . $i]) ? sanitize_text_field($new_instance['p2_set' . $i]) : '';
        }

        return $instance;
    }

} // class Dedeportes_Tennis_Scoreboard_Widget
