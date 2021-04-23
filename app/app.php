<?php
require 'controllers/Sudoku.php';
require 'services/Board.php';
require 'services/Search.php';

$sudoku = new \Controllers\Sudoku(
                new \Services\Board,
                new \Services\Search
            );

$sudoku->loadByFile($_FILES['sudoku_file']['tmp_name']);

$resolvedSudoku = $sudoku->resolveSudoku($sudoku->sudokuBoard);

echo "<style>body { display: flex; align-items: center; justify-content: space-around; flex-wrap: wrap; }</style>";

$last_item = $resolvedSudoku['search_tree'][array_key_last($resolvedSudoku['search_tree'])];
echo "<div>Generated nodes: " .$last_item['node']. "</div>";

echo "<table style='border: 1px solid black;'>";

$sudoku_length  = count($resolvedSudoku['solution']);
$line_counter   = 0;

foreach ($resolvedSudoku['solution'] as $line_key => $line) {
    echo "<tr>";

    $line_counter++;
    $line_length = count($line);

    $border_bottom  = false;
    $border_top     = false;
    if ($line_counter == $line_length && ($sudoku_length - 1) > $line_key) {
        $border_bottom  = true;
        $line_counter   = 0;
    }
    if ($line_counter === 1 && $line_key > 0) {
        $border_top     = true;
    }

    $item_counter = 0;
    array_walk_recursive(
        $line,
        function($item, $key) use (&$item_counter, $sudoku_length, $line_length, $border_bottom, $border_top) {
            $item_counter++;

            echo "<td style='border: 1px solid black; padding: .75rem 1rem;";

            $style = "";
            if ($border_bottom) {
                $style = "border-bottom: 3px solid black;";
            }
            else if ($border_top) {
                $style = "border-top: 3px solid black;";
            }

            if ($item_counter > 1 && $key == 0) {
                $style .= "border-left: 3px solid black;";
            }
            else if (($key + 1) === $line_length && ($sudoku_length / $item_counter) != 1) {
                $style .= "border-right: 3px solid black;";
            }

            echo "$style'>$item</td>";
        },
        $item_counter
    );

    echo "</tr>";
}

echo "</table>";
