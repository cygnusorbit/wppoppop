<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Module lifecycle: install / uninstall / delete / upload (module.zip).
 *   Install   = state "installed" (module is active in the builder and on the frontend).
 *   Uninstall = state "available" (files and popup data are kept, one click to reinstall).
 *   Delete    = uploaded module: files removed. Built-in module: state "deleted" (hidden until restored from Extend).
 * Locked modules can never be uninstalled or deleted. Every method returns an array or a WP_Error.
 */
class WpPopPop_Modules_Installer {

    const MAX_ZIP      = 5242880;
    const MAX_FILES    = 200;
    const MAX_FILE     = 2097152;
    const MAX_UNPACKED = 10485760;
    const MAX_DEPTH    = 4;
    const SEED_OPT     = 'wppoppop_modules_seeded';
    const RENAMED      = ['acc-20-animation' => 'acc-animation', 'acc-16-popup-overlay' => 'acc-popup-overlay', 'acc-17-popup-overlay' => 'acc-popup-overlay', 'acc-21-popup-positions' => 'acc-popup-positions', 'acc-18-notifications' => 'acc-notifications', 'acc-17-double-optin' => 'acc-double-optin', 'acc-22-targeting-triggers' => 'acc-targeting-triggers', 'acc-19-success-celebration' => 'acc-success-celebration', 'acc-08-autoresponder' => 'acc-autoresponder', 'acc-04-side-tabs' => 'acc-side-tabs', 'acc-09-webhooks' => 'acc-webhooks', 'acc-13-woocommerce' => 'acc-woocommerce', 'acc-06-downloads' => 'acc-downloads', 'acc-03-logic-math' => 'logic-math', 'acc-05-payments' => 'logic-payments', 'acc-07-video' => 'acc-video', 'acc-10-sms' => 'acc-sms', 'acc-11-targeting' => 'acc-targeting', 'acc-15-quiz' => 'acc-quiz', 'acc-12-frequency' => 'acc-frequency', 'acc-14-custom-code' => 'acc-custom-code'];
    const ALLOWED_EXT  = ['json', 'css', 'js', 'html', 'txt', 'md', 'png', 'jpg', 'jpeg', 'gif', 'webp', 'woff', 'woff2'];
    const BAD_SEGMENT  = '/^(php\d*|phtml|pht|phps|phar|cgi|pl|py|rb|sh|bash|asp|aspx|jsp|exe|dll|so|htaccess|htpasswd)$/i';

    protected static function err($code, $message, $data = null) {
        return new WP_Error($code, $message, $data);
    }

    /* ------------------------------------------------------ capabilities */

    public static function file_mods_allowed() {
        return !(defined('DISALLOW_FILE_MODS') && DISALLOW_FILE_MODS);
    }

    public static function can_manage() {
        return current_user_can('manage_options');
    }

    /** Uploading or physically deleting module files: single-site admins and network super admins only. */
    public static function can_upload() {
        return self::can_manage() && self::file_mods_allowed() && (!is_multisite() || is_super_admin());
    }

    /* ------------------------------------------------------ summaries */

    public static function summary($m) {
        $slug = $m['slug'];
        $by   = array_keys(WpPopPop_Modules::required_by($slug));
        return [
            'slug'        => $slug,
            'name'        => ($m['source'] === 'core') ? translate($m['name'], 'wppoppop') : $m['name'],
            'version'     => $m['version'],
            'author'      => $m['author'],
            'description' => ($m['source'] === 'core') ? translate($m['description'], 'wppoppop') : $m['description'],
            'source'      => $m['source'],
            'state'       => WpPopPop_Modules::state($slug),
            'locked'      => (bool) $m['locked'],
            'can_edit'    => !isset($m['allow_edit']) || !empty($m['allow_edit']),
            'can_delete'  => !isset($m['allow_delete']) || !empty($m['allow_delete']),
            'can_manage'  => !isset($m['allow_manage']) || !empty($m['allow_manage']),
            'can_uninstall' => !isset($m['allow_uninstall']) || !empty($m['allow_uninstall']),
            'show_source' => !isset($m['show_source_pill']) || !empty($m['show_source_pill']),
            'show_state'  => !isset($m['show_state_pill']) || !empty($m['show_state_pill']),
            'pills'       => isset($m['pills']) ? array_values((array) $m['pills']) : [],
            'deprecated'  => (bool) $m['deprecated'],
            'compatible'  => (bool) $m['compatible'],
            'requires'    => array_values($m['requires']),
            'required_by' => $by,
            'category'    => WpPopPop_Module_Categories::of($m),
            'buttons'     => self::actions_out(isset($m['buttons']) ? $m['buttons'] : [], $m),
            'links'       => self::actions_out(isset($m['links']) ? $m['links'] : [], $m),
            'image'       => WpPopPop_Module_Categories::image_of($m),
            'image_raw'   => WpPopPop_Module_Categories::image_raw($m),
            'image_base'  => WpPopPop_Modules::module_url($m),
        ];
    }

    /** Turn manifest buttons/links into final hrefs: absolute and /site paths as-is, page.php?x=1 -> admin URL, anything else -> module folder file. */
    protected static function actions_out($list, $m) {
        $out = [];
        foreach ((array) $list as $b) {
            $u = $b['url'];
            if (preg_match('#^(?:https?:|mailto:|tel:|/)#i', $u) !== 1) {
                if (preg_match('#^[a-z0-9_-]+\.php(?:\?|$)#i', $u) === 1) {
                    $u = admin_url($u);
                } else {
                    $base = WpPopPop_Modules::module_url($m);
                    $u    = $base === '' ? '' : $base . $u;
                }
            }
            if ($u !== '') {
                $b['url'] = $u;
                $out[]    = $b;
            }
        }
        return $out;
    }

