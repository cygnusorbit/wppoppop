<?php
namespace WPPopPop\Integrations;

interface CRMAdapterInterface {
    public function sync_contact(string $email, string $name, array $custom_fields = []): array;
}
