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
    public function getBoardAsMultidimensionalData($boardLines)
    {
        return array_values(
            $this->removeColumnDivisorsOfBoard(
                $this->removeLineDivisorsOfBoard($boardLines)
            )
        );
    }

    /**
     * Remove line divisors string of sudoku board
     *
     * @param array $boardLines
     * @return array
     */
    private function removeLineDivisorsOfBoard($boardLines)
    {
        return array_map(
            fn($item) => trim(str_replace("\n", "", $item)),
            array_filter(
                $boardLines,
                fn($line) => strstr($line, "|")
            )
        );
    }

    /**
     * Remove column divisors string of sudoku board
     *
     * @param array $boardLines
     * @return array
     */
    private function removeColumnDivisorsOfBoard($boardLines)
    {
        return array_map(
            fn($line) => explode(" ", str_replace("| ", "", $line)),
            $boardLines
        );
    }
}