    protected static function names($slugs) {
        $out = [];
        foreach ($slugs as $s) {
            $m     = WpPopPop_Modules::get($s);
            $out[] = $m ? $m['name'] : $s;
        }
        return implode(', ', $out);
    }

    /* ------------------------------------------------------ popup data */

    protected static function each_popup($cb) {
        global $wpdb;
        $table = $wpdb->prefix . 'wppoppop_items';
        $last  = 0;
        while (true) {
            $rows = $wpdb->get_results($wpdb->prepare("SELECT id, uid, title, data FROM {$table} WHERE id > %d ORDER BY id ASC LIMIT 200", $last), ARRAY_A);
            if (!$rows) {
                break;
            }
            foreach ($rows as $row) {
                $last = (int) $row['id'];
                $cb($row);
            }
        }
    }

    /** True when a scalar is truthy or an array holds at least one truthy leaf. */
    protected static function any_truthy($v) {
        if (is_array($v)) {
            foreach ($v as $x) {
                if (self::any_truthy($x)) {
                    return true;
                }
            }
            return false;
        }
        return WpPopPop_Modules::truthy($v);
    }

    /** How many popups really use the module (in_use paths, else any stored data key). */
    public static function usage($slug) {
        $m = WpPopPop_Modules::get($slug);
        if (!$m) {
            return ['count' => 0, 'titles' => []];
        }
        $paths = $m['in_use'] ? $m['in_use'] : $m['data_keys'];
        if (!$paths) {
            return ['count' => 0, 'titles' => []];
        }
        $count  = 0;
        $titles = [];
        self::each_popup(function ($row) use ($paths, &$count, &$titles) {
            $d = json_decode((string) $row['data'], true);
            $s = (is_array($d) && isset($d['settings']) && is_array($d['settings'])) ? $d['settings'] : [];
            foreach ($paths as $p) {
                if (self::any_truthy(WpPopPop_Modules::path_value($s, $p))) {
                    $count++;
                    if (count($titles) < 5) {
                        $titles[] = (string) $row['title'];
                    }
                    break;
                }
            }
        });
        return ['count' => $count, 'titles' => $titles];
    }

    /** Settings keys owned only by this module. */
    protected static function owned_keys($m) {
        $others = [];
        foreach (WpPopPop_Modules::all() as $slug => $o) {
            if ($slug !== $m['slug']) {
                foreach ($o['data_keys'] as $k) {
                    $others[$k] = true;
                }
            }
        }
        return array_values(array_filter($m['data_keys'], function ($k) use ($others) {
            return !isset($others[$k]);
        }));
    }

    /** "Remove data": strip the module's keys from every popup and drop its stored toggle / order slot. */
    public static function purge_data($slug) {
        $m = WpPopPop_Modules::get($slug);
        if (!$m || $m['locked']) {
            return 0;
        }
        global $wpdb;
        $table   = $wpdb->prefix . 'wppoppop_items';
        $keys    = self::owned_keys($m);
        $changed = 0;
        if ($keys) {
            self::each_popup(function ($row) use ($keys, $wpdb, $table, &$changed) {
                $o = json_decode((string) $row['data']);
                if (!is_object($o) || !isset($o->settings) || !is_object($o->settings)) {
                    return;
                }
                $hit = false;
                foreach ($keys as $k) {
                    if (property_exists($o->settings, $k)) {
                        unset($o->settings->$k);
                        $hit = true;
                    }
                }
                if ($hit) {
                    $enc = wp_json_encode($o);
                    if (is_string($enc) && $enc !== '') {
                        $wpdb->update($table, ['data' => $enc], ['id' => (int) $row['id']], ['%s'], ['%d']);
                        $changed++;
                    }
                }
            });
        }
        $opt = get_option('wppoppop_settings', []);
        if (is_array($opt)) {
            $dirty = false;
            if (isset($opt['modules']) && is_array($opt['modules']) && array_key_exists($slug, $opt['modules'])) {
                unset($opt['modules'][$slug]);
                $dirty = true;
            }
            if (isset($opt['module_order']) && is_array($opt['module_order']) && in_array($slug, $opt['module_order'], true)) {
                $opt['module_order'] = array_values(array_diff($opt['module_order'], [$slug]));
                $dirty = true;
            }
            if ($dirty) {
                update_option('wppoppop_settings', $opt);
            }
        }
        return $changed;
    }

    /* ------------------------------------------------------ install / uninstall / delete */

    /**
     * Modules listed on the Extend page: every valid folder with a module.json, i.e. everything in /modules
     * (built-in or custom) and every upload, in any state.
     */
    public static function extend_items() {
        $rank = ['available' => 0, 'deleted' => 1, 'installed' => 2];
        $out  = [];
        foreach (WpPopPop_Modules::all() as $m) {
            $out[] = self::summary($m);
        }
        usort($out, function ($a, $b) use ($rank) {
            $ra = $a['locked'] ? 3 : (isset($rank[$a['state']]) ? $rank[$a['state']] : 2);
            $rb = $b['locked'] ? 3 : (isset($rank[$b['state']]) ? $rank[$b['state']] : 2);
            return $ra === $rb ? strcasecmp($a['name'], $b['name']) : $ra - $rb;
        });
        return $out;
    }

