<?php

declare(strict_types=1);

namespace Rector\ConsoleDiffer;

use Nette\Utils\Strings;
use SebastianBergmann\Diff\Differ;

final class MarkdownDifferAndFormatter
{
    /**
     * @var string
     */
    private const METADATA_REGEX = '#^(.*\n){1}#';

    /**
     * @var string
     */
    private const SPACE_AND_NEWLINE_REGEX = '#( ){1,}\n#';

    /**
     * @var Differ
     */
    private $markdownDiffer;

    public function __construct(Differ $markdownDiffer)
    {
        $this->markdownDiffer = $markdownDiffer;
    }

    public function bareDiffAndFormatWithoutColors(string $old, string $new): string
    {
        if ($old === $new) {
            return '';
        }

        $diff = $this->markdownDiffer->diff($old, $new);

        // remove first line, just meta info added by UnifiedDiffOutputBuilder
        $diff = Strings::replace($diff, self::METADATA_REGEX);

        return $this->removeTrailingWhitespaces($diff);
    }

    /**
     * Removes UnifiedDiffOutputBuilder generated pre-spaces " \n" => "\n"
     */
    private function removeTrailingWhitespaces(string $diff): string
    {
        $diff = Strings::replace($diff, self::SPACE_AND_NEWLINE_REGEX, PHP_EOL);

        return rtrim($diff);
    }
}
