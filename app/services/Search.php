<?php

namespace Services;

class Search
{
    /**
     * Get empty items of parent node
     *
     * @param array $parentNode
     * @return array $emptyItems
     */
    public function getEmptyItems($parentNode)
    {
        $emptyItems = [];

        // walk line
        foreach ($parentNode as $line_key => $line) {
            // walk section
            foreach ($line as $section_key => $section) {
                // walk item
                foreach ($section as $item_key => $item) {
                    if ($item === "_") {
                        $emptyItems[] = [
                            "line"      => $line_key,
                            "section"   => $section_key,
                            "item"      => $item_key
                        ];
                    }
                }
            }
        }

        return $emptyItems;
    }

    /**
     * Generate frontier with possibilities
     *
     * @param array $parentNode
     * @param array $emptyItem
     * @return array $possibilities
     */
    public function generateFrontierPossibilities($emptyItem, $parentNode)
    {
        $possiblesNodes = [];
        $possible_items = count($parentNode);

        for ($i = 0; $i < $possible_items; $i++) {
            $possiblesNodes[] = $parentNode;
            $possiblesNodes[$i][$emptyItem['line']][$emptyItem['section']][$emptyItem['item']] = (string) ($i+1);
        }

        return $possiblesNodes;
    }

    /**
     * Validates frontiers possibilities
     *
     * @param array $possibilityItem
     * @param array $frontierPossibilities
     * @return array $validFrontiers
     */
    public function validatesFrontiersPossibilities($possibilityItem, $frontierPossibilities)
    {
        $validFrontiers = [];

        foreach ($frontierPossibilities as $key => $frontierPossibilitie) {

            $possibility_item_value = $frontierPossibilitie[$possibilityItem['line']][$possibilityItem['section']][$possibilityItem['item']];

            if (!$this->validSudokuLine($possibility_item_value, $frontierPossibilitie[$possibilityItem['line']])) {
                continue;
            }

            if (!$this->validSudokuColumn($possibility_item_value, $frontierPossibilitie, $possibilityItem)) {
                continue;
            }

            if (!$this->validSudokuBlock($possibility_item_value, $frontierPossibilitie, $possibilityItem)) {
                continue;
            }

            $validFrontiers[] = [
                'possibility_item_value'    => $possibility_item_value,
                'sudoku_board'              => $frontierPossibilitie
            ];
        }

        return $validFrontiers;
    }

    /**
     * Valid possibility item in sudoku line
     *
     * @param int $possibility_item_value
     * @param array $frontierPossibilitieLine
     * @return bool $is_valid
     */
    private function validSudokuLine($possibility_item_value, $frontierPossibilitieLine)
    {
        $number_repeat_items    = 0;
        $is_valid               = true;

        array_walk_recursive(
            $frontierPossibilitieLine,
            function($item) use (&$possibility_item_value, &$number_repeat_items) {
                if ($item === $possibility_item_value) {
                    $number_repeat_items++;
                }
            }
        );

        if ($number_repeat_items > 1) {
            $is_valid = false;
        }

        return $is_valid;
    }

    /**
     * Valid possibility item in sudoku column
     *
     * @param int $possibility_item_value
     * @param array $frontierPossibilitie
     * @param array $possibilityItem
     * @return bool $is_valid
     */
    private function validSudokuColumn($possibility_item_value, $frontierPossibilitie, $possibilityItem)
    {
        $number_repeat_items   = 0;
        $is_valid               = true;

        for ($i = 0; $i < count($frontierPossibilitie); $i++) {
            if ($possibility_item_value === $frontierPossibilitie[$i][$possibilityItem['section']][$possibilityItem['item']]) {
                $number_repeat_items++;
            }
        }

        if ($number_repeat_items > 1) {
            $is_valid = false;
        }

        return $is_valid;
    }

    /**
     * Valid possibility item in sudoku block section
     *
     * @param int $possibility_item_value
     * @param array $frontierPossibilitie
     * @param array $possibilityItem
     * @return bool $is_valid
     */
    private function validSudokuBlock($possibility_item_value, $frontierPossibilitie, $possibilityItem)
    {
        $blocks_on_the_board        = sqrt(count($frontierPossibilitie));
        $block_of_item_line         = intval($possibilityItem['line'] / $blocks_on_the_board);
        $number_predecessor_lines   = 0;
        $number_repeat_items        = 0;
        $is_valid                   = true;

        if ($possibilityItem['line'] - 1 >= 0) {
            for ($i = ($possibilityItem['line'] - 1); $i > 0; $i--) {
                $block_line = intval( $i / $blocks_on_the_board );

                if ($block_line === $block_of_item_line) {
                    $number_predecessor_lines++;
                }
            }
        }

        $number_successor_lines = intval( $blocks_on_the_board - $number_predecessor_lines - 1 );
        $block_items            = [];
        $block_items[]          = $frontierPossibilitie[$possibilityItem['line']][$possibilityItem['section']];

        if ($number_predecessor_lines > 0) {
            for ($i = 0; $i < $number_predecessor_lines; $i++) {
                $index_line = $possibilityItem['line'] - ($i + 1);
                $block_items[] = $frontierPossibilitie[$index_line][$possibilityItem['section']];
            }
        }

        if ($number_successor_lines > 0) {
            for ($i = 0; $i < $number_successor_lines; $i++) {
                $index_line = $possibilityItem['line'] + ($i + 1);
                $block_items[] = $frontierPossibilitie[$index_line][$possibilityItem['section']];
            }
        }

        array_walk_recursive(
            $block_items,
            function($item) use (&$possibility_item_value, &$number_repeat_items) {
                if ($item === $possibility_item_value) {
                    $number_repeat_items++;
                }
            }
        );

        if ($number_repeat_items > 1) {
            $is_valid = false;
        }

        return $is_valid;
    }

}
