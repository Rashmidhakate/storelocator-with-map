<?php
namespace Brainvire\Customtheme\Block;
class Dynamiccss extends \Magento\Framework\View\Element\Template
{
	public function __construct(
		\Magento\Framework\View\Element\Template\Context $context,
		\Brainvire\Customtheme\Helper\Data $helper
	)
	{ 
		$this->_helper = $helper;
		parent::__construct($context);
	}

	public function color_right_headingh() {
		return $this->_helper->getGeneralConfig('wall_color');
	}
}
