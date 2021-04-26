<?php
require 'controllers/Sudoku.php';
require 'services/Board.php';
require 'services/Search.php';

$sudoku = new \Controllers\Sudoku(
                new \Services\Board,
                new \Services\Search
            );


$sudoku->loadByFile($_FILES['sudoku_file']['tmp_name']);

ini_set('max_execution_time', 360);

$withHeuristic = $_POST['sudoku_method'] === "heuristic" ? true : false;
$resolvedSudoku = $sudoku->resolveSudoku($sudoku->sudokuBoard, $withHeuristic);

echo "<style>body { display: flex; align-items: center; justify-content: space-around; flex-wrap: wrap; }</style>";

$last_item = $resolvedSudoku['search_tree'][array_key_last($resolvedSudoku['search_tree'])];
echo "<section><div>Generated nodes: " .$last_item['node']. "</div>";

$seconds_execution = $resolvedSudoku['end_time'] - $resolvedSudoku['start_time'];
echo "<div>Seconds execution: " .$seconds_execution. "s</div></section>";

echo "<table style='border: 1px solid black;'>";

$sudoku_length  = count($resolvedSudoku['solution']);
$line_length    = (int) sqrt($sudoku_length);
$line_counter   = 0;

foreach ($resolvedSudoku['solution'] as $line_key => $line) {
    echo "<tr>";

    $line_counter++;

    $border_bottom  = false;
    $border_top     = false;

    if ($line_counter == $line_length && ($sudoku_length - 1) > $line_key) {
        $border_bottom  = true;
        $line_counter   = 0;
    }

    if ($line_counter === 1 && $line_key > 0) {
        $border_top = true;
    }

    $item_counter = 0;
    foreach ($line as $key => $item) {

        $item_counter++;
        $style = "";

        if ($border_bottom) {
            $style = "border-bottom: 3px solid black;";
        }
        else if ($border_top) {
            $style = "border-top: 3px solid black;";
        }

        if ($item_counter > 1 && ($item_counter - 1) == $line_length) {
            $style .= "border-left: 3px solid black;";
            $item_counter = 1;
        }
        else if ($item_counter === $line_length && ($sudoku_length / $item_counter) != 1 && $key < ($sudoku_length - 1)) {
            $style .= "border-right: 3px solid black;";
        }

        echo "<td style='border: 1px solid black; padding: .75rem 1rem;";
        echo "$style'>$item</td>";
    }

    echo "</tr>";
}

echo "</table>";
