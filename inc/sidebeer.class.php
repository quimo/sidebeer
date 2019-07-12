<?php

class Sidebeer extends WpbSettings {

    public function __construct() {
        parent::__construct();
        add_action('widgets_init', function() {
            $sidebeer = get_option('sidebeer');
            /* registro le sidebar */
            for ($i = 0; $i < count($sidebeer); $i++) {
                register_sidebar(array(
                    'name'          => 'Sidebeer | ' . $sidebeer[$i],
                    'id'            => 'sidebeer_' . $sidebeer[$i],
                    'before_widget' => '<div>',
                    'after_widget'  => '</div>',
                ));
            }
        });
        add_shortcode('sidebeer', array($this, 'render'));
    }
    
    /**
     * Salvataggio impostazioni
     */
    public function add_settings() {
        $sidebeer = array(
            'demo',
        );
        add_option('sidebeer', $sidebeer);
    }

    /**
     * Rimozione impostazioni
     */
    public function remove_settings() {
        /* rimuovo le sidebar dal database */
        $sidebars = get_option('sidebars_widgets');
        foreach ($sidebars as $key => $value) {
            if (strpos($key, 'sidebeer_') !== false) {
                unset($sidebars[$key]);
                unregister_sidebar($key);
            }
        }
        update_option('sidebars_widgets');
        delete_option('sidebeer');
    }

    /**
     * Aggiunta della pagina delle impostazioni
     */
    public function add_settings_page() {
        add_options_page(
            'Sidebeer',
            'Sidebeer',
            'manage_options',
            'sidebeer',
            array($this,'render_settings_page')
        );
    }

    /**
     * Render della pagina delle impostazioni
     */
    public function render_settings_page() {
        if (!current_user_can('manage_options')) wp_die('Non possiedi i permessi per accedere a questa pagina');
        ?>
        <div class="wrap">
            <h2>Sidebeer</h2>
            <?php
            if (isset($_POST['submit']) && wp_verify_nonce($_POST['modify_settings_nonce'], 'modify_settings')) {
                update_option ('sidebeer', $_POST['sidebeer']);        
            }
            ?>
            <form method="post">
                <?php
                $sidebeer = get_option('sidebeer');
                ?><div class="sidebeers__admin"><?php
                    for ($i = 0; $i < count($sidebeer); $i++) {
                        ?>
                        <div class="sidebeer__input">
                            <input placeholder="Sidebar slug" type="text" name="sidebeer[]" value="<?php echo $sidebeer[$i] ?>" required>
                            <span title="Rimuovi questa sidebar" class="sidebeer__remove">-</span>
                        </div>
                        <?php
                    }
                    ?>
                </div>
                <span title="Aggiungi una sidebar" class="sidebeer__add">+</span>
                <?php wp_nonce_field('modify_settings', 'modify_settings_nonce') ?>
                <?php submit_button(); ?>
            </form>
        </div>
        <script>
            $('.sidebeer__add').on('click', function(e){
                e.preventDefault();
                $('.sidebeers__admin').append('<div class="sidebeer__input"><input placeholder="Sidebar slug" type="text" name="sidebeer[]" required></div>');
            })
            $('.sidebeer__remove').each(function(e){
                $(this).on('click', function(e){
                    e.preventDefault();
                    $(this).parent().remove();
                });
            });
        </script>
        <?php
    }

    function render($sidebar = 'sidebar-1') {
        global $post;
        ob_start();
        /* ritorna il nome del file del template solo se non uso il 'template standard' */
        $slug = get_page_template_slug($post->ID);
        if ($slug) list($filename, $extension) = explode('.', $slug);
        else $filename = '';
        /* se non ho una sidebar con il nome uguale al nome del file di un template (estensione esclusa) */
        if (!dynamic_sidebar($filename)) {
            $custom_post_type = get_post_type($post->ID);
            /* se non ho una sidebar con il nome uguale allo slug di un custom post */
            if (!dynamic_sidebar($custom_post_type)) {
                /* se non ho una sidebar con il nome della pagina */
                if (!dynamic_sidebar('sidebeer_' . $post->post_name)) {
                    if (is_active_sidebar($sidebar)) {
                        dynamic_sidebar($sidebar);
                    }
                }
            }
        }
        return ob_get_clean();
    }

}