    /** Data shared by the Module tab and the Extend page scripts. */
    public static function js_vars($extra = []) {
        $vars = [
            'ajax_url'   => admin_url('admin-ajax.php'),
            'nonce'      => wp_create_nonce('wppoppop_admin_nonce'),
            'extend_url' => admin_url('admin.php?page=wppoppop-extend'),
            'modules_url' => admin_url('admin.php?page=wppoppop-settings#module'),
            'can_upload' => self::can_upload() && class_exists('ZipArchive'),
            'max_zip'    => self::MAX_ZIP,
            'categories'   => WpPopPop_Module_Categories::all(),
            'cat_max'      => WpPopPop_Module_Categories::MAX,
            'cat_name_max' => WpPopPop_Module_Categories::NAME_MAX,
            'i18n'       => [
                'cancel'          => __('Cancel', 'wppoppop'),
                'install'         => __('Install', 'wppoppop'),
                'restore'         => __('Reinstall', 'wppoppop'),
                'uninstall'       => __('Uninstall', 'wppoppop'),
                'delete'          => __('Delete', 'wppoppop'),
                'manage'          => __('Manage', 'wppoppop'),
                'working'         => __('Working…', 'wppoppop'),
                'uploading'       => __('Uploading…', 'wppoppop'),
                'upload'          => __('Upload Module', 'wppoppop'),
                'error_generic'   => __('Something went wrong. Please try again.', 'wppoppop'),
                'uninstall_title' => __('Uninstall module', 'wppoppop'),
                /* translators: %s: module name */
                'uninstall_msg'   => __('Uninstall "%s"? It stops working in the builder and on the frontend. Its files and saved popup data are kept, and you can reinstall it from Extend.', 'wppoppop'),
                'delete_title'    => __('Delete module', 'wppoppop'),
                /* translators: %s: module name */
                'delete_msg_uploaded' => __('Delete "%s"? Its files are removed from the uploads folder. This cannot be undone.', 'wppoppop'),
                /* translators: %s: module name */
                'delete_msg_core' => __('Delete "%s"? It is removed from your modules and can be restored from Extend.', 'wppoppop'),
                'remove_data'     => __('Remove data: also erase this module\'s saved data from all popups', 'wppoppop'),
                'in_use_title'    => __('Module is in use', 'wppoppop'),
                'in_use_msg'      => __('Popups that use it will lose this feature until it is installed again. Continue?', 'wppoppop'),
                'continue_anyway' => __('Continue', 'wppoppop'),
                /* translators: 1: module name, 2: installed version, 3: new version */
                'replace_msg'     => __('"%s" is already installed (version %s). Replace it with version %s? Its installed state and popup data are kept.', 'wppoppop'),
                'replace'         => __('Replace', 'wppoppop'),
                'replace_title'   => __('Replace module', 'wppoppop'),
                'cmp_current'     => __('Current module', 'wppoppop'),
                'cmp_new'         => __('Uploaded module', 'wppoppop'),
                'cmp_msg'         => __('The uploaded module has the same slug and name as the module in the uploads folder. Replace it with the uploaded version? Its installed state and popup data are kept.', 'wppoppop'),
                'cmp_msg_core'    => __('The uploaded module has the same slug and name as a module in /modules. Replace it? The older copies in /modules and in the uploads folder are removed, the uploaded one takes its place, and its installed state and popup data are kept.', 'wppoppop'),
                'pick_zip'        => __('Choose a module .zip file first.', 'wppoppop'),
                'not_zip'         => __('Only a .zip file can be uploaded.', 'wppoppop'),
                /* translators: %s: maximum size */
                'too_big'         => __('The zip is larger than the %s limit.', 'wppoppop'),
                'installed_ok'    => __('Module installed.', 'wppoppop'),
                'manage_link'     => __('Manage in Settings', 'wppoppop'),
                'extend_link'     => __('Open Extend', 'wppoppop'),
                'empty'           => __('No modules match.', 'wppoppop'),
                'builtin'         => __('Built-in', 'wppoppop'),
                'uploaded'        => __('Uploaded', 'wppoppop'),
                'locked'          => __('Locked', 'wppoppop'),
                'deprecated'      => __('Deprecated', 'wppoppop'),
                'st_available'    => __('Available', 'wppoppop'),
                'st_installed'    => __('Installed', 'wppoppop'),
                'st_deleted'      => __('Deleted', 'wppoppop'),
                /* translators: %s: plugin version */
                'needs_version'   => __('Needs a newer WpPopPop version.', 'wppoppop'),
                /* translators: %s: version number */
                'version'         => __('Version %s', 'wppoppop'),
                /* translators: %s: author name */
                'by'              => __('By %s', 'wppoppop'),
                /* translators: %s: module names */
                'requires'        => __('Requires: %s', 'wppoppop'),
                'no_match'        => __('No modules match your search.', 'wppoppop'),
                'cat_label'       => __('Category', 'wppoppop'),
                /* translators: %s: module name */
                'cat_for'         => __('Category for %s', 'wppoppop'),
                'cat_manage'      => __('Manage categories', 'wppoppop'),
                'cat_name'        => __('Category name', 'wppoppop'),
                'cat_default'     => __('Default', 'wppoppop'),
                'cat_up'          => __('Move up', 'wppoppop'),
                'cat_down'        => __('Move down', 'wppoppop'),
                'cat_remove'      => __('Delete category', 'wppoppop'),
                'cat_empty'       => __('Enter a category name.', 'wppoppop'),
                /* translators: %s: category name */
                'cat_dup'         => __('"%s" is already used by another category.', 'wppoppop'),
                /* translators: %s: maximum number of categories */
                'cat_max_msg'     => __('You can have up to %s categories.', 'wppoppop'),
                'cat_save'        => __('Save Categories', 'wppoppop'),
                'cat_saving'      => __('Saving…', 'wppoppop'),
                'cat_saved'       => __('Categories saved.', 'wppoppop'),
                'edit'            => __('Edit', 'wppoppop'),
                /* translators: %s: module name */
                'edit_title'      => __('Edit %s', 'wppoppop'),
                'edit_note'       => __('The category and image URL are saved to this module\'s module.json.', 'wppoppop'),
                'edit_image'      => __('Image URL', 'wppoppop'),
                'edit_img_bad'    => __('Enter a valid image URL or path.', 'wppoppop'),
                'edit_save'       => __('Save', 'wppoppop'),
                'edit_saving'     => __('Saving…', 'wppoppop'),
            ],
        ];
        return array_merge($vars, $extra);
    }

