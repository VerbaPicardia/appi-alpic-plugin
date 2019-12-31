<?php
class AlpicPlugin {

    public function activation() {
        global $wpdb;

        $table_name = $this->maps_table_name();
        $charset_collate = $wpdb->get_charset_collate();
        $query = "CREATE TABLE $table_name (
          number varchar(10),
          title varchar(100),
          PRIMARY KEY  (number)
        ) $charset_collate;";
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $query );
    }

    private function maps_table_name() {
        global $wpdb;
        return $wpdb->prefix . 'alpic_maps';
    }

    public function deactivation() {

    }

    public function init() {
        add_action( 'admin_menu', array($this, 'add_menu') );
        add_action( 'admin_init', array($this, 'register_settings') );
        add_shortcode( self::SHORTCODE, array($this, 'render_shortcode') );
    }

    const SHORTCODE = 'appi_alpic';

    public function add_menu() {
        add_menu_page(
            "Alpic",
            "Alpic",
            'administrator',
			'appi_alpic',
            array($this, 'menu_page')
            );
    }

    public function menu_page() {
        ?>
<div class="wrap">
<h1>Alpic</h1>

<form method="POST" action="options.php">
    <?php settings_fields( self::SETTINGS_GROUP ); ?>
    <?php do_settings_sections( self::SETTINGS_GROUP ); ?>


    <table class="form-table">
        <tr valign="top">
        <th scope="row">Map base URL</th>
        <td><input type="text" name="<?php echo self::MAP_BASE_URL_SETTING ?>" value="<?php echo esc_attr( $this->map_directory() ); ?>" /></td>
        </tr>
    </table>

    <?php submit_button(); ?>

</form>
</div>
        <?php
    }

    const SETTINGS_GROUP = 'appi-alpic-settings-group';

    private function map_directory() {
        return get_option(self::MAP_BASE_URL_SETTING);
    }

    const MAP_BASE_URL_SETTING = 'alpic_map_base_url';

    public function register_settings() {
        register_setting(self::SETTINGS_GROUP, self::MAP_BASE_URL_SETTING);
    }

    public function render_shortcode() {
        $maps_table_name = $this->maps_table_name();

        global $wpdb;
 ?>


<?php
  $requete = $_POST['requete'];

if (htmlspecialchars(isset($_POST['requete']))) {

        $query = "SELECT * FROM $maps_table_name
		WHERE title LIKE '%$requete%'
		OR number LIKE '%$requete%'
		ORDER BY number + 0, number ASC";
        $maps = $wpdb->get_results($query);

        $lines = array("<table>");
        $lines = array_merge($lines, $this->header_row(array(
            "Notion",
            "N° Carte",
            "Lien"
        )));
        foreach($maps as $map) {
            $lines = array_merge($lines, $this->row(array(
                $map->title,
                $map->number,
                $this->map_link($map)
            )));
        }
        $lines[] = '</table>';

        return join('', $lines);


	}
	else {
        $query = "SELECT * FROM $maps_table_name ORDER BY number + 0, number ASC";
        $maps = $wpdb->get_results($query);

        $lines = array("<table>");
        $lines = array_merge($lines, $this->header_row(array(
            "Notion",
            "N° Carte",
            "Lien"
        )));
        foreach($maps as $map) {
            $lines = array_merge($lines, $this->row(array(
                $map->title,
                $map->number,
                $this->map_link($map)
            )));
        }
        $lines[] = '</table>';

        return join('', $lines);
    }
	}

    private function header_row($columns) {
        $lines = array("<tr>");
        foreach($columns as $column) {
            $lines[] = '<th>';
            $lines[] = $column;
            $lines[] = '</th>';
        }
        $lines[] = '</tr>';
        return $lines;
    }

    private function row($columns) {
        $lines = array("<tr>");
        foreach($columns as $column) {
            $lines[] = '<td>';
            $lines[] = $column;
            $lines[] = '</td>';
        }
        $lines[] = '</tr>';
        return $lines;
    }

    private function map_link($map) {
        return '<a target="_blank" href="' . $this->map_directory() . '/ALPic%23' . $map->number . '.pdf">Télécharger</a>';
    }
}
?>