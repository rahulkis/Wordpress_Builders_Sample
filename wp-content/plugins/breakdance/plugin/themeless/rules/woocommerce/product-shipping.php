<?php

namespace Breakdance\Themeless\Rules;

use function Breakdance\Util\WP\safe_get_terms;

\Breakdance\Themeless\registerCondition(
    [
        'supports' => ['element_display', 'query_builder'],
        'availableForType' => getProductConditionPostTypes(),
        'slug' => 'woocommerce-product-shipping-class',
        'label' => 'Shipping Class',
        'category' => 'Product',
        'operands' => [OPERAND_ONE_OF, OPERAND_NONE_OF, OPERAND_ALL_OF],
        'values' => function () {
            /** @var \WP_Term[] $terms */
            $terms = safe_get_terms(['taxonomy' => 'product_shipping_class', 'hide_empty' => false]);
            $items = [];

            /**
             * get_terms can sometimes return an array of associative arrays instead
             * of WP_Term objects, so use wp_list_pluck to get the required properties
             * which works either an object or associative array
             *
             * @var string[] $termNamesKeyedByTermId
             */
            $termNamesKeyedByTermId = wp_list_pluck($terms, 'name', 'term_id');;
            foreach ($termNamesKeyedByTermId as $termId => $termName) {
                $items[] = ['text' => $termName, 'value' => (string) $termId];
            }

            return [[
                'label' => 'Shipping Classes',
                'items' => $items
            ]];
        },
        'callback' => /**
         * @param mixed $operand
         * @param string[] $value
         * @return bool
         */
            function ($operand, $value): bool {
                $results = array_map(static function($termId) {
                    return has_term((int) $termId);
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
         * @param array{value: string}[] $value
         * @return WordPressQueryVars
         */
            function ($query, $operand, $value) {
                if (!$value) {
                    return $query;
                }
                $taxonomies = [];
                foreach ($value as $selected) {
                    /** @var \WP_Term $term */
                    $term = get_term((int) $selected['value']);
                    if (is_wp_error($term)) {
                        continue;
                    }
                    $taxonomies[$term->taxonomy][] = $term->term_id;
                }
                $operator = operandToQueryCompare($operand);
                $taxQuery = $query['tax_query'] ?? [];
                foreach ($taxonomies as $taxonomy => $terms) {
                    $taxQuery[] = [
                        'operator' => $operator,
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