    public static function install($slug) {
        $m = WpPopPop_Modules::get($slug);
        if (!$m) {
            return self::err('not_found', __('Module not found.', 'wppoppop'));
        }
        if (!$m['compatible']) {
            /* translators: %s: minimum plugin version */
            return self::err('incompatible', sprintf(__('This module needs WpPopPop %s or newer.', 'wppoppop'), $m['min_version']));
        }
        $missing = [];
        foreach ($m['requires'] as $r) {
            if (!WpPopPop_Modules::is_installed($r)) {
                $missing[] = $r;
            }
        }
        if ($missing) {
            /* translators: %s: module names */
            return self::err('requires', sprintf(__('Install the required modules first: %s', 'wppoppop'), self::names($missing)), ['requires' => $missing]);
        }
        if (!$m['locked']) {
            WpPopPop_Modules::set_state($slug, 'installed');
        }
        do_action('wppoppop_module_installed', $slug);
        return ['module' => self::summary(WpPopPop_Modules::get($slug)), 'message' => __('Module installed.', 'wppoppop')];
    }

    /** Shared checks for uninstall and delete. Returns the manifest or a WP_Error. */
    protected static function guard_removal($slug, $force) {
        $m = WpPopPop_Modules::get($slug);
        if (!$m) {
            return self::err('not_found', __('Module not found.', 'wppoppop'));
        }
        if ($m['locked']) {
            return self::err('locked', __('Locked modules cannot be uninstalled or deleted.', 'wppoppop'));
        }
        $by = array_keys(WpPopPop_Modules::required_by($slug));
        if ($by) {
            /* translators: %s: module names */
            return self::err('required_by', sprintf(__('Required by: %s. Uninstall those first.', 'wppoppop'), self::names($by)), ['required_by' => $by]);
        }
        if (!$force) {
            $u = self::usage($slug);
            if ($u['count'] > 0) {
                /* translators: %d: number of popups */
                return self::err('in_use', sprintf(_n('%d popup uses this module.', '%d popups use this module.', $u['count'], 'wppoppop'), $u['count']), $u);
            }
        }
        return $m;
    }

    public static function uninstall($slug, $force = false, $remove_data = false) {
        $m = self::guard_removal($slug, $force);
        if (is_wp_error($m)) {
            return $m;
        }
        if (isset($m['allow_uninstall']) && !$m['allow_uninstall']) {
            return self::err('no_uninstall', __('Uninstalling is disabled for this module in its module.json.', 'wppoppop'));
        }
        if (WpPopPop_Modules::state($slug) !== 'installed') {
            return self::err('not_installed', __('This module is not installed.', 'wppoppop'));
        }
        WpPopPop_Modules::set_state($slug, 'available');
        if ($remove_data) {
            self::purge_data($slug);
        }
        do_action('wppoppop_module_uninstalled', $slug, (bool) $remove_data);
        return ['module' => self::summary(WpPopPop_Modules::get($slug)), 'message' => __('Module uninstalled.', 'wppoppop')];
    }

    public static function delete($slug, $force = false, $remove_data = false) {
        $m = self::guard_removal($slug, $force);
        if (is_wp_error($m)) {
            return $m;
        }
        if (isset($m['allow_delete']) && !$m['allow_delete']) {
            return self::err('no_delete', __('Deleting is disabled for this module in its module.json.', 'wppoppop'));
        }
        if ($m['source'] === 'uploaded' && !self::can_upload()) {
            return self::err('forbidden', __('You are not allowed to delete module files on this site.', 'wppoppop'));
        }
        if (WpPopPop_Modules::state($slug) === 'deleted') {
            return self::err('deleted', __('This module is already deleted.', 'wppoppop'));
        }
        if ($remove_data) {
            self::purge_data($slug);
        }
        if ($m['source'] === 'uploaded') {
            $dir  = $m['dir'];
            $real = realpath($dir);
            $root = realpath(WpPopPop_Modules::uploads_dir());
            if (!$real || !$root || is_link($dir) || $real === $root || strpos($real . '/', rtrim($root, '/') . '/') !== 0) {
                return self::err('unsafe_path', __('Module folder is outside the modules directory.', 'wppoppop'));
            }
            self::rrmdir($dir);
            if (is_dir($dir)) {
                return self::err('delete_failed', __('Could not remove the module files. Check folder permissions.', 'wppoppop'));
            }
            WpPopPop_Modules::forget_state($slug);
            if ($remove_data || !$m['data_keys']) {
                WpPopPop_Modules::clear_orphan($slug);
            } else {
                WpPopPop_Modules::set_orphan($slug, $m['data_keys']);
            }
        } else {
            WpPopPop_Modules::set_state($slug, 'deleted');
        }
        do_action('wppoppop_module_deleted', $slug, (bool) $remove_data);
        return ['slug' => $slug, 'message' => __('Module deleted.', 'wppoppop')];
    }

    /* ------------------------------------------------------ bundled packages */

    protected static function copy_tree($src, $dst, $depth = 0) {
        if ($depth > self::MAX_DEPTH || (!@mkdir($dst, 0755, true) && !is_dir($dst))) {
            return false;
        }
        $items = scandir($src);
        if (!is_array($items)) {
            return false;
        }
        foreach ($items as $i) {
            if ($i === '.' || $i === '..') {
                continue;
            }
            $s = $src . '/' . $i;
            $d = $dst . '/' . $i;
            if (is_link($s)) {
                continue;
            }
            if (is_dir($s)) {
                if (!self::copy_tree($s, $d, $depth + 1)) {
                    return false;
                }
                continue;
            }
            if (!in_array(strtolower(pathinfo($i, PATHINFO_EXTENSION)), self::ALLOWED_EXT, true)) {
                continue;
            }
            if (!@copy($s, $d)) {
                return false;
            }
        }
        if ($depth === 0) {
            @file_put_contents($dst . '/index.php', "<?php\n// Silence is golden.\n");
        }
        return true;
    }

