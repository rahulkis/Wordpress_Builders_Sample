<?php

namespace Breakdance\Themeless\Rules;

use function Breakdance\Util\WP\safe_get_terms;

\Breakdance\Themeless\registerCondition(
    [
        'supports' => ['element_display', 'query_builder'],
        'availableForType' => getProductConditionPostTypes(),
        'slug' => 'woocommerce-product-categories',
        'label' => 'Categories',
        'category' => 'Product',
        'operands' => [OPERAND_ONE_OF, OPERAND_NONE_OF, OPERAND_ALL_OF],
        'values' => function () {
            /** @var \WP_Term[] $terms */
            $terms = safe_get_terms(['taxonomy' => 'product_cat', 'hide_empty' => false]);
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
                'label' => 'Categories',
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
                    return has_term($termId, 'product_cat');
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
                    // selected value is a term ID which needs to be mapped
                    // to it's taxonomy slug for the tax_query
                    $term = get_term((int) $selected['value']);
                    if (!$term || is_wp_error($term)) {
                        continue;
                    }

                    /** @var \WP_Term $term */
                    $term = $term;
                    $taxonomies[$term->taxonomy][] = $term->term_id;
                }
                $taxQuery = $query['tax_query'] ?? [];
                foreach ($taxonomies as $taxonomy => $terms) {
                    $taxQuery[] = [
                        'operator' => operandToQueryCompare($operand),
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
