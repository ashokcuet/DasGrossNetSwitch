<?php declare(strict_types=1);

namespace DasGrossNetSwitch;

use Shopware\Core\Framework\Plugin;
use Shopware\Core\Framework\Plugin\Context\InstallContext;
use Shopware\Core\Framework\Plugin\Context\UninstallContext;
use Shopware\Core\Framework\Plugin\Context\UpdateContext;

class DasGrossNetSwitch extends Plugin
{
    public function install(InstallContext $installContext): void
    {
        parent::Install($installContext);
    }

    public function update(UpdateContext $updateContext): void
    {
        parent::update($updateContext);
    }

    public function uninstall(UninstallContext $uninstallContext): void
    {
        parent::uninstall($uninstallContext);

        if ($uninstallContext->keepUserData()) {
            parent::uninstall($uninstallContext);

            return;
        }
    }
}