    protected static function manifest_version($dir) {
        $f = $dir . '/module.json';
        if (!is_file($f) || is_link($f) || filesize($f) > WpPopPop_Modules::MAX_JSON) {
            return '';
        }
        $j = json_decode((string) file_get_contents($f), true);
        return (is_array($j) && isset($j['version']) && is_string($j['version'])) ? $j['version'] : '';
    }

    /**
     * Refreshes an already seeded module when the plugin ships a newer version of its package.
     * State and saved data are untouched; a module the user deleted is never recreated.
     * Returns the version to remember, or false when nothing was done.
     */
    protected static function upgrade_bundled($slug, $dir, $seeded) {
        $new = self::manifest_version($dir);
        $was = (is_string($seeded) && $seeded !== '') ? $seeded : '1.0.0';
        if ($new === '' || version_compare($new, $was, '<=')) {
            return false;
        }
        $base = WpPopPop_Modules::uploads_dir();
        $dest = $base . '/' . $slug;
        if ($base === '' || is_link($dest) || !is_dir($dest)) {
            return false;
        }
        $cur = self::manifest_version($dest);
        if ($cur !== '' && version_compare($cur, $new, '>=')) {
            return $new;
        }
        if (!self::prepare_root($base)) {
            return false;
        }
        $tmp = $base . '/' . self::rand_name('.tmp-');
        if (!self::copy_tree($dir, $tmp)) {
            self::rrmdir($tmp);
            return false;
        }
        $old = $base . '/' . self::rand_name('.old-');
        if (!@rename($dest, $old)) {
            self::rrmdir($tmp);
            return false;
        }
        if (!@rename($tmp, $dest)) {
            @rename($old, $dest);
            self::rrmdir($tmp);
            return false;
        }
        self::rrmdir($old);
        WpPopPop_Modules::flush();
        return $new;
    }

    /** Installed/uninstalled/deleted state follows a renamed slug (runs before seeding, so nothing is reinstalled by mistake). */
    protected static function migrate_renamed_state() {
        $st    = WpPopPop_Modules::states();
        $dirty = false;
        foreach (self::RENAMED as $old => $new) {
            if (!array_key_exists($old, $st)) {
                continue;
            }
            if (!array_key_exists($new, $st)) {
                $st[$new] = $st[$old];
            }
            unset($st[$old]);
            $dirty = true;
        }
        if ($dirty) {
            update_option(WpPopPop_Modules::STATE_OPT, $st, false);
            WpPopPop_Modules::flush();
        }
    }

    /** The on/off switch and the position in Popup Settings follow a renamed slug (runs once the new module is registered). */
    protected static function migrate_renamed_settings() {
        $s = get_option('wppoppop_settings', []);
        if (!is_array($s)) {
            return;
        }
        $dirty = false;
        foreach (self::RENAMED as $old => $new) {
            if (!WpPopPop_Modules::get($new)) {
                continue;
            }
            if (isset($s['modules']) && is_array($s['modules']) && array_key_exists($old, $s['modules'])) {
                if (!array_key_exists($new, $s['modules'])) {
                    $s['modules'][$new] = $s['modules'][$old];
                }
                unset($s['modules'][$old]);
                $dirty = true;
            }
            if (isset($s['module_order']) && is_array($s['module_order'])) {
                $i = array_search($old, $s['module_order'], true);
                if ($i !== false) {
                    if (in_array($new, $s['module_order'], true)) {
                        unset($s['module_order'][$i]);
                    } else {
                        $s['module_order'][$i] = $new;
                    }
                    $s['module_order'] = array_values($s['module_order']);
                    $dirty = true;
                }
            }
        }
        if ($dirty) {
            update_option('wppoppop_settings', $s);
        }
    }

    /**
     * One-time copy of the plugin's packages/<slug>/ folders into uploads/wppoppop-modules/, so they show up
     * on the Extend page as normal Uploaded modules. A module removed later is never copied back.
     */
    public static function seed_bundled() {
        if (!current_user_can('manage_options')) {
            return;
        }
        self::migrate_renamed_state();
        $src = WPPOPPOP_PATH . 'packages';
        if (!is_dir($src)) {
            return;
        }
        $dirs = glob($src . '/*', GLOB_ONLYDIR);
        if (!is_array($dirs) || !$dirs) {
            return;
        }
        $done  = get_option(self::SEED_OPT, []);
        $done  = is_array($done) ? $done : [];
        $dirty = false;
        foreach ($dirs as $dir) {
            $slug = basename($dir);
            if (is_link($dir) || !WpPopPop_Modules::slug_valid($slug) || !is_file($dir . '/module.json')) {
                continue;
            }
            if (isset($done[$slug])) {
                $v = self::upgrade_bundled($slug, $dir, $done[$slug]);
                if ($v !== false) {
                    $done[$slug] = $v;
                    $dirty       = true;
                }
                continue;
            }
            if (is_dir(WpPopPop_Modules::core_dir() . '/' . $slug)) {
                $done[$slug] = 1;
                $dirty       = true;
                continue;
            }
            $base = WpPopPop_Modules::uploads_dir();
            if ($base === '' || !self::prepare_root($base)) {
                break;
            }
            $dest = $base . '/' . $slug;
            if (!is_dir($dest) && !self::copy_tree($dir, $dest)) {
                self::rrmdir($dest);
                continue;
            }
            WpPopPop_Modules::flush();
            $m  = WpPopPop_Modules::get($slug);
            $st = WpPopPop_Modules::states();
            if (!isset($st[$slug])) {
                WpPopPop_Modules::set_state($slug, ($m && $m['default_active']) ? 'installed' : 'available');
            } elseif ($st[$slug] === 'deleted') {
                WpPopPop_Modules::set_state($slug, 'available');
            }
            WpPopPop_Modules::clear_orphan($slug);
            $v           = self::manifest_version($dir);
            $done[$slug] = ($v !== '') ? $v : 1;
            $dirty       = true;
        }
        if ($dirty) {
            update_option(self::SEED_OPT, $done, false);
        }
        self::migrate_renamed_settings();
    }

