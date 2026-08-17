<?php

namespace JCamelo\NfseNacionalLib\XML\Validator;

class XmlValidator
{
    public static function validate(string $xml, string $xsdPath): array
    {
        libxml_use_internal_errors(true);

        $doc = new \DOMDocument();
        $doc->loadXML($xml);

        $isValid = $doc->schemaValidate($xsdPath);

        $errors = libxml_get_errors();

        return [
            'valid' => $isValid,
            'errors' => $errors
        ];
    }
}
