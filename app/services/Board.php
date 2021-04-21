<?php

namespace Services;
class Board
{
    /**
     * Remove divisors string of sudoku board
     *
     * @param array $boardLines
     * @return array
     */
    public function removeDivisorsOfBoard($boardLines)
    {
        return array_filter(
            $boardLines,
            fn($line) => strstr($line, "|")
        );
    }

    /**
     * Break into section of sudoku board
     *
     * @param array $boardLines
     * @return array
     */
    public function breakIntoSectionsOfBoard($boardLines)
    {
        return array_map(
            fn($line) => explode("|", $line),
            $boardLines
        );
    }

    /**
     * Break into itens of sudoku board
     *
     * @param array $boardSections
     * @return array
     */
    public function breakIntoItensOfBoard($boardSections)
    {
        $board = [];

        foreach ($boardSections as $line) {
            $board[] =
                array_map(
                    function($item) {
                        $item = explode(" ", $item);
                        return array_filter($item, fn($i) => !empty($i));
                    },
                    $line
                );
        }

        return $this->removeSpacesOfBoard($board);
    }

    /**
     * Remove items spaces of sudoku board
     *
     * @param array $board
     * @return array
     */
    private function removeSpacesOfBoard($board)
    {
        array_walk_recursive(
            $board,
            fn(&$item) => $item = trim(str_replace("\n", "", $item))
        );

        return $board;
    }
}
