<?php

namespace Breviam\MpesaSdk\Tests\Unit;

use Breviam\MpesaSdk\Tests\TestCase;
use Breviam\MpesaSdk\Helpers\Utils;

class UtilsTest extends TestCase
{
    /** @test */
    public function it_formats_phone_numbers_correctly()
    {
        // Test various phone number formats
        $this->assertEquals('254712345678', Utils::formatPhoneNumber('0712345678'));
        $this->assertEquals('254712345678', Utils::formatPhoneNumber('+254712345678'));
        $this->assertEquals('254712345678', Utils::formatPhoneNumber('254712345678'));
        $this->assertEquals('254712345678', Utils::formatPhoneNumber('712345678'));
    }

    /** @test */
    public function it_validates_phone_numbers()
    {
        // Valid numbers
        $this->assertTrue(Utils::isValidPhoneNumber('0712345678'));
        $this->assertTrue(Utils::isValidPhoneNumber('0722345678'));
        $this->assertTrue(Utils::isValidPhoneNumber('+254712345678'));

        // Invalid numbers
        $this->assertFalse(Utils::isValidPhoneNumber('0812345678')); // Invalid prefix
        $this->assertFalse(Utils::isValidPhoneNumber('071234567'));  // Too short
        $this->assertFalse(Utils::isValidPhoneNumber('07123456789')); // Too long
    }

    /** @test */
    public function it_generates_timestamp()
    {
        $timestamp = Utils::generateTimestamp();

        $this->assertIsString($timestamp);
        $this->assertEquals(14, strlen($timestamp));
        $this->assertMatchesRegularExpression('/^\d{14}$/', $timestamp);
    }

    /** @test */
    public function it_generates_password()
    {
        $shortcode = '174379';
        $passkey = 'bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919';
        $timestamp = '20240708123456';

        $password = Utils::generatePassword($shortcode, $passkey, $timestamp);

        $this->assertIsString($password);
        $this->assertEquals(base64_encode($shortcode . $passkey . $timestamp), $password);
    }

    /** @test */
    public function it_generates_reference()
    {
        $reference = Utils::generateReference('TEST');

        $this->assertIsString($reference);
        $this->assertStringStartsWith('TEST', $reference);
        $this->assertGreaterThan(4, strlen($reference));
    }

    /** @test */
    public function it_masks_sensitive_data()
    {
        $data = [
            'Password' => 'secret123',
            'SecurityCredential' => 'credential123',
            'CommandID' => 'command123',
            'Amount' => 100,
        ];

        $masked = Utils::maskSensitiveData($data);

        $this->assertEquals('***MASKED***', $masked['Password']);
        $this->assertEquals('***MASKED***', $masked['SecurityCredential']);
        $this->assertEquals('***MASKED***', $masked['CommandID']);
        $this->assertEquals(100, $masked['Amount']);
    }
}
