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
     * @param array $withHeuristic
     * @return array
     */
    public function resolveSudoku($sudokuBoard, $withHeuristic = false)
    {
        try {
            $nodeEmptyItems = $this->search->getEmptyItems($sudokuBoard, $withHeuristic);

            if (count($nodeEmptyItems) === 0) {
                return $sudokuBoard;
            }

            $searchTree = [];
            $node       = 0;
            $start_time = microtime(true);
            $index_valid_frontier   = 0;
            $currentStateNode       = $sudokuBoard;

            while (count($nodeEmptyItems) > 0) {

                $node++;
                $nodeEmptyItem      = $nodeEmptyItems[0];
                $frontiers          = $this->search->generateFrontierPossibilities($nodeEmptyItem, $currentStateNode);
                $validatesFrontiers = $this->search->validatesFrontiersPossibilities($nodeEmptyItem, $frontiers);
                // $validatesFrontiers = $nodeEmptyItems[0]['validates_frontiers'];

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

                    $nodeEmptyItems = $this->search->getEmptyItems($currentStateNode, $withHeuristic);
                    continue;
                }

                $searchTree[] = [
                    'empty_frontier_item'   => $nodeEmptyItem,
                    'node'                  => $node,
                    // 'frontiers'             => $frontiers,
                    'valid_frontiers'       => $validatesFrontiers
                ];

                $nodeEmptyItems = $this->search->getEmptyItems($currentStateNode, $withHeuristic);
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
