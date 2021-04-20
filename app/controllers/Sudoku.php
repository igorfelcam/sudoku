<?php

namespace Controllers;

use Services\Board;

class Sudoku
{
    protected $sudokuBoard;

    public function __construct(Board $sudokuBoard)
    {
        $this->sudokuBoard = $sudokuBoard;
    }

    public function __get($prop) {
        return $this->$prop;
    }

    public function __set($prop, $val) {
        $this->$prop = $val;
    }

    public function loadByFile($file_dir)
    {
        try {
            $this->sudokuBoard =
                $this->sudokuBoard->breakIntoItensOfBoard(
                    $this->sudokuBoard->breakIntoSectionsOfBoard(
                        $this->sudokuBoard->removeDivisorsOfBoard(
                            file($file_dir)
                        )
                    )
                );

            return true;

        } catch (\Exception $ex) {
            return $ex->getMessage();
        }
    }
}