    /* ------------------------------------------------------ zip upload */

    protected static function rrmdir($dir) {
        if (!is_dir($dir) || is_link($dir)) {
            return;
        }
        $items = scandir($dir);
        if (!is_array($items)) {
            return;
        }
        foreach ($items as $i) {
            if ($i === '.' || $i === '..') {
                continue;
            }
            $p = $dir . '/' . $i;
            if (is_dir($p) && !is_link($p)) {
                self::rrmdir($p);
            } else {
                @unlink($p);
            }
        }
        @rmdir($dir);
    }

    /** Manifest (name, version) of an installed upload, or null when it is missing or unreadable. */
    protected static function read_uploaded($dir) {
        $f = $dir . '/module.json';
        if (is_link($dir) || !is_file($f) || is_link($f) || filesize($f) > WpPopPop_Modules::MAX_JSON) {
            return null;
        }
        $raw = json_decode((string) file_get_contents($f), true);
        if (!is_array($raw)) {
            return null;
        }
        $m = WpPopPop_Modules::normalize($raw, 'uploaded', basename($dir));
        return $m ? $m : null;
    }

    /** Put the /modules copy back when the upload could not be moved into place. */
    protected static function undo_core($bak, $path) {
        if ($bak !== '' && is_dir($bak) && !is_dir($path)) {
            @rename($bak, $path);
        }
    }

    /** Uploaded (not yet installed) module as shown in the replace confirmation. Only absolute image URLs can be previewed. */
    protected static function incoming($m) {
        $img = isset($m['image']) ? (string) $m['image'] : '';
        return [
            'slug'        => $m['slug'],
            'name'        => $m['name'],
            'version'     => $m['version'],
            'author'      => $m['author'],
            'description' => $m['description'],
            'source'      => 'uploaded',
            'image'       => preg_match('#^(?:https?:)?//|^/(?!/)#i', $img) === 1 ? $img : '',
        ];
    }

    protected static function rand_name($prefix) {
        return $prefix . bin2hex(function_exists('random_bytes') ? random_bytes(6) : md5(uniqid('', true)));
    }

    /** Create the uploads/wppoppop-modules root with a silence file and a no-PHP rule. */
    protected static function prepare_root($base) {
        if (!is_dir($base) && !@mkdir($base, 0755, true) && !is_dir($base)) {
            return false;
        }
        if (!file_exists($base . '/index.php')) {
            @file_put_contents($base . '/index.php', "<?php\n// Silence is golden.\n");
        }
        if (!file_exists($base . '/.htaccess')) {
            $rule  = "# WpPopPop modules: static assets only.\n";
            $rule .= "<IfModule mod_authz_core.c>\n<FilesMatch \"\\.(php[0-9]*|phtml|pht|phar)$\">\nRequire all denied\n</FilesMatch>\n</IfModule>\n";
            $rule .= "<IfModule !mod_authz_core.c>\n<FilesMatch \"\\.(php[0-9]*|phtml|pht|phar)$\">\nOrder allow,deny\nDeny from all\n</FilesMatch>\n</IfModule>\n";
            @file_put_contents($base . '/.htaccess', $rule);
        }
        return is_dir($base) && is_writable($base);
    }

    protected static function junk($name) {
        $base = strtolower(basename(rtrim($name, '/')));
        return strpos($name, '__MACOSX/') === 0 || $base === '.ds_store' || $base === 'thumbs.db' || strpos($base, '._') === 0;
    }

    /** Normalised entry name, or null when it is unsafe. */
    protected static function clean_name($name) {
        if ($name === '' || strpos($name, "\0") !== false || strpos($name, '\\') !== false || $name[0] === '/' || preg_match('/^[a-zA-Z]:/', $name)) {
            return null;
        }
        $dir = substr($name, -1) === '/';
        foreach (explode('/', rtrim($name, '/')) as $seg) {
            if ($seg === '' || strlen($seg) > 100 || !preg_match('/^[A-Za-z0-9][A-Za-z0-9._-]*$/', $seg)) {
                return null;
            }
        }
        return rtrim($name, '/') . ($dir ? '/' : '');
    }

