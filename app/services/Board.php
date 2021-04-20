<?php

namespace Services;

class Board
{
    public function removeDivisorsOfBoard($boardLines)
    {
        return array_filter(
            $boardLines,
            fn($line) => strstr($line, "|")
        );
    }

    public function breakIntoSectionsOfBoard($boardLines)
    {
        return array_map(
            fn($line) => explode("|", $line),
            $boardLines
        );
    }

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

    private function removeSpacesOfBoard($board)
    {
        array_walk_recursive(
            $board,
            fn(&$item) => $item = trim(str_replace("\n", "", $item))
        );

        return $board;
    }
}
