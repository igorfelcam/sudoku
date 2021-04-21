<?php

namespace Controllers;

use Services\Board;
use Services\Search;
class Sudoku
{
    /**
     * @var $sudokuBoard
     * @var $search
     */
    protected $sudokuBoard;
    protected $search;

    /**
     * Sudoku constructor
     *
     * @param Board $sudokuBoard
     * @param Search $search
     */
    public function __construct(Board $sudokuBoard, Search $search)
    {
        $this->sudokuBoard  = $sudokuBoard;
        $this->search       = $search;
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

    /**
     * Resolve Sudoku
     *
     * @param array $sudokuBoard
     */
    public function resolveSudoku($sudokuBoard)
    {
        try {

            $emptyItems = $this->search->getEmptyItems($sudokuBoard);

            return $emptyItems;


        } catch (\Exception $ex) {
            return $ex->getMessage();
        }
    }
}