    protected static function extract($zip, $dest) {
        $n = (int) $zip->numFiles;
        if ($n < 1 || $n > self::MAX_FILES) {
            return self::err('bad_zip', __('The zip is empty or contains too many files.', 'wppoppop'));
        }
        $entries = [];
        for ($i = 0; $i < $n; $i++) {
            $orig = $zip->getNameIndex($i, ZipArchive::FL_UNCHANGED);
            if (!is_string($orig)) {
                return self::err('bad_zip', __('The zip could not be read.', 'wppoppop'));
            }
            if (self::junk($orig)) {
                continue;
            }
            $clean = self::clean_name($orig);
            if ($clean === null) {
                /* translators: %s: file name inside the zip */
                return self::err('unsafe_path', sprintf(__('Unsafe file name in zip: %s', 'wppoppop'), substr(preg_replace('/[^\x20-\x7e]/', '?', $orig), 0, 80)));
            }
            $op   = 0;
            $attr = 0;
            if ($zip->getExternalAttributesIndex($i, $op, $attr) && $op === ZipArchive::OPSYS_UNIX && ((($attr >> 16) & 0170000) === 0120000)) {
                return self::err('unsafe_path', __('Symbolic links are not allowed in a module zip.', 'wppoppop'));
            }
            $entries[] = ['orig' => $orig, 'name' => $clean, 'dir' => substr($clean, -1) === '/'];
        }
        $names  = array_column($entries, 'name');
        $prefix = '';
        if (!in_array('module.json', $names, true)) {
            $tops = [];
            foreach ($names as $nm) {
                $tops[explode('/', $nm)[0]] = true;
            }
            $tops = array_keys($tops);
            if (count($tops) !== 1 || !in_array($tops[0] . '/module.json', $names, true)) {
                return self::err('no_manifest', __('module.json was not found at the top of the zip.', 'wppoppop'));
            }
            $prefix = $tops[0] . '/';
        }
        $total = 0;
        $files = 0;
        foreach ($entries as $e) {
            $rel = substr($e['name'], strlen($prefix));
            if ($rel === '' || $rel === false || ($prefix !== '' && $e['name'] === rtrim($prefix, '/'))) {
                continue;
            }
            $segs = explode('/', rtrim($rel, '/'));
            if (count($segs) > self::MAX_DEPTH) {
                return self::err('too_deep', __('The zip has folders nested too deeply.', 'wppoppop'));
            }
            $target = $dest . '/' . rtrim($rel, '/');
            if ($e['dir']) {
                if (!is_dir($target) && !@mkdir($target, 0755, true)) {
                    return self::err('write_failed', __('Could not create a folder for the module.', 'wppoppop'));
                }
                continue;
            }
            $file = end($segs);
            foreach (explode('.', $file) as $part) {
                if (preg_match(self::BAD_SEGMENT, $part)) {
                    /* translators: %s: file name */
                    return self::err('bad_type', sprintf(__('File type not allowed in a module: %s', 'wppoppop'), $file));
                }
            }
            $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
            if (!in_array($ext, self::ALLOWED_EXT, true)) {
                /* translators: %s: file name */
                return self::err('bad_type', sprintf(__('File type not allowed in a module: %s', 'wppoppop'), $file));
            }
            $parent = dirname($target);
            if (!is_dir($parent) && !@mkdir($parent, 0755, true)) {
                return self::err('write_failed', __('Could not create a folder for the module.', 'wppoppop'));
            }
            $in = $zip->getStream($e['orig']);
            if (!$in) {
                return self::err('bad_zip', __('The zip could not be read.', 'wppoppop'));
            }
            $out = @fopen($target, 'wb');
            if (!$out) {
                fclose($in);
                return self::err('write_failed', __('Could not write a module file.', 'wppoppop'));
            }
            $size = 0;
            while (!feof($in)) {
                $buf = fread($in, 8192);
                if ($buf === false || $buf === '') {
                    break;
                }
                $size  += strlen($buf);
                $total += strlen($buf);
                if ($size > self::MAX_FILE || $total > self::MAX_UNPACKED) {
                    fclose($in);
                    fclose($out);
                    return self::err('too_big', __('The module is too large once unpacked.', 'wppoppop'));
                }
                fwrite($out, $buf);
            }
            fclose($in);
            fclose($out);
            @chmod($target, 0644);
            $files++;
        }
        if ($files < 1 || !is_file($dest . '/module.json')) {
            return self::err('no_manifest', __('module.json was not found at the top of the zip.', 'wppoppop'));
        }
        return true;
    }

