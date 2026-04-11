<?php
/**
 * Dedeportes Performance Widget
 * 
 * Muestra el Top 5 de mejores rendimientos globales basados en la base de datos de fútbol.
 *
 * @package Dedeportes_Modern
 */

class Dedeportes_Performance_Widget extends WP_Widget
{

    /**
     * Register widget with WordPress.
     */
    public function __construct()
    {
        parent::__construct(
            'dedeportes_performance_widget', // Base ID
            esc_html__('Dedeportes: Mejores Rendimientos', 'dedeportes-modern'), // Name
            array('description' => esc_html__('Muestra el Top 5 de equipos con mejor rendimiento porcentual global.', 'dedeportes-modern'), ) // Args
        );
    }

    /**
     * Front-end display of widget.
     */
    public function widget($args, $instance)
    {
        $title = !empty($instance['title']) ? $instance['title'] : esc_html__('Mejores Rendimientos', 'dedeportes-modern');
        $limit = !empty($instance['limit']) ? (int) $instance['limit'] : 5;

        $selected_teams = !empty($instance['teams']) ? (array) $instance['teams'] : array();

        // DB Credentials
        $host = 'localhost';
        $dbname = 'pjdmenag_futbol';
        $user = 'pjdmenag_futbol';
        $pass = 'n[[cY^7gvog~';

        $standings = [];

        try {
            $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Base SQL
            $sql = "SELECT 
                        equipo AS Equipo,
                        COUNT(*) AS PJ,
                        SUM(CASE WHEN goles_equipo > goles_rival THEN 3 WHEN goles_equipo = goles_rival THEN 1 ELSE 0 END) AS Pts,
                        ROUND((SUM(CASE WHEN goles_equipo > goles_rival THEN 3 WHEN goles_equipo = goles_rival THEN 1 ELSE 0 END) / (COUNT(*) * 3)) * 100, 1) AS Rendimiento
                    FROM partidos
                    WHERE estado = 'finalizado'";

            // Filter by the 16 target teams as requested for this deployment
            $target_teams = [
                'Colo Colo',
                'Deportes Limache',
                'Universidad Católica',
                'Universidad de Chile',
                'Ñublense',
                "O'Higgins",
                'Huachipato',
                'Coquimbo Unido',
                'Universidad de Concepción',
                'Audax Italiano',
                'Unión La Calera',
                'Deportes La Serena',
                'Everton',
                'Palestino',
                'Cobresal',
                'Deportes Concepción'
            ];

            $placeholders = str_repeat('?,', count($target_teams) - 1) . '?';
            $sql .= " AND equipo IN ($placeholders)";

            $sql .= " GROUP BY equipo
                      ORDER BY Rendimiento DESC, Pts DESC
                      LIMIT " . (int) $limit; // PDO limit binding can be tricky with IN clause array merging, concatenating is safe here with (int) cast

            $stmt = $pdo->prepare($sql);

            $stmt->execute($target_teams);

            $standings = $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            // Silently fail or log error if not admin
            if (current_user_can('manage_options')) {
                echo '<!-- Error DB Widget: ' . esc_html($e->getMessage()) . ' -->';
            }
        }

        if (empty($standings))
            return;

        echo $args['before_widget'];
        if (!empty($title)) {
            echo $args['before_title'] . apply_filters('widget_title', $title) . $args['after_title'];
        }
        ?>

        <div class="widget-content">
            <table class="ranking-table" style="width: 100%; font-size: 0.9rem;">
                <thead>
                    <tr>
                        <th style="text-align: left;">Equipo</th>
                        <th style="text-align: center;">PJ</th>
                        <th style="text-align: right;">% Rend</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($standings as $row): ?>
                        <tr>
                            <td style="font-weight: 600;">
                                <?php echo esc_html($row['Equipo']); ?>
                            </td>
                            <td style="text-align: center; opacity: 0.7;">
                                <?php echo $row['PJ']; ?>
                            </td>
                            <td style="text-align: right; font-weight: 700; color: var(--primary);">
                                <?php echo $row['Rendimiento']; ?>%
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <p style="font-size: 0.7rem; margin-top: 1rem; opacity: 0.6; text-align: center;">
                * Calculado sobre total de partidos jugados.
            </p>
        </div>

        <?php
        echo $args['after_widget'];
    }

    /**
     * Back-end widget form.
     */
    public function form($instance)
    {
        $title = !empty($instance['title']) ? $instance['title'] : esc_html__('Mejores Rendimientos', 'dedeportes-modern');
        $limit = !empty($instance['limit']) ? (int) $instance['limit'] : 5;
        $selected_teams = !empty($instance['teams']) ? (array) $instance['teams'] : array();

        // Fetch available teams for the form checkboxes
        $available_teams = [];
        try {
            $pdo = new PDO("mysql:host=localhost;dbname=pjdmenag_futbol;charset=utf8", 'pjdmenag_futbol', 'n[[cY^7gvog~');
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $stmt = $pdo->query("SELECT DISTINCT equipo FROM partidos ORDER BY equipo ASC");
            $available_teams = $stmt->fetchAll(PDO::FETCH_COLUMN);
        } catch (PDOException $e) {
            $available_teams = [];
        }
        ?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>">
                <?php esc_attr_e('Título:', 'dedeportes-modern'); ?>
            </label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>"
                name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text"
                value="<?php echo esc_attr($title); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('limit')); ?>">
                <?php esc_attr_e('Cantidad de equipos (max rows):', 'dedeportes-modern'); ?>
            </label>
            <input class="tiny-text" id="<?php echo esc_attr($this->get_field_id('limit')); ?>"
                name="<?php echo esc_attr($this->get_field_name('limit')); ?>" type="number" step="1" min="1" max="25"
                value="<?php echo esc_attr($limit); ?>" size="3">
        </p>
        <p>
            <strong><?php esc_attr_e('Equipos a filtrar (Si no marcas ninguno, se mostrarán todos los disponibles en la BD):', 'dedeportes-modern'); ?></strong>
            <br />
        <div
            style="max-height: 200px; overflow-y: auto; padding: 5px; border: 1px solid #ddd; background: #fff; margin-top: 5px;">
            <?php if (!empty($available_teams)): ?>
                <?php foreach ($available_teams as $team): ?>
                    <label style="display: block; margin-bottom: 3px;">
                        <input type="checkbox" name="<?php echo esc_attr($this->get_field_name('teams')); ?>[]"
                            value="<?php echo esc_attr($team); ?>" <?php checked(in_array($team, $selected_teams)); ?> />
                        <?php echo esc_html($team); ?>
                    </label>
                <?php endforeach; ?>
            <?php else: ?>
                <em>Error connecting to database to fetch teams.</em>
            <?php endif; ?>
        </div>
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
        $instance['limit'] = (!empty($new_instance['limit'])) ? (int) $new_instance['limit'] : 5;

        // Sanitize the array of teams
        if (!empty($new_instance['teams']) && is_array($new_instance['teams'])) {
            $instance['teams'] = array_map('sanitize_text_field', $new_instance['teams']);
        } else {
            $instance['teams'] = array();
        }

        return $instance;
    }

} // class Dedeportes_Performance_Widget
