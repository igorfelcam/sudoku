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
     * Load file to sudoku board
     *
     * @param string $file_dir
     * @return bool
     */
    public function loadByFile($file_dir)
    {
        try {
            $this->sudokuBoard = $this->sudokuBoard->getBoardAsMultidimensionalData(
                file($file_dir)
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
     * @param bool $withHeuristic
     * @return array
     */
    public function resolveSudoku($sudokuBoard, $withHeuristic = false)
    {
        try {
            $nodeEmptyItems = $this->search->getIndexOfEmptyItems($sudokuBoard, $withHeuristic);

            if (count($nodeEmptyItems) === 0) {
                return $sudokuBoard;
            }

            $start_time = microtime(true);
            $searchTree = [];
            $node       = 0;
            $index_valid_frontier   = 0;
            $currentStateNode       = $sudokuBoard;

            while (count($nodeEmptyItems) > 0) {
                $node++;
                $validatesFrontiers = $nodeEmptyItems[0]['valid_frontiers'];

                if (count($validatesFrontiers) > 0) {
                    $index_valid_frontier = $this->search->getIndexNextValidFrontierNotVerified($validatesFrontiers);

                    if ($index_valid_frontier === null) {
                        throw new \Exception("Index valid frontier not found", 400);
                    }

                    $currentStateNode = $validatesFrontiers[$index_valid_frontier]['sudoku_board'];
                    $validatesFrontiers[$index_valid_frontier]['is_verified'] = true;
                }
                else {
                    $index_valid_frontier = null;

                    while ($index_valid_frontier === null) {
                        $parent_index = array_key_last($searchTree);

                        if ($parent_index === null) {
                            throw new \Exception("Parent index not found", 400);
                        }

                        $validatesFrontiers     = $searchTree[$parent_index]['valid_frontiers'];
                        $index_valid_frontier   = $this->search->getIndexNextValidFrontierNotVerified($validatesFrontiers);

                        if ($index_valid_frontier === null) {
                            unset($searchTree[$parent_index]);
                            $searchTree = array_values($searchTree);
                        }
                    }

                    $currentStateNode = $validatesFrontiers[$index_valid_frontier]['sudoku_board'];
                    $searchTree[$parent_index]['valid_frontiers'][$index_valid_frontier]['is_verified'] = true;

                    $nodeEmptyItems = $this->search->getIndexOfEmptyItems($currentStateNode, $withHeuristic);
                    continue;
                }

                $searchTree[] = [
                    'empty_frontier_item'   => $nodeEmptyItems[0],
                    'node'                  => $node,
                    'valid_frontiers'       => $validatesFrontiers
                ];

                $nodeEmptyItems = $this->search->getIndexOfEmptyItems($currentStateNode, $withHeuristic);
            }

            $end_time = microtime(true);

            return [
                'solution'      => $currentStateNode,
                'search_tree'   => $searchTree,
                'start_time'    => $start_time,
                'end_time'      => $end_time
            ];

        } catch (\Exception $ex) {
            return $ex->getMessage();
        }
    }
}
