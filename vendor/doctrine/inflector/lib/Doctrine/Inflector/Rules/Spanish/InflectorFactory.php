<?php

declare (strict_types=1);
namespace RectorPrefix20220110\Doctrine\Inflector\Rules\Spanish;

use RectorPrefix20220110\Doctrine\Inflector\GenericLanguageInflectorFactory;
use RectorPrefix20220110\Doctrine\Inflector\Rules\Ruleset;
final class InflectorFactory extends \RectorPrefix20220110\Doctrine\Inflector\GenericLanguageInflectorFactory
{
    protected function getSingularRuleset() : \RectorPrefix20220110\Doctrine\Inflector\Rules\Ruleset
    {
        return \RectorPrefix20220110\Doctrine\Inflector\Rules\Spanish\Rules::getSingularRuleset();
    }
    protected function getPluralRuleset() : \RectorPrefix20220110\Doctrine\Inflector\Rules\Ruleset
    {
        return \RectorPrefix20220110\Doctrine\Inflector\Rules\Spanish\Rules::getPluralRuleset();
    }
}
