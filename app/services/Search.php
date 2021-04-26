<?php

namespace Services;

class Search
{
    /**
     * Get empty items of parent node
     *
     * @param array $parentNode
     * @param bool $withHeuristic
     * @return array
     */
    public function getIndexOfEmptyItems($parentNode, $withHeuristic = false)
    {
        $indexOfEmptyItems = [];

        foreach ($parentNode as $line_key => $line) {
            foreach ($line as $item_key => $item) {
                if ($item === "_") {
                    $indexOfEmptyItems[] = [
                        'line'          => $line_key,
                        'column'        => $item_key,
                        'possibilities' => 0
                    ];
                }
            }
        }

        if ($withHeuristic) {
            foreach ($indexOfEmptyItems as &$indexOfEmptyItem) {
                $indexOfEmptyItem['possibilities'] = count($this->validatesFrontiers($indexOfEmptyItem, $parentNode));
            }

            usort(
                $indexOfEmptyItems,
                fn($a, $b) => $a['possibilities'] <=> $b['possibilities']
            );
        }

        return $indexOfEmptyItems;
    }

    /**
     * Get validates frontiers to empty item
     * 
     * @param array $indexOfEmptyItem
     * @param array $parentNode
     * @return array
     */
    public function validatesFrontiers($indexOfEmptyItem, $parentNode)
    {
        return $this->validatesFrontiersPossibilities(
            $indexOfEmptyItem,
            $this->generateFrontierPossibilities(
                $indexOfEmptyItem,
                $parentNode
            )
        );
    }

    /**
     * Get the index of next valid frontier not verified
     *
     * @param array $validatesFrontiers
     * @return int $index_valid_frontier
     */
    public function getIndexNextValidFrontierNotVerified($validatesFrontiers)
    {
        $filterValidFrontiers = array_filter(
            $validatesFrontiers,
            fn($frontier) => !$frontier['is_verified']
        );

        return array_key_first($filterValidFrontiers);
    }

    /**
     * Generate frontier with possibilities
     *
     * @param array $parentNode
     * @param array $emptyItem
     * @return array $possibilities
     */
    private function generateFrontierPossibilities($indexOfEmptyItem, $parentNode)
    {
        $possiblesNodes = [];
        $possible_items = count($parentNode);

        for ($i = 0; $i < $possible_items; $i++) {
            $possiblesNodes[] = $parentNode;
            $possiblesNodes[$i][$indexOfEmptyItem['line']][$indexOfEmptyItem['column']] = (string) ($i+1);
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
    private function validatesFrontiersPossibilities($indexOfEmptyItem, $possibleFrontiers)
    {
        $validFrontiers = [];

        foreach ($possibleFrontiers as $key => $frontier) {

            $possibility_item_value = $frontier[$indexOfEmptyItem['line']][$indexOfEmptyItem['column']];

            if (!$this->validSudokuLine($possibility_item_value, $frontier[$indexOfEmptyItem['line']])) {
                continue;
            }

            if (!$this->validSudokuColumn($possibility_item_value, $frontier, $indexOfEmptyItem['column'])) {
                continue;
            }

            if (!$this->validSudokuBlock($frontier, $indexOfEmptyItem)) {
                continue;
            }

            $validFrontiers[] = [
                'possibility_item_value'    => $possibility_item_value,
                'sudoku_board'              => $frontier,
                'is_verified'               => false
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
    private function validSudokuLine($item_value, $line)
    {
        $number_repeat_items    = 0;
        $is_valid               = true;

        foreach ($line as $value) {
            if ($value === $item_value) {
                $number_repeat_items++;
            }
        }

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
    private function validSudokuColumn($item_value, $sudokuBoard, $column_id)
    {
        $number_repeat_items    = 0;
        $is_valid               = true;

        foreach ($sudokuBoard as $line) {
            if ($line[$column_id] === $item_value) {
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
    private function validSudokuBlock($sudokuBoard, $indexOfItem)
    {
        $dimension_board    = count($sudokuBoard);
        $block_length       = (int) sqrt($dimension_board);

        $block_line_id  = 0;
        $count_line     = 0;
        for ($i = 0; $i < $dimension_board; $i++) {

            if ($count_line === $block_length) {
                $block_line_id++;
                $count_line = 0;
            }

            if ($indexOfItem['line'] === $i) {
                break;
            }

            $count_line++;
        }

        $block_column_id    = 0;
        $count_column       = 0;
        for ($i = 0; $i < $dimension_board; $i++) {

            if ($count_column === $block_length) {
                $block_column_id++;
                $count_column = 0;
            }

            if ($indexOfItem['column'] === $i) {
                break;
            }

            $count_column++;
        }

        $count_line     = 0;
        $count_block    = 0;
        $lineBlockItems = [];
        foreach ($sudokuBoard as $line) {
            
            if ($count_line === $block_length) {
                $count_block++;
                $count_line = 0;
            }

            if ($count_block === $block_line_id) {
                $lineBlockItems[] = $line;
            }

            $count_line++;
        }

        $blockItems = [];
        foreach ($lineBlockItems as $key => $line) {
            $count_column     = 0;
            $count_block    = 0;
            
            foreach ($line as $item) {
                
                if ($count_column === $block_length) {
                    $count_block++;
                    $count_column = 0;
                }
    
                if ($count_block === $block_column_id) {
                    $blockItems[] = $item;
                }
    
                $count_column++;
            }

        }

        $number_repeat_items    = 0;
        $is_valid               = true;

        foreach ($blockItems as $item) {
            if ($item === $sudokuBoard[$indexOfItem['line']][$indexOfItem['column']]) {
                $number_repeat_items++;
            }
        }

        if ($number_repeat_items > 1) {
            $is_valid = false;
        }
        
        return $is_valid;
    }







    /**
     * Get possible item possibilities
     *
     * @param array $parentNode
     * @param array $emptyItems
     * @return array $emptyItemsWithPossibilities
     */
    private function getPossibleItemPossibilities($emptyItems, $parentNode)
    {
        foreach ($emptyItems as $key => &$emptyItem) {
            $frontierPossibilities  = $this->generateFrontierPossibilities($emptyItem, $parentNode);
            $validatesFrontiers     = $this->validatesFrontiersPossibilities($emptyItem, $frontierPossibilities);

            $emptyItems[$key]['possibilities'] = count($validatesFrontiers);
        }

        usort(
            $emptyItems,
            fn($a, $b) => $a['possibilities'] <=> $b['possibilities']
        );

        return $emptyItems;
    }
}
