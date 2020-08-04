<?php
/**
 * Copyright © 2019 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace Brainvire\StoreLocator\Block\Adminhtml\Manage\Renderer;

/**
 * Class Hours
 * @package Wyomind\PointOfSale\Block\Adminhtml\Manage\Renderer
 */
class Hours extends \Magento\Framework\Data\Form\Element\AbstractElement
{
    /**
     * Retrieve allow attributes
     *
     * @return array
     */
    public function getHtmlAttributes()
    {
        return ['type', 'name', 'class', 'style', 'checked', 'onclick', 'onchange', 'disabled'];
    }

    /**
     * Prepare value list
     *
     * @return array
     */
    protected function _prepareValues()
    {
        $values = [
            [
                'value' => "Hours",
                'label' => __('Working Hours')
            ],
           
        ];

        return $values;
    }

    /**
     * Retrieve HTML
     *
     * @return string
     */
    public function getElementHtml()
    {
        $values = $this->_prepareValues();

        if (!$values) {
            return '';
        }
        $id = $this->getHtmlId();

        $html= "<script>
            require(['jquery', 'edit_hours'], function ($, pointofsale) {
                'use strict';
                var elementId = '" . $id . "';
                    
                setTimeout(pointofsale.initializeGMap, 5000);
                
                // initialize hours
                pointofsale.initializeHours(elementId);
                
                $(document).on('click', '." . $id . "_day', function() {
                    pointofsale.activeField(this, elementId);
                });
                
                $(document).on('click', '." . $id . "_lunch', function() {
                    pointofsale.activeFieldLunch(this, elementId);
                });
                
                $(document).on('change', '.hours_summary', function() {
                    pointofsale.summary(elementId);
                });
            });
        </script>";

        $html .=  '<ul class="checkboxes">';

         foreach ($values as $day) {
            $html .= '<li style="display:inline-block;width:300px;float:left">';
            $html .= '<label class="data-grid-checkbox-cell-inner hidden">'
                . '<input value="' . $day['value'] . '" '
                . 'class="' . $id . '_day admin__control-checkbox" '
                . 'id="' . $day['value'] . '" '
                . 'type="checkbox" '

                . 'value="' . $day['value'] . '" checked/>'
                . '<label for="' . $day['value'] . '">&nbsp;<b>' . $day['label'] . '</b></label>'
                . '</label>';

            $html .= "<div style='margin-top:-40px;'> <select style='width:100px;' id='" . $day['value'] . "_open' class='hours_summary'>";
            for ($h = 0; $h <= 24; $h++) {
                for ($m = 0; $m < 60; $m = $m + 15) {
                    $html .= "<option value='" . str_pad($h, 2, 0, STR_PAD_LEFT) . ':' . str_pad($m, 2, 0, STR_PAD_LEFT) . "'>"
                        . str_pad($h, 2, 0, STR_PAD_LEFT) . ':' . str_pad($m, 2, 0, STR_PAD_LEFT)
                        . "</option>";
                    if ($h == 24) {
                        break;
                    }
                }
            }
            $html .= "</select> - ";
            $html .= "<select style='width:100px;' id='" . $day['value'] . "_close' class='hours_summary'>";
            for ($h = 0; $h <= 24; $h++) {
                $selected = ($h == 24) ? "selected " : "";
                for ($m = 0; $m < 60; $m = $m + 15) {
                    $html .= "<option " . $selected . "value='" . str_pad($h, 2, 0, STR_PAD_LEFT) . ':' . str_pad($m, 2, 0, STR_PAD_LEFT) . "'>"
                        . str_pad($h, 2, 0, STR_PAD_LEFT) . ':' . str_pad($m, 2, 0, STR_PAD_LEFT)
                        . "</option>";
                    if ($h == 24) {
                        break;
                    }
                }
            }
            $html .= "</select></div>";
            $html .= '</li>';

        }
       $html .= '</ul>' . $this->getAfterElementHtml();

        return $html;
    }
}