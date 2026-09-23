<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Blade;
use ParseError;
use PHPUnit\Framework\Attributes\Test;
use SplFileInfo;
use Symfony\Component\Finder\Finder;
use Tests\TestCase;

/**
 * Every Blade template must compile to valid PHP.
 *
 * A template that does not only fails when a page happens to reach that branch, which is how a
 * broken tile reached staging: the compiled file was invalid, but only profiles that rendered
 * that one case returned a 500. This walks every template instead of waiting for the right
 * profile type to be visited.
 */
class BladeTemplatesCompileTest extends TestCase
{
    /**
     * A dead copy of the legacy Form Builder page: the live page includes the plain
     * views/legacy/formbuilder/index.php instead (legacy/formbuilder/shell.blade.php), and this
     * copy carries a broken PHP tag that has never compiled. Reported, not fixed here.
     *
     * @var list<string>
     */
    private const NOT_RENDERED = ['legacy/formbuilder/index.blade.php'];

    #[Test]
    public function every_blade_template_compiles_to_valid_php(): void
    {
        $templates = Finder::create()->files()->in(resource_path('views'))->name('*.blade.php');
        $checked = 0;
        $broken = [];

        foreach ($templates as $template) {
            /** @var SplFileInfo $template */
            if (in_array(str_replace(DIRECTORY_SEPARATOR, '/', $template->getRelativePathname()), self::NOT_RENDERED, true)) {
                continue;
            }

            $compiled = Blade::compileString((string) file_get_contents($template->getRealPath()));

            try {
                // Parsed in full before the return runs, so a syntax error surfaces here and
                // nothing in the template is executed.
                eval('return true; ?>'.$compiled);
            } catch (ParseError $e) {
                $broken[] = $template->getRelativePathname().': '.$e->getMessage();
            }

            $checked++;
        }

        $this->assertGreaterThan(50, $checked, 'the templates were found');
        $this->assertSame([], $broken, 'these templates do not compile');
    }
}
