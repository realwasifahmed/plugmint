<?php

if (!function_exists('view')) {
    /**
     * Render a view from includes/views with optional data.
     *
     * @param string $view     The view file name (no .php, e.g. 'admin-page').
     * @param array  $data     Associative array of variables for the view.
     */
    function view($view, $data = [])
    {
        $viewPath = __DIR__ . '/../views/' . $view . '.php';
        if (!file_exists($viewPath)) {
            echo "<div style='color: red;'>View not found: $viewPath</div>";
            return;
        }

        // Extract variables for use in the view
        extract($data, EXTR_SKIP);

        // Include the view file (scoped)
        include $viewPath;
    }
}


if (! function_exists('asset')) {
    /**
     * Generate a plugin asset URL.
     *
     * Builds a fully qualified URL for assets stored under the plugin’s
     * `includes/assets/` directory, and appends a version query-string if provided.
     *
     * @param string      $path    Relative path to the asset file (e.g. 'css/admin.css').
     * @param string|bool $version Optional. Version string or false to omit. Default null.
     * 
     * @return string Escaped URL to the requested asset, with optional `?ver=` parameter.
     */
    function asset($path = '', $version = null)
    {
        $plugin_url = plugin_dir_url(__DIR__);
        $url        = trailingslashit($plugin_url . 'includes/Assets') . ltrim($path, '/');

        if ($version) {
            $url = add_query_arg('ver', $version, $url);
        }

        return esc_url($url);
    }
}
