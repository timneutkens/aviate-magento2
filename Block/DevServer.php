<?php
namespace WeProvide\Aviate\Magento2\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use WeProvide\Aviate\Magento2\Bridge as AviateBridge;

class DevServer extends Template {
    protected $aviate;

    public function __construct(Context $context, AviateBridge $aviate, array $data = []) {
        $this->aviate = $aviate;
        parent::__construct($context, $data);
    }

    public function aviate() {
        return $this->aviate;
    }

    public function getFiles() {
        return $this->aviate()->getFiles();
    }
}