    /**
     * Install a module from an uploaded zip. The module lands as "Available"; it is activated only by Install.
     * $replace = true overwrites an existing uploaded module with the same slug (its installed state is kept).
     */
    public static function install_zip($file, $replace = false) {
        if (!self::can_upload()) {
            return self::err('forbidden', __('You are not allowed to upload modules on this site.', 'wppoppop'));
        }
        if (!class_exists('ZipArchive')) {
            return self::err('no_zip', __('The PHP zip extension is required to upload modules.', 'wppoppop'));
        }
        if (!is_string($file) || !is_file($file) || !is_readable($file)) {
            return self::err('bad_file', __('The uploaded file could not be read.', 'wppoppop'));
        }
        $size = filesize($file);
        if ($size < 1 || $size > self::MAX_ZIP) {
            /* translators: %s: size limit */
            return self::err('too_big', sprintf(__('The zip must be smaller than %s.', 'wppoppop'), size_format(self::MAX_ZIP)));
        }
        $base = WpPopPop_Modules::uploads_dir();
        if ($base === '' || !self::prepare_root($base)) {
            return self::err('no_dir', __('The modules folder could not be created in uploads.', 'wppoppop'));
        }
        $tmp = $base . '/' . self::rand_name('.tmp-');
        if (!@mkdir($tmp, 0755)) {
            return self::err('no_dir', __('The modules folder is not writable.', 'wppoppop'));
        }
        $zip = new ZipArchive();
        if ($zip->open($file) !== true) {
            self::rrmdir($tmp);
            return self::err('bad_zip', __('This is not a valid zip file.', 'wppoppop'));
        }
        $res = self::extract($zip, $tmp);
        $zip->close();
        if (is_wp_error($res)) {
            self::rrmdir($tmp);
            return $res;
        }
        $mf = $tmp . '/module.json';
        if (is_link($mf) || filesize($mf) > WpPopPop_Modules::MAX_JSON) {
            self::rrmdir($tmp);
            return self::err('bad_manifest', __('module.json is missing or too large.', 'wppoppop'));
        }
        $raw  = json_decode((string) file_get_contents($mf), true);
        $slug = (is_array($raw) && isset($raw['slug']) && is_string($raw['slug'])) ? $raw['slug'] : '';
        if (!WpPopPop_Modules::slug_valid($slug)) {
            self::rrmdir($tmp);
            return self::err('bad_slug', __('module.json needs a valid "slug" (lowercase letters, numbers and dashes).', 'wppoppop'));
        }
        if (!isset($raw['category']) || !is_string($raw['category']) || trim($raw['category']) === '') {
            $raw['category'] = WpPopPop_Module_Categories::DEFAULT_SLUG;
            $json = wp_json_encode($raw, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            if (is_string($json) && $json !== '') {
                @file_put_contents($mf, $json . "\n");
            }
        }
        $m = WpPopPop_Modules::normalize($raw, 'uploaded', $slug);
        if (!$m) {
            self::rrmdir($tmp);
            return self::err('bad_manifest', __('module.json is not a valid WpPopPop module manifest.', 'wppoppop'));
        }
        if (!empty($m['html']) && (!is_file($tmp . '/accordion.html') || is_link($tmp . '/accordion.html') || filesize($tmp . '/accordion.html') > WpPopPop_Modules::MAX_HTML)) {
            self::rrmdir($tmp);
            return self::err('bad_manifest', __('accordion.html is missing or too large.', 'wppoppop'));
        }
        if (!empty($m['html']) && !empty($m['data_keys']) && (!is_file($tmp . '/admin.js') || is_link($tmp . '/admin.js'))) {
            self::rrmdir($tmp);
            return self::err('bad_manifest', __('This module saves settings, so it needs an admin.js.', 'wppoppop'));
        }
        $core_path = WpPopPop_Modules::core_dir() . '/' . $slug;
        $old_core  = null;
        if (is_dir($core_path)) {
            $cm = WpPopPop_Modules::get($slug);
            if (!$cm || $cm['source'] !== 'core' || $cm['locked'] || is_link($core_path) || strcasecmp(trim($cm['name']), trim($m['name'])) !== 0) {
                self::rrmdir($tmp);
                return self::err('core_slug', __('This slug belongs to a different or locked built-in module and cannot be uploaded.', 'wppoppop'));
            }
            if (version_compare($m['version'], $cm['version'], '<')) {
                self::rrmdir($tmp);
                /* translators: 1: module name, 2: version in /modules, 3: uploaded version */
                return self::err('older_version', sprintf(__('"%1$s" in /modules is version %2$s, which is newer than the uploaded version %3$s.', 'wppoppop'), $cm['name'], $cm['version'], $m['version']));
            }
            $old_core = $cm;
        }
        $dest   = $base . '/' . $slug;
        $exists = is_dir($dest);
        $old_up = $exists ? self::read_uploaded($dest) : null;
        if ($old_up) {
            if (strcasecmp(trim($old_up['name']), trim($m['name'])) !== 0) {
                self::rrmdir($tmp);
                /* translators: 1: module name in the uploads folder, 2: uploaded module name */
                return self::err('name_mismatch', sprintf(__('The module with this slug in the uploads folder is named "%1$s", not "%2$s", so it cannot be replaced.', 'wppoppop'), $old_up['name'], $m['name']));
            }
            if (version_compare($m['version'], $old_up['version'], '<')) {
                self::rrmdir($tmp);
                /* translators: 1: module name, 2: version in the uploads folder, 3: uploaded version */
                return self::err('older_version', sprintf(__('"%1$s" in the uploads folder is version %2$s, which is newer than the uploaded version %3$s.', 'wppoppop'), $old_up['name'], $old_up['version'], $m['version']));
            }
        }
        if (($exists || $old_core) && !$replace) {
            self::rrmdir($tmp);
            $old = $old_core ? $old_core : WpPopPop_Modules::get($slug);
            return self::err('exists', __('A module with this slug is already installed.', 'wppoppop'), [
                'slug'        => $slug,
                'name'        => $m['name'],
                'old_version' => $old ? $old['version'] : '',
                'new_version' => $m['version'],
                'old'         => $old ? self::summary($old) : null,
                'new'         => self::incoming($m),
            ]);
        }
        $prev_state = $old_core ? WpPopPop_Modules::state($slug) : '';
        $core_bak   = '';
        if ($old_core) {
            $core_bak = WpPopPop_Modules::core_dir() . '/' . self::rand_name('.old-');
            if (!@rename($core_path, $core_bak)) {
                self::rrmdir($tmp);
                return self::err('replace_failed', __('The older module could not be removed from /modules. Check that the folder is writable.', 'wppoppop'));
            }
        }
        @file_put_contents($tmp . '/index.php', "<?php\n// Silence is golden.\n");
        if ($exists) {
            $old = $base . '/' . self::rand_name('.old-');
            if (is_link($dest) || !@rename($dest, $old)) {
                self::undo_core($core_bak, $core_path);
                self::rrmdir($tmp);
                return self::err('replace_failed', __('The existing module could not be replaced.', 'wppoppop'));
            }
            if (!@rename($tmp, $dest)) {
                @rename($old, $dest);
                self::undo_core($core_bak, $core_path);
                self::rrmdir($tmp);
                return self::err('replace_failed', __('The existing module could not be replaced.', 'wppoppop'));
            }
            self::rrmdir($old);
        } elseif (!@rename($tmp, $dest)) {
            self::undo_core($core_bak, $core_path);
            self::rrmdir($tmp);
            return self::err('write_failed', __('The module could not be moved into place.', 'wppoppop'));
        }
        if ($core_bak !== '') {
            self::rrmdir($core_bak);
        }
        WpPopPop_Modules::flush();
        WpPopPop_Modules::clear_orphan($slug);
        if ($old_core) {
            WpPopPop_Modules::set_state($slug, $prev_state);
        } elseif (!$exists) {
            WpPopPop_Modules::set_state($slug, 'available');
        }
        if (!empty($m['category'])) {
            WpPopPop_Module_Categories::ensure($m['category']);
        }
        $exists = $exists || $old_core !== null;
        do_action('wppoppop_module_uploaded', $slug, $exists);
        $mod = WpPopPop_Modules::get($slug);
        return [
            'module'   => $mod ? self::summary($mod) : null,
            'replaced' => $exists,
            'message'  => $old_core
                ? __('Module updated. The older version was removed from /modules.', 'wppoppop')
                : ($exists ? __('Module updated.', 'wppoppop') : __('Module uploaded. Click Install to activate it.', 'wppoppop')),
        ];
    }
}
