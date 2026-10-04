<?php
namespace WPPopPop\Core;

use WPPopPop\Targeting\PopupPostType;
use WPPopPop\Triggers\TriggerManager;

class Plugin {
    private static ?Plugin $instance = null;
    public PopupPostType $post_type;
    public TriggerManager $triggers;

    public static function get_instance(): Plugin {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        $this->post_type = new PopupPostType();
        $this->triggers  = new TriggerManager();
    }

    public function init(): void {
        $this->post_type->init();
        $this->triggers->init();
    }
}
