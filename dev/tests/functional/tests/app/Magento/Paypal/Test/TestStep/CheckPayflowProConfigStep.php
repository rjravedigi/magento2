<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Paypal\Test\TestStep;

use Magento\Backend\Test\Page\Adminhtml\SystemConfigEditSectionPayment;
use Magento\Mtf\TestStep\TestStepInterface;
use Magento\Payment\Test\Constraint\AssertFieldsAreActive;
use Magento\Payment\Test\Constraint\AssertFieldsAreDisabled;
use Magento\Payment\Test\Constraint\AssertFieldsAreEnabled;
use Magento\Payment\Test\Constraint\AssertFieldsArePresent;

/**
 * Check PayPal Payflow Pro configuration.
 */
class CheckPayflowProConfigStep implements TestStepInterface
{
    /**
     * @var SystemConfigEditSectionPayment
     */
    private $systemConfigEditSectionPayment;

    /**
     * @var AssertFieldsAreDisabled
     */
    private $assertFieldsAreDisabled;

    /**
     * @var AssertFieldsArePresent
     */
    private $assertFieldsArePresent;

    /**
     * @var AssertFieldsAreActive
     */
    private $assertFieldsAreActive;

    /**
     * @var AssertFieldsAreEnabled
     */
    private $assertFieldsAreEnabled;

    /**
     * @var
     */
    private $countryCode;

    /**
     * @var
     */
    private $sections;

    /**
     * @var \Magento\Paypal\Test\Block\System\Config\PayflowPro
     */
    private $payflowProConfigBlock;

    /**
     * @param SystemConfigEditSectionPayment $systemConfigEditSectionPayment
     * @param AssertFieldsAreDisabled $assertFieldsAreDisabled
     * @param AssertFieldsArePresent $assertFieldsArePresent
     * @param AssertFieldsAreActive $assertFieldsAreActive
     * @param AssertFieldsAreEnabled $assertFieldsAreEnabled
     * @param $countryCode
     * @param $sections
     */
    public function __construct(
        SystemConfigEditSectionPayment $systemConfigEditSectionPayment,
        AssertFieldsAreDisabled $assertFieldsAreDisabled,
        AssertFieldsArePresent $assertFieldsArePresent,
        AssertFieldsAreActive $assertFieldsAreActive,
        AssertFieldsAreEnabled $assertFieldsAreEnabled,
        $countryCode,
        $sections
    ) {
        $this->systemConfigEditSectionPayment = $systemConfigEditSectionPayment;
        $this->assertFieldsAreDisabled = $assertFieldsAreDisabled;
        $this->assertFieldsArePresent = $assertFieldsArePresent;
        $this->assertFieldsAreActive = $assertFieldsAreActive;
        $this->assertFieldsAreEnabled = $assertFieldsAreEnabled;
        $this->countryCode = $countryCode;
        $this->sections = $sections;
        $this->payflowProConfigBlock = $this->systemConfigEditSectionPayment->getPayflowProConfigBlock();
    }

    /**
     * .
     *
     * @return void
     */
    public function run()
    {
        $this->systemConfigEditSectionPayment->getPaymentsConfigBlock()->expandPaymentSections($this->sections);
        $this->enablePayflowPro();
        $this->disablePayflowPro();
    }

    /**
     * Enables Payflow Pro and makes assertions for fields.
     */
    private function enablePayflowPro()
    {
        $this->payflowProConfigBlock->clickConfigureButton();
        $this->payflowProConfigBlock->clearCredentials();
        $enablers = $this->payflowProConfigBlock->getEnablerFields();
        $this->assertFieldsAreDisabled->processAssert(
            $this->systemConfigEditSectionPayment,
            [$enablers['Enable this Solution'], $enablers['Enable PayPal Credit']]
        );
        $this->payflowProConfigBlock->specifyCredentials();
        $this->assertFieldsAreActive->processAssert(
            $this->systemConfigEditSectionPayment,
            [$enablers['Enable this Solution']]
        );
        $this->assertFieldsAreDisabled->processAssert(
            $this->systemConfigEditSectionPayment,
            [$enablers['Enable PayPal Credit']]
        );
        $this->payflowProConfigBlock->enablePayflowPro();
        $this->assertFieldsAreActive->processAssert(
            $this->systemConfigEditSectionPayment,
            [$enablers['Enable this Solution'], $enablers['Enable PayPal Credit'], $enablers['Vault enabled']]
        );
        $this->assertFieldsAreEnabled->processAssert(
            $this->systemConfigEditSectionPayment,
            [$enablers['Enable this Solution'], $enablers['Enable PayPal Credit']]
        );
        $this->systemConfigEditSectionPayment->getPageActions()->save();
        $this->systemConfigEditSectionPayment->getMessagesBlock()->waitSuccessMessage();
    }

    /**
     * Disables Payflow Pro and makes assertions for fields.
     */
    private function disablePayflowPro()
    {
        $enablers = $this->payflowProConfigBlock->getEnablerFields();
        $this->payflowProConfigBlock->clickConfigureButton();
        $this->assertFieldsAreActive->processAssert($this->systemConfigEditSectionPayment, $enablers);
        $this->assertFieldsAreEnabled->processAssert(
            $this->systemConfigEditSectionPayment,
            [$enablers['Enable this Solution'], $enablers['Enable PayPal Credit']]
        );
        $this->payflowProConfigBlock->disablePayflowPro();
        $this->assertFieldsAreDisabled->processAssert(
            $this->systemConfigEditSectionPayment,
            [$enablers['Enable PayPal Credit']]
        );
    }
}
