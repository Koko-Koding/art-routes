<?php
/**
 * SVG Sanitizer for Art Routes Plugin
 *
 * Sanitizes SVG files to prevent XSS attacks by removing
 * potentially dangerous elements and attributes.
 */

// If this file is called directly, abort.
if (!defined('ABSPATH')) {
    exit;
}

/**
 * SVG Sanitizer Class
 */
class WP_Art_Routes_SVG_Sanitizer {

    /**
     * Allowed SVG elements
     *
     * @var array
     */
    private $allowed_elements = [
        'svg', 'g', 'path', 'circle', 'ellipse', 'line', 'polygon', 'polyline',
        'rect', 'text', 'tspan', 'textPath', 'defs', 'symbol', 'use', 'image',
        'clipPath', 'mask', 'pattern', 'linearGradient', 'radialGradient', 'stop',
        'title', 'desc', 'metadata', 'switch', 'foreignObject',
    ];

    /**
     * Allowed attributes
     *
     * @var array
     */
    private $allowed_attributes = [
        // Core attributes
        'id', 'class', 'style', 'lang', 'tabindex',
        // Presentation attributes
        'fill', 'fill-opacity', 'fill-rule', 'stroke', 'stroke-width',
        'stroke-linecap', 'stroke-linejoin', 'stroke-dasharray', 'stroke-dashoffset',
        'stroke-opacity', 'opacity', 'transform', 'display', 'visibility',
        'color', 'font-family', 'font-size', 'font-style', 'font-weight',
        'text-anchor', 'text-decoration', 'dominant-baseline', 'alignment-baseline',
        // Geometry attributes
        'x', 'y', 'width', 'height', 'cx', 'cy', 'r', 'rx', 'ry',
        'd', 'points', 'x1', 'y1', 'x2', 'y2',
        // ViewBox and sizing
        'viewBox', 'preserveAspectRatio', 'xmlns', 'xmlns:xlink', 'version',
        // Gradient/pattern attributes
        'gradientUnits', 'gradientTransform', 'spreadMethod', 'offset',
        'stop-color', 'stop-opacity', 'patternUnits', 'patternTransform',
        // Clip/mask attributes
        'clipPathUnits', 'maskUnits', 'maskContentUnits',
        'clip-path', 'clip-rule', 'mask',
        // Reference attributes
        'href', 'xlink:href',
        // Image attributes
        'xlink:title',
    ];

    /**
     * Sanitize SVG content
     *
     * @param string $svg_content The SVG content to sanitize
     * @return string|false Sanitized SVG content or false on failure
     */
    public function sanitize($svg_content) {
        if (empty($svg_content)) {
            return false;
        }

        // Disable external entity loading
        $previous_value = libxml_disable_entity_loader(true);
        libxml_use_internal_errors(true);

        // Parse the SVG
        $dom = new DOMDocument();
        $dom->formatOutput = true;
        $dom->preserveWhiteSpace = false;

        // Load the SVG with proper encoding
        if (!$dom->loadXML($svg_content, LIBXML_NONET | LIBXML_NOBLANKS)) {
            libxml_disable_entity_loader($previous_value);
            libxml_clear_errors();
            return false;
        }

        // Check if root element is SVG
        $root = $dom->documentElement;
        if (!$root || strtolower($root->nodeName) !== 'svg') {
            libxml_disable_entity_loader($previous_value);
            return false;
        }

        // Recursively sanitize all elements
        $this->sanitize_element($root);

        // Get the sanitized SVG
        $sanitized = $dom->saveXML($root);

        libxml_disable_entity_loader($previous_value);
        libxml_clear_errors();

        return $sanitized;
    }

    /**
     * Recursively sanitize an element and its children
     *
     * @param DOMElement $element The element to sanitize
     */
    private function sanitize_element($element) {
        if (!$element instanceof DOMElement) {
            return;
        }

        // Get all child nodes (make a copy since we'll be modifying)
        $children = [];
        foreach ($element->childNodes as $child) {
            $children[] = $child;
        }

        // Process children first
        foreach ($children as $child) {
            if ($child instanceof DOMElement) {
                $tag_name = strtolower($child->nodeName);

                // Remove script and other dangerous elements
                if ($this->is_dangerous_element($tag_name)) {
                    $element->removeChild($child);
                    continue;
                }

                // Remove elements not in whitelist
                if (!in_array($tag_name, $this->allowed_elements, true)) {
                    // Keep text content, remove the element
                    while ($child->firstChild) {
                        $element->insertBefore($child->firstChild, $child);
                    }
                    $element->removeChild($child);
                    continue;
                }

                // Sanitize this element's attributes
                $this->sanitize_attributes($child);

                // Recursively process children
                $this->sanitize_element($child);
            }
        }
    }

    /**
     * Check if an element is dangerous
     *
     * @param string $tag_name The tag name to check
     * @return bool True if dangerous
     */
    private function is_dangerous_element($tag_name) {
        $dangerous = [
            'script', 'iframe', 'object', 'embed', 'applet',
            'form', 'input', 'button', 'select', 'textarea',
            'frame', 'frameset', 'layer', 'ilayer', 'bgsound',
            'base', 'link', 'meta', 'style', 'animate',
            'set', 'handler', 'listener',
        ];

        return in_array($tag_name, $dangerous, true);
    }

    /**
     * Sanitize element attributes
     *
     * @param DOMElement $element The element to sanitize
     */
    private function sanitize_attributes($element) {
        $attributes_to_remove = [];

        foreach ($element->attributes as $attr) {
            $attr_name = strtolower($attr->nodeName);
            $attr_value = $attr->nodeValue;

            // Check if attribute is allowed
            if (!$this->is_allowed_attribute($attr_name)) {
                $attributes_to_remove[] = $attr->nodeName;
                continue;
            }

            // Check for dangerous attribute values
            if ($this->has_dangerous_value($attr_value)) {
                $attributes_to_remove[] = $attr->nodeName;
                continue;
            }
        }

        // Remove disallowed attributes
        foreach ($attributes_to_remove as $attr_name) {
            $element->removeAttribute($attr_name);
        }
    }

    /**
     * Check if an attribute is allowed
     *
     * @param string $attr_name The attribute name to check
     * @return bool True if allowed
     */
    private function is_allowed_attribute($attr_name) {
        // Always block event handlers
        if (strpos($attr_name, 'on') === 0) {
            return false;
        }

        return in_array($attr_name, $this->allowed_attributes, true);
    }

    /**
     * Check if attribute value contains dangerous content
     *
     * @param string $value The attribute value to check
     * @return bool True if dangerous
     */
    private function has_dangerous_value($value) {
        $value = strtolower($value);

        // Check for javascript: URLs
        if (preg_match('/^\s*javascript\s*:/i', $value)) {
            return true;
        }

        // Check for data: URLs (except safe image types)
        if (preg_match('/^\s*data\s*:/i', $value)) {
            // Allow data URIs for images only
            if (!preg_match('/^\s*data\s*:\s*image\/(png|gif|jpeg|jpg|webp|svg\+xml)\s*[;,]/i', $value)) {
                return true;
            }
        }

        // Check for vbscript: URLs
        if (preg_match('/^\s*vbscript\s*:/i', $value)) {
            return true;
        }

        // Check for expression() in CSS
        if (preg_match('/expression\s*\(/i', $value)) {
            return true;
        }

        // Check for url() with javascript
        if (preg_match('/url\s*\(\s*["\']?\s*javascript/i', $value)) {
            return true;
        }

        return false;
    }
}
