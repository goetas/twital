<?php
namespace Goetas\Twital\SourceAdapter;

use Goetas\Twital\SourceAdapter;
use Goetas\Twital\Template;

/**
 *
 * @author Asmir Mustafic <goetas@gmail.com>
 *
 */
class XMLAdapter implements SourceAdapter
{
    /**
     * {@inheritdoc}
     */
    public function load($source)
    {
        $dom = $this->createDom($source);

        return new Template($dom, $this->collectMetadata($dom, $source));
    }

    /**
     * {@inheritdoc}
     */
    public function dump(Template $template)
    {
        $metadata = $template->getMetadata();
        $dom = $template->getDocument();
        $dom->preserveWhiteSpace = true;
        $dom->formatOutput = false;

        if ($metadata['xmldeclaration']) {
            return $dom->saveXML();
        } else {
            $xml = '';
            foreach ($dom->childNodes as $node) {
                $xml .= $dom->saveXML($node);
                if ($node instanceof \DOMDocumentType) {
                    $xml .= PHP_EOL;
                }
            }

            return $xml;
        }
    }

    /**
     * Collect some metadata about $dom and $source
     * @param \DOMDocument $dom
     * @param string $source
     * @return mixed
     */
    protected function collectMetadata(\DOMDocument $dom, $source)
    {
        $metadata = array();

        $metadata['xmldeclaration'] = strpos(rtrim($source), '<?xml ') === 0;
        $metadata['doctype'] = !!$dom->doctype;

        return $metadata;
    }

    protected function createDom($source)
    {
        $internalErrors = libxml_use_internal_errors(true);
        libxml_clear_errors();

        $dom = new \DOMDocument('1.0', 'UTF-8');
        if ('' !== trim($source) && !$dom->loadXML($source, LIBXML_NONET | (defined('LIBXML_COMPACT') ? LIBXML_COMPACT : 0))) {
            throw new \InvalidArgumentException(implode("\n", $this->getXmlErrors($internalErrors)));
        }

        libxml_use_internal_errors($internalErrors);

        return $dom;
    }

    protected function getXmlErrors($internalErrors)
    {
        $errors = array();
        foreach (libxml_get_errors() as $error) {
            $errors[] = sprintf(
                '[%s %s] %s (in %s - line %d, column %d)',
                LIBXML_ERR_WARNING == $error->level ? 'WARNING' : 'ERROR',
                $error->code,
                trim($error->message),
                $error->file ? $error->file : 'n/a',
                $error->line,
                $error->column
            );
        }

        libxml_clear_errors();
        libxml_use_internal_errors($internalErrors);

        return $errors;
    }
}
