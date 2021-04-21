<?php

namespace Controllers;

use Services\Board;
class Sudoku
{
    /**
     * @var $sudokuBoard
     */
    protected $sudokuBoard;

    /**
     * Sudoku constructor
     *
     * @param Board $sudokuBoard
     */
    public function __construct(Board $sudokuBoard)
    {
        $this->sudokuBoard = $sudokuBoard;
    }

    /**
     * Sudoku getter
     */
    public function __get($prop) {
        return $this->$prop;
    }

    /**
     * Sudoku setter
     */
    public function __set($prop, $val) {
        $this->$prop = $val;
    }

    /**
     * Load file to sudoku board
     *
     * @param string $file_dir
     * @return bool
     */
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
