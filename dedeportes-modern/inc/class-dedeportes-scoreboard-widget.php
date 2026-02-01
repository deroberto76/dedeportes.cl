<?php
/**
 * Dedeportes Scoreboard Widget
 * 
 * A custom widget that displays a scoreboard.
 * Significantly, it renders NOTHING if the team names are empty,
 * allowing the sidebar to collapse gracefully.
 *
 * @package Dedeportes_Modern
 */

class Dedeportes_Scoreboard_Widget extends WP_Widget
{

    /**
     * Register widget with WordPress.
     */
    public function __construct()
    {
        parent::__construct(
            'dedeportes_scoreboard_widget', // Base ID
            esc_html__('Dedeportes: Marcador', 'dedeportes-modern'), // Name
            array('description' => esc_html__('Un marcador que se oculta automÃ¡ticamente si no hay equipos definidos.', 'dedeportes-modern'), ) // Args
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
        $team1 = !empty($instance['team1']) ? $instance['team1'] : '';
        $team2 = !empty($instance['team2']) ? $instance['team2'] : '';

        // CRITICAL LOGIC: Hide widget completely if teams are missing
        if (empty($team1) || empty($team2)) {
            return;
        }

        $title = !empty($instance['title']) ? $instance['title'] : '';
        $competition = !empty($instance['competition']) ? $instance['competition'] : '';
        $score1 = isset($instance['score1']) ? $instance['score1'] : '-';
        $score2 = isset($instance['score2']) ? $instance['score2'] : '-';
        $footer_text = !empty($instance['footer_text']) ? $instance['footer_text'] : '';
        $is_live = !empty($instance['is_live']) ? true : false;

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

            <table class="scoreboard-table">
                <thead>
                    <tr>
                        <th>
                            <?php esc_html_e('Equipo', 'dedeportes-modern'); ?>
                        </th>
                        <th>
                            <?php esc_html_e('Marcador', 'dedeportes-modern'); ?>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <?php echo esc_html($team1); ?>
                        </td>
                        <td>
                            <?php echo esc_html($score1); ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <?php echo esc_html($team2); ?>
                        </td>
                        <td>
                            <?php echo esc_html($score2); ?>
                        </td>
                    </tr>
                </tbody>
            </table>

            <?php if (!empty($footer_text)): ?>
                <div class="scoreboard-footer">
                    <?php if ($is_live): ?>
                        <span class="live-indicator">●</span>
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
        $team1 = !empty($instance['team1']) ? $instance['team1'] : '';
        $score1 = isset($instance['score1']) ? $instance['score1'] : '';
        $team2 = !empty($instance['team2']) ? $instance['team2'] : '';
        $score2 = isset($instance['score2']) ? $instance['score2'] : '';
        $footer_text = !empty($instance['footer_text']) ? $instance['footer_text'] : '';
        $is_live = !empty($instance['is_live']) ? (bool) $instance['is_live'] : false;
        ?>

        <!-- Widget Title -->
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>">
                <?php esc_html_e('Título Widget:', 'dedeportes-modern'); ?>
            </label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>"
                name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text"
                value="<?php echo esc_attr($title); ?>">
            <small>
                <?php esc_html_e('Ej: En vivo', 'dedeportes-modern'); ?>
            </small>
        </p>

        <!-- Competition -->
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('competition')); ?>">
                <?php esc_html_e('Competencia:', 'dedeportes-modern'); ?>
            </label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('competition')); ?>"
                name="<?php echo esc_attr($this->get_field_name('competition')); ?>" type="text"
                value="<?php echo esc_attr($competition); ?>">
        </p>

        <hr>

        <!-- Team 1 -->
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('team1')); ?>">
                <?php esc_html_e('Equipo 1:', 'dedeportes-modern'); ?>
            </label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('team1')); ?>"
                name="<?php echo esc_attr($this->get_field_name('team1')); ?>" type="text"
                value="<?php echo esc_attr($team1); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('score1')); ?>">
                <?php esc_html_e('Marcador 1:', 'dedeportes-modern'); ?>
            </label>
            <input class="tiny-text" id="<?php echo esc_attr($this->get_field_id('score1')); ?>"
                name="<?php echo esc_attr($this->get_field_name('score1')); ?>" type="text"
                value="<?php echo esc_attr($score1); ?>">
        </p>

        <hr>

        <!-- Team 2 -->
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('team2')); ?>">
                <?php esc_html_e('Equipo 2:', 'dedeportes-modern'); ?>
            </label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('team2')); ?>"
                name="<?php echo esc_attr($this->get_field_name('team2')); ?>" type="text"
                value="<?php echo esc_attr($team2); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('score2')); ?>">
                <?php esc_html_e('Marcador 2:', 'dedeportes-modern'); ?>
            </label>
            <input class="tiny-text" id="<?php echo esc_attr($this->get_field_id('score2')); ?>"
                name="<?php echo esc_attr($this->get_field_name('score2')); ?>" type="text"
                value="<?php echo esc_attr($score2); ?>">
        </p>

        <hr>

        <!-- Footer Info -->
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('footer_text')); ?>">
                <?php esc_html_e('Texto Pie:', 'dedeportes-modern'); ?>
            </label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('footer_text')); ?>"
                name="<?php echo esc_attr($this->get_field_name('footer_text')); ?>" type="text"
                value="<?php echo esc_attr($footer_text); ?>">
            <small>
                <?php esc_html_e('Ej: Primer Tiempo, Finalizado', 'dedeportes-modern'); ?>
            </small>
        </p>

        <!-- Is Live Checkbox -->
        <p>
            <input class="checkbox" type="checkbox" <?php checked($is_live); ?> id="
    <?php echo esc_attr($this->get_field_id('is_live')); ?>" name="
    <?php echo esc_attr($this->get_field_name('is_live')); ?>" />
            <label for="<?php echo esc_attr($this->get_field_id('is_live')); ?>">
                <?php esc_html_e('Mostrar indicador "En Vivo" (Punto Rojo)', 'dedeportes-modern'); ?>
            </label>
        </p>

        <p class="description">
            <strong>Nota:</strong> Si dejas los nombres de los equipos vacíos, este widget no se mostrará en el sitio.
        </p>

        <?php
    }

    /**
     * Sanitize widget form values as they are saved.
     *
     * @see WP_Widget::update()
     *
     * @param array $new_instance Values just sent to be saved.
     * @param array $old_instance Previously saved values from database.
     *
     * @return array Updated safe values to be saved.
     */
    public function update($new_instance, $old_instance)
    {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title'])) ? sanitize_text_field($new_instance['title']) : '';
        $instance['competition'] = (!empty($new_instance['competition'])) ? sanitize_text_field($new_instance['competition']) : '';
        $instance['team1'] = (!empty($new_instance['team1'])) ? sanitize_text_field($new_instance['team1']) : '';
        $instance['score1'] = (isset($new_instance['score1'])) ? sanitize_text_field($new_instance['score1']) : '';
        $instance['team2'] = (!empty($new_instance['team2'])) ? sanitize_text_field($new_instance['team2']) : '';
        $instance['score2'] = (isset($new_instance['score2'])) ? sanitize_text_field($new_instance['score2']) : '';
        $instance['footer_text'] = (!empty($new_instance['footer_text'])) ? sanitize_text_field($new_instance['footer_text']) : '';
        $instance['is_live'] = (isset($new_instance['is_live'])) ? (bool) $new_instance['is_live'] : false;

        return $instance;
    }

} // class Dedeportes_Scoreboard_Widget
