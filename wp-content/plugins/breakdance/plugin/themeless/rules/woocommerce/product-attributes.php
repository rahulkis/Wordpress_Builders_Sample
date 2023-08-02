<?php

namespace Breakdance\Themeless\Rules;

if (function_exists('wc_get_attribute_taxonomies')) {
    \Breakdance\Themeless\registerCondition(
        [
            'supports' => ['element_display', 'query_builder'],
            'availableForType' => getProductConditionPostTypes(),
            'slug' => 'woocommerce-product-attributes',
            'label' => 'Attributes',
            'category' => 'Product',
            'operands' => [OPERAND_ONE_OF, OPERAND_NONE_OF, OPERAND_ALL_OF],
            'values' => function () {
                /** @var object{attribute_name: string}[] $attributes */
                $attributes = wc_get_attribute_taxonomies();
                $items = [];
                foreach ($attributes as $attribute) {
                    /** @var \WP_Term[] $terms */
                    $terms = get_terms(['taxonomy' => 'pa_' . $attribute->attribute_name]);

                    if (is_wp_error($terms)){
                        continue;
                    }

                    /**
                     * get_terms can sometimes return an array of associative arrays instead
                     * of WP_Term objects, so use wp_list_pluck to get the required properties
                     * which works either an object or associative array
                     *
                     * @var string[] $termNamesKeyedByTermId
                     */
                    $termNamesKeyedByTermId = wp_list_pluck($terms, 'name', 'term_id');
                    foreach ($termNamesKeyedByTermId as $termId => $termName) {
                        $items[] = ['text' => $termName, 'value' => (string) $termId];
                    }
                }
                return [[
                    'label' => 'Attributes',
                    'items' => $items
                ]];

                return false;
            },
            'callback' => /**
             * @param string $operand
             * @param string[] $value
             * @return bool
             */
                function ($operand, $value): bool {
                    $results = array_map(static function($termId) {
                        /** @var \WP_term $term */
                        $term = get_term((int) $termId);
                        if (is_wp_error($term)) {
                            return false;
                        }
                        return has_term($termId, $term->taxonomy);
                    }, $value);
                    if ($operand === OPERAND_ONE_OF) {
                        return in_array(true, $results);
                    }
                    if ($operand === OPERAND_NONE_OF) {
                        return !in_array(true, $results);
                    }
                    if ($operand === OPERAND_ALL_OF) {
                        return !in_array(false, $results);
                    }
                    return false;
                },
            'templatePreviewableItems' => false,
            'queryCallback' => /**
             * @param WordPressQueryVars $query
             * @param string $operand
             * @param mixed $value
             * @return WordPressQueryVars
             */
            function ($query, $operand, $value) {
                if (!is_array($value)) {
                    return $query;
                }
                $taxonomies = [];
                /** @var array{value: string} $selected */
                foreach ($value as $selected) {
                    $termId = (int) $selected['value'];
                    if (!$termId) {
                        continue;
                    }
                    // selected value is a term ID which needs to be mapped
                    // to it's taxonomy slug for the tax_query
                    /** @var \WP_Term $term */
                    $term = get_term((int) $termId);
                    if (is_wp_error($term)) {
                        continue;
                    }
                    $taxonomies[$term->taxonomy][] = $term->term_id;
                }
                $taxQuery = $query['tax_query'] ?? [];
                foreach ($taxonomies as $taxonomy => $terms) {
                    $compare = operandToQueryCompare($operand);
                    $taxQuery[] = [
                        'operator' => $compare,
                        'taxonomy' => $taxonomy,
                        'terms' => $terms
                    ];
                }

                if (!empty($taxQuery)) {
                    /** @var array<array-key, WordPressTaxQuery|string> $taxQuery */
                    $query['tax_query'] = $taxQuery;
                }

                return $query;
            },
        ]
    );
}
