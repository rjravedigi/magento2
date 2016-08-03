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
 * Check PayPal Payments Advanced configuration.
 */
class CheckPaymentsAdvancedConfigStep implements TestStepInterface
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
     * @var \Magento\Paypal\Test\Block\System\Config\PaymentsAdvanced
     */
    private $paymentsAdvancedConfigBlock;

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
        $this->paymentsAdvancedConfigBlock = $this->systemConfigEditSectionPayment->getPaymentsAdvancedConfigBlock();
    }

    /**
     * .
     *
     * @return void
     */
    public function run()
    {
        $this->systemConfigEditSectionPayment->getPaymentsConfigBlock()->expandPaymentSections($this->sections);
        $this->enablePaymentsAdvanced();
        $this->disablePaymentsAdvanced();
    }

    /**
     * Enables Payments Advanced and makes assertions for fields.
     */
    private function enablePaymentsAdvanced()
    {
        $this->paymentsAdvancedConfigBlock->clickConfigureButton();
        $this->paymentsAdvancedConfigBlock->clearCredentials();
        $enablers = $this->paymentsAdvancedConfigBlock->getEnablerFields();
        $this->assertFieldsAreDisabled->processAssert($this->systemConfigEditSectionPayment, $enablers);
        $this->paymentsAdvancedConfigBlock->specifyCredentials();
        $this->assertFieldsAreActive->processAssert(
            $this->systemConfigEditSectionPayment,
            [$enablers['Enable this Solution']]
        );
        $this->paymentsAdvancedConfigBlock->enablePaymentsAdvanced();
        $this->assertFieldsAreActive->processAssert(
            $this->systemConfigEditSectionPayment,
            [$enablers['Enable this Solution'], $enablers['Enable PayPal Credit']]
        );
        $this->assertFieldsAreEnabled->processAssert(
            $this->systemConfigEditSectionPayment,
            [$enablers['Enable this Solution'], $enablers['Enable PayPal Credit']]
        );
        $this->systemConfigEditSectionPayment->getPageActions()->save();
        $this->systemConfigEditSectionPayment->getMessagesBlock()->waitSuccessMessage();
    }

    /**
     * Disables Payments Advanced and makes assertions for fields.
     */
    private function disablePaymentsAdvanced()
    {
        $enablers = $this->paymentsAdvancedConfigBlock->getEnablerFields();
        $this->paymentsAdvancedConfigBlock->clickConfigureButton();
        $this->assertFieldsAreActive->processAssert($this->systemConfigEditSectionPayment, $enablers);
        $this->assertFieldsAreEnabled->processAssert($this->systemConfigEditSectionPayment, $enablers);
        $this->paymentsAdvancedConfigBlock->disablePaymentsAdvanced();
        $this->assertFieldsAreDisabled->processAssert(
            $this->systemConfigEditSectionPayment,
            [$enablers['Enable PayPal Credit']]
        );
    }
}
