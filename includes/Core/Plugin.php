<?php
namespace WPPopPop\Core;

use WPPopPop\Triggers\TriggerManager;
use WPPopPop\Targeting\PopupPostType;
use WPPopPop\Targeting\ABTestingManager;
use WPPopPop\API\SubmissionController;
use WPPopPop\API\PaymentController;
use WPPopPop\API\RemoteEmbedController;
use WPPopPop\Export\LeadExporter;
use WPPopPop\Export\PopupConfigManager;
use WPPopPop\Analytics\StatsTracker;

class Plugin {
    private static ?Plugin $instance = null;
    public TriggerManager $triggers;
    public PopupPostType $post_type;
    public ABTestingManager $ab_testing;
    public SubmissionController $api;
    public PaymentController $payments;
    public RemoteEmbedController $remote_embed;
    public LeadExporter $exporter;
    public PopupConfigManager $config_manager;
    public StatsTracker $stats;
    public ConfirmationManager $confirmations;
    public AutoresponderService $autoresponder;
    public GutenbergBlocks $gutenberg;
    public PrivacyManager $privacy;

    public static function get_instance(): Plugin {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        $this->post_type      = new PopupPostType();
        $this->ab_testing     = new ABTestingManager();$this->triggers       = new TriggerManager();
        $this->api            = new SubmissionController();$this->payments       = new PaymentController();
        $this->remote_embed   = new RemoteEmbedController();$this->exporter       = new LeadExporter();
        $this->config_manager = new PopupConfigManager();$this->stats          = new StatsTracker();
        $this->confirmations  = new ConfirmationManager();$this->autoresponder  = new AutoresponderService();
        $this->gutenberg      = new GutenbergBlocks();$this->privacy        = new PrivacyManager();
    }

    public function init(): void {
        $this->post_type->init();$this->triggers->init();
        $this->api->init();$this->payments->init();
        $this->remote_embed->init();$this->exporter->init();
        $this->config_manager->init();$this->stats->init();
        $this->confirmations->init();$this->autoresponder->init();
        $this->gutenberg->init();$this->privacy->init();
    }
}
