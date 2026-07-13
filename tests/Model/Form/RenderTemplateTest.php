<?php

namespace Appacman\Tests\Model\Form;

use Appacman\Model\Form\Check;
use Appacman\Model\Form\Varchar;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * Varchar/Check::getInputHTML() now render a Twig template (FormInput::renderTemplate())
 * instead of hand-building an HTML string. FormInput's constructor pulls in a real DB
 * connection/session via Core\Model\Model's defaults, so these tests build instances via
 * reflection (no constructor call) and set only the properties getInputHTML() actually
 * reads - keeping the test scoped to the rendering path this change touched.
 */
class RenderTemplateTest extends TestCase
{

    private function makeInput(string $class, array $props): object
    {
        $reflection = new ReflectionClass($class);
        $input      = $reflection->newInstanceWithoutConstructor();
        foreach ($props as $name => $value) {
            $reflection->getProperty($name)->setValue($input, $value);
        }
        return $input;
    }

    private function callGetInputHTML(object $input): string
    {
        $reflection = new ReflectionClass($input);
        return $reflection->getMethod('getInputHTML')->invoke($input, null);
    }

    private function callRenderTemplate(object $input, string $name, array $data): string
    {
        $reflection = new ReflectionClass($input);
        return $reflection->getMethod('renderTemplate')->invoke($input, $name, $data);
    }

    public function testVarcharRendersATextInputAndEscapesTheValue(): void
    {
        $input = $this->makeInput(Varchar::class, array(
            'fieldName' => 'title',
            'value'     => 'a "quoted" value',
        ));

        $html = $this->callGetInputHTML($input);

        $this->assertStringContainsString('name="title"', $html);
        $this->assertStringContainsString('id="title"', $html);
        $this->assertStringNotContainsString('value="a "quoted" value"', $html, 'raw quotes must not break out of the attribute');
        $this->assertStringContainsString(htmlspecialchars('a "quoted" value', ENT_QUOTES), $html);
    }

    public function testCheckRendersCheckedAndDisabledWhenValueIsTruthy(): void
    {
        $input = $this->makeInput(Check::class, array(
            'fieldName' => 'active',
            'value'     => '1',
        ));

        $html = $this->callGetInputHTML($input);

        $this->assertStringContainsString('type="hidden"', $html);
        $this->assertStringContainsString('type="checkbox"', $html);
        $this->assertStringContainsString('checked', $html);
        $this->assertStringContainsString('disabled', $html);
    }

    public function testCheckOmitsCheckedAndDisabledWhenValueIsFalsy(): void
    {
        $input = $this->makeInput(Check::class, array(
            'fieldName' => 'active',
            'value'     => '',
        ));

        $html = $this->callGetInputHTML($input);

        $this->assertStringNotContainsString('checked', $html);
        $this->assertStringNotContainsString('disabled', $html);
    }

    public function testGenericInputTemplateAppendsExtraAttributes(): void
    {
        $input = $this->makeInput(Varchar::class, array());

        $html = $this->callRenderTemplate($input, '_input', array(
            'type'        => 'text',
            'postName'    => 'address',
            'value'       => '',
            'placeholder' => '',
            'extra'       => 'autocomplete="off"',
        ));

        $this->assertStringContainsString('autocomplete="off"', $html);
    }

    public function testSelectTemplateRendersOptionsWithSelectedAndDisabledFlags(): void
    {
        $input = $this->makeInput(Varchar::class, array());

        $optionsHTML = $this->callRenderTemplate($input, '_select-options', array(
            'options' => array(
                array('id' => 1, 'name' => 'Foo', 'selected' => true, 'disabled' => false),
                array('id' => 2, 'name' => 'Bar', 'selected' => false, 'disabled' => true),
            ),
        ));

        $this->assertStringContainsString('<option value="1" selected>Foo</option>', $optionsHTML);
        $this->assertStringContainsString('<option value="2" disabled>Bar</option>', $optionsHTML);

        $html = $this->callRenderTemplate($input, 'select', array(
            'postName'    => 'category',
            'placeholder' => 'Selecciona categoria',
            'optionsHTML' => $optionsHTML,
        ));

        $this->assertStringContainsString('name="category"', $html);
        $this->assertStringContainsString('<option value="1" selected>Foo</option>', $html);
    }

    public function testSelectOptionsOmitsTheBlankOptionWhenToldTo(): void
    {
        $input = $this->makeInput(Varchar::class, array());

        $withBlank = $this->callRenderTemplate($input, '_select-options', array(
            'options' => array(),
        ));
        $withoutBlank = $this->callRenderTemplate($input, '_select-options', array(
            'options'      => array(),
            'includeBlank' => false,
        ));

        $this->assertStringContainsString('<option></option>', $withBlank);
        $this->assertStringNotContainsString('<option></option>', $withoutBlank);
    }

    public function testSelectDeepLinkMainTemplateRendersValueAndDataId(): void
    {
        $input = $this->makeInput(Varchar::class, array());

        $html = $this->callRenderTemplate($input, 'select-deeplink-main', array(
            'fieldName'   => 'deeplink',
            'placeholder' => 'Selecciona enlace',
            'options'     => array(
                array('id' => 5, 'value' => '5_club', 'name' => 'Club', 'selected' => true),
            ),
        ));

        $this->assertStringContainsString('id="deeplink"', $html);
        $this->assertStringContainsString('<option value="5_club" selected data-id="5">Club</option>', $html);
    }

}
