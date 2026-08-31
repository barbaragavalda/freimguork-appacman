<?php

namespace Appacman\Tests\Model\Form;

use Appacman\Model\Form\Uri;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * FormInput's constructor pulls in a real DB connection (Core\Model\Model's defaults) and
 * queries it directly, so Uri is built via reflection (no constructor call) - the default
 * $isHidden=true path of getPostValue() only reads $_POST, no other state.
 */
class UriTest extends TestCase
{

    protected function tearDown(): void
    {
        unset($_POST['name'], $_POST['name_2']);
    }

    private function callGetPostValue(?int $langID = null): string
    {
        $uri        = (new ReflectionClass(Uri::class))->newInstanceWithoutConstructor();
        $reflection = new ReflectionClass($uri);
        return $reflection->getMethod('getPostValue')->invoke($uri, $langID);
    }

    public function testSlugifiesTheNameLowercasedAndDashSeparated(): void
    {
        $_POST['name'] = 'Títol de Prova';

        $this->assertSame('titol-de-prova', $this->callGetPostValue());
    }

    public function testStripsHtmlTagsBeforeSlugifying(): void
    {
        $_POST['name'] = 'Title <script>alert(1)</script> here';

        $this->assertSame('title-alert1-here', $this->callGetPostValue());
    }

    public function testDropsPunctuationAndCollapsesRepeatedDashes(): void
    {
        $_POST['name'] = 'Foo!! -- Bar??';

        $this->assertSame('foo-bar', $this->callGetPostValue());
    }

    public function testReadsThePerLanguagePostKeyWhenLangIdIsGiven(): void
    {
        $_POST['name']   = 'Default title';
        $_POST['name_2'] = 'Títol en català';

        $this->assertSame('titol-en-catala', $this->callGetPostValue(2));
    }

}
