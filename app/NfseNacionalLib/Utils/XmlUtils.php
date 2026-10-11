<?php
namespace JCamelo\NfseNacionalLib\Utils;

use DOMDocument;
use DOMElement;
use InvalidArgumentException;

trait XmlUtils
{
    /**
     * Adiciona elemento obrigatório.
     */
    private static function appendText(
        DOMDocument $dom,
        DOMElement $parent,
        string $name,
        mixed $value
    ): DOMElement {

        if ($value === null || $value === '') {
            throw new InvalidArgumentException(
                "O campo {$name} é obrigatório."
            );
        }

        $element = $dom->createElement(
            $name
        );

        $element->appendChild(
            $dom->createTextNode((string) $value)
        );

        $parent->appendChild($element);

        return $element;
    }

    /**
     * Adiciona elemento somente quando possui valor.
     */
    private static function appendOptionalText(
        DOMDocument $dom,
        DOMElement $parent,
        string $name,
        mixed $value
    ): ?DOMElement {

        if ($value === null || $value === '') {
            return null;
        }

        $element = $dom->createElement(
            $name
        );

        $element->appendChild(
            $dom->createTextNode((string) $value)
        );

        $parent->appendChild($element);

        return $element;
    }
}
