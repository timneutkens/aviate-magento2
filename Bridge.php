<?php
namespace WeProvide\Aviate\Magento2;

use Magento\Framework\View\Design\ThemeInterface;
use Magento\Framework\View\DesignInterface;
use Magento\Store\Model\ScopeInterface;
use WeProvide\Aviate\Aviate;
use Magento\Framework\App\State;
use \Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\View\Element\Template\Context;
use Magento\Theme\Model\Theme\ThemeProvider;

class Bridge extends Aviate {
    protected $directoryList;
    protected $developerMode;
    protected $storeManager;
    protected $scopeConfig;
    protected $themeProvider;

    public function __construct(Context $context, DirectoryList $directoryList, ThemeProvider $themeProvider) {
        $state = $context->getAppState();
        $this->storeManager = $context->getStoreManager();
        $this->scopeConfig = $context->getScopeConfig();

        $this->directoryList = $directoryList;
        $this->themeProvider = $themeProvider;

        $this->developerMode = $state->getMode() === $state::MODE_DEVELOPER;
    }

    public function isDevMode(): bool {
        return $this->developerMode;
    }

    public function getTheme(): ThemeInterface
    {
        $themeId = $this->scopeConfig->getValue(
            DesignInterface::XML_PATH_THEME_ID,
            ScopeInterface::SCOPE_STORE,
            $this->storeManager->getStore()->getId()
        );
        /** @var $theme ThemeInterface */
        $theme = $this->themeProvider->getThemeById($themeId);
        return $theme;
    }

    public function getProjectRoot(): string
    {
        return $this->directoryList->getRoot();
    }

    public function getFiles(): array
    {
        $types = parent::getFiles();

        $themePath = $this->getTheme()->getThemePath();

        if($this->isDevMode()) {
            $types['js'][] = $this->getDevServerUrl($themePath . '.js');

            return $types;
        }

        $types['css'][] = $this->getViewFileUrl('dist/' . $themePath . '.css' );

        return $types;
    }